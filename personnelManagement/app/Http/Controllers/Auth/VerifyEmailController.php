<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailCodeNotification;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;

class VerifyEmailController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display the email verification notice.
     */
    public function notice()
    {
        // Check if we have an email in session
        if (!session('verification_email')) {
            return redirect()->route('login')->with('error', 'Please register or login first.');
        }

        return view('auth.verify-email');
    }

    /**
     * Verify the email using the provided code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse(
                ['email' => ['User not found.']],
                'User not found.',
                $request->all()
            );
        }

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse(
                'Your email is already verified. You can now log in.',
                route('login')
            );
        }

        if ($user->verifyCode($request->code)) {
            // Clear verification email from session
            session()->forget('verification_email');

            return $this->successResponse(
                'Email verified successfully! You can now log in to your account.',
                route('login')
            );
        }

        // Check if code expired
        if ($user->isVerificationCodeExpired()) {
            return $this->errorResponse(
                ['code' => ['The verification code has expired. Please request a new one.']],
                'The verification code has expired.',
                $request->all()
            );
        }

        return $this->errorResponse(
            ['code' => ['Invalid verification code. Please try again.']],
            'Invalid verification code.',
            $request->all()
        );
    }

    /**
     * Resend the verification code.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse(
                ['email' => ['User not found.']],
                'User not found.',
                $request->all()
            );
        }

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse(
                'Your email is already verified.',
                route('login')
            );
        }

        // Generate new code
        $code = $user->generateVerificationCode();

        // Send new code
        $user->notify(new VerifyEmailCodeNotification($code));

        return $this->successResponse(
            'A new verification code has been sent to your email address.',
            null,
            ['email' => $user->email]
        );
    }
}
