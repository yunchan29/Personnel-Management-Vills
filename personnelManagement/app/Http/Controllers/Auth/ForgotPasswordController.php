<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\PasswordResetMail;

class ForgotPasswordController
{
public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $token = Str::random(64);
    $hashedToken = hash('sha256', $token);

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

        return back()->with('status', 'Reset link sent!');
    } catch (\Exception $e) {
        \Log::error('Password reset email error: ' . $e->getMessage());
        return back()->withErrors(['email' => 'Failed to send reset link. Please try again.']);
    }
}


public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    // Hash the incoming token for comparison
    $hashedToken = hash('sha256', $request->token);

    $resetRecord = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $hashedToken)
        ->first();

    // Reduced expiration time from 60 to 15 minutes for better security
    if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
        return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'User not found.']);
    }

    $user->password = bcrypt($request->password);
    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('status', 'Your password has been reset!');
}

}

