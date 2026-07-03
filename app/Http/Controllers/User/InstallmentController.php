<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Installment;
use App\Models\LoanInstallment;

class InstallmentController extends Controller
{
    // 📅 Get Pending Installments (Both Committee & Loans)
    public function pending(Request $request)
    {
        $userId = $request->user()->id;

        $committeeInstallments = Installment::with('committee')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->get();

        $loanInstallments = LoanInstallment::with('loan')
            ->whereHas('loan', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->get();

        return ApiResponse::success([
            'committee_installments' => $committeeInstallments,
            'loan_installments' => $loanInstallments
        ], 'Pending installments fetched');
    }

    // ✅ Get Paid Installments
    public function paid(Request $request)
    {
        $userId = $request->user()->id;

        $committeeInstallments = Installment::with('committee')
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->orderBy('paid_date', 'desc')
            ->get();

        $loanInstallments = LoanInstallment::with('loan')
            ->whereHas('loan', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'paid')
            ->orderBy('paid_date', 'desc')
            ->get();

        return ApiResponse::success([
            'committee_installments' => $committeeInstallments,
            'loan_installments' => $loanInstallments
        ], 'Paid installments fetched');
    }
}
