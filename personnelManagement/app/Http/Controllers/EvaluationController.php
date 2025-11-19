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

            // Validate that the job has available vacancies
            if (!$job->hasAvailableVacancies()) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No vacancies available for this position.'
                    ], 400);
                }
                return redirect()->back()->withErrors(['vacancy' => 'No vacancies available for this position.']);
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
     * Check vacancy status for a job before promotion
     */
    public function checkVacancy(Request $request, $applicationId)
    {
        $application = Application::with('job')->findOrFail($applicationId);
        $job = $application->job;

        // Get remaining active applicants (excluding current application)
        $remainingApplicants = $job->getActiveApplications()
            ->where('id', '!=', $application->id)
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'job_title' => $job->job_title,
            'remaining_vacancies' => $job->getRemainingVacancies(),
            'has_vacancies' => $job->hasAvailableVacancies(),
            'remaining_applicants_count' => $remainingApplicants->count(),
            'remaining_applicants' => $remainingApplicants->map(function($app) {
                return [
                    'id' => $app->id,
                    'name' => $app->user->full_name,
                    'status' => $app->status->label()
                ];
            }),
            'is_last_vacancy' => $job->vacancies === 1
        ], 200);
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
