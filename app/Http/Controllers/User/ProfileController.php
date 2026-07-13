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
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
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
            'name', 'email', 'phone', 'address', 
            'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_type'
        ]));

        return ApiResponse::success($user, 'Profile updated successfully');
    }

    // 🔐 Get Document Vault (Uploaded KYC)
    public function vault(Request $request)
    {
        $user = $request->user();

        // Generate secure private URLs instead of public storage URLs
        $documents = [
            'aadhar_card' => $user->aadhar_card ? url("/api/documents/{$user->aadhar_card}") : null,
            'pan_card' => $user->pan_card ? url("/api/documents/{$user->pan_card}") : null,
            'id_proof' => $user->id_proof ? url("/api/documents/{$user->id_proof}") : null,
            'photo' => $user->photo ? url("/api/documents/{$user->photo}") : null,
        ];

        $additional = [];
        if ($user->additional_documents) {
            $decoded = json_decode($user->additional_documents, true);
            if (is_array($decoded)) {
                foreach ($decoded as $key => $docInfo) {
                    $additional[$key] = [
                        'title' => $docInfo['title'] ?? $key,
                        'url' => isset($docInfo['path']) && $docInfo['path'] ? url("/api/documents/{$docInfo['path']}") : null
                    ];
                }
            }
        }
        $documents['additional'] = (object)$additional;

        return ApiResponse::success($documents, 'Vault documents fetched securely');
    }

    // 📁 Upload to Vault (Secure Local Storage)
    public function uploadVault(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aadhar_card' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'pan_card' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'id_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'additional_file' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'additional_title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        $user = $request->user();

        try {
            $updates = [];

            if ($request->hasFile('aadhar_card')) {
                if ($user->aadhar_card) Storage::disk('local')->delete($user->aadhar_card);
                $updates['aadhar_card'] = $request->file('aadhar_card')->store("kyc/{$user->id}", 'local');
            }

            if ($request->hasFile('pan_card')) {
                if ($user->pan_card) Storage::disk('local')->delete($user->pan_card);
                $updates['pan_card'] = $request->file('pan_card')->store("kyc/{$user->id}", 'local');
            }

            if ($request->hasFile('id_proof')) {
                if ($user->id_proof) Storage::disk('local')->delete($user->id_proof);
                $updates['id_proof'] = $request->file('id_proof')->store("kyc/{$user->id}", 'local');
            }

            if ($request->hasFile('photo')) {
                if ($user->photo) Storage::disk('local')->delete($user->photo);
                $updates['photo'] = $request->file('photo')->store("kyc/{$user->id}", 'local');
            }

            if ($request->hasFile('additional_file') && $request->has('additional_title')) {
                $title = $request->additional_title;
                $key = strtolower(str_replace(' ', '_', $title));
                $path = $request->file('additional_file')->store("kyc/{$user->id}", 'local');

                $additional = [];
                if ($user->additional_documents) {
                    $decoded = json_decode($user->additional_documents, true);
                    if (is_array($decoded)) {
                        $additional = $decoded;
                    }
                }

                // Delete old file if key already exists
                if (isset($additional[$key]) && isset($additional[$key]['path'])) {
                    Storage::disk('local')->delete($additional[$key]['path']);
                }

                $additional[$key] = [
                    'title' => $title,
                    'path' => $path
                ];

                $updates['additional_documents'] = json_encode($additional);
            }

            if (!empty($updates)) {
                $user->update($updates);
                return ApiResponse::success($updates, 'Documents uploaded successfully to secure vault');
            }

            return ApiResponse::error('No files provided', 400);

        } catch (\Exception $e) {
            return ApiResponse::error('Upload failed: ' . $e->getMessage(), 500);
        }
    }
}
