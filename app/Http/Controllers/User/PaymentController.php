<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Installment;
use App\Models\LoanInstallment;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // 💳 Process Payment (Collection)
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:committee,loan',
            'installment_id' => 'required|integer',
            'amount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        $user = $request->user();
        $type = $request->type;
        $installmentId = $request->installment_id;
        $amount = $request->amount;

        DB::beginTransaction();

        try {
            $description = '';
            
            if ($type === 'committee') {
                $installment = Installment::where('id', $installmentId)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                if ($installment->status === 'paid') {
                    return ApiResponse::error('Installment already paid', 400);
                }

                $installment->update([
                    'status' => 'paid',
                    'paid_date' => now()
                ]);

                $description = "Paid committee installment #{$installment->id}";
                $referenceType = Installment::class;
                $referenceId = $installment->id;

            } else {
                $installment = LoanInstallment::where('id', $installmentId)
                    ->whereHas('loan', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->firstOrFail();

                if ($installment->status === 'paid') {
                    return ApiResponse::error('Loan installment already paid', 400);
                }

                $installment->update([
                    'status' => 'paid',
                    'paid_date' => now()
                ]);

                $description = "Paid loan installment #{$installment->id}";
                $referenceType = LoanInstallment::class;
                $referenceId = $installment->id;
            }

            // Create Journal Entry (Credit to User's Account/Ledger)
            JournalEntry::create([
                'transaction_date' => now(),
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'account_id' => $user->id,
                'credit' => $amount,
                'debit' => null
            ]);

            DB::commit();
            return ApiResponse::success(null, 'Payment successful');

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Payment failed: ' . $e->getMessage(), 500);
        }
    }
}
