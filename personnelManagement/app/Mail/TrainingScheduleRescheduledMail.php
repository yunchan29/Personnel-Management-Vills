<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingScheduleRescheduledMail extends Mailable
{
    public $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function build()
    {

    // Ensure related application is loaded
    $this->schedule->load('application.user', 'application.job');

        return $this->subject('Training Rescheduled')
                    ->view('emails.training_schedule_rescheduled')
                    ->with([
                        'schedule'    => $this->schedule,
                        'application' => $this->schedule->application
                    ]);
    }
}
