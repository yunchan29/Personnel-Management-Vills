<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Log;

class ApplicantJobController extends Controller
{
    /**
     * Show applicant dashboard with latest job listings.
     */
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

    /**
     * Show all jobs.
     */
    public function index()
    {
        $user = auth()->user();

        $jobs = Job::latest()->get();

        // Get all job IDs the user has applied to
        $appliedJobIds = Application::where('user_id', $user->id)
            ->pluck('job_id')
            ->toArray();

        return view('applicant.jobs', compact('jobs', 'appliedJobIds'));
    }

    /**
     * Apply to a specific job using route model binding.
     */
    public function apply(Request $request, Job $job)
    {
        $user = auth()->user();

        // Resume check
        if (!$user->resume || !$user->resume->resume) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You must upload a resume before applying.'], 422);
            }

            return redirect()->back()->with('error', 'You must upload a resume before applying.');
        }

        // Prevent duplicate application
        $existing = Application::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You have already applied for this job.'], 409);
            }

            return redirect()->back()->with('error', 'You have already applied for this job.');
        }

        // Create application
        Application::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your application was submitted successfully.']);
        }

        return redirect()->back()->with('success', 'Your application was submitted successfully.');
    }

    /**
     * Show all jobs the applicant has applied to.
     */
    public function myApplications()
    {
        $user = auth()->user();

        Log::info("Fetching applications for user ID: {$user->id}");

        $applications = Application::with('job')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        Log::info("Total applications found: " . $applications->count());

        foreach ($applications as $application) {
            Log::info("Application ID: {$application->id}, Job ID: {$application->job_id}, Job Title: " . ($application->job->title ?? 'No Job Found'));
        }

        $resume = $user->resume ?? null;

        if ($resume && $resume->resume) {
            Log::info("Resume found: {$resume->resume}");
        } else {
            Log::info("No resume found for user ID: {$user->id}");
        }

        return view('applicant.application', compact('applications', 'resume'));
    }
}
