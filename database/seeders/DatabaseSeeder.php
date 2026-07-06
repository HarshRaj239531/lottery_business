<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'member']);
        Role::firstOrCreate(['name' => 'agent']);

        // Create Test User (Super Admin + member)
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );
        if (!$user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
        }
        if (!$user->hasRole('member')) {
            $user->assignRole('member');
        }

        // Create Admin User (Super Admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );
        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }

        // Create a fake agent
        $agent = User::firstOrCreate(
            ['email' => 'agent@example.com'],
            ['name' => 'Field Agent', 'password' => bcrypt('password')]
        );
        $agent->assignRole('agent');

        $faker = \Faker\Factory::create();

        // Create 10 Fake Members
        $members = collect();
        for ($i = 0; $i < 10; $i++) {
            $member = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'password' => bcrypt('password')
            ]);
            $member->assignRole('member');
            $members->push($member);
        }

        // Create 3 Fake Committees
        $committees = collect();
        foreach (['Gold Plan', 'Silver Plan', 'Bronze Plan'] as $plan) {
            $committee = \App\Models\Committee::create([
                'name' => $plan,
                'amount' => $faker->randomElement([5000, 10000, 20000]),
                'total_members' => 10,
                'duration' => 10,
                'payment_frequency' => 'monthly',
                'start_date' => now()->subMonths(2)->format('Y-m-d'),
                'end_date' => now()->addMonths(8)->format('Y-m-d'),
                'status' => 'active'
            ]);
            $committees->push($committee);
            
            // Enroll 5 random members in each committee
            $enrolled = $members->random(5);
            $committee->members()->attach($enrolled->pluck('id')->toArray());

            // Add some Installment history for these enrolled members
            foreach ($enrolled as $member) {
                // Past Paid Installment
                \App\Models\Installment::create([
                    'user_id' => $member->id,
                    'committee_id' => $committee->id,
                    'amount' => $committee->amount / $committee->duration,
                    'paid_date' => now()->subMonth()->format('Y-m-d'),
                    'due_date' => now()->subMonth()->format('Y-m-d'),
                    'status' => 'paid',
                    'collected_by' => $agent->id
                ]);

                // Current Pending Installment
                \App\Models\Installment::create([
                    'user_id' => $member->id,
                    'committee_id' => $committee->id,
                    'amount' => $committee->amount / $committee->duration,
                    'paid_date' => null,
                    'due_date' => now()->format('Y-m-d'),
                    'status' => 'pending',
                    'collected_by' => null
                ]);
            }

            // Generate 1 past lottery draw for each committee
            \App\Models\Lottery::create([
                'committee_id' => $committee->id,
                'winner_id' => $enrolled->random()->id,
                'draw_date' => now()->subMonth()->format('Y-m-d'),
            ]);
        }

        // Create 4 Materials (Cement, Concrete, Bricks, Steel)
        $materialsData = [
            [
                'name' => 'Cement',
                'price' => 385.00,
                'unit' => 'kg',
                'image_url' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?q=80&w=200&auto=format&fit=crop',
                'status' => 'active'
            ],
            [
                'name' => 'Concrete',
                'price' => 4500.00,
                'unit' => 'm³',
                'image_url' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=200&auto=format&fit=crop',
                'status' => 'active'
            ],
            [
                'name' => 'Bricks',
                'price' => 7.00,
                'unit' => 'per brick',
                'image_url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=200&auto=format&fit=crop',
                'status' => 'active'
            ],
            [
                'name' => 'Steel',
                'price' => 45.00,
                'unit' => 'gm',
                'image_url' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?q=80&w=200&auto=format&fit=crop',
                'status' => 'active'
            ]
        ];

        $materials = collect();
        foreach ($materialsData as $mat) {
            $materials->push(\App\Models\Material::create($mat));
        }

        // Create 3 Material Stocks (Cement, Concrete, Steel)
        \App\Models\MaterialStock::create([
            'material_id' => $materials->firstWhere('name', 'Cement')->id,
            'user_id' => $user->id,
            'title' => 'Cement Bulk Order #827',
            'amount' => 24500.00,
            'status' => 'success',
            'type' => 'credit'
        ]);

        \App\Models\MaterialStock::create([
            'material_id' => $materials->firstWhere('name', 'Concrete')->id,
            'user_id' => $user->id,
            'title' => 'Concrete Mixture Installment',
            'amount' => 18200.00,
            'status' => 'success',
            'type' => 'credit'
        ]);

        \App\Models\MaterialStock::create([
            'material_id' => $materials->firstWhere('name', 'Steel')->id,
            'user_id' => $user->id,
            'title' => 'Steel Rebar Purchase',
            'amount' => 42000.00,
            'status' => 'pending',
            'type' => 'debit'
        ]);
    }
}
