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
                \App\Enums\ApplicationStatus::TO_REVIEW->value,
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
        $job->delete();

        return redirect()->route('hrAdmin.jobPosting')->with('success', 'Job deleted successfully.');
    }
}
