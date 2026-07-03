<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Securely serve KYC documents
     */
    public function showKyc(Request $request, $userId, $filename)
    {
        $user = $request->user();

        // 🛡️ Authorization Check
        // Only the owner of the document or a Super Admin can view it
        if ($user->id != $userId && !$user->hasRole('Super Admin')) {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        $path = "kyc/{$userId}/{$filename}";

        // Check if file exists on 'local' disk
        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        // Return the file stream securely
        return Storage::disk('local')->response($path);
    }
}
