<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ContractExpiringNotification extends Notification
{
    use Queueable;

    public $file201;
    public $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct($file201, $daysUntilExpiry)
    {
        $this->file201 = $file201;
        $this->daysUntilExpiry = $daysUntilExpiry;
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
        $contractEnd = Carbon::parse($this->file201->contractEnd);

        return [
            'type' => 'application',
            'title' => 'Contract Expiring Soon',
            'message' => 'Your contract expires in ' . $this->daysUntilExpiry . ' day' . ($this->daysUntilExpiry > 1 ? 's' : ''),
            'days_until' => $this->daysUntilExpiry,
            'details' => [
                'Contract End' => $contractEnd->format('M d, Y'),
                'Position' => $this->file201->positionTitle ?? 'N/A',
            ],
            'action_url' => route('employee.dashboard'),
            'action_text' => 'View Contract',
        ];
    }
}
