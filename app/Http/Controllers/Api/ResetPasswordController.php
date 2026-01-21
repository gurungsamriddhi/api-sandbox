<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'otp'      => 'required|string|size:6',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check OTP
        if (
            !$user->otp_hash ||
            $user->otp_expires_at->isPast() ||
            !Hash::check($request->otp, $user->otp_hash)
        ) {
            return response()->json([
                'error' => 'Invalid or expired OTP'
            ], 400);
        }

        // Success: update password
        $user->update([
            'password'         => Hash::make($request->password),
            'password_set'     => true,
            'otp_hash'         => null,
            'otp_created_at'   => null,
            'otp_expires_at'   => null,
        ]);

        // Optional: auto-login with new token
        $token = $user->createToken('reset-token')->plainTextToken;

        return response()->json([
            'message' => 'Password reset successful. You are now logged in.',
            'user'    => $user,
            'token'   => $token
        ]);
    }
}
