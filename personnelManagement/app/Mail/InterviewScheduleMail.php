<?php

namespace App\Mail;

use App\Models\Interview; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;

    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    public function build()
    {
    return $this->subject('Interview Invitation for ' . ($this->interview->application->job->job_title ?? 'Your Application'))
                ->view('emails.interview_schedule')
                ->with([
                    'interview' => $this->interview
                ]);
    }
}
