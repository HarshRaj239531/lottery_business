<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\ApiResponse;

class AuthController extends Controller
{
    /**
     * ADMIN LOGIN (SECURE)
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Find user
        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        // Role check (safe way)
        if ($user->role !== 'admin') {
            return ApiResponse::error('Unauthorized. Admin only.', 403);
        }

        // Delete old tokens (important security step)
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('admin_token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'token' => $token
        ], 'Login successful');
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::error('User not authenticated', 401);
        }

        // Delete current token safely
        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return ApiResponse::success([], 'Logged out successfully');
    }
}