<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Installment;
use App\Models\Payout;
use App\Models\User;
use App\Models\Committee;
use Carbon\Carbon;
use App\Helpers\ApiResponse;

class AccountingController extends Controller
{
    // Member Ledger
    public function memberLedger($userId)
    {
        $user = User::findOrFail($userId);
        
        // Find all installments and payouts for this user
        $installmentIds = Installment::where('user_id', $userId)->pluck('id');
        $payoutIds = Payout::where('user_id', $userId)->pluck('id');

        $entries = JournalEntry::where(function($q) use ($installmentIds) {
                $q->where('reference_type', Installment::class)->whereIn('reference_id', $installmentIds);
            })
            ->orWhere(function($q) use ($payoutIds) {
                $q->where('reference_type', Payout::class)->whereIn('reference_id', $payoutIds);
            })
            ->with('account')
            ->orderBy('transaction_date', 'asc')
            ->get();

        return ApiResponse::success([
            'member' => $user->name,
            'entries' => $entries
        ]);
    }

    // Committee Ledger
    public function committeeLedger($committeeId)
    {
        $committee = Committee::findOrFail($committeeId);

        $installmentIds = Installment::where('committee_id', $committeeId)->pluck('id');
        $payoutIds = Payout::where('committee_id', $committeeId)->pluck('id');

        $entries = JournalEntry::where(function($q) use ($installmentIds) {
                $q->where('reference_type', Installment::class)->whereIn('reference_id', $installmentIds);
            })
            ->orWhere(function($q) use ($payoutIds) {
                $q->where('reference_type', Payout::class)->whereIn('reference_id', $payoutIds);
            })
            ->with('account')
            ->orderBy('transaction_date', 'asc')
            ->get();

        return ApiResponse::success([
            'committee' => $committee->name,
            'entries' => $entries
        ]);
    }

    // Profit & Loss Statement
    public function profitAndLoss()
    {
        $revenueAccounts = Account::where('type', 'revenue')->withSum('journalEntries', 'credit')->get();
        $expenseAccounts = Account::where('type', 'expense')->withSum('journalEntries', 'debit')->get();

        $totalRevenue = $revenueAccounts->sum('journal_entries_sum_credit');
        $totalExpense = $expenseAccounts->sum('journal_entries_sum_debit');

        return ApiResponse::success([
            'revenue' => $revenueAccounts,
            'total_revenue' => $totalRevenue,
            'expenses' => $expenseAccounts,
            'total_expense' => $totalExpense,
            'net_profit' => $totalRevenue - $totalExpense
        ]);
    }

    // Balance Sheet
    public function balanceSheet()
    {
        // 1. COMMITTEE FINANCIALS
        $commCashCollected = \App\Models\Installment::where('status', 'paid')->sum('amount');
        $commPendingReceivables = \App\Models\Installment::where('status', 'pending')->sum('amount');
        $commPayoutsDisbursed = \App\Models\Payout::where('status', 'paid')->sum('total_payout');
        $commPendingPayouts = \App\Models\Payout::where('status', 'pending')->sum('total_payout');

        // 2. LOAN FINANCIALS
        $loanPrincipalDisbursed = \App\Models\Loan::whereIn('status', ['active', 'paid'])->sum('amount');
        $loanCashCollected = \App\Models\LoanInstallment::where('status', 'paid')->sum('total_amount');
        
        $loanPrincipalPending = \App\Models\LoanInstallment::where('status', 'pending')->sum('principal_component');
        $loanInterestPending = \App\Models\LoanInstallment::where('status', 'pending')->sum('interest_component');
        
        $loanInterestCollected = \App\Models\LoanInstallment::where('status', 'paid')->sum('interest_component');

        // 3. LOAN BREAKDOWN
        $loans = \App\Models\Loan::with(['user', 'installments'])->get()->map(function($loan) {
            $totalInterest = $loan->installments->sum('interest_component');
            $recoveredInterest = $loan->installments->where('status', 'paid')->sum('interest_component');
            $totalAmount = $loan->installments->sum('total_amount');
            $recoveredAmount = $loan->installments->where('status', 'paid')->sum('total_amount');
            
            return [
                'id' => $loan->id,
                'user_name' => $loan->user ? $loan->user->name : 'Unknown',
                'principal' => $loan->amount,
                'interest_rate' => $loan->interest_rate_percent . '% (' . ucfirst($loan->payment_frequency) . ')',
                'total_expected_interest' => $totalInterest, // This is the "extra money"
                'recovered_interest' => $recoveredInterest,
                'total_expected_return' => $totalAmount,
                'total_recovered' => $recoveredAmount,
                'status' => $loan->status
            ];
        });

        return ApiResponse::success([
            'committee' => [
                'assets' => [
                    ['name' => 'Cash in Hand (Net)', 'balance' => max(0, $commCashCollected - $commPayoutsDisbursed)],
                    ['name' => 'Pending Installments (Receivables)', 'balance' => $commPendingReceivables],
                ],
                'total_assets' => max(0, $commCashCollected - $commPayoutsDisbursed) + $commPendingReceivables,
                'liabilities' => [
                    ['name' => 'Pending Member Payouts', 'balance' => $commPendingPayouts],
                ],
                'total_liabilities' => $commPendingPayouts,
                'equity' => [
                    ['name' => 'Total Cash Collected (Gross)', 'balance' => $commCashCollected],
                    ['name' => 'Total Payouts Disbursed', 'balance' => $commPayoutsDisbursed],
                ],
            ],
            'loan' => [
                'assets' => [
                    ['name' => 'Cash Collected (Principal + Interest)', 'balance' => $loanCashCollected],
                    ['name' => 'Pending Loan Principal', 'balance' => $loanPrincipalPending],
                    ['name' => 'Pending Loan Interest', 'balance' => $loanInterestPending],
                ],
                'total_assets' => $loanCashCollected + $loanPrincipalPending + $loanInterestPending,
                'liabilities' => [
                    ['name' => 'Total Principal Disbursed', 'balance' => $loanPrincipalDisbursed],
                ],
                'total_liabilities' => $loanPrincipalDisbursed,
                'equity' => [
                    ['name' => 'Total Interest Earned (Realized Profit)', 'balance' => $loanInterestCollected],
                ],
                'breakdown' => $loans
            ]
        ]);
    }
}
