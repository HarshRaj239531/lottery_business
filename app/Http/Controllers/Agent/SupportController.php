<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    // 🎫 Submit Support Ticket
    public function submitTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open'
        ]);

        return ApiResponse::success($ticket, 'Support ticket submitted successfully. Admin will contact you soon.');
    }

    // 📜 List My Tickets (Optional, for future UI updates)
    public function myTickets(Request $request)
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success($tickets, 'Support tickets fetched');
    }
}
