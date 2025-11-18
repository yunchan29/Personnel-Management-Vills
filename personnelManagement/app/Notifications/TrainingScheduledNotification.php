<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class TrainingScheduledNotification extends Notification
{
    use Queueable;

    public $training;
    public $job;

    /**
     * Create a new notification instance.
     */
    public function __construct($training, $job)
    {
        $this->training = $training;
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
        $startDate = Carbon::parse($this->training->startDate);
        $daysUntil = Carbon::now()->diffInDays($startDate, false);

        return [
            'type' => 'training',
            'title' => 'Training Scheduled',
            'message' => 'Training for ' . $this->job->jobTitle,
            'days_until' => max(0, $daysUntil),
            'details' => [
                'Job' => $this->job->jobTitle,
                'Start Date' => $startDate->format('M d, Y'),
                'End Date' => Carbon::parse($this->training->endDate)->format('M d, Y'),
                'Location' => $this->training->location ?? 'TBD',
            ],
            'action_url' => route('user.applications'),
            'action_text' => 'View Details',
        ];
    }
}
