<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Interview;
use App\Models\TrainingSchedule;
use App\Enums\ApplicationStatus;
use App\Services\RequirementsCheckService;
use Carbon\Carbon;

class ApplicantJobController extends Controller
{
    /* Show applicant dashboard with latest job listings. */
   public function dashboard(Request $request)
{
    $user = auth()->user();
    $resume = $user->resume ?? null;

    // ✅ Apply user's preferred industry by default on page load
    $industry = null;

    if ($request->has('industry')) {
        // User explicitly selected a filter
        $rawIndustry = $request->query('industry');
        // Ensure it's a string, not an array - prevent htmlspecialchars error
        if (is_array($rawIndustry)) {
            $rawIndustry = null;
        }
        // Empty string means "All Industries" - show all jobs
        $industry = ($rawIndustry !== '' && $rawIndustry !== null) ? $rawIndustry : null;
    } else {
        // No filter selected - apply user's preferred industry by default
        $userPreference = $user->job_industry;
        if (!empty($userPreference) && is_string($userPreference)) {
            $industry = $userPreference;
        }
    }

    $jobs = Job::latest()
        ->whereDate('apply_until', '>=', \Carbon\Carbon::today()) // 👈 filter expired jobs
        ->where('vacancies', '>', 0) // 👈 filter jobs with no available vacancies
        ->when($industry, fn($query) => $query->where('job_industry', $industry))
        ->get();

    $appliedJobIds = Application::where('user_id', $user->id)
        ->pluck('job_id')
        ->toArray();

    $hasTrainingOrPassed = Application::where('user_id', $user->id)
         ->whereIn('status', ['scheduled_for_training', 'training_passed'])
         ->exists();

    // Get notifications for applicant
    $notifications = $this->getApplicantNotifications($user);

    // Get database notifications and unread count
    $dbNotifications = $this->getFormattedNotifications();
    $unreadCount = $user->unreadNotifications->count();

    // Merge with dynamic notifications (for transition period)
    $allNotifications = array_merge($dbNotifications, $notifications);

    return view('users.dashboard', compact(
    'jobs',
    'resume',
    'appliedJobIds',
    'industry',
    'hasTrainingOrPassed',
    'notifications',
    'allNotifications',
    'unreadCount'
));

}

    /**
     * Get notifications for applicant dashboard
     */
    private function getApplicantNotifications($user)
    {
        $notifications = [];
        $today = Carbon::today();

        // Get user's applications with interviews and training schedules (including all statuses)
        $applications = Application::with(['job', 'interview', 'trainingSchedule'])
            ->where('user_id', $user->id)
            ->get();

        foreach ($applications as $application) {
            $jobTitle = $application->job->job_title;
            $companyName = $application->job->company_name;
            $hasTimeBasedNotification = false;

            // Check for scheduled interviews (time-sensitive)
            if ($application->interview && in_array($application->interview->status, ['scheduled', 'rescheduled'])) {
                $interviewDate = Carbon::parse($application->interview->scheduled_at);
                $daysUntil = $today->diffInDays($interviewDate, false);

                if ($daysUntil >= 0 && $daysUntil <= 7) {
                    $notifications[] = [
                        'type' => 'interview',
                        'title' => 'Interview Scheduled',
                        'message' => "{$jobTitle} at {$companyName}",
                        'days_until' => (int)$daysUntil,
                        'details' => [
                            'Date' => $interviewDate->format('M d, Y'),
                            'Time' => $interviewDate->format('h:i A'),
                        ],
                        'action_url' => route('applicant.application') . '?application=' . $application->id,
                        'action_text' => 'View Details'
                    ];
                    $hasTimeBasedNotification = true;
                }
            }

            // Check for scheduled training (time-sensitive)
            if ($application->trainingSchedule && $application->status->value === ApplicationStatus::SCHEDULED_FOR_TRAINING->value) {
                $trainingStartDate = Carbon::parse($application->trainingSchedule->start_date);
                $daysUntil = $today->diffInDays($trainingStartDate, false);

                if ($daysUntil >= 0 && $daysUntil <= 7) {
                    $notifications[] = [
                        'type' => 'training',
                        'title' => 'Training Scheduled',
                        'message' => "{$jobTitle} at {$companyName}",
                        'days_until' => (int)$daysUntil,
                        'details' => [
                            'Starts' => $trainingStartDate->format('M d, Y'),
                            'Duration' => $trainingStartDate->diffInDays(Carbon::parse($application->trainingSchedule->end_date)) . ' days',
                        ],
                        'action_url' => route('applicant.application') . '?application=' . $application->id,
                        'action_text' => 'View Details'
                    ];
                    $hasTimeBasedNotification = true;
                }
            }

            // Add status-based notifications (only if no time-based notification exists for this application)
            if (!$hasTimeBasedNotification) {
                switch ($application->status->value) {
                    case ApplicationStatus::PENDING->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Application Submitted',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Under review by HR',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Application'
                        ];
                        break;

                    case ApplicationStatus::APPROVED->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Application Approved',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Awaiting interview schedule',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Details'
                        ];
                        break;

                    case ApplicationStatus::INTERVIEWED->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Interview Passed',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Awaiting training schedule',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Status'
                        ];
                        break;

                    case ApplicationStatus::TRAINED->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Training Completed',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Awaiting evaluation',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Status'
                        ];
                        break;

