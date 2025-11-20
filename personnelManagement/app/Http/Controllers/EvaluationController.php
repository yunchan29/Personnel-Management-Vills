<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\TrainingEvaluation;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Mail\PassedEvaluationMail;
use App\Mail\FailedEvaluationMail;
use App\Enums\ApplicationStatus;

class EvaluationController extends Controller
{
    /**
     * Submit evaluation for an applicant
     */
    public function store(Request $request, $applicationId)
{
    // ✅ Validate form inputs
    $validated = $request->validate([
        'knowledge_score' => 'required|integer|min:0|max:30',
        'skill_score' => 'required|integer|min:0|max:30',
        'participation_score' => 'required|integer|min:0|max:20',
        'professionalism_score' => 'required|integer|min:0|max:20',
        'result' => 'required|in:Passed,Failed',
    ]);

    $application = Application::with('user', 'job')->findOrFail($applicationId);

    $totalScore = $validated['knowledge_score']
                + $validated['skill_score']
                + $validated['participation_score']
                + $validated['professionalism_score'];

    // Validate total score is within expected range (0-100)
    if ($totalScore < 0 || $totalScore > 100) {
        return back()->withErrors(['scores' => 'Total score must be between 0 and 100. Current total: ' . $totalScore]);
    }

    $user = $application->user;
    $job = $application->job;

    DB::transaction(function () use ($application, $validated, $totalScore, $user, $job) {
        // ✅ Create or update evaluation
        TrainingEvaluation::updateOrCreate(
            ['application_id' => $application->id],
            [
                'knowledge'       => $validated['knowledge_score'],
                'skill'           => $validated['skill_score'],
                'participation'   => $validated['participation_score'],
                'professionalism' => $validated['professionalism_score'],
                'total_score'     => $totalScore,
                'result'          => $validated['result'],
                'evaluated_by'    => auth()->id(),
                'evaluated_at'    => now(),
            ]
        );

        if ($validated['result'] === 'Passed') {
            // ✅ Update application status
            $application->setStatus(ApplicationStatus::PASSED_EVALUATION);
            $application->save();

        } else {
            // 1️⃣ Update status to failed (observer handles auto-archiving)
            $application->setStatus(ApplicationStatus::FAILED_EVALUATION);
            $application->save();
        }
    });

    // ✅ Send email *after* transaction finishes
    if ($validated['result'] === 'Passed') {
        Mail::to($user->email)->send(
            new PassedEvaluationMail(
                $user,
                $application,
                $validated['knowledge_score'],
                $validated['skill_score'],
                $validated['participation_score'],
                $validated['professionalism_score'],
                $totalScore
            )
        );
        return redirect()->back()->with('success', 'Evaluation submitted successfully.');
    } else {
        Mail::to($user->email)->send(
            new FailedEvaluationMail(
                $user,
                $application,
                $validated['knowledge_score'],
                $validated['skill_score'],
                $validated['participation_score'],
                $validated['professionalism_score'],
                $totalScore
            )
        );
        return redirect()->back()->with('failed', 'This applicant failed and has been moved to archive.');
    }
}

