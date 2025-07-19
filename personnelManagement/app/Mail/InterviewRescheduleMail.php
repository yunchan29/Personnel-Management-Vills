<?php

namespace App\Mail;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewRescheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;

    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    public function build()
    {
        return $this->subject('Interview Rescheduled for ' . ($this->interview->application->job->job_title ?? 'Your Application'))
                    ->view('emails.intervew_reschedule')
                    ->with([
                        'interview' => $this->interview
                    ]);
    }
}
