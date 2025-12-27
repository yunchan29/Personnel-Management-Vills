<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
   public function index()
{
    // Auto-delete archived applications older than 30 days
    Application::where('is_archived', true)
        ->where('updated_at', '<=', now()->subDays(30))
        ->delete();

    // Get remaining archived applications
    $applications = Application::with(['user', 'job'])
        ->where('is_archived', true)
        ->get();

    return view('admins.shared.archive', compact('applications'));
}


    public function show($id)
    {
        // Authorization: Only HR Admin can view archived application details
        if (auth()->user()->role !== 'hrAdmin') {
            abort(403, 'Unauthorized. Only HR administrators can view archived application details.');
        }

        $application = Application::with([
                'user',
                'job',
                'interview.scheduledBy',
                'trainingSchedule.scheduler',
                'evaluation.evaluator'
            ])
            ->where('id', $id)
            ->where('is_archived', true)
            ->firstOrFail();

        return response()->json([
            'id' => $application->id,
            'status' => $application->status,
            'user' => [
                'first_name' => $application->user->first_name ?? '',
                'last_name' => $application->user->last_name ?? '',
                'name' => trim(($application->user->first_name ?? '') . ' ' . ($application->user->last_name ?? '')),
                'email' => $application->user->email,
                'profile_picture' => $application->user->profile_picture,
            ],
            'job' => [
                'title' => $application->job->job_title ?? 'N/A',
                'company' => $application->job->company_name ?? 'N/A',
            ],
            'interview' => $application->interview ? [
                'scheduled_at' => $application->interview->scheduled_at,
                'rescheduled_at' => $application->interview->rescheduled_at,
                'status' => $application->interview->status,
                'scheduled_by' => $application->interview->scheduledBy ?
                    trim(($application->interview->scheduledBy->first_name ?? '') . ' ' . ($application->interview->scheduledBy->last_name ?? '')) : null,
            ] : null,
            'training_schedule' => $application->trainingSchedule ? [
                'start_date' => $application->trainingSchedule->start_date,
                'end_date' => $application->trainingSchedule->end_date,
                'start_time' => $application->trainingSchedule->start_time,
                'end_time' => $application->trainingSchedule->end_time,
                'location' => $application->trainingSchedule->location,
                'status' => $application->trainingSchedule->status,
                'scheduled_at' => $application->trainingSchedule->scheduled_at,
                'scheduled_by' => $application->trainingSchedule->scheduler ?
                    trim(($application->trainingSchedule->scheduler->first_name ?? '') . ' ' . ($application->trainingSchedule->scheduler->last_name ?? '')) : null,
            ] : null,
            'evaluation' => $application->evaluation ? [
                'knowledge' => $application->evaluation->knowledge,
                'skill' => $application->evaluation->skill,
                'participation' => $application->evaluation->participation,
                'professionalism' => $application->evaluation->professionalism,
                'total_score' => $application->evaluation->total_score,
                'result' => $application->evaluation->result,
                'evaluated_by' => $application->evaluation->evaluator ?
                    trim(($application->evaluation->evaluator->first_name ?? '') . ' ' . ($application->evaluation->evaluator->last_name ?? '')) : null,
                'evaluated_at' => $application->evaluation->evaluated_at,
            ] : null,
            'reviewed_at' => $application->reviewed_at,
            'contract_signing_schedule' => $application->contract_signing_schedule,
            'contract_start' => $application->contract_start,
            'contract_end' => $application->contract_end,
            'updated_at' => $application->updated_at,
            'created_at' => $application->created_at,
        ]);
    }

    public function destroy($id)
    {
        // Authorization: Only HR Admin can permanently delete archived applications
        if (auth()->user()->role !== 'hrAdmin') {
            abort(403, 'Unauthorized. Only HR administrators can permanently delete applications.');
        }

        $application = Application::findOrFail($id);

        $application->delete(); // permanently delete application only

        return redirect()->route('hrAdmin.archive.index')
            ->with('success', 'Application permanently deleted.');
    
    }

}
