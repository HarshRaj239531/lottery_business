<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\JournalEntry;
use App\Models\Loan;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate total balance from Journal Entries
        $credits = JournalEntry::where('account_id', $user->id) // Assuming account_id matches user_id for member accounts
            ->whereNotNull('credit')
            ->sum('credit');
            
        $debits = JournalEntry::where('account_id', $user->id)
            ->whereNotNull('debit')
            ->sum('debit');
            
        $totalBalance = $credits - $debits;

        // Active Committees
        $activeCommitteesCount = $user->committees()->where('committee_user.status', 'active')->count();

        // Active Loans
        $activeLoansCount = Loan::where('user_id', $user->id)->where('status', 'active')->count();

        // Recent Transactions
        $recentTransactions = JournalEntry::where('account_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();

        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo ? \Illuminate\Support\Facades\Storage::url($user->photo) : null,
            ],
            'wallet' => [
                'total_balance' => $totalBalance,
                'credits' => $credits,
                'debits' => $debits,
            ],
            'stats' => [
                'active_committees' => $activeCommitteesCount,
                'active_loans' => $activeLoansCount,
            ],
            'recent_activity' => $recentTransactions
        ];

        return ApiResponse::success($data, 'Dashboard data fetched');
    }
}

