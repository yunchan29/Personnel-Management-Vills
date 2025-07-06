<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Http\Request;
use App\Models\User;

class ForgotPasswordController
{
public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $token = Str::random(64);

    DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        [
            'token' => $token,
            'created_at' => Carbon::now()
        ]
    );

    $resetUrl = url("/reset-password?token={$token}&email=" . urlencode($request->email));

    // Render the Blade view as email HTML
    $htmlContent = view('emails.password-reset', [
        'resetUrl' => $resetUrl
    ])->render();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME');
        $mail->Password   = env('MAIL_PASSWORD');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
        $mail->Port       = env('MAIL_PORT');

        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $mail->addAddress($request->email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password - Personnel Management System';
        $mail->Body    = $htmlContent;
        $mail->AltBody = "To reset your password, visit: {$resetUrl}";

        $mail->send();

        return back()->with('status', 'Reset link sent!');
    } catch (Exception $e) {
        return back()->withErrors(['email' => "Mailer Error: {$mail->ErrorInfo}"]);
    }
}


public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    $resetRecord = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
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

