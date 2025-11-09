<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ContractSigningInvitationMail extends Mailable
{
    public $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function build()
    {
        // Ensure related user and job are loaded
        $this->application->load('user', 'job');

        return $this->subject('Contract Signing Invitation - Congratulations!')
                    ->view('emails.contract_signing_invitation')
                    ->with([
                        'applicantName' => $this->application->user->full_name,
                        'jobTitle' => $this->application->job->job_title,
                        'companyName' => $this->application->job->company_name,
                        'signingDate' => Carbon::parse($this->application->contract_signing_schedule)->format('F d, Y'),
                        'signingTime' => Carbon::parse($this->application->contract_signing_schedule)->format('g:i A'),
                    ]);
    }
}
