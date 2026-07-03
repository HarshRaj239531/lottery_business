<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

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
        try {
            $request->validate([
                'phone' => 'required|string|min:10|max:15',
                'message' => 'required|string|max:500'
            ]);

            $result = $this->service->sendSMS(
                $request->phone,
                $request->message
            );

            return ApiResponse::success([
                'phone' => $request->phone,
                'message' => $request->message,
                'response' => $result
            ], 'SMS sent successfully');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // 💬 Send WhatsApp
    public function sendWhatsApp(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string|min:10|max:15',
                'message' => 'required|string|max:500'
            ]);

            $result = $this->service->sendWhatsApp(
                $request->phone,
                $request->message
            );

            return ApiResponse::success([
                'phone' => $request->phone,
                'message' => $request->message,
                'response' => $result
            ], 'WhatsApp message sent successfully');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // 🚀 BONUS: Bulk SMS
    public function sendBulkSMS(Request $request)
    {
        try {
            $request->validate([
                'phones' => 'required|array',
                'phones.*' => 'required|string|min:10|max:15',
                'message' => 'required|string|max:500'
            ]);

            $responses = [];

            foreach ($request->phones as $phone) {
                $responses[] = $this->service->sendSMS($phone, $request->message);
            }

            return ApiResponse::success($responses, 'Bulk SMS sent');

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}