<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee;
use App\Models\Loan;
use App\Models\Installment;
use App\Models\AccountingEntry;
use App\Helpers\ApiResponse;

class MemberDashboardController extends Controller
{
    // 📊 Member Dashboard Overview
    public function index(Request $request)
    {
        $user = $request->user();

        $activeCommittees = $user->committees()->wherePivot('status', 'active')->count();
        $activeLoans = $user->loans()->where('status', 'active')->count();
        
        $totalPaid = $user->installments()->where('status', 'paid')->sum('total_amount');
        $totalPending = $user->installments()->where('status', 'pending')->sum('total_amount');
        
        $completedInstallmentsCount = $user->installments()->where('status', 'paid')->count();
        $remainingInstallmentsCount = $user->installments()->where('status', 'pending')->count();
        $totalInstallmentsCount = $completedInstallmentsCount + $remainingInstallmentsCount;
        $installmentRatio = $totalInstallmentsCount > 0 ? round(($completedInstallmentsCount / $totalInstallmentsCount) * 100) : 0;
        
        $nextDueInstallment = $user->installments()->where('status', 'pending')->orderBy('due_date', 'asc')->first();

        return ApiResponse::success([
            'user' => $user,
            'summary' => [
                'active_committees' => $activeCommittees,
                'active_loans' => $activeLoans,
                'total_paid' => $totalPaid,
                'remaining_balance' => $totalPending, // As per Figma Remaining Balance
                'completed_installments' => $completedInstallmentsCount,
                'remaining_installments' => $remainingInstallmentsCount,
                'installment_ratio' => $installmentRatio,
                'next_due' => $nextDueInstallment ? [
                    'date' => $nextDueInstallment->due_date,
                    'amount' => $nextDueInstallment->total_amount
                ] : null,
            ],
            'recent_installments' => $user->installments()->with('committee')->latest('due_date')->take(5)->get(),
        ], 'Dashboard data retrieved successfully.');
    }

    // 💰 View Installments
    public function installments(Request $request)
    {
        $installments = $request->user()->installments()->with('committee')->orderBy('due_date', 'asc')->get();
        return ApiResponse::success($installments, 'Installments retrieved successfully.');
    }

    // 💳 View Loans
    public function loans(Request $request)
    {
        $loans = $request->user()->loans()->with('installments')->get();
        return ApiResponse::success($loans, 'Loans retrieved successfully.');
    }

    // 🏦 View Enrolled Committees
    public function committees(Request $request)
    {
        $committees = $request->user()->committees()->get();
        return ApiResponse::success($committees, 'Enrolled committees retrieved successfully.');
    }



    // 📖 Passbook (Ledger)
    public function passbook(Request $request)
    {
        $user = $request->user();
        
        // Find member account or create it
        $account = \App\Models\Account::firstOrCreate(
            ['name' => 'Member - ' . $user->name, 'type' => 'Asset']
        );

        $entries = AccountingEntry::where('account_id', $account->id)->orderBy('transaction_date', 'desc')->get();

        return ApiResponse::success($entries, 'Passbook retrieved successfully.');
    }

    // 👤 Member Profile
    public function profile(Request $request)
    {
        $user = $request->user();
        
        $completedInstallmentsCount = $user->installments()->where('status', 'paid')->count();
        $totalInstallmentsCount = $user->installments()->count();
        $smartScore = $totalInstallmentsCount > 0 ? round(($completedInstallmentsCount / $totalInstallmentsCount) * 100) : 0;
        
        $totalLoans = $user->loans()->count();
        $activeCommittees = $user->committees()->wherePivot('status', 'active')->count();
        $tenureYears = $user->created_at ? $user->created_at->diffInYears(now()) : 0;

        return ApiResponse::success([
            'user' => $user,
            'insights' => [
                'total_loans' => $totalLoans,
                'smart_score' => $smartScore, // max 100
                'tenure_years' => $tenureYears,
                'active_committees' => $activeCommittees
            ],
            'preferences' => [
                'bank_account_setup' => !empty($user->bank_account_number),
                'kyc_verified' => $user->is_phone_verified && $user->id_proof,
            ]
        ], 'Member profile retrieved successfully.');
    }

    // 📄 View Documents
    public function documents(Request $request)
    {
        $user = $request->user();
        return ApiResponse::success([
            'id_proof' => $user->id_proof ? url("/api/documents/{$user->id_proof}") : null,
            'address_proof' => $user->address,
            'photo' => $user->photo ? url("/api/documents/{$user->photo}") : null,
            'aadhar_card' => $user->aadhar_card ? url("/api/documents/{$user->aadhar_card}") : null,
            'pan_card' => $user->pan_card ? url("/api/documents/{$user->pan_card}") : null,
            'bank_account' => [
                'bank_name' => $user->bank_name,
                'account_number' => $user->bank_account_number,
                'ifsc' => $user->bank_ifsc,
                'type' => $user->bank_account_type
            ]
        ], 'Documents retrieved successfully.');
    }

    // 📤 Upload Document
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:id_proof,photo,aadhar_card,pan_card',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $user = $request->user();
        $type = $request->document_type;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store("kyc/{$user->id}", 'local');
            $user->$type = $path;
            $user->save();
        }

        return ApiResponse::success([
            $type => url("/api/documents/{$user->$type}")
        ], 'Document uploaded securely.');
    }

    // 🏦 Update Bank Account
    public function updateBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_ifsc' => 'required|string|max:20',
            'bank_account_type' => 'nullable|string|max:50'
        ]);

        $user = $request->user();
        $user->update($request->only(['bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_type']));

        return ApiResponse::success($user, 'Bank account updated successfully.');
    }

    // 🎟️ Lotteries / Wealth Multiplier
    public function lotteries(Request $request)
    {
        // For Wealth Multiplier page
        // Find upcoming lottery or draw
        // The Figma design mentions "Draw Starts in"
        // And "Winner's Circle"
        $winners = \App\Models\Lottery::with(['winner', 'committee'])->latest()->take(10)->get();
        
        return ApiResponse::success([
            'winners_circle' => $winners,
            'past_results' => $winners, // Can be paginated or structured differently if needed
            'upcoming_draw' => null // Logic for upcoming draw depends on how you schedule lotteries. You can add a 'scheduled_at' column to lotteries later.
        ], 'Lotteries retrieved successfully.');
    }

    // 💳 Show Specific Loan
    public function showLoan(Request $request, $id)
    {
        $loan = $request->user()->loans()->with(['installments' => function($query) {
            $query->orderBy('due_date', 'asc');
        }])->findOrFail($id);
        
        $transactionHistory = $loan->installments()->where('status', 'paid')->orderBy('paid_date', 'desc')->get();

        return ApiResponse::success([
            'loan' => $loan,
            'transaction_history' => $transactionHistory
        ], 'Loan details retrieved successfully.');
    }
}
