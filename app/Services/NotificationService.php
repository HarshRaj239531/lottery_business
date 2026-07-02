<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    // 🔔 SEND NOTIFICATION (DB + SMS/WhatsApp Simulation)
    public function sendNotification($user, $title, $message)
    {
        // 1. Save to Database
        Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
        ]);

        // 2. Simulate SMS/WhatsApp (Log it)
        $phone = $user->phone ?? 'Unknown';
        Log::info("[Notification Dispatched] To $phone: $title - $message");

        return true;
    }

    // 📲 SEND SMS (Simulation for now)
    public function sendSMS($phone, $message)
    {
        // Here you would integrate a paid API like Twilio.
        // For now, we simulate it for free by logging it.
        Log::info("[SMS API Call Simulated] To $phone: $message");
        return "SMS successfully queued for $phone";
    }

    // 💬 SEND WHATSAPP (Simulation for now)
    public function sendWhatsApp($phone, $message)
    {
        // Here you would integrate WhatsApp Cloud API.
        // For now, we simulate it for free by logging it.
        Log::info("[WhatsApp API Call Simulated] To $phone: $message");
        return "WhatsApp message successfully queued for $phone";
    }
}