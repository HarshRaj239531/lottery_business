<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function show()
    {
        $setting = PaymentSetting::latest()->first();
        if (!$setting) {
            $setting = PaymentSetting::create([
                'qr_code' => null,
                'admin_phone' => '+919999999999'
            ]);
        }

        $qrCodeUrl = null;
        if ($setting->qr_code) {
            $qrCodeUrl = asset('storage/' . $setting->qr_code);
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'data' => [
                'qr_code' => $qrCodeUrl,
                'admin_phone' => $setting->admin_phone
            ]
        ]);
    }
}
