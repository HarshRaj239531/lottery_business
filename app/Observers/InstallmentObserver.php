<?php

namespace App\Observers;

use App\Models\Installment;

class InstallmentObserver
{
    /**
     * Handle the Installment "created" event.
     */
    public function created(Installment $installment): void
    {
        //
    }

    /**
     * Handle the Installment "updated" event.
     */
    public function updated(Installment $installment): void
    {
        if ($installment->isDirty('status') && $installment->status === 'paid') {
            $cashAccount = \App\Models\Account::where('name', 'Cash in Hand')->first();
            $revenueAccount = \App\Models\Account::where('name', 'Committee Revenue')->first();

            if ($cashAccount && $revenueAccount) {
                $date = $installment->paid_date ?? now()->toDateString();
                
                // Check if entry already exists (idempotency)
                $exists = \App\Models\JournalEntry::where('reference_type', Installment::class)
                    ->where('reference_id', $installment->id)
                    ->exists();

                if (!$exists) {
                    \App\Models\JournalEntry::create([
                        'transaction_date' => $date,
                        'description' => 'Installment Payment from Member #' . $installment->user_id,
                        'reference_type' => Installment::class,
                        'reference_id' => $installment->id,
                        'account_id' => $cashAccount->id,
                        'debit' => $installment->amount,
                        'credit' => 0,
                    ]);
                    
                    \App\Models\JournalEntry::create([
                        'transaction_date' => $date,
                        'description' => 'Installment Payment from Member #' . $installment->user_id,
                        'reference_type' => Installment::class,
                        'reference_id' => $installment->id,
                        'account_id' => $revenueAccount->id,
                        'debit' => 0,
                        'credit' => $installment->amount,
                    ]);

                    $cashAccount->increment('balance', $installment->amount);
                    $revenueAccount->increment('balance', $installment->amount);
                }
            }
        }
    }

    /**
     * Handle the Installment "deleted" event.
     */
    public function deleted(Installment $installment): void
    {
        //
    }

    /**
     * Handle the Installment "restored" event.
     */
    public function restored(Installment $installment): void
    {
        //
    }

    /**
     * Handle the Installment "force deleted" event.
     */
    public function forceDeleted(Installment $installment): void
    {
        //
    }
}
