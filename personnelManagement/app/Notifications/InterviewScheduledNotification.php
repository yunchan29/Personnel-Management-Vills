<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class InterviewScheduledNotification extends Notification
{
    use Queueable;

    public $interview;
    public $job;

    /**
     * Create a new notification instance.
     */
    public function __construct($interview, $job)
    {
        $this->interview = $interview;
        $this->job = $job;
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
        $interviewDate = Carbon::parse($this->interview->interviewDate);
        $daysUntil = Carbon::now()->diffInDays($interviewDate, false);

        return [
            'type' => 'interview',
            'title' => 'Interview Scheduled',
            'message' => 'Interview for ' . $this->job->jobTitle,
            'days_until' => max(0, $daysUntil),
            'details' => [
                'Job' => $this->job->jobTitle,
                'Date' => $interviewDate->format('M d, Y'),
                'Time' => $this->interview->interviewTime,
                'Location' => $this->interview->interviewLocation ?? 'TBD',
            ],
            'action_url' => route('user.applications'),
            'action_text' => 'View Details',
        ];
    }
}
