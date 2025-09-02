<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

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
                            'application' => $this->schedule->application, // 👈 add this
                            'startDate'   => $this->schedule->start_date->format('m/d/Y'),
                            'endDate'     => $this->schedule->end_date->format('m/d/Y'),
                            'startTime'   => Carbon::parse($this->schedule->start_time)->format('g:i A'),
                            'endTime'     => Carbon::parse($this->schedule->end_time)->format('g:i A'),
                            'location'    => $this->schedule->location,
                    ]);
    }
}
