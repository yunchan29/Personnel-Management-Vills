<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job; // Make sure to import your Job model

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

        // Convert qualifications string to array
        if (!empty($validated['qualifications'])) {
            $qualArray = array_filter(array_map('trim', explode("\n", $validated['qualifications'])));
            $validated['qualifications'] = $qualArray;
        }

        // Convert a 
        if (!empty($validated['additional_info'])) {
            $additionalInfoArray = array_filter(array_map('trim', explode("\n", $validated['additional_info'])));
            $validated['additional_info'] = $additionalInfoArray;
        }


        Job::create($validated);

        // Redirect back to the job posting page with success message
        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job posted successfully!');
    }

    // This will show the job posting form and also display all posted jobs
    public function index()
    {
        $jobs = Job::latest()->get(); // Get all jobs, newest first
        return view('hrAdmin.jobPosting', compact('jobs'));
    }
    public function show($id)
{
    $job = Job::findOrFail($id);
    return view('jobs.show', compact('job'));
}

}
