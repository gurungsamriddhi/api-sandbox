<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResendOtpController extends Controller
{
    public function resend(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Find the user
        $user = User::where('email', $request->email)->first();

        // 3. Optional: Block if already verified
        if (!is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'Your email is already verified. No need to resend OTP.'
            ], 200);
        }

        // 4. Generate new OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = Hash::make($otp);
        $expires = now()->addMinutes(15);

        // 5. Save new OTP
        $user->update([
            'otp_hash'         => $otpHash,
            'otp_created_at'   => now(),
            'otp_expires_at'   => $expires,
        ]);

        Mail::to($user->email)->queue(new OtpMail($otp, $expires->format('Y-m-d H:i:s')));

        // Response
        return response()->json([
            'message' => 'New OTP has been sent. Please check your email.',
            'user_id' => $user->id  // optional for debugging
        ]);
    }
}
