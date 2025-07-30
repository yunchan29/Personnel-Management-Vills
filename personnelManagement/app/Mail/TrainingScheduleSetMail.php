<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingScheduleSetMail extends Mailable
{
    public $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function build()
    {
        return $this->subject('Your Training Schedule')
                    ->view('emails.training.schedule_set')
                    ->with(['schedule' => $this->schedule]);
    }
}

