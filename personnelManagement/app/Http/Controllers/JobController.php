<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use App\Models\Interview;
use App\Models\TrainingSchedule;
use Illuminate\Support\Facades\DB;

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
                ->map(fn($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        if (!empty($validated['additional_info'])) {
            $validated['additional_info'] = collect(preg_split('/\r\n|\r|\n/', $validated['additional_info']))
                ->map(fn($line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        Job::create($validated);

        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job posted successfully!');
    }

    public function index(Request $request)
{
    $query = Job::withCount([
        'applications as applications_count' => function ($query) {
            $query->whereIn('status', [
                \App\Enums\ApplicationStatus::PENDING->value,
                \App\Enums\ApplicationStatus::APPROVED->value,
                \App\Enums\ApplicationStatus::FOR_INTERVIEW->value,
                \App\Enums\ApplicationStatus::INTERVIEWED->value,
                \App\Enums\ApplicationStatus::SCHEDULED_FOR_TRAINING->value,
            ]);
        }
    ]);

    // 🔍 Search by job title / position
    if ($request->filled('search')) {
        $query->where('job_title', 'like', '%' . $request->search . '%');
    }

    // 🏢 Filter by company name
    if ($request->filled('company_name')) {
        $query->where('company_name', $request->company_name);
    }

    // 🔃 Sort logic
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

    // 🏢 Distinct company list for filter dropdown
    $companies = Job::select('company_name')->distinct()->pluck('company_name');

    return view('admins.hrAdmin.jobPosting', compact('jobs', 'companies'));
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
    $job = Job::findOrFail($id);

    if ($job->apply_until < now()->addDay()->startOfDay()) {
    // Expired jobs → only allow limited editing
    $rules = [
        'vacancies'   => 'required|integer|min:1',
        'apply_until' => 'required|date|after_or_equal:today',
    ];
} else {
    // Normal validation for active jobs
    $rules = [
        'job_title'      => 'required|string|max:255',
        'company_name'   => 'required|string|max:255',
        'job_industry'   => 'required|string|max:255',
        'location'       => 'required|string|max:255',
        'vacancies'      => 'required|integer|min:1',
        'apply_until'    => 'required|date',
        'qualifications' => 'nullable|string',
        'additional_info'=> 'nullable|string',
    ];
}

    $validated = $request->validate($rules);

    // Transform qualifications / additional info if present
    if (!empty($validated['qualifications'])) {
        $validated['qualifications'] = collect(preg_split('/\r\n|\r|\n/', $validated['qualifications']))
            ->map(fn($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    if (!empty($validated['additional_info'])) {
        $validated['additional_info'] = collect(preg_split('/\r\n|\r|\n/', $validated['additional_info']))
            ->map(fn($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    $job->update($validated);

    return redirect()->route('hrAdmin.jobPosting')
        ->with('success', 'Job updated successfully!');
}


    public function destroy($id)
    {
        $job = Job::findOrFail($id);

        // Check for existing applications before deletion
        $applicationsCount = $job->applications()->count();

        if ($applicationsCount > 0) {
            return redirect()->route('hrAdmin.jobPosting')->with('error',
                "Cannot delete this job. It has {$applicationsCount} application(s). Please archive or handle applications first.");
        }

        $job->delete();

        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job deleted successfully.');
    }

    /**
     * Repost an expired or filled job with a new deadline and reset vacancies
     */
    public function repost($id)
    {
        $originalJob = Job::findOrFail($id);

        // Create a new job with same details but fresh deadline
        $newJob = Job::create([
            'job_title' => $originalJob->job_title,
            'company_name' => $originalJob->company_name,
            'job_industry' => $originalJob->job_industry,
            'location' => $originalJob->location,
            'vacancies' => $originalJob->vacancies,
            'apply_until' => now()->addDays(30), // Default: 30 days from now
            'qualifications' => $originalJob->qualifications,
            'additional_info' => $originalJob->additional_info,
            'status' => 'active',
        ]);

        return redirect()->route('hrAdmin.jobPosting')
            ->with('success', "Job '{$newJob->job_title}' has been reposted successfully!");
    }

    /**
     * Quick extend a job deadline by specified days
     */
    public function quickExtend(Request $request, $id)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $job = Job::findOrFail($id);

        $newDeadline = \Carbon\Carbon::parse($job->apply_until)->addDays($validated['days']);
        $job->update(['apply_until' => $newDeadline]);

        return redirect()->route('hrAdmin.jobPosting')
            ->with('success', "Job deadline extended by {$validated['days']} days. New deadline: {$newDeadline->format('F d, Y')}");
    }

    /**
     * Bulk extend deadlines for multiple jobs
     */
    public function bulkExtend(Request $request)
    {
        $validated = $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:jobs,id',
            'days' => 'required|integer|min:1|max:365',
        ]);

        $count = 0;
        foreach ($validated['job_ids'] as $jobId) {
            $job = Job::find($jobId);
            if ($job) {
                $newDeadline = \Carbon\Carbon::parse($job->apply_until)->addDays($validated['days']);
                $job->update(['apply_until' => $newDeadline]);
                $count++;
            }
        }

        return redirect()->route('hrAdmin.jobPosting')
            ->with('success', "Successfully extended {$count} job(s) by {$validated['days']} days.");
    }

    /**
     * Bulk delete multiple jobs (only if they have no applications)
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:jobs,id',
        ]);

        $deleted = 0;
        $failed = 0;
        $failedJobs = [];

        foreach ($validated['job_ids'] as $jobId) {
            $job = Job::find($jobId);
            if ($job) {
                $applicationsCount = $job->applications()->count();

                if ($applicationsCount > 0) {
                    $failed++;
                    $failedJobs[] = $job->job_title;
                } else {
                    $job->delete();
                    $deleted++;
                }
            }
        }

        if ($deleted > 0 && $failed === 0) {
            return redirect()->route('hrAdmin.jobPosting')
                ->with('success', "Successfully deleted {$deleted} job(s).");
        } elseif ($deleted > 0 && $failed > 0) {
            return redirect()->route('hrAdmin.jobPosting')
                ->with('warning', "Deleted {$deleted} job(s). {$failed} job(s) could not be deleted due to existing applications: " . implode(', ', $failedJobs));
        } else {
            return redirect()->route('hrAdmin.jobPosting')
                ->with('error', "No jobs were deleted. All selected jobs have existing applications.");
        }
    }

    /**
     * Bulk update status for multiple jobs
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:jobs,id',
            'status' => 'required|in:active,expired,filled',
        ]);

        $count = Job::whereIn('id', $validated['job_ids'])
            ->update(['status' => $validated['status']]);

        return redirect()->route('hrAdmin.jobPosting')
            ->with('success', "Successfully updated status for {$count} job(s) to '{$validated['status']}'.");
    }
}
