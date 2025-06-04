<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;

class ApplicantJobController extends Controller
{
    /**
     * Show applicant dashboard with latest job listings.
     */
    public function dashboard()
    {
        $jobs = Job::latest()->get(); // You can change to ->paginate(10) if needed
        $resume = auth()->user()->resume ?? null;

        return view('applicant.dashboard', compact('jobs', 'resume'));
    }

    /**
     * Show all available jobs.
     */
    public function index()
    {
        $jobs = Job::latest()->get();
        return view('applicant.jobs', compact('jobs'));
    }

    /**
     * Apply to a specific job.
     */
    public function apply(Request $request, $job_id)
    {
        $user = auth()->user();

        // ✅ Resume check
        if (!$user->resume || !$user->resume->resume) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You must upload a resume before applying.'], 422);
            }

            return redirect()->back()->with('error', 'You must upload a resume before applying.');
        }

        // ✅ Prevent duplicate application
        $existing = Application::where('user_id', $user->id)
            ->where('job_id', $job_id)
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You have already applied for this job.'], 409);
            }

            return redirect()->back()->with('error', 'You have already applied for this job.');
        }

        // ✅ Create application
        Application::create([
            'user_id' => $user->id,
            'job_id' => $job_id,
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

        $applications = Application::with('job')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $resume = $user->resume ?? null;

        return view('applicant.application', compact('applications', 'resume'));
    }
}
