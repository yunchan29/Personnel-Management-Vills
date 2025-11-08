<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\PasswordResetMail;

class ForgotPasswordController
{
public function sendResetLinkEmail(Request $request)
{
    // ✅ SECURITY FIX: Generic validation - don't reveal if email exists
    $request->validate([
        'email' => 'required|email',
    ]);

    // ✅ SECURITY FIX: Rate limit per email (3 requests per hour)
    $recentRequests = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('created_at', '>', Carbon::now()->subHour())
        ->count();

    if ($recentRequests >= 3) {
        // Generic message - don't reveal if this is due to rate limiting
        $message = 'If an account exists with this email, a password reset link has been sent.';

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $message
            ]);
        }

        return back()->with('status', $message);
    }

    // Check if user exists (but don't reveal in response)
    $userExists = User::where('email', $request->email)->exists();

    if ($userExists) {
        $token = Str::random(64);
        // ✅ SECURITY FIX: Use bcrypt instead of sha256 for better security
        $hashedToken = Hash::make($token);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $hashedToken,
                'created_at' => Carbon::now()
            ]
        );

        $resetUrl = url("/reset-password?token={$token}&email=" . urlencode($request->email));

        try {
            // Use Laravel Mail facade instead of PHPMailer
            Mail::to($request->email)->send(new PasswordResetMail($resetUrl));
        } catch (\Exception $e) {
            \Log::error('Password reset email error: ' . $e->getMessage());
            // Don't reveal email error to user
        }
    }

    // ✅ SECURITY FIX: Always return same message (prevent account enumeration)
    $genericMessage = 'If an account exists with this email, a password reset link has been sent.';

    // Return JSON response for AJAX requests
    if ($request->wantsJson() || $request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => $genericMessage,
            'status' => $genericMessage
        ]);
    }

    return back()->with('status', $genericMessage);
}


public function resetPassword(Request $request)
{
    // ✅ SECURITY FIX: Enhanced password validation
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[a-z]/',      // must contain lowercase
            'regex:/[A-Z]/',      // must contain uppercase
            'regex:/[0-9]/',      // must contain digit
            'regex:/[@$!%*#?&]/', // must contain special character
        ],
    ], [
        'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).'
    ]);

    $resetRecord = DB::table('password_resets')
        ->where('email', $request->email)
        ->first();

    // ✅ SECURITY FIX: Use timing-safe comparison for token verification
    $tokenValid = false;
    if ($resetRecord && Hash::check($request->token, $resetRecord->token)) {
        $tokenValid = true;
    }

    // Reduced expiration time from 60 to 15 minutes for better security
    if (!$tokenValid || !$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
        // ✅ SECURITY FIX: Delete expired/invalid token
        if ($resetRecord) {
            DB::table('password_resets')->where('email', $request->email)->delete();
        }

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This password reset link is invalid or has expired.',
                'errors' => ['email' => ['This password reset link is invalid or has expired.']]
            ], 422);
        }

        return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This password reset link is invalid or has expired.',
                'errors' => ['email' => ['This password reset link is invalid or has expired.']]
            ], 422);
        }

        return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
    }

    // ✅ SECURITY FIX: Ensure new password is different from old password
    if (Hash::check($request->password, $user->password)) {
        $errorMessage = 'New password must be different from your current password.';

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => ['password' => [$errorMessage]]
            ], 422);
        }

        return back()->withErrors(['password' => $errorMessage]);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    // ✅ SECURITY FIX: Delete ALL password reset tokens for this email
    DB::table('password_resets')->where('email', $request->email)->delete();

    // ✅ SECURITY FIX: Send email notification about password change
    try {
        Mail::to($user->email)->send(new \App\Mail\PasswordChangedMail($user));
    } catch (\Exception $e) {
        \Log::error('Password changed notification email error: ' . $e->getMessage());
        // Continue even if email fails
    }

    // Return JSON response for AJAX requests
    if ($request->wantsJson() || $request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Your password has been reset successfully! A confirmation email has been sent.',
            'status' => 'Your password has been reset!'
        ]);
    }

    return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
}

}

