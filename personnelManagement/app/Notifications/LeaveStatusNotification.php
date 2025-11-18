<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class LeaveStatusNotification extends Notification
{
    use Queueable;

    public $leaveForm;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($leaveForm, $status)
    {
        $this->leaveForm = $leaveForm;
        $this->status = $status;
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
        $statusMessages = [
            'pending' => 'Your leave request is pending approval',
            'approved' => 'Your leave request has been approved',
            'declined' => 'Your leave request has been declined',
        ];

        // Parse date range
        $dateRange = $this->leaveForm->date_range;
        $dates = explode(' - ', $dateRange);
        $startDate = isset($dates[0]) ? Carbon::parse(trim($dates[0])) : null;
        $endDate = isset($dates[1]) ? Carbon::parse(trim($dates[1])) : null;

        return [
            'type' => 'application',
            'title' => 'Leave Request ' . ucfirst($this->status),
            'message' => $statusMessages[$this->status] ?? 'Leave request status updated',
            'details' => [
                'Type' => $this->leaveForm->leave_type,
                'From' => $startDate ? $startDate->format('M d, Y') : 'N/A',
                'To' => $endDate ? $endDate->format('M d, Y') : 'N/A',
                'Status' => ucfirst($this->status),
            ],
            'action_url' => route('employee.leaveForm'),
            'action_text' => 'View Details',
        ];
    }
}
