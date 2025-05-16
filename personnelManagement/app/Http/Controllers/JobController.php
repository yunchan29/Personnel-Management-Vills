<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    // Save to database (assuming you have a Job model)
    \App\Models\Job::create($validated);

    return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job posted successfully!');
}

}
