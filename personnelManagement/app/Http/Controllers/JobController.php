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
        'job_industry' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'vacancies' => 'required|integer|min:1',
        'apply_until' => 'required|date',
        'qualifications' => 'nullable|string',
        'additional_info' => 'nullable|string',
    ]);

    if (!empty($validated['qualifications'])) {
        $validated['qualifications'] = collect(preg_split('/\r\n|\r|\n/', $validated['qualifications']))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    if (!empty($validated['additional_info'])) {
        $validated['additional_info'] = collect(preg_split('/\r\n|\r|\n/', $validated['additional_info']))
            ->map(fn ($line) => trim($line))
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
        'job_industry' => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'vacancies' => 'required|integer|min:1',
        'apply_until' => 'required|date',
        'qualifications' => 'nullable|string',
        'additional_info' => 'nullable|string',
    ]);

    if (!empty($validated['qualifications'])) {
        $validated['qualifications'] = collect(preg_split('/\r\n|\r|\n/', $validated['qualifications']))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    if (!empty($validated['additional_info'])) {
        $validated['additional_info'] = collect(preg_split('/\r\n|\r|\n/', $validated['additional_info']))
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
        $job = Job::with(['applications.user'])->findOrFail($id);
        $applications = $job->applications;

        return view('hrAdmin.viewApplicants', compact('job', 'applications'));
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
