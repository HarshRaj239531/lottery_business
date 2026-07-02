<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Installment;
use App\Models\LoanInstallment;
use App\Models\AgentCollection;
use Carbon\Carbon;

class AgentTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find an agent
        $agent = User::whereHas('roles', function($q) {
            $q->where('name', 'agent');
        })->first();

        if (!$agent) {
            $agent = User::first();
        }

        if (!$agent) {
            $this->command->error('No users in database.');
            return;
        }

        // Add 3 pending committee collections
        $pendingCommittees = Installment::where('status', 'pending')->take(3)->get();
        foreach ($pendingCommittees as $installment) {
            AgentCollection::firstOrCreate([
                'installment_id' => $installment->id,
            ], [
                'agent_id' => $agent->id,
                'member_id' => $installment->user_id,
                'collection_type' => 'committee',
                'amount_collected' => $installment->amount,
                'collected_at' => Carbon::now()->subDays(rand(1, 3)),
                'status' => 'pending',
            ]);
        }
        $this->command->info('Added ' . $pendingCommittees->count() . ' pending committee collections.');

        // Add 3 pending loan collections
        $pendingLoans = LoanInstallment::with('loan')->where('status', 'pending')->take(3)->get();
        foreach ($pendingLoans as $loanInstallment) {
            if ($loanInstallment->loan) {
                AgentCollection::firstOrCreate([
                    'loan_installment_id' => $loanInstallment->id,
                ], [
                    'agent_id' => $agent->id,
                    'member_id' => $loanInstallment->loan->user_id,
                    'collection_type' => 'loan',
                    'amount_collected' => $loanInstallment->total_amount,
                    'collected_at' => Carbon::now()->subDays(rand(1, 3)),
                    'status' => 'pending',
                ]);
            }
        }
        $this->command->info('Added pending loan collections.');
        $this->command->info('Test data added successfully! You can view it in the Admin Dashboard.');
    }
}
