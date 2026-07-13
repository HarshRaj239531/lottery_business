<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\UserTransaction;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate total balance from User Transactions
        $credits = UserTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');
            
        $debits = UserTransaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');
            
        $totalBalance = $credits - $debits;
 
        // Active Committees
        $activeCommitteesCount = $user->committees()->where('committee_user.status', 'active')->count();
 
        // Active Loans
        $activeLoansCount = Loan::where('user_id', $user->id)->where('status', 'active')->count();
 
        // Recent Transactions
        $recentTransactions = UserTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
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

