<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Services\NotificationService;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    protected $notify;

    public function __construct(NotificationService $notify)
    {
        $this->notify = $notify;
    }

    // 📋 List all payouts
    public function index()
    {
        try {
            $payouts = Payout::with(['user', 'committee'])->latest()->get();

            return ApiResponse::success($payouts, 'Payout list fetched');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // 💰 Mark payout as paid
    public function pay($id)
    {
        DB::beginTransaction();

        try {
            $payout = Payout::with(['user', 'committee'])->findOrFail($id);

            // ❌ Already paid
            if ($payout->status === 'paid') {
                return ApiResponse::error('Already paid', 400);
            }

            // ❌ Optional: other states check
            if ($payout->status === 'cancelled') {
                return ApiResponse::error('Payout is cancelled', 400);
            }

            // ✅ Update payout
            $payout->update([
                'status' => 'paid',
                'paid_date' => now()
            ]);

            // 📲 Notify user
            if ($payout->user) {
                $this->notify->sendNotification(
                    $payout->user,
                    "Payout Transferred",
                    "Great news! Your payout of ₹{$payout->total_payout} from {$payout->committee->name} has been transferred."
                );
            }

            DB::commit();

            return ApiResponse::success(null, 'Payout marked as paid & user notified');

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }
}