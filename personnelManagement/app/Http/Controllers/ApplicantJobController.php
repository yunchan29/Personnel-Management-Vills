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
    public function dashboard()
    {
        $user = auth()->user();

        $jobs = Job::latest()->get();
        $resume = $user->resume ?? null;

        // Get all job IDs the user has applied to
        $appliedJobIds = Application::where('user_id', $user->id)
            ->pluck('job_id')
            ->toArray();

        return view('applicant.dashboard', compact('jobs', 'resume', 'appliedJobIds'));
    }

    /* Apply to a specific job. */
    public function apply(Request $request, Job $job)
    {
        $user = auth()->user();
        $file201 = $user->file201;

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

        // Check for existing application
        $existing = Application::where('user_id', $user->id)
            ->where('job_id', $job->id)
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
            'status' => 'Pending',
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

        // ← dump here:
        dd('this method is being hit');
        dd($applications);

        $resume = $user->resume ?? null;

        return view('applicant.applications', compact('applications', 'resume'));
    }
}
