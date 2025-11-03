<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequirementsLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $missingRequirements;


    public function __construct($user, $missingRequirements)
    {
        $this->user = $user;
        $this->missingRequirements = $missingRequirements;
    }

  
    public function build()
    {
        return $this->subject('Notice: Missing Requirements')
                    ->view('emails.requirements')
                    ->with([
                        'user' => $this->user,
                        'missingRequirements' => $this->missingRequirements,
                    ]);
    }
}
