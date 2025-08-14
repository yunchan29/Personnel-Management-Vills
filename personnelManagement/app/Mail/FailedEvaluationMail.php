<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Application;

class FailedEvaluationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $application;
    public $knowledge;
    public $skill;
    public $participation;
    public $professionalism;
    public $totalScore;

    public function __construct(User $user, Application $application, $knowledge, $skill, $participation, $professionalism, $totalScore)
    {
        $this->user = $user;
        $this->application = $application;
        $this->knowledge = $knowledge;
        $this->skill = $skill;
        $this->participation = $participation;
        $this->professionalism = $professionalism;
        $this->totalScore = $totalScore;
    }

    public function build()
    {
        return $this->subject('Training Evaluation Result')
                    ->view('emails.evaluations.failed');
    }
}
