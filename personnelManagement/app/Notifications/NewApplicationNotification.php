<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewApplicationNotification extends Notification
{
    use Queueable;

    public $application;
    public $job;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $job)
    {
        $this->application = $application;
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
        return [
            'type' => 'application',
            'title' => 'New Application',
            'message' => 'New application for ' . $this->job->jobTitle,
            'details' => [
                'Applicant' => $this->application->user->first_name . ' ' . $this->application->user->last_name,
                'Job' => $this->job->jobTitle,
                'Applied' => $this->application->created_at->format('M d, Y'),
            ],
            'action_url' => route('view.applications', $this->job->id),
            'action_text' => 'View Application',
        ];
    }
}
