<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class EvaluationPendingNotification extends Notification
{
    use Queueable;

    public $training;
    public $applicant;
    public $job;
    public $urgency;

    /**
     * Create a new notification instance.
     */
    public function __construct($training, $applicant, $job, $urgency = 'info')
    {
        $this->training = $training;
        $this->applicant = $applicant;
        $this->job = $job;
        $this->urgency = $urgency;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $endDate = Carbon::parse($this->training->endDate);
        $daysUntil = Carbon::now()->diffInDays($endDate, false);

        return [
            'type' => 'evaluation',
            'title' => 'Evaluation Pending',
            'message' => 'Evaluation needed for ' . $this->applicant->first_name . ' ' . $this->applicant->last_name,
            'days_until' => abs($daysUntil),
            'urgency' => $this->urgency,
            'details' => [
                'Applicant' => $this->applicant->first_name . ' ' . $this->applicant->last_name,
                'Job' => $this->job->jobTitle,
                'Training End' => $endDate->format('M d, Y'),
            ],
            'action_url' => route('hr-staff.perfEval'),
            'action_text' => 'Evaluate Now',
        ];
    }
}
