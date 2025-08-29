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
  public function index(Request $request)
{
    // Jobs query
    $jobsQuery = Job::withCount('applications');

    // 🔎 Search jobs by title
    if ($request->filled('search')) {
        $jobsQuery->where('job_title', 'like', '%' . $request->search . '%');
    }

    // 🏢 Filter by company name
    if ($request->filled('company_name')) {
        $jobsQuery->where('company_name', $request->company_name);
    }

    // 🔃 Sort logic
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

    // Applications (always latest, but filtered if company_name is selected)
    $applicationsQuery = Application::with('user', 'job')->latest();

    if ($request->filled('company_name')) {
        $applicationsQuery->whereHas('job', function ($q) use ($request) {
            $q->where('company_name', $request->company_name);
        });
    }

    $applications = $applicationsQuery->get();

    // 📌 Distinct companies for filter dropdown
    $companies = Job::select('company_name')->distinct()->pluck('company_name');

    return view('hrAdmin.application', compact('jobs', 'applications', 'companies'));
}


   public function viewApplicants($jobId)
{
    $job = Job::findOrFail($jobId);
    $jobs = Job::withCount('applications')->get();

    $applications = Application::with(['user', 'job', 'interview', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->get();

    $approvedApplicants = Application::with(['user', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereIn('status', ['approved', 'for_interview'])
        ->get();

    $interviewApplicants = Application::with(['user', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->whereIn('status', ['interviewed', 'scheduled_for_training'])
        ->get();

    $forTrainingApplicants = Application::with(['user', 'job', 'trainingSchedule'])
        ->where('job_id', $jobId)
        ->where('status', 'for_evaluation')
        ->get();

    // ✅ Add this line
    $companies = Job::select('company_name')->distinct()->pluck('company_name');

    return view('hrAdmin.application', [
        'jobs' => $jobs,
        'applications' => $applications,
        'selectedJob' => $job,
        'selectedTab' => 'applicants',
        'approvedApplicants' => $approvedApplicants,
        'interviewApplicants' => $interviewApplicants,
        'forTrainingApplicants' => $forTrainingApplicants,
        'companies' => $companies, // ✅ Pass it to the view
    ]);
}


    // Approve or decline an application
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = Application::with(['user', 'job'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:approved,declined,interviewed,for_interview,trained,fail_interview'
        ]);

        $oldStatus = $application->status;
        $application->status = $validated['status'];
        $application->save();

        if ($validated['status'] === 'interviewed') {
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
                    break;
                case 'interviewed':
                    Mail::to($application->user->email)->send(new PassInterviewMail($application));
                    break;
                case 'fail_interview':
                    Mail::to($application->user->email)->send(new FailInterviewMail($application));
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
