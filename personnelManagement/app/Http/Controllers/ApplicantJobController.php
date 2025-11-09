<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ApplicantJobController extends Controller
{
    /* Show applicant dashboard with latest job listings. */
   public function dashboard(Request $request)
{
    $user = auth()->user();
    $resume = $user->resume ?? null;

    // ✅ Load ALL jobs by default, only filter when explicitly requested
    $industry = null;

    // Only apply industry filter if explicitly provided in query string
    if ($request->has('industry')) {
        $rawIndustry = $request->query('industry');
        // Empty string means "All Industries" - show all jobs
        $industry = ($rawIndustry !== '' && $rawIndustry !== null) ? $rawIndustry : null;
    }

    $jobs = Job::latest()
        ->whereDate('apply_until', '>=', \Carbon\Carbon::today()) // 👈 filter expired jobs
        ->when($industry, fn($query) => $query->where('job_industry', $industry))
        ->get();

    $appliedJobIds = Application::where('user_id', $user->id)
        ->pluck('job_id')
        ->toArray();

    $hasTrainingOrPassed = Application::where('user_id', $user->id)
         ->whereIn('status', ['scheduled_for_training', 'training_passed'])
         ->exists();

    return view('users.dashboard', compact(
    'jobs',
    'resume',
    'appliedJobIds',
    'industry',
    'hasTrainingOrPassed'
));

}
    /* Apply to a specific job. */
    public function apply(Request $request, Job $job)
{
    $user = auth()->user();
    $file201 = $user->file201;
    
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

    // Prevent duplicate application (if not failed)
    $existing = Application::where('user_id', $user->id)
        ->where('job_id', $job->id)
        ->whereNotIn('status', ['failed']) // allow retry only if not failed
        ->first();

    if ($existing) {
        return response()->json([
            'message' => 'You have already applied for this job.'
        ], 409);
    }

    // Snapshot resume
    $resumePath = $user->resume->resume;
    $resumeSnapshotPath = null;

    if ($resumePath && \Storage::disk('public')->exists($resumePath)) {
        $extension = pathinfo($resumePath, PATHINFO_EXTENSION);
        $snapshotFilename = 'resume_snapshots/' . uniqid('resume_') . '.' . $extension;

        \Storage::disk('public')->copy($resumePath, $snapshotFilename);
        $resumeSnapshotPath = $snapshotFilename;
    }

    // Create application
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
