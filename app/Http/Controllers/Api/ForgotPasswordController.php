<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Generate new OTP for password reset
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = Hash::make($otp);
        $expires = now()->addMinutes(15);

        // Save OTP
        $user->update([
            'otp_hash'         => $otpHash,
            'otp_created_at'   => now(),
            'otp_expires_at'   => $expires,
        ]);

        // Send email with OTP
        Mail::to($user->email)->queue(new OtpMail($otp, $expires->format('Y-m-d H:i:s')));

        return response()->json([
            'message' => 'Password reset OTP has been sent to your email. Please check your inbox.'
        ]);
    }
}
