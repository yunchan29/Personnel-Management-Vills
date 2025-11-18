<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequirementsMissingNotification extends Notification
{
    use Queueable;

    public $missingRequirements;

    /**
     * Create a new notification instance.
     */
    public function __construct($missingRequirements)
    {
        $this->missingRequirements = $missingRequirements;
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
        $count = count($this->missingRequirements);

        return [
            'type' => 'application',
            'title' => 'Missing Requirements',
            'message' => 'You have ' . $count . ' missing requirement' . ($count > 1 ? 's' : ''),
            'details' => [
                'Missing' => implode(', ', array_slice($this->missingRequirements, 0, 3)) . ($count > 3 ? ' and ' . ($count - 3) . ' more' : ''),
            ],
            'action_url' => route('employee.dashboard'),
            'action_text' => 'Update Requirements',
        ];
    }
}
