<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginAttempt;
use App\Http\Traits\ApiResponseTrait;

class LoginController extends Controller
{
    use ApiResponseTrait;

    /**
     * Show the login form (redirect to welcome page with login modal)
     */
    public function showLoginForm()
    {
        // Redirect to landing page where login modal will be shown
        return redirect()->route('welcome');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ✅ SECURITY FIX: Check if account is locked due to failed attempts
        if (LoginAttempt::isAccountLocked($credentials['email'])) {
            $remainingMinutes = LoginAttempt::getRemainingLockoutTime($credentials['email']);
            return $this->accountLockedResponse($credentials['email'], $remainingMinutes);
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ EMAIL VERIFICATION: Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Store email in session for verification page
                session(['verification_email' => $user->email]);

                return $this->errorResponse(
                    ['email' => ['Please verify your email address before logging in.']],
                    'Email not verified. Please check your email for the verification code.',
                    ['redirect' => route('verification.notice')]
                );
            }

            // ✅ SECURITY FIX: Clear failed login attempts on successful login
            LoginAttempt::clearAttempts($credentials['email']);

            // ✅ SECURITY FIX: Record successful login attempt
            LoginAttempt::recordAttempt($credentials['email'], true);

            // ✅ SECURITY ENHANCEMENT: Track last login time and IP
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'last_activity_at' => now(),
            ]);

            // Determine redirect URL based on role
            $redirectUrl = match ($user->role) {
                'hrAdmin'   => route('hrAdmin.dashboard'),
                'applicant' => route('applicant.dashboard'),
                'employee'  => route('employee.dashboard'),
                'hrStaff'   => route('hrStaff.dashboard'),
                default     => route('welcome'),
            };

            return $this->successResponse(
                'Login successful!',
                $redirectUrl,
                [
                    'user' => [
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ]
                ]
            );
        }

        // ✅ SECURITY FIX: Record failed login attempt
        LoginAttempt::recordAttempt($credentials['email'], false);

        // ✅ SECURITY FIX: Generic error message to prevent account enumeration
        return $this->errorResponse(
            ['email' => ['The provided credentials are incorrect.']],
            'The provided credentials are incorrect.',
            ['email' => $credentials['email']]
        );
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('success', 'You have been logged out successfully.');
    }
}
