<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function submit(Request $request)
    {
        // 🔐 Authorization
        if (!$request->user()->hasRole('Super Admin')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Validation
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'document_type' => 'required|in:aadhar,pan,passport,voter_id',
            'front_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'back_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($data['user_id']);

            // 📁 Upload paths
            $frontPath = null;
            $backPath = null;

            // Delete old files (optional but recommended)
            if ($user->aadhar_card) {
                Storage::disk('local')->delete($user->aadhar_card);
            }
            if ($user->pan_card) {
                Storage::disk('local')->delete($user->pan_card);
            }

            // Upload front
            if ($request->hasFile('front_image')) {
                $frontPath = $request->file('front_image')
                    ->store("kyc/{$user->id}", 'local');
            }

            // Upload back
            if ($request->hasFile('back_image')) {
                $backPath = $request->file('back_image')
                    ->store("kyc/{$user->id}", 'local');
            }

            // Save based on document type
            switch ($data['document_type']) {
                case 'aadhar':
                    $user->aadhar_card = $frontPath;
                    $user->id_proof = $backPath ?: $frontPath;
                    break;

                case 'pan':
                    $user->pan_card = $frontPath;
                    break;

                default:
                    $user->id_proof = $frontPath;
                    break;
            }

            // KYC Status (better approach)
            $user->kyc_status = 'pending';

            $user->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'KYC submitted securely',
                'data' => [
                    'user_id' => $user->id,
                    'document_type' => $data['document_type'],
                    'front_url' => $frontPath ? url("/api/documents/{$frontPath}") : null,
                    'back_url' => $backPath ? url("/api/documents/{$backPath}") : null,
                ]
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'KYC upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}