    /**
     * Promote an applicant to employee manually via "Add" button
     */
    public function promoteApplicant(Request $request, $applicationId)
    {
        try {
            $application = Application::with('user', 'job')->findOrFail($applicationId);
            $user = $application->user;
            $job = $application->job;

            // Strict vacancy enforcement - block promotion if no vacancies available
            if ($job->vacancies <= 0) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot promote: No vacancies available for this job position.'
                    ], 422);
                }
                return redirect()->back()->withErrors(['promotion' => 'Cannot promote: No vacancies available for this job position.']);
            }

            // Execute promotion in a transaction
            $result = DB::transaction(function () use ($application, $user, $job) {
                // Only promote if not already an employee
                if ($user->role !== 'employee') {
                    $user->role = 'employee';
                    $user->job_id = $application->job_id;
                    $user->save();
                }

                // Mark application as hired
                $application->setStatus(ApplicationStatus::HIRED);
                $application->save();

                // Decrement vacancy (atomic operation to prevent race conditions)
                $job->decrement('vacancies');
                $job->refresh();

                // Mark job as filled if no vacancies remain
                if ($job->vacancies <= 0) {
                    $job->status = 'filled';
                    $job->save();
                }

                // Get remaining active applicants for the same job
                $remainingApplicants = $job->getActiveApplications()
                    ->where('id', '!=', $application->id)
                    ->get();

                return [
                    'remaining_vacancies' => $job->vacancies,
                    'remaining_applicants_count' => $remainingApplicants->count(),
                    'remaining_applicants' => $remainingApplicants,
                    'job_filled' => $job->status === 'filled'
                ];
            });

            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$user->full_name} has been promoted to employee.",
                    'vacancy_info' => $result
                ], 200);
            }

            return redirect()->back()->with('success', "{$user->full_name} has been promoted to employee.");
        } catch (\Exception $e) {
            \Log::error('Error promoting applicant: ' . $e->getMessage(), [
                'application_id' => $applicationId,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to promote applicant: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['promotion' => 'Failed to promote applicant: ' . $e->getMessage()]);
        }
    }

    /**
     * Check vacancy status for a job before promotion/invitation
     */
    public function checkVacancy(Request $request, $id)
    {
        try {
            \Log::info('checkVacancy START', [
                'application_id' => $id,
                'selected_count' => $request->input('selected_count')
            ]);

            $application = Application::with('job')->findOrFail($id);
            \Log::info('checkVacancy: Application found', ['app_id' => $application->id]);

            $job = $application->job;

            if (!$job) {
                \Log::error('checkVacancy: Job not found for application', ['app_id' => $application->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found for this application'
                ], 404);
            }

            \Log::info('checkVacancy: Job found', ['job_id' => $job->id]);

            // Get the number of selected applicants from the request (for bulk promotion/invitation)
            $selectedCount = $request->input('selected_count', 1);
            \Log::info('checkVacancy: Selected count', ['count' => $selectedCount]);

            // Count applicants who already have invitations for this job
            // (contract_signing_schedule is not null = invitation sent)
            $applicantsWithInvitations = $job->applications()
                ->whereNotNull('contract_signing_schedule')
                ->where('is_archived', false)
                ->count();
            \Log::info('checkVacancy: Applicants with invitations', ['count' => $applicantsWithInvitations]);

            // Calculate ACTUAL available vacancies
            // = Total vacancies - Already invited applicants
            $actualAvailableVacancies = $job->vacancies - $applicantsWithInvitations;
            $actualAvailableVacancies = max(0, $actualAvailableVacancies); // Never negative
            \Log::info('checkVacancy: Available vacancies', ['count' => $actualAvailableVacancies]);

            // Check if selected count exceeds actual available slots
            $exceeds = $selectedCount > $actualAvailableVacancies;

            // Get remaining active applicants (excluding current application)
            \Log::info('checkVacancy: About to get active applications');
            $remainingApplicants = $job->getActiveApplications()
                ->where('id', '!=', $application->id)
                ->with('user')
                ->get();
            \Log::info('checkVacancy: Got remaining applicants', ['count' => $remainingApplicants->count()]);

            \Log::info('checkVacancy: Building response');
            $response = [
                'success' => true,
                'job_title' => $job->job_title,
                'total_vacancies' => $job->vacancies,
                'invited_count' => $applicantsWithInvitations,
                'remaining_vacancies' => $actualAvailableVacancies,
                'has_vacancies' => $actualAvailableVacancies > 0,
                'remaining_applicants_count' => $remainingApplicants->count(),
                'remaining_applicants' => $remainingApplicants->map(function($app) {
                    return [
                        'id' => $app->id,
                        'name' => $app->user ? $app->user->full_name : 'Unknown',
                        'status' => $app->status ? $app->status->label() : 'N/A'
                    ];
                }),
                'is_last_vacancy' => $actualAvailableVacancies === 1,
                'selected_count' => $selectedCount,
                'exceeds_vacancies' => $exceeds,
                'shortage' => $exceeds ? ($selectedCount - $actualAvailableVacancies) : 0
            ];

            \Log::info('checkVacancy: Returning response', ['response' => $response]);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            \Log::error('Error in checkVacancy', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check vacancy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle remaining applicants when a position is filled
     */
    public function handleRemainingApplicants(Request $request, $applicationId)
    {
        $validated = $request->validate([
            'action' => 'required|in:reject_all,keep_pending'
        ]);

        $application = Application::with('job')->findOrFail($applicationId);
        $job = $application->job;

        if ($validated['action'] === 'reject_all') {
            // Get active applicants before rejecting them
            $activeApplicants = $job->getActiveApplications()
                ->where('id', '!=', $application->id)
                ->with('user')
                ->get();

            // Reject all remaining active applicants
            $rejectedCount = $job->rejectRemainingApplicants('Position has been filled');

            // Send emails to rejected applicants
            foreach ($activeApplicants as $rejectedApp) {
                try {
                    Mail::to($rejectedApp->user->email)->send(
                        new \App\Mail\PositionFilledMail($rejectedApp->user, $job)
                    );
                } catch (\Exception $e) {
                    // Log email failure but don't stop the process
                    \Log::error("Failed to send position filled email to {$rejectedApp->user->email}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$rejectedCount} applicant(s) have been auto-rejected.",
                'rejected_count' => $rejectedCount
            ], 200);
        }

        // If action is 'keep_pending', do nothing
        return response()->json([
            'success' => true,
            'message' => 'Remaining applicants will be kept pending.',
            'rejected_count' => 0
        ], 200);
    }
}