                    case ApplicationStatus::FOR_EVALUATION->value:
                        $notifications[] = [
                            'type' => 'evaluation',
                            'title' => 'Under Evaluation',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Training evaluation in progress',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'Check Status'
                        ];
                        break;

                    case ApplicationStatus::PASSED_EVALUATION->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Evaluation Passed',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Congratulations! Awaiting final hiring process',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Details'
                        ];
                        break;

                    case ApplicationStatus::HIRED->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'You\'ve Been Hired!',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Welcome aboard!',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Contract Details'
                        ];
                        break;

                    // Failed statuses
                    case ApplicationStatus::DECLINED->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Application Declined',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Unfortunately, your application was not approved',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Details'
                        ];
                        break;

                    case ApplicationStatus::FAILED_INTERVIEW->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Interview Not Passed',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Better luck next time',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Details'
                        ];
                        break;

                    case ApplicationStatus::FAILED_EVALUATION->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Evaluation Not Passed',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'Training evaluation was not successful',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Details'
                        ];
                        break;

                    case ApplicationStatus::REJECTED->value:
                        $notifications[] = [
                            'type' => 'application',
                            'title' => 'Application Rejected',
                            'message' => "{$jobTitle} at {$companyName}",
                            'details' => [
                                'Status' => 'This application has been closed',
                            ],
                            'action_url' => route('applicant.application') . '?application=' . $application->id,
                            'action_text' => 'View Details'
                        ];
                        break;
                }
            }
        }

        // Check for missing requirements (government IDs and documents)
        // Only show notification if HR Staff has emailed them about missing requirements
        if ($user->requirements_notified_at) {
            $requirementsCheck = RequirementsCheckService::checkMissingRequirements($user->id);

            // Show notification only if they still have missing requirements
            if ($requirementsCheck['has_missing']) {
                $notifiedDate = Carbon::parse($user->requirements_notified_at);
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
                    'action_url' => route('applicant.files') . '#additional-files',
                    'action_text' => 'Submit Requirements'
                ];
            } else {
                // If requirements are now complete, clear the notification flags
                $user->requirements_notified_at = null;
                $user->requirements_reminder_count = 0;
                $user->save();
            }
        }

        // Sort notifications by urgency (days_until ascending)
        usort($notifications, function($a, $b) {
            $aDays = $a['days_until'] ?? 999;
            $bDays = $b['days_until'] ?? 999;
            return $aDays <=> $bDays;
        });

        return $notifications;
    }
    /* Apply to a specific job. */
    public function apply(Request $request, Job $job)
{
    $user = auth()->user();
    $file201 = $user->file201;

    // Check if job has expired
    if (\Carbon\Carbon::parse($job->apply_until)->lt(\Carbon\Carbon::today())) {
        return response()->json([
            'message' => 'This job posting has expired and is no longer accepting applications.',
            'expired' => true
        ], 422);
    }

    // Check if job has available vacancies
    if ($job->vacancies <= 0) {
        return response()->json([
            'message' => 'This job posting has no available vacancies at the moment.',
            'filled' => true
        ], 422);
    }

    // merge submitted data into user profile for validation
    $user->fill($request->only(['full_address', 'city', 'province']));

    // Check required fields
    $requiredFields = [
        'first_name', 'last_name', 'gender', 'birth_date',
        'email', 'mobile_number', 'full_address', 'city', 'province'
    ];

    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (empty($user->{$field})) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        return response()->json([
            'message' => 'Please complete your profile before applying. Missing: ' . implode(', ', $missingFields)
        ], 422);
    }

    // Check resume
    if (!$user->resume || !$user->resume->resume) {
        return response()->json([
            'message' => 'You must upload a resume before applying.'
        ], 422);
    }

    // ❌ Rule 1: Block ALL applications if applicant already scheduled for training
    $hasTrainingOrPassed = Application::where('user_id', $user->id)
    ->whereIn('status', ['scheduled_for_training', 'training_passed'])
    ->exists();

    if ($hasTrainingOrPassed) {
    return response()->json([
        'message' => 'You cannot apply for other jobs while you are scheduled for or have already passed training.'
    ], 403);
   }

    // ❌ Rule 2: Block reapplying to SAME job if applicant failed before
    $failedBefore = Application::where('user_id', $user->id)
        ->where('job_id', $job->id)
        ->where('status', 'failed')
        ->exists();

    if ($failedBefore) {
        return response()->json([
            'message' => 'You cannot reapply to this job since you already failed.'
        ], 403);
    }

    // Snapshot resume before transaction
    $resumePath = $user->resume->resume;
    $resumeSnapshotPath = null;

    if ($resumePath && \Storage::disk('public')->exists($resumePath)) {
        $extension = pathinfo($resumePath, PATHINFO_EXTENSION);
        $snapshotFilename = 'resume_snapshots/' . uniqid('resume_') . '.' . $extension;

        \Storage::disk('public')->copy($resumePath, $snapshotFilename);
        $resumeSnapshotPath = $snapshotFilename;
    }

    // Use transaction with pessimistic locking to prevent race conditions
    try {
        \DB::transaction(function () use ($user, $job, $resumeSnapshotPath, $file201) {
            // Lock check: Prevent duplicate application (if not failed)
            $existing = Application::where('user_id', $user->id)
                ->where('job_id', $job->id)
                ->whereNotIn('status', ['failed']) // allow retry only if not failed
                ->lockForUpdate() // Pessimistic lock
                ->first();

            if ($existing) {
                throw new \Exception('You have already applied for this job.');
            }

            // Create application within transaction
            Application::create([
                'user_id' => $user->id,
                'job_id' => $job->id,
                'resume_snapshot' => $resumeSnapshotPath,
                'licenses' => $file201->licenses ?? [],
                'sss_number' => $file201->sss_number ?? null,
                'philhealth_number' => $file201->philhealth_number ?? null,
                'tin_id_number' => $file201->tin_id_number ?? null,
                'pagibig_number' => $file201->pagibig_number ?? null,
                'status' => 'pending', // lowercase to match enum
            ]);
        });
    } catch (\Exception $e) {
        // Clean up resume snapshot if transaction fails
        if ($resumeSnapshotPath && \Storage::disk('public')->exists($resumeSnapshotPath)) {
            \Storage::disk('public')->delete($resumeSnapshotPath);
        }

        return response()->json([
            'message' => $e->getMessage()
        ], 409);
    }

    return response()->json([
        'message' => 'Your application was submitted successfully.'
    ], 200);
}

    /* Show all jobs the applicant has applied to. */
    public function myApplications()
    {
        $user = auth()->user();

        $applications = Application::with('job')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $resume = $user->resume ?? null;

        return view('applicant.applications', compact('applications', 'resume'));
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
