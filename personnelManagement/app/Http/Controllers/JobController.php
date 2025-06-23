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
            'role_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'vacancies' => 'required|integer|min:1',
            'apply_until' => 'required|date',
            'qualifications' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        $validated['role_type'] = strtoupper($validated['role_type']);

        // Split lines and commas into proper array
        if (!empty($validated['qualifications'])) {
            $validated['qualifications'] = collect(preg_split('/\r\n|\r|\n/', $validated['qualifications']))
                ->flatMap(fn ($line) => explode(',', $line))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        if (!empty($validated['additional_info'])) {
            $validated['additional_info'] = collect(preg_split('/\r\n|\r|\n/', $validated['additional_info']))
                ->flatMap(fn ($line) => explode(',', $line))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'role_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'vacancies' => 'required|integer|min:1',
            'apply_until' => 'required|date',
            'qualifications' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        $validated['role_type'] = strtoupper($validated['role_type']);

        if (!empty($validated['qualifications'])) {
            $validated['qualifications'] = collect(preg_split('/\r\n|\r|\n/', $validated['qualifications']))
                ->flatMap(fn ($line) => explode(',', $line))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        if (!empty($validated['additional_info'])) {
            $validated['additional_info'] = collect(preg_split('/\r\n|\r|\n/', $validated['additional_info']))
                ->flatMap(fn ($line) => explode(',', $line))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        }

        $job = Job::findOrFail($id);
        $job->update($validated);

        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job updated successfully!');
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
    public function destroy($id)
{
    $job = Job::findOrFail($id);
    $job->delete();

    return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job deleted successfully.');
}
}
