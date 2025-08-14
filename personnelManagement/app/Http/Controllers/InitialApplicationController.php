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

class InitialApplicationController extends Controller
{
    // View all jobs and all applications
    public function index()
    {
        $jobs = Job::withCount('applications')->get();
        $applications = Application::with('user', 'job')->latest()->get();

        return view('hrAdmin.application', compact('jobs', 'applications'));
    }

    // View applicants related to a specific job
    public function viewApplicants($jobId)
    {
        $job = Job::findOrFail($jobId);
        $jobs = Job::withCount('applications')->get();

        // All applicants for the job (all statuses)
        $applications = Application::with(['user', 'job', 'interview', 'trainingSchedule'])
            ->where('job_id', $jobId)
            ->get();

        // Applicants approved or for interview
        $approvedApplicants = Application::with(['user', 'job', 'trainingSchedule'])
            ->where('job_id', $jobId)
            ->whereIn('status', ['approved', 'for_interview'])
            ->get();

        // Applicants interviewed or scheduled for training
        $interviewApplicants = Application::with(['user', 'job', 'trainingSchedule'])
            ->where('job_id', $jobId)
            ->whereIn('status', ['interviewed', 'scheduled_for_training'])
            ->get();

        // Applicants pending evaluation
        $forTrainingApplicants = Application::with(['user', 'job', 'trainingSchedule'])
            ->where('job_id', $jobId)
            ->where('status', 'for_evaluation')
            ->get();

        return view('hrAdmin.application', [
            'jobs' => $jobs,
            'applications' => $applications,
            'selectedJob' => $job,
            'selectedTab' => 'applicants',

            // Passed to Blade
            'approvedApplicants' => $approvedApplicants,
            'interviewApplicants' => $interviewApplicants,
            'forTrainingApplicants' => $forTrainingApplicants,
        ]);
    }

    // Approve or decline an application
    public function updateApplicationStatus(Request $request, $id)
    {
        // Load related user and job for email sending
        $application = Application::with(['user', 'job'])->findOrFail($id);

        // ✅ Status validation
        $validated = $request->validate([
            'status' => 'required|string|in:approved,declined,interviewed,for_interview,trained,fail_interview'
        ]);

        $oldStatus = $application->status;
        $application->status = $validated['status'];
        $application->save();

        // ✅ Restore interview completion logic
        if ($validated['status'] === 'interviewed') {
            $updated = DB::table('interviews')
                ->where('application_id', $application->id)
                ->update(['status' => 'completed']);

            if (!$updated) {
                logger()->warning("Interview update failed for application_id: {$application->id}");
            }
        }

        // ✅ Send Emails based on status change
        if ($oldStatus !== $application->status) {
            switch ($application->status) {
                case 'approved':
                    Mail::to($application->user->email)
                        ->send(new ApprovedLetterMail($application));
                    break;

                case 'declined':
                    Mail::to($application->user->email)
                        ->send(new DeclinedLetterMail($application));
                    break;

                case 'interviewed':
                    Mail::to($application->user->email)
                        ->send(new PassInterviewMail($application)); // <-- new
                    break;

                case 'fail_interview':
                    Mail::to($application->user->email)
                        ->send(new FailInterviewMail($application)); // <-- new
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


}
