<?php

namespace App\Mail;

use App\Models\LeaveForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveDeclineMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveform;

    public function __construct(LeaveForm $leaveform)
    {
        $this->leaveform = $leaveform;
    }

    public function build()
    {
        return $this->subject('Your Leave Request Has Been Declined')
                    ->view('emails.leaveforms.leaveDecline');
    }
}
