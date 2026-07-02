<?php

namespace App\Observers;

use App\Models\Payout;

class PayoutObserver
{
    /**
     * Handle the Payout "created" event.
     */
    public function created(Payout $payout): void
    {
        $cashAccount = \App\Models\Account::where('name', 'Cash in Hand')->first();
        $expenseAccount = \App\Models\Account::where('name', 'Payout Expense')->first();

        if ($cashAccount && $expenseAccount) {
            $date = $payout->payout_date ?? now()->toDateString();
            
            \App\Models\JournalEntry::create([
                'transaction_date' => $date,
                'description' => 'Payout to Member #' . $payout->user_id,
                'reference_type' => Payout::class,
                'reference_id' => $payout->id,
                'account_id' => $expenseAccount->id,
                'debit' => $payout->amount,
                'credit' => 0,
            ]);
            
            \App\Models\JournalEntry::create([
                'transaction_date' => $date,
                'description' => 'Payout to Member #' . $payout->user_id,
                'reference_type' => Payout::class,
                'reference_id' => $payout->id,
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $payout->amount,
            ]);

            $expenseAccount->increment('balance', $payout->amount);
            $cashAccount->decrement('balance', $payout->amount);
        }
    }

    /**
     * Handle the Payout "updated" event.
     */
    public function updated(Payout $payout): void
    {
        //
    }

    /**
     * Handle the Payout "deleted" event.
     */
    public function deleted(Payout $payout): void
    {
        //
    }

    /**
     * Handle the Payout "restored" event.
     */
    public function restored(Payout $payout): void
    {
        //
    }

    /**
     * Handle the Payout "force deleted" event.
     */
    public function forceDeleted(Payout $payout): void
    {
        //
    }
}
