<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveForm;
use App\Models\Application;
use App\Services\RequirementsCheckService;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    /**
     * Display the employee dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Get latest 5 leave forms
        $leaveForms = LeaveForm::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get employee notifications
        $notifications = $this->getEmployeeNotifications($user);

        // Get database notifications and unread count
        $dbNotifications = $this->getFormattedNotifications();
        $unreadCount = $user->unreadNotifications->count();

        // Merge with dynamic notifications (for transition period)
        $allNotifications = array_merge($dbNotifications, $notifications);

        // Requirements collection (empty for now, HR Staff will create requirements)
        $requirements = collect([]);

        return view('users.dashboard', compact('leaveForms', 'requirements', 'notifications', 'allNotifications', 'unreadCount'));
    }

    /**
     * Get notifications for employee dashboard
     */
    private function getEmployeeNotifications($user)
    {
        $notifications = [];
        $today = Carbon::today();

        // 1. LEAVE NOTIFICATIONS

        // Pending leave requests
        $pendingLeaves = LeaveForm::where('user_id', $user->id)
            ->where('status', 'Pending')
            ->get();

        foreach ($pendingLeaves as $leave) {
            $createdDate = Carbon::parse($leave->created_at);
            $daysWaiting = $today->diffInDays($createdDate);

            $notifications[] = [
                'type' => 'application',
                'title' => 'Leave Request Pending',
                'message' => "{$leave->leave_type} - {$leave->date_range}",
                'details' => [
                    'Submitted' => $createdDate->format('M d, Y'),
                    'Status' => 'Awaiting HR approval',
                ],
                'action_url' => route('employee.leaveForm'),
                'action_text' => 'View Leave Requests'
            ];
        }

        // Recently approved leaves (within last 7 days)
        $approvedLeaves = LeaveForm::where('user_id', $user->id)
            ->where('status', 'Approved')
            ->where('updated_at', '>=', $today->copy()->subDays(7))
            ->get();

        foreach ($approvedLeaves as $leave) {
            $approvedDate = Carbon::parse($leave->updated_at);
            $daysAgo = $today->diffInDays($approvedDate);

            $notifications[] = [
                'type' => 'application',
                'title' => 'Leave Request Approved',
                'message' => "{$leave->leave_type} - {$leave->date_range}",
                'details' => [
                    'Approved' => $approvedDate->format('M d, Y'),
                    'Status' => 'Approved',
                ],
                'action_url' => route('employee.leaveForm'),
                'action_text' => 'View Details'
            ];
        }

        // Recently declined leaves (within last 7 days)
        $declinedLeaves = LeaveForm::where('user_id', $user->id)
            ->where('status', 'Declined')
            ->where('updated_at', '>=', $today->copy()->subDays(7))
            ->get();

        foreach ($declinedLeaves as $leave) {
            $declinedDate = Carbon::parse($leave->updated_at);

            $notifications[] = [
                'type' => 'application',
                'title' => 'Leave Request Declined',
                'message' => "{$leave->leave_type} - {$leave->date_range}",
                'details' => [
                    'Declined' => $declinedDate->format('M d, Y'),
                    'Status' => 'Not approved',
                ],
                'action_url' => route('employee.leaveForm'),
                'action_text' => 'View Details'
            ];
        }

        // 2. REQUIREMENTS NOTIFICATIONS

        // Only show notification if HR Staff has emailed them about missing requirements
        if ($user->requirements_notified_at) {
            $requirementsCheck = RequirementsCheckService::checkMissingRequirements($user->id);

            // Show notification only if they still have missing requirements
            if ($requirementsCheck['has_missing']) {
                $notifiedDate = \Carbon\Carbon::parse($user->requirements_notified_at);
                $reminderCount = $user->requirements_reminder_count ?? 1;

                // Build reminder text
                $reminderText = $reminderCount === 1
                    ? 'Initial notification'
                    : ($reminderCount === 2 ? '2nd reminder' : $reminderCount . 'th reminder');

                // Build message with list of missing requirements
                $missingList = implode(', ', $requirementsCheck['missing']);
                $message = 'Please submit: ' . $missingList;

                // Build details array
                $details = [
                    'Status' => $reminderText . ' from HR',
                    'Last Sent' => $notifiedDate->format('M d, Y \a\t h:i A'),
                    'Count' => $requirementsCheck['missing_count'] . ' item(s) needed',
                ];

                $notifications[] = [
                    'type' => 'application',
                    'title' => 'Missing Requirements (' . $requirementsCheck['missing_count'] . ')',
                    'message' => $message,
                    'details' => $details,
                    'action_url' => route('employee.files') . '#additional-files',
                    'action_text' => 'Submit Requirements'
                ];
            } else {
                // If requirements are now complete, clear the notification flags
                $user->requirements_notified_at = null;
                $user->requirements_reminder_count = 0;
                $user->save();
            }
        }

        // 3. CONTRACT NOTIFICATIONS

        // Get employee's application/contract details
        $application = Application::where('user_id', $user->id)
            ->where('status', 'hired')
            ->whereNotNull('contract_end')
            ->first();

        if ($application && $application->contract_end) {
            $contractEnd = Carbon::parse($application->contract_end);
            $daysUntil = $today->diffInDays($contractEnd, false);

            // Urgent warning: Contract expiring within 7 days
            if ($daysUntil >= 0 && $daysUntil <= 7) {
                $notifications[] = [
                    'type' => 'application',
                    'title' => 'Contract Expiring Soon',
                    'message' => 'Your employment contract ends on ' . $contractEnd->format('M d, Y'),
                    'days_until' => (int)$daysUntil,
                    'details' => [
                        'End Date' => $contractEnd->format('M d, Y'),
                        'Days Remaining' => $daysUntil . ' day(s)',
                    ],
                    'action_url' => route('employee.profile'),
                    'action_text' => 'View Contract'
                ];
            }
            // Warning: Contract expiring within 30 days
            elseif ($daysUntil > 7 && $daysUntil <= 30) {
                $notifications[] = [
                    'type' => 'application',
                    'title' => 'Contract Renewal Reminder',
                    'message' => 'Your contract ends in ' . $daysUntil . ' days',
                    'days_until' => (int)$daysUntil,
                    'details' => [
                        'End Date' => $contractEnd->format('M d, Y'),
                        'Days Remaining' => $daysUntil . ' days',
                    ],
                    'action_url' => route('employee.profile'),
                    'action_text' => 'View Contract'
                ];
            }
        }

        // Sort notifications by urgency (days_until ascending, then by created/updated date)
        usort($notifications, function($a, $b) {
            $aDays = $a['days_until'] ?? 999;
            $bDays = $b['days_until'] ?? 999;
            return $aDays <=> $bDays;
        });

        return $notifications;
    }

    /**
     * Get formatted notifications from database
     */
    private function getFormattedNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->take(20)->get();
        $formattedNotifications = [];

        foreach ($notifications as $notification) {
            $data = $notification->data;
            $formattedNotifications[] = array_merge($data, [
                'read_at' => $notification->read_at,
                'id' => $notification->id,
                'created_at' => $notification->created_at,
            ]);
        }

        return $formattedNotifications;
    }
}
