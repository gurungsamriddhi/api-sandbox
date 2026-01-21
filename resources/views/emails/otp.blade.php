@component('mail::message')
# Your OTP Code

Hello {{ $user->name ?? 'User' }},

Your one-time password (OTP) to verify your email is:

**{{ $otp }}**

This code will expire at **{{ $expires }}**.

Please enter this code in the app to complete verification.

If you didn't request this, please ignore this email.

Thanks,  
Forum API Team
@endcomponent