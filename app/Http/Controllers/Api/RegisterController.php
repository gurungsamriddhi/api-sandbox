<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Step 1: Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',  // password_confirmation must match
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);  // Bad input
        }

        // Step 2: Create user in database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Secure hash
            'password_set' => 1,
        ]);

        $otp=str_pad(rand(0,999999),6,'0',STR_PAD_LEFT);
        $otpHash=Hash::make($otp);
        $expires=now()->addMinutes(10);

        $user->update([
            'otp_hash'        => $otpHash,
            'otp_created_at'  => now(),
            'otp_expires_at'  => $expires,
        ]);

        Mail::to($user->email)->queue(new OtpMail($otp, $expires->format('Y-m-d H:i:s')));

        return response()->json([
            'message' => 'Registration successful. Please check your email for a 6-digit OTP to verify your account.',
            'user_id' => $user->id,  
            // NO token here!
        ], 201);
    }
}
