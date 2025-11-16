<?php

namespace App\Mail;

use App\Models\LeaveForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveApproveMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveform;

    public function __construct(LeaveForm $leaveform)
    {
        $this->leaveform = $leaveform;
    }

    public function build()
    {
        return $this->subject('Your Leave Request Has Been Approved')
                    ->view('emails.leaveforms.leaveApprove');
    }
}
