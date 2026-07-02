<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payout;
use App\Services\NotificationService;

class PayoutController extends Controller
{
    public function index()
    {
        return response()->json(Payout::with(['user', 'committee'])->get());
    }

    public function pay($id, NotificationService $notify)
    {
        $payout = Payout::with('user', 'committee')->findOrFail($id);

        if ($payout->status === 'paid') {
            return response()->json(['status' => false, 'message' => 'Already paid.'], 400);
        }

        $payout->update([
            'status' => 'paid',
            'paid_date' => now()->format('Y-m-d')
        ]);

        if ($payout->user) {
            $notify->sendNotification(
                $payout->user, 
                "Payout Transferred", 
                "Great news! Your total payout of ₹{$payout->total_payout} from the {$payout->committee->name} committee has been successfully transferred to your bank account."
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Payout marked as paid and user notified!'
        ]);
    }
}
