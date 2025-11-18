<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\Auth;

class StaffArchiveController extends Controller
{
    public function index()
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can access archives.');
        }

        // Show all manually archived applications (staff side)
        $applications = Application::with(['user', 'job'])
            ->where('is_archived', true)
            ->get();

        return view('admins.shared.archive', compact('applications'));
    }

    public function show($id)
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can view archived application details.');
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

        // Determine archive reason
        $archiveReason = 'Manually Archived';
        if ($application->evaluation && $application->evaluation->result === 'failed') {
            $archiveReason = 'Failed Evaluation';
        }

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
            'archive_reason' => $archiveReason,
            'reviewed_at' => $application->reviewed_at,
            'contract_signing_schedule' => $application->contract_signing_schedule,
            'contract_start' => $application->contract_start,
            'contract_end' => $application->contract_end,
            'updated_at' => $application->updated_at,
            'created_at' => $application->created_at,
        ]);
    }

    public function store(Request $request, $id)
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can archive applications.');
        }

        $application = Application::findOrFail($id);

        // Use direct assignment since is_archived is guarded
        $application->is_archived = true;
        $application->save();

        // Check if it's an AJAX request
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Application archived successfully.'
            ], 200);
        }

        return redirect()->back()->with('success', 'Application archived successfully.');
    }

    public function restore(Request $request, $id)
    {
        \Log::info('Restore attempt', [
            'id' => $id,
            'user' => Auth::user()->email,
            'request_data' => $request->all()
        ]);

        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can restore applications.');
        }

        $application = Application::findOrFail($id);

        \Log::info('Application found', [
            'app_id' => $application->id,
            'current_status' => $application->status,
            'is_archived' => $application->is_archived
        ]);

        // Define restorable statuses
        $restorableStatuses = [
            ApplicationStatus::DECLINED,
            ApplicationStatus::FAILED_INTERVIEW,
            ApplicationStatus::FAILED_EVALUATION
        ];

        // Check if application can be restored
        if (!in_array($application->status, $restorableStatuses)) {
            \Log::warning('Restore failed: Status not restorable', [
                'status' => $application->status
            ]);
            return redirect()->route('hrStaff.archive.index')
                ->with('error', 'Only Declined, Failed Interview, and Failed Evaluation applications can be restored.');
        }

        $newStatus = null;
        $message = '';

        // Handle restoration based on current status
        if ($application->status === ApplicationStatus::DECLINED) {
            // DECLINED: Auto-restore to PENDING (no user input needed)
            $newStatus = 'pending';
            $message = 'Application restored and set to Pending status for reconsideration.';

            \Log::info('Restoring DECLINED application to PENDING', [
                'app_id' => $application->id
            ]);

        } elseif ($application->status === ApplicationStatus::FAILED_INTERVIEW) {
            // FAILED_INTERVIEW: User chooses between FOR_INTERVIEW or APPROVED
            $request->validate([
                'status' => 'required|in:for_interview,approved'
            ]);

            $newStatus = $request->input('status');
            $message = $newStatus === 'for_interview'
                ? 'Application restored and set to For Interview status.'
                : 'Application restored and set to Approved status.';

            \Log::info('Restoring FAILED_INTERVIEW application', [
                'app_id' => $application->id,
                'new_status' => $newStatus
            ]);

        } elseif ($application->status === ApplicationStatus::FAILED_EVALUATION) {
            // FAILED_EVALUATION: User chooses between FOR_EVALUATION or SCHEDULED_FOR_TRAINING
            $request->validate([
                'status' => 'required|in:for_evaluation,scheduled_for_training'
            ]);

            $newStatus = $request->input('status');

            \Log::info('Restoring FAILED_EVALUATION application', [
                'app_id' => $application->id,
                'new_status' => $newStatus
            ]);

            // Clean up old evaluation and training data based on restore scenario
            if ($newStatus === 'for_evaluation') {
                // Scenario 1: Re-evaluation only
                // Delete old failed evaluation, but keep training schedule (training was completed)
                if ($application->evaluation) {
                    \Log::info('Deleting old evaluation record for re-evaluation', [
                        'evaluation_id' => $application->evaluation->id
                    ]);
                    $application->evaluation->delete();
                }
            } elseif ($newStatus === 'scheduled_for_training') {
                // Scenario 2: Redo entire training
                // Delete both evaluation and training schedule (fresh start)
                if ($application->evaluation) {
                    \Log::info('Deleting old evaluation record for training redo', [
                        'evaluation_id' => $application->evaluation->id
                    ]);
                    $application->evaluation->delete();
                }
                if ($application->trainingSchedule) {
                    \Log::info('Deleting old training schedule for training redo', [
                        'training_schedule_id' => $application->trainingSchedule->id
                    ]);
                    $application->trainingSchedule->delete();
                }
            }

            $message = $newStatus === 'for_evaluation'
                ? 'Application restored and set to For Evaluation status.'
                : 'Application restored and set to Scheduled for Training status.';
        }

        // Update both archived status and application status
        // Use direct assignment instead of mass assignment since these fields are guarded
        $application->is_archived = false;
        $application->status = ApplicationStatus::from($newStatus);
        $application->save();

        // Refresh to get latest data from database
        $application->refresh();

        \Log::info('Application updated successfully', [
            'after_update_status' => $application->status,
            'after_update_is_archived' => $application->is_archived
        ]);

        return redirect()->route('hrStaff.archive.index')
            ->with('success', $message);
    }

    public function bulkRestore(Request $request)
    {
        // Verify user has HR Staff role
        if (Auth::user()->role !== 'hrStaff') {
            abort(403, 'Unauthorized. Only HR Staff can restore applications.');
        }

        $request->validate([
            'ids' => 'required|json'
        ]);

        $ids = json_decode($request->ids, true);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('hrStaff.archive.index')
                ->with('error', 'No items selected for restoration.');
        }

        // Get all applications that can be restored (all restorable statuses)
        $restorableStatuses = [
            ApplicationStatus::DECLINED,
            ApplicationStatus::FAILED_INTERVIEW,
            ApplicationStatus::FAILED_EVALUATION
        ];

        $applications = Application::whereIn('id', $ids)
            ->where('is_archived', true)
            ->whereIn('status', $restorableStatuses)
            ->get();

        if ($applications->isEmpty()) {
            return redirect()->route('hrStaff.archive.index')
                ->with('error', 'No valid applications found for restoration. Only Declined, Failed Interview, and Failed Evaluation applications can be restored.');
        }

        $restoredCount = 0;

        foreach ($applications as $application) {
            // Handle restoration based on status
            if ($application->status === ApplicationStatus::DECLINED) {
                // Auto-restore DECLINED to PENDING
                $application->is_archived = false;
                $application->status = ApplicationStatus::PENDING;
                $application->save();
                $restoredCount++;

            } elseif ($application->status === ApplicationStatus::FAILED_INTERVIEW) {
                // Auto-restore FAILED_INTERVIEW to FOR_INTERVIEW
                $application->is_archived = false;
                $application->status = ApplicationStatus::FOR_INTERVIEW;
                $application->save();
                $restoredCount++;

            } elseif ($application->status === ApplicationStatus::FAILED_EVALUATION) {
                // Delete old evaluation (for re-evaluation scenario)
                if ($application->evaluation) {
                    $application->evaluation->delete();
                }

                // Restore to FOR_EVALUATION status by default
                $application->is_archived = false;
                $application->status = ApplicationStatus::FOR_EVALUATION;
                $application->save();
                $restoredCount++;
            }
        }

        return redirect()->route('hrStaff.archive.index')
            ->with('success', "Successfully restored {$restoredCount} application(s).");
    }
}
