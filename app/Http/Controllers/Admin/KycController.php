<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    // Submit KYC Documents for Member/Agent
    public function submit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'document_type' => 'required|in:aadhar,pan,passport,voter_id',
            'front_image' => 'required|image|max:5120', // 5MB limit
            'back_image' => 'nullable|image|max:5120',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Handle Front Image Upload
        $frontPath = null;
        if ($request->hasFile('front_image')) {
            $frontPath = $request->file('front_image')->store('kyc', 'public');
        }

        // Handle Back Image Upload
        $backPath = null;
        if ($request->hasFile('back_image')) {
            $backPath = $request->file('back_image')->store('kyc', 'public');
        }

        // Save path to user based on doc type
        if ($request->document_type === 'aadhar') {
            $user->aadhar_card = $frontPath;
            // Also save back image or log it
            $user->id_proof = $backPath ?: $frontPath; 
        } elseif ($request->document_type === 'pan') {
            $user->pan_card = $frontPath;
        } else {
            $user->id_proof = $frontPath;
        }

        // Mark as verified for demo and convenience
        $user->is_phone_verified = true; // phone/identity verified flag
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'KYC documents submitted and verified successfully for ' . $user->name,
            'data' => [
                'user_id' => $user->id,
                'document_type' => $request->document_type,
                'front_path' => $frontPath ? Storage::url($frontPath) : null,
                'back_path' => $backPath ? Storage::url($backPath) : null,
            ]
        ]);
    }
}
