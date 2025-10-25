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

class InitialApplicationController extends Controller
{
    public function index(Request $request)
    {
        $jobsQuery = Job::withCount('applications');

        if ($request->filled('search')) {
            $jobsQuery->where('job_title', 'like', '%' . $request->search . '%');
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

        $applicationsQuery = Application::with('user', 'job')->latest();

        if ($request->filled('company_name')) {
            $applicationsQuery->whereHas('job', function ($q) use ($request) {
                $q->where('company_name', $request->company_name);
            });
        }

        $applications = $applicationsQuery->get();
        $companies = Job::select('company_name')->distinct()->pluck('company_name');

        return view('hrAdmin.application', compact('jobs', 'applications', 'companies'));
    }

    public function viewApplicants($jobId)
{
    $job = Job::findOrFail($jobId);

    // Jobs with filtered application count (exclude hired + declined)
    $jobs = Job::withCount([
        'applications as applications_count' => function ($query) {
            $query->whereNotIn('status', ['hired', 'declined']);
        }
    ])->get();

    // Main applicants list (exclude hired + declined)
    $applications = Application::with(['user', 'job', 'interview', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereNotIn('status', ['hired', 'declined'])
        ->get();

    // Approved applicants (approved + for_interview, but still exclude hired/declined just in case)
    $approvedApplicants = Application::with(['user', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereIn('status', ['approved', 'for_interview'])
        ->whereNotIn('status', ['hired', 'declined'])
        ->get();

    // Interview applicants
    $interviewApplicants = Application::with(['user', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereIn('status', ['interviewed', 'scheduled_for_training'])
        ->whereNotIn('status', ['hired', 'declined'])
        ->get();

    // For training applicants
    $forTrainingApplicants = Application::with(['user', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->where('status', 'for_evaluation')
        ->whereNotIn('status', ['hired', 'declined'])
        ->get();

    $companies = Job::select('company_name')->distinct()->pluck('company_name');

    return view('hrAdmin.application', [
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
            'status' => 'required|string|in:approved,declined,interviewed,for_interview,trained,fail_interview'
        ]);

        $oldStatus = $application->status;
        $application->status = $validated['status'];
        $application->save();

        if (in_array($validated['status'], ['interviewed', 'fail_interview'])) {
            DB::table('interviews')
                ->where('application_id', $application->id)
                ->update(['status' => 'completed']);
        }

        if ($oldStatus !== $application->status) {
            switch ($application->status) {
                case 'approved':
                    Mail::to($application->user->email)->send(new ApprovedLetterMail($application));
                    break;

                case 'declined':
                    Mail::to($application->user->email)->send(new DeclinedLetterMail($application));

                    // ✅ Archive user when declined
                    $application->is_archived = true;
                    $application->save();

                    break;

                case 'interviewed':
                    Mail::to($application->user->email)->send(new PassInterviewMail($application));
                    break;

                case 'fail_interview':
                    Mail::to($application->user->email)->send(new FailInterviewMail($application));

                    // ✅ Archive user when failed interview
                    $application->is_archived = true;
                    $application->save();

                    break;
            }
        }

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
            'status' => 'required|string|in:approved,declined,interviewed,fail_interview,for_interview,trained'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $applications = Application::with(['user', 'job'])
            ->whereIn('id', $validated['ids'])
            ->where('status', '!=', $validated['status'])
            ->get();

        foreach ($applications as $application) {
            $oldStatus = $application->status;
            $application->status = $validated['status'];
            $application->save();

            // 🔔 Sync interview record if interviewed or failed interview
            if (in_array($validated['status'], ['interviewed', 'fail_interview'])) {
                DB::table('interviews')
                    ->where('application_id', $application->id)
                    ->update(['status' => 'completed']);
            }

            // 🔔 Send mails and archive rules
            if ($oldStatus !== $application->status) {
                switch ($application->status) {
                    case 'approved':
                        Mail::to($application->user->email)->send(new ApprovedLetterMail($application));
                        break;

                    case 'declined':
                        Mail::to($application->user->email)->send(new DeclinedLetterMail($application));
                        $application->is_archived = true;
                        $application->save();
                        break;

                    case 'interviewed': // Pass
                        Mail::to($application->user->email)->send(new PassInterviewMail($application));
                        break;

                    case 'fail_interview': // Fail
                        Mail::to($application->user->email)->send(new FailInterviewMail($application));
                        $application->is_archived = true;
                        $application->save();
                        break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $applications->count() . ' applications updated successfully.',
            'status' => $validated['status'],
            'ids' => $applications->pluck('id'),
        ]);
    }
}
