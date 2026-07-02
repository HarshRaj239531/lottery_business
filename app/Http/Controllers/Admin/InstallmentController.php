<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Http\Requests\InstallmentRequest;
use App\Services\NotificationService;

class InstallmentController extends Controller
{
    // 💰 Collect Payment
    public function collect(InstallmentRequest $request, NotificationService $notify)
    {
        $data = $request->validated();
        
        // Find the oldest pending installment
        $installment = Installment::where('user_id', $data['user_id'])
            ->where('committee_id', $data['committee_id'])
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->first();

        if (!$installment) {
            return response()->json([
                'status' => false,
                'message' => 'No pending installments found for this user in this committee.'
            ], 400);
        }

        $installment->update([
            'amount' => $data['amount'],
            'status' => 'paid',
            'paid_date' => $data['paid_date'] ?? now()->format('Y-m-d'),
            'collected_by' => $request->user()->id
        ]);

        // 📲 Send SMS after payment
        $user = \App\Models\User::find($request->user_id);
        $notify->sendNotification($user, "Payment Received", "Success! Your deposit of ₹{$installment->amount} has been received.");

        // Check if all installments are paid for this user in this committee
        $pendingCount = Installment::where('user_id', $data['user_id'])
            ->where('committee_id', $data['committee_id'])
            ->where('status', 'pending')
            ->count();

        if ($pendingCount === 0) {
            $committee = \App\Models\Committee::find($data['committee_id']);
            $totalDeposits = Installment::where('user_id', $data['user_id'])
                ->where('committee_id', $data['committee_id'])
                ->where('status', 'paid')
                ->sum('amount');
            
            $returnPercentage = $committee->return_percentage ?? 0;
            $returnAmount = ($totalDeposits * $returnPercentage) / 100;
            $totalPayout = $totalDeposits + $returnAmount;

            // Auto-Create Payout
            \App\Models\Payout::firstOrCreate([
                'user_id' => $data['user_id'],
                'committee_id' => $data['committee_id']
            ], [
                'total_deposits' => $totalDeposits,
                'return_amount' => $returnAmount,
                'total_payout' => $totalPayout,
                'status' => 'pending'
            ]);

            // Final SMS
            $notify->sendNotification($user, "Committee Completed!", "Congratulations! You have completed the {$committee->name} committee. Total Deposited: ₹{$totalDeposits}. Profit: ₹{$returnAmount}. Your Total Payout of ₹{$totalPayout} is being processed.");

            // Update Pivot status
            \Illuminate\Support\Facades\DB::table('committee_user')
                ->where('committee_id', $data['committee_id'])
                ->where('user_id', $data['user_id'])
                ->update(['status' => 'completed']);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment Collected Successfully',
            'data' => $installment
        ]);
    }

    // ⚠️ Send Due Warnings
    public function sendWarnings(NotificationService $notify)
    {
        // Find all pending installments where the due_date is in the past
        $pendingInstallments = Installment::with('user')
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        $count = 0;
        foreach ($pendingInstallments as $installment) {
            $user = $installment->user;
            if ($user) {
                $notify->sendNotification(
                    $user, 
                    "Payment Overdue", 
                    "Warning: You have missed the due date for your ₹{$installment->amount} payment. Please deposit immediately."
                );
                $count++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Warning notifications sent to {$count} overdue members."
        ]);
    }

    // ⏰ Send Payment Reminders
    public function sendPaymentReminders(NotificationService $notify)
    {
        // Find pending installments due within the next 3 days
        $upcomingInstallments = Installment::with('user')
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(3))
            ->get();

        $count = 0;
        foreach ($upcomingInstallments as $installment) {
            $user = $installment->user;
            if ($user) {
                $dueDate = \Carbon\Carbon::parse($installment->due_date)->format('M d, Y');
                $notify->sendNotification(
                    $user, 
                    "Upcoming Payment Reminder", 
                    "Friendly Reminder: Your upcoming payment of ₹{$installment->amount} is due on {$dueDate}. Please ensure timely deposit."
                );
                $count++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Payment reminders sent to {$count} members."
        ]);
    }

    // 📋 All Payments
    public function index()
    {
        return response()->json(
            Installment::with(['user', 'committee'])
                ->orderBy('id', 'desc')
                ->take(200)
                ->get()
        );
    }

    // 👁️ Show Payment
    public function show($id)
    {
        return response()->json(Installment::with(['user', 'committee'])->findOrFail($id));
    }

    // ✏️ Update Payment
    public function update(InstallmentRequest $request, $id)
    {
        $installment = Installment::findOrFail($id);
        $installment->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Payment Updated',
            'data' => $installment
        ]);
    }

    // ❌ Delete Payment
    public function destroy($id)
    {
        Installment::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment Deleted'
        ]);
    }
}