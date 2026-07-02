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

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password') // Set a default password
            ]
        );

        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'member']);
        Role::firstOrCreate(['name' => 'agent']);

        $user->assignRole('Super Admin');

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
    }
}
