<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\Facades\Log;

class ApplicantJobController extends Controller
{
    /* Show applicant dashboard with latest job listings.*/
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


    /* Apply to a specific job.*/
    public function apply(Request $request, Job $job)
    {
        $user = auth()->user();
        $file201 = $user->file201;

        // ✅ Resume check
        if (!$user->resume || !$user->resume->resume) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You must upload a resume before applying.'], 422);
            }

            return redirect()->back()->with('error', 'You must upload a resume before applying.');
        }

        // ✅ Prevent duplicate application
        $existing = Application::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You have already applied for this job.'], 409);
            }

            return redirect()->back()->with('error', 'You have already applied for this job.');
        }

        // ✅ Create application with initial status = 'Pending'
        Application::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
            'resume_id' => $user->resume->id ?? null,
            'licenses' => $file201->licenses ?? [],
            'sss_number' => $file201->sss_number,
            'philhealth_number' => $file201->philhealth_number,
            'tin_id_number' => $file201->tin_id_number,
            'pagibig_number' => $file201->pagibig_number,
            'status' => 'Pending', // ✅ status tracking
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your application was submitted successfully.']);
        }

        return redirect()->back()->with('success', 'Your application was submitted successfully.');
    }

    /*Show all jobs the applicant has applied to.*/
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
