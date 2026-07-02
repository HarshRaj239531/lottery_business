<?php

use App\Models\Account;
use App\Models\Installment;
use App\Models\Payout;
use App\Models\JournalEntry;
use Carbon\Carbon;

// 1. Create Default Accounts
$accounts = [
    ['name' => 'Cash in Hand', 'type' => 'asset'],
    ['name' => 'Bank Account', 'type' => 'asset'],
    ['name' => 'Members Payout Payable', 'type' => 'liability'],
    ['name' => 'Committee Revenue', 'type' => 'revenue'],
    ['name' => 'Payout Expense', 'type' => 'expense'],
];

foreach ($accounts as $acc) {
    Account::firstOrCreate(['name' => $acc['name']], ['type' => $acc['type']]);
}

$cashAccount = Account::where('name', 'Cash in Hand')->first();
$revenueAccount = Account::where('name', 'Committee Revenue')->first();
$expenseAccount = Account::where('name', 'Payout Expense')->first();

echo "Accounts created.\n";

// 2. Generate retroactive journal entries for PAID installments
$paidInstallments = Installment::where('status', 'paid')->get();
foreach ($paidInstallments as $inst) {
    // Check if entry already exists
    $exists = JournalEntry::where('reference_type', Installment::class)
        ->where('reference_id', $inst->id)
        ->exists();
    
    if (!$exists) {
        $date = $inst->paid_date ?? $inst->updated_at->toDateString();
        
        // Debit Cash
        JournalEntry::create([
            'transaction_date' => $date,
            'description' => 'Installment Payment from Member #' . $inst->user_id,
            'reference_type' => Installment::class,
            'reference_id' => $inst->id,
            'account_id' => $cashAccount->id,
            'debit' => $inst->amount,
            'credit' => 0,
        ]);
        
        // Credit Revenue
        JournalEntry::create([
            'transaction_date' => $date,
            'description' => 'Installment Payment from Member #' . $inst->user_id,
            'reference_type' => Installment::class,
            'reference_id' => $inst->id,
            'account_id' => $revenueAccount->id,
            'debit' => 0,
            'credit' => $inst->amount,
        ]);
    }
}
echo "Retroactive installment entries created.\n";

// 3. Generate retroactive journal entries for Payouts
$payouts = Payout::all();
foreach ($payouts as $payout) {
    $exists = JournalEntry::where('reference_type', Payout::class)
        ->where('reference_id', $payout->id)
        ->exists();
        
    if (!$exists) {
        $date = $payout->payout_date ?? $payout->created_at->toDateString();
        
        // Debit Expense
        JournalEntry::create([
            'transaction_date' => $date,
            'description' => 'Payout to Member #' . $payout->user_id,
            'reference_type' => Payout::class,
            'reference_id' => $payout->id,
            'account_id' => $expenseAccount->id,
            'debit' => $payout->amount,
            'credit' => 0,
        ]);
        
        // Credit Cash
        JournalEntry::create([
            'transaction_date' => $date,
            'description' => 'Payout to Member #' . $payout->user_id,
            'reference_type' => Payout::class,
            'reference_id' => $payout->id,
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => $payout->amount,
        ]);
    }
}
echo "Retroactive payout entries created.\n";

// Update balances
foreach (Account::all() as $account) {
    $debits = $account->journalEntries()->sum('debit');
    $credits = $account->journalEntries()->sum('credit');
    
    // Normal balance logic
    if (in_array($account->type, ['asset', 'expense'])) {
        $account->balance = $debits - $credits;
    } else {
        $account->balance = $credits - $debits;
    }
    $account->save();
}

echo "Account balances updated.\n";
