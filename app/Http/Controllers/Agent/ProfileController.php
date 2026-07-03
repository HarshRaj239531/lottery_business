<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // 👤 Get Agent Profile & Settings
    public function index(Request $request)
    {
        $agent = $request->user()->load('roles');
        
        $settings = [
            'name' => $agent->name,
            'id' => 'AGT' . str_pad($agent->id, 4, '0', STR_PAD_LEFT),
            'verified' => true,
            'preferences' => [
                'dark_mode' => false,
                'notifications' => true,
                'language' => 'English'
            ],
            'bank_details' => [
                'bank_name' => $agent->bank_name,
                'account_number' => $agent->bank_account_number,
                'ifsc' => $agent->bank_ifsc,
            ]
        ];

        return ApiResponse::success($settings, 'Agent profile fetched');
    }

    // ✏️ Edit Profile
    public function update(Request $request)
    {
        $agent = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $agent->id,
            'address' => 'sometimes|string',
            'bank_name' => 'sometimes|string',
            'bank_account_number' => 'sometimes|string',
            'bank_ifsc' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        $agent->update($request->only([
            'name', 'phone', 'address', 
            'bank_name', 'bank_account_number', 'bank_ifsc'
        ]));

        return ApiResponse::success($agent, 'Profile updated successfully');
    }

    // 🔗 Generate QR Code for Agent
    public function qrCode(Request $request)
    {
        $agent = $request->user();
        
        if (!$agent->bank_account_number || !$agent->bank_ifsc) {
            return ApiResponse::error('Bank details not found to generate QR', 400);
        }

        // Dummy QR Data for UI binding
        $qrData = [
            'upi_id' => strtolower(str_replace(' ', '', $agent->name)) . '@bank',
            'merchant_name' => $agent->name,
            'qr_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=upi://pay?pa=' . strtolower(str_replace(' ', '', $agent->name)) . '@bank&pn=' . urlencode($agent->name)
        ];

        return ApiResponse::success($qrData, 'QR Code generated');
    }
}
