<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return ApiResponse::error('Invalid email or password', 401);
        }

        $user = Auth::user();

        // Check if user is an admin
        if (!$user->hasRole('Super Admin')) {
            return ApiResponse::error('Unauthorized access. Admin only.', 403);
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'token' => $token
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success([], 'Logged out successfully');
    }
}
