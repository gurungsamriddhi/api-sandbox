<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => 'Invalid credentials'  // email or password wrong
            ], 401);
        }

        
        /** @var User $user */
        $user = auth()->user();

     
        if (is_null($user->email_verified_at)) {
            return response()->json([
                'error' => 'Your email is not verified. Please check your inbox for the OTP and verify your account.'
            ], 403);
        }

        
        $user->update([
            'last_login_at' => now()
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token
        ]);
    }
}
