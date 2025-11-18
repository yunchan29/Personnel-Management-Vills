<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class NewLeaveRequestNotification extends Notification
{
    use Queueable;

    public $leaveForm;
    public $employee;

    /**
     * Create a new notification instance.
     */
    public function __construct($leaveForm, $employee)
    {
        $this->leaveForm = $leaveForm;
        $this->employee = $employee;
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
        // Parse date range
        $dateRange = $this->leaveForm->date_range;
        $dates = explode(' - ', $dateRange);
        $startDate = isset($dates[0]) ? Carbon::parse(trim($dates[0])) : null;
        $endDate = isset($dates[1]) ? Carbon::parse(trim($dates[1])) : null;

        // Calculate days until start
        $daysUntil = $startDate ? Carbon::now()->diffInDays($startDate, false) : 0;

        // Determine the correct route based on user role
        $actionUrl = '#';
        if ($notifiable->role === 'hrAdmin') {
            $actionUrl = route('hrAdmin.leaveForm');
        } elseif ($notifiable->role === 'hrStaff') {
            $actionUrl = route('hrStaff.leaveForm');
        }

        return [
            'type' => 'application',
            'title' => 'New Leave Request',
            'message' => $this->employee->first_name . ' ' . $this->employee->last_name . ' has filed for ' . $this->leaveForm->leave_type,
            'days_until' => max(0, $daysUntil),
            'details' => [
                'Employee' => $this->employee->first_name . ' ' . $this->employee->last_name,
                'Leave Type' => $this->leaveForm->leave_type,
                'Start Date' => $startDate ? $startDate->format('M d, Y') : 'N/A',
                'End Date' => $endDate ? $endDate->format('M d, Y') : 'N/A',
                'Status' => 'Pending',
            ],
            'action_url' => $actionUrl,
            'action_text' => 'Review Request',
        ];
    }
}
