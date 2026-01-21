<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VerifyOtpController extends Controller
{
    public function verify(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (
            !$user->otp_hash ||
            $user->otp_expires_at->isPast() ||
            !Hash::check($request->otp, $user->otp_hash)
        ) {
            return response()->json(['error' => 'Invalid or expired OTP'], 400);
        }

        // Success!
        $user->update([
            'email_verified_at' => now(),
            'otp_hash'          => null,
            'otp_created_at'    => null,
            'otp_expires_at'    => null,
        ]);

        $token = $user->createToken('forum-token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully!',
            'user'    => $user,
            'token'   => $token,
        ]);
    
    }
}
