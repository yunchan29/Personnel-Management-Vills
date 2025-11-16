<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Job;
use App\Models\Interview;
use App\Models\TrainingSchedule;
use Illuminate\Support\Facades\DB;
use App\Mail\ApprovedLetterMail;
use App\Mail\DeclinedLetterMail;
use App\Mail\PassInterviewMail;
use App\Mail\FailInterviewMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // ✅ needed for archiving
use App\Enums\ApplicationStatus;

class InitialApplicationController extends Controller
{
    public function index(Request $request)
    {
        $jobsQuery = Job::withCount([
            'applications as applications_count' => function ($query) {
                $query->whereIn('status', [
                    ApplicationStatus::PENDING->value,
                    ApplicationStatus::APPROVED->value,
                    ApplicationStatus::FOR_INTERVIEW->value,
                    ApplicationStatus::INTERVIEWED->value,
                    ApplicationStatus::SCHEDULED_FOR_TRAINING->value,
                ]);
            }
        ]);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $jobsQuery->where(function($query) use ($searchTerm) {
                $query->where('job_title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('company_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('location', 'like', '%' . $searchTerm . '%')
                      ->orWhere('qualifications', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('company_name')) {
            $jobsQuery->where('company_name', $request->company_name);
        }

        switch ($request->sort) {
            case 'latest':
                $jobsQuery->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $jobsQuery->orderBy('created_at', 'asc');
                break;
            case 'position_asc':
                $jobsQuery->orderBy('job_title', 'asc');
                break;
            case 'position_desc':
                $jobsQuery->orderBy('job_title', 'desc');
                break;
            default:
                $jobsQuery->orderBy('created_at', 'desc');
        }

        $jobs = $jobsQuery->get();

        $applicationsQuery = Application::with('user.resume', 'job')->latest();

        if ($request->filled('company_name')) {
            $applicationsQuery->whereHas('job', function ($q) use ($request) {
                $q->where('company_name', $request->company_name);
            });
        }

        $applications = $applicationsQuery->get();
        $companies = Job::select('company_name')->distinct()->pluck('company_name');

        return view('admins.hrAdmin.application', compact('jobs', 'applications', 'companies'));
    }

    public function viewApplicants($jobId)
{
    $job = Job::findOrFail($jobId);

    // Jobs with filtered application count (Applicants + Interview + Training tabs, excluding evaluated)
    $jobs = Job::withCount([
        'applications as applications_count' => function ($query) {
            $query->whereIn('status', [
                ApplicationStatus::PENDING->value,
                ApplicationStatus::APPROVED->value,
                ApplicationStatus::FOR_INTERVIEW->value,
                ApplicationStatus::INTERVIEWED->value,
                ApplicationStatus::SCHEDULED_FOR_TRAINING->value,
            ]);
        }
    ])->get();

    // Applicants tab - Only pending status
    $applications = Application::with(['user.resume', 'user.file201', 'user.workExperiences', 'job', 'interview', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->where('status', ApplicationStatus::PENDING->value)
        ->get();

    // Interview tab - Approved and for_interview statuses
    $approvedApplicants = Application::with(['user.resume', 'user.file201', 'user.workExperiences', 'job', 'interview', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereIn('status', [
            ApplicationStatus::APPROVED->value,
            ApplicationStatus::FOR_INTERVIEW->value,
        ])
        ->get();

    // Training tab - Interviewed and scheduled_for_training statuses
    $interviewApplicants = Application::with(['user.resume', 'user.file201', 'user.workExperiences', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereIn('status', [
            ApplicationStatus::INTERVIEWED->value,
            ApplicationStatus::SCHEDULED_FOR_TRAINING->value,
        ])
        ->get();

    // Evaluation tab - Trained and for_evaluation statuses
    $forTrainingApplicants = Application::with(['user.resume', 'job', 'trainingSchedule', 'evaluation'])
        ->where('job_id', $jobId)
        ->whereIn('status', [
            ApplicationStatus::TRAINED->value,
            ApplicationStatus::FOR_EVALUATION->value,
            ApplicationStatus::PASSED_EVALUATION->value,
        ])
        ->get();

    $companies = Job::select('company_name')->distinct()->pluck('company_name');

    return view('admins.hrAdmin.application', [
        'jobs' => $jobs,
        'applications' => $applications,
        'selectedJob' => $job,
        'selectedTab' => 'applicants',
        'approvedApplicants' => $approvedApplicants,
        'interviewApplicants' => $interviewApplicants,
        'forTrainingApplicants' => $forTrainingApplicants,
        'companies' => $companies,
    ]);
}



    public function updateApplicationStatus(Request $request, $id)
    {
        $application = Application::with(['user', 'job'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:approved,declined,interviewed,for_interview,trained,failed_interview,fail_interview'
        ]);

        // ✅ SECURITY FIX: Wrap in database transaction for data consistency
        DB::transaction(function () use ($application, $validated, $request) {
            $oldStatus = $application->status;
            $application->setStatus($validated['status']); // Use setStatus which handles enum conversion
            $application->save();

            // Observer now handles interview status sync automatically

            if ($oldStatus != $application->status) {
                $status = $application->status;

                switch ($status) {
                    case ApplicationStatus::APPROVED:
                        Mail::to($application->user->email)->send(new ApprovedLetterMail($application));
                        break;

                    case ApplicationStatus::DECLINED:
                        Mail::to($application->user->email)->send(new DeclinedLetterMail($application));
                        // Observer handles archiving automatically
                        break;

                    case ApplicationStatus::INTERVIEWED:
                        Mail::to($application->user->email)->send(new PassInterviewMail($application));
                        break;

                    case ApplicationStatus::FAILED_INTERVIEW:
                        Mail::to($application->user->email)->send(new FailInterviewMail($application));
                        // Observer handles archiving automatically
                        break;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
            'status' => $application->status,
            'application_id' => $application->id,
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',   // match frontend
            'ids.*' => 'exists:applications,id',
            'status' => 'required|string|in:approved,declined,interviewed,failed_interview,fail_interview,for_interview,trained'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // ✅ SECURITY FIX: Wrap bulk operations in database transaction
        $updatedCount = DB::transaction(function () use ($validated) {
            $applications = Application::with(['user', 'job'])
                ->whereIn('id', $validated['ids'])
                ->get();

            foreach ($applications as $application) {
                $oldStatus = $application->status;
                $application->setStatus($validated['status']); // Use setStatus method
                $application->save();

                // Observer now handles interview and training status sync automatically

                // 🔔 Send mails
                if ($oldStatus != $application->status) {
                    $status = $application->status;

                    switch ($status) {
                        case ApplicationStatus::APPROVED:
                            Mail::to($application->user->email)->send(new ApprovedLetterMail($application));
                            break;

                        case ApplicationStatus::DECLINED:
                            Mail::to($application->user->email)->send(new DeclinedLetterMail($application));
                            // Observer handles archiving
                            break;

                        case ApplicationStatus::INTERVIEWED:
                            Mail::to($application->user->email)->send(new PassInterviewMail($application));
                            break;

                        case ApplicationStatus::FAILED_INTERVIEW:
                            Mail::to($application->user->email)->send(new FailInterviewMail($application));
                            // Observer handles archiving
                            break;
                    }
                }
            }

            return $applications->count();
        });

        return response()->json([
            'success' => true,
            'message' => $updatedCount . ' applications updated successfully.',
            'status' => $validated['status'],
        ]);
    }
}
