<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $timestamp;

    public function __construct($user)
    {
        $this->user = $user;
        $this->timestamp = now()->format('F d, Y h:i A');
    }

    public function build()
    {
        return $this->subject('Password Changed - Personnel Management System')
                    ->view('emails.password-changed')
                    ->with([
                        'user' => $this->user,
                        'timestamp' => $this->timestamp,
                    ]);
    }
}
