<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    // 📲 Send SMS
    public function sendSMS(Request $request)
    {
        return response()->json([
            'message' => $this->service->sendSMS(
                $request->phone,
                $request->message
            )
        ]);
    }

    // 💬 Send WhatsApp
    public function sendWhatsApp(Request $request)
    {
        return response()->json([
            'message' => $this->service->sendWhatsApp(
                $request->phone,
                $request->message
            )
        ]);
    }

}



