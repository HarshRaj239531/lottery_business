<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with('user')->orderBy('created_at', 'desc')->get();
        return ApiResponse::success($loans);
    }

    public function show($id)
    {
        $loan = Loan::with(['user', 'installments'])->findOrFail($id);
        return ApiResponse::success($loan);
    }

    public function store(Request $request)
    {
        $request->validate([
            // User Data
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required_without:user_id|string|max:255',
            'email' => 'required_without:user_id|email|unique:users,email',
            'phone' => 'required_without:user_id|string|unique:users,phone',
            'password' => 'required_without:user_id|string|min:6',
            'address' => 'required_without:user_id|string',
            
            // KYC Data
            'id_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'aadhar_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Loan Data
            'amount' => 'required|numeric|min:1',
            'interest_rate_percent' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'payment_frequency' => 'required|in:daily,weekly,monthly',
        ]);

        DB::beginTransaction();
        try {
            // 1. Handle User
            $user = null;
            if ($request->user_id) {
                $user = User::find($request->user_id);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'is_phone_verified' => true,
                ]);
                $user->assignRole('member');
            }

            // 2. Handle KYC Uploads
            if ($request->hasFile('id_proof')) {
                $user->id_proof = $request->file('id_proof')->store('kyc/id_proofs', 'public');
            }
            if ($request->hasFile('photo')) {
                $user->photo = $request->file('photo')->store('kyc/photos', 'public');
            }
            if ($request->hasFile('aadhar_card')) {
                $user->aadhar_card = $request->file('aadhar_card')->store('kyc/aadhar', 'public');
            }
            if ($request->hasFile('pan_card')) {
                $user->pan_card = $request->file('pan_card')->store('kyc/pan', 'public');
            }
            $user->save();

            // 3. Create Loan
            $loan = Loan::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'interest_rate_percent' => $request->interest_rate_percent,
                'duration_months' => $request->duration_months,
                'payment_frequency' => $request->payment_frequency,
                'status' => 'active',
            ]);

            // 4. Calculate EMI & Installments
            $P = (float) $loan->amount;
            $rate = (float) $loan->interest_rate_percent;
            $duration = (int) $loan->duration_months;
            $frequency = $loan->payment_frequency;

            $N = 0; // Total installments
            $R = 0; // Rate per installment
            $step = ''; // Carbon add method

            if ($frequency === 'monthly') {
                $N = $duration;
                $R = ($rate / 12) / 100;
                $step = 'addMonth';
            } elseif ($frequency === 'weekly') {
                $N = $duration * 4; // Approx 4 weeks per month
                $R = ($rate / 52) / 100;
                $step = 'addWeek';
            } elseif ($frequency === 'daily') {
                $N = $duration * 30; // Approx 30 days per month
                $R = ($rate / 365) / 100;
                $step = 'addDay';
            }

            $EMI = 0;
            if ($R > 0) {
                $EMI = ($P * $R * pow(1 + $R, $N)) / (pow(1 + $R, $N) - 1);
            } else {
                $EMI = $P / $N;
            }

            $balance = $P;
            $currentDate = Carbon::today();

            for ($i = 1; $i <= $N; $i++) {
                $currentDate = $currentDate->copy()->$step();
                
                $interest = $balance * $R;
                $principal = $EMI - $interest;
                
                // Final adjustment to avoid rounding issues
                if ($i === $N) {
                    $principal = $balance;
                    $EMI = $principal + $interest;
                }

                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'due_date' => $currentDate->format('Y-m-d'),
                    'total_amount' => round($EMI, 2),
                    'principal_component' => round($principal, 2),
                    'interest_component' => round($interest, 2),
                    'status' => 'pending',
                ]);

                $balance -= $principal;
            }

            DB::commit();

            return ApiResponse::success($loan, 'Loan created successfully with generated EMIs.');

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Failed to create loan: ' . $e->getMessage(), 500);
        }
    }

    public function installments()
    {
        $installments = LoanInstallment::with(['loan.user'])->orderBy('id', 'desc')->take(200)->get();
        return ApiResponse::success($installments);
    }

    public function collect(Request $request, $id)
    {
        $installment = LoanInstallment::findOrFail($id);
        
        if ($installment->status === 'paid') {
            return ApiResponse::error('Installment already paid.', 400);
        }

        $installment->update([
            'status' => 'paid',
            'paid_date' => Carbon::today(),
        ]);

        // Check if all installments are paid
        $pending = LoanInstallment::where('loan_id', $installment->loan_id)
            ->where('status', 'pending')
            ->count();

        if ($pending === 0) {
            $installment->loan->update(['status' => 'paid']);
        }

        return ApiResponse::success($installment, 'Payment collected successfully.');
    }
}
