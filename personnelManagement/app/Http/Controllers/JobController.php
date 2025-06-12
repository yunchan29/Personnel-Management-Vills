<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'vacancies' => 'required|integer|min:1',
            'apply_until' => 'required|date',
            'qualifications' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        // Convert qualifications to array
        if (!empty($validated['qualifications'])) {
            $qualArray = array_filter(array_map('trim', explode("\n", $validated['qualifications'])));
            $validated['qualifications'] = $qualArray;
        }

        if (!empty($validated['additional_info'])) {
            $additionalInfoArray = array_filter(array_map('trim', explode("\n", $validated['additional_info'])));
            $validated['additional_info'] = $additionalInfoArray;
        }

        Job::create($validated);

        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job posted successfully!');
    }

    public function index()
    {
        $jobs = Job::withCount('applications')->get();
        return view('hrAdmin.jobPosting', compact('jobs'));
    }

    public function show($id)
    {
        $job = Job::findOrFail($id);
        return view('jobs.show', compact('job'));
    }

    public function edit($id)
    {
        $job = Job::findOrFail($id);
        return view('jobPostingEdit', compact('job'));
    }

    public function viewApplicants($id)
    {
        $job = Job::with('applications')->findOrFail($id); // Load applicants with the job
        $applicants = $job->applicants;
        return view('hrAdmin.viewApplicants', compact('job', 'applicants'));
    }

    public function applications()
    {
        $jobs = Job::withCount('applications')->get(); // Adds 'applicants_count' for application.blade.php
        return view('hrAdmin.application', compact('jobs'));
    }
}
