<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Job;

class PositionFilledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $job;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Job $job
     */
    public function __construct(User $user, Job $job)
    {
        $this->user = $user;
        $this->job = $job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Application Update - Position Filled')
                    ->view('emails.applications.position_filled');
    }
}
