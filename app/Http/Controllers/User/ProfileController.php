<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // 👤 Get User Profile
    public function index(Request $request)
    {
        $user = $request->user()->load('roles');
        return ApiResponse::success($user, 'Profile fetched successfully');
    }

    // ✏️ Update Profile
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'address' => 'sometimes|string',
            'bank_name' => 'sometimes|string',
            'bank_account_number' => 'sometimes|string',
            'bank_ifsc' => 'sometimes|string',
            'bank_account_type' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        $user->update($request->only([
            'name', 'phone', 'address', 
            'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_type'
        ]));

        return ApiResponse::success($user, 'Profile updated successfully');
    }

    // 🔐 Get Document Vault (Uploaded KYC)
    public function vault(Request $request)
    {
        $user = $request->user();

        $documents = [
            'aadhar_card' => $user->aadhar_card ? Storage::url($user->aadhar_card) : null,
            'pan_card' => $user->pan_card ? Storage::url($user->pan_card) : null,
            'id_proof' => $user->id_proof ? Storage::url($user->id_proof) : null,
            'photo' => $user->photo ? Storage::url($user->photo) : null,
        ];

        return ApiResponse::success($documents, 'Vault documents fetched');
    }

    // 📁 Upload to Vault
    public function uploadVault(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aadhar_card' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'pan_card' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'id_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        $user = $request->user();

        try {
            $updates = [];

            if ($request->hasFile('aadhar_card')) {
                if ($user->aadhar_card) Storage::disk('public')->delete($user->aadhar_card);
                $updates['aadhar_card'] = $request->file('aadhar_card')->store("kyc/{$user->id}", 'public');
            }

            if ($request->hasFile('pan_card')) {
                if ($user->pan_card) Storage::disk('public')->delete($user->pan_card);
                $updates['pan_card'] = $request->file('pan_card')->store("kyc/{$user->id}", 'public');
            }

            if ($request->hasFile('id_proof')) {
                if ($user->id_proof) Storage::disk('public')->delete($user->id_proof);
                $updates['id_proof'] = $request->file('id_proof')->store("kyc/{$user->id}", 'public');
            }

            if ($request->hasFile('photo')) {
                if ($user->photo) Storage::disk('public')->delete($user->photo);
                $updates['photo'] = $request->file('photo')->store("kyc/{$user->id}", 'public');
            }

            if (!empty($updates)) {
                $user->update($updates);
                return ApiResponse::success($updates, 'Documents uploaded successfully');
            }

            return ApiResponse::error('No files provided', 400);

        } catch (\Exception $e) {
            return ApiResponse::error('Upload failed: ' . $e->getMessage(), 500);
        }
    }
}
