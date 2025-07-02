<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;

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

    public function index(Request $request)
    {
        $query = Job::withCount('applications');

        // Search by job title / position
        if ($request->filled('search')) {
            $query->where('job_title', 'like', '%' . $request->search . '%');
        }

        // Sort logic
        switch ($request->sort) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'position_asc':
                $query->orderBy('job_title', 'asc');
                break;
            case 'position_desc':
                $query->orderBy('job_title', 'desc');
                break;
        }

        $jobs = $query->get();

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

  // View all applications (default page)
    public function applications()
    {
        $jobs = Job::withCount('applications')->get();
        $applications = Application::with('user', 'job')->latest()->get();

        return view('hrAdmin.application', compact('jobs', 'applications'));
    }

   // View applicants per job
public function viewApplicants($jobId)
{
    // Load the selected job
    $job = Job::findOrFail($jobId);

    // Load all jobs with application count for the sidebar or filter
    $jobs = Job::withCount('applications')->get();

    // Get all applications for the selected job, including user and job relationships
    $applications = Application::with(['user', 'job'])
        ->where('job_id', $jobId)
        ->get();

    // Get all approved applicants (for training schedule, etc.)
    $approvedApplicants = Application::with(['user', 'job'])
        ->where('status', 'approved')
        ->get();

    return view('hrAdmin.application', [
        'jobs' => $jobs,
        'applications' => $applications,
        'selectedJob' => $job,
        'selectedTab' => 'applicants',
        'approvedApplicants' => $approvedApplicants,
    ]);
}

    // Update application status (approve / decline)
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,declined,interviewed,pending'
        ]);

        $application->status = $validated['status'];
        $application->save();

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
            'status' => $application->status,
            'application_id' => $application->id,
        ]);
    }

    // ✅ Set interview date (new method)
    public function setInterviewDate(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $validated = $request->validate([
            'interview_date' => 'required|date|after_or_equal:today',
        ]);

        $application->interview_date = $validated['interview_date'];
        $application->save();

        return response()->json([
            'success' => true,
            'message' => 'Interview date set successfully.',
            'application_id' => $application->id,
            'interview_date' => $application->interview_date,
        ]);
    }

    // Show training schedule for approved applicants
    public function trainingSchedule()
    {
        $applications = Application::with(['user', 'job'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('hrAdmin.trainingSchedule', compact('applications'));
    }

    // Delete a job posting
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job deleted successfully.');
    }


}
