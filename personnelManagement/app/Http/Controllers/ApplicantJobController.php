<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Interview;
use App\Models\TrainingSchedule;
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

    return view('users.dashboard', compact(
    'jobs',
    'resume',
    'appliedJobIds',
    'industry',
    'hasTrainingOrPassed',
    'notifications'
));

}

    /**
     * Get notifications for applicant dashboard
     */
    private function getApplicantNotifications($user)
    {
        $notifications = [];
        $today = Carbon::today();

        // Get user's applications with interviews and training schedules
        $applications = Application::with(['job', 'interview', 'trainingSchedule'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['declined', 'failed_interview', 'failed_evaluation', 'rejected'])
            ->get();

        foreach ($applications as $application) {
            // Check for scheduled interviews
            if ($application->interview && in_array($application->interview->status, ['scheduled', 'rescheduled'])) {
                $interviewDate = Carbon::parse($application->interview->scheduled_at);
                $daysUntil = $today->diffInDays($interviewDate, false);

                if ($daysUntil >= 0 && $daysUntil <= 7) {
                    $notifications[] = [
                        'type' => 'interview',
                        'title' => 'Interview Scheduled',
                        'message' => "{$application->job->job_title} at {$application->job->company_name}",
                        'days_until' => (int)$daysUntil,
                        'details' => [
                            'Date' => $interviewDate->format('M d, Y'),
                            'Time' => $interviewDate->format('h:i A'),
                        ],
                        'action_url' => route('applicant.application'),
                        'action_text' => 'View Details'
                    ];
                }
            }

            // Check for scheduled training
            if ($application->trainingSchedule && in_array($application->status, ['scheduled_for_training', 'in_training'])) {
                $trainingStartDate = Carbon::parse($application->trainingSchedule->start_date);
                $daysUntil = $today->diffInDays($trainingStartDate, false);

                if ($daysUntil >= 0 && $daysUntil <= 7) {
                    $notifications[] = [
                        'type' => 'training',
                        'title' => 'Training Scheduled',
                        'message' => "{$application->job->job_title} at {$application->job->company_name}",
                        'days_until' => (int)$daysUntil,
                        'details' => [
                            'Starts' => $trainingStartDate->format('M d, Y'),
                            'Duration' => $trainingStartDate->diffInDays(Carbon::parse($application->trainingSchedule->end_date)) . ' days',
                        ],
                        'action_url' => route('applicant.application'),
                        'action_text' => 'View Details'
                    ];
                }
            }

            // Check for pending evaluation
            if ($application->status === 'for_evaluation') {
                $notifications[] = [
                    'type' => 'evaluation',
                    'title' => 'Under Evaluation',
                    'message' => "{$application->job->job_title} at {$application->job->company_name}",
                    'details' => [
                        'Status' => 'Training completed',
                    ],
                    'action_url' => route('applicant.application'),
                    'action_text' => 'Check Status'
                ];
            }

            // Check for approved applications pending interview
            if ($application->status === 'approved' && !$application->interview) {
                $notifications[] = [
                    'type' => 'application',
                    'title' => 'Application Approved',
                    'message' => "{$application->job->job_title} at {$application->job->company_name}",
                    'details' => [
                        'Status' => 'Awaiting interview schedule',
                    ],
                    'action_url' => route('applicant.application'),
                    'action_text' => 'View Details'
                ];
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
}
