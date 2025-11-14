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
        'result' => 'required|in:passed,failed',
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

        if ($validated['result'] === 'passed') {
            // ✅ Update application status
            $application->setStatus(ApplicationStatus::PASSED_EVALUATION);
            $application->save();

            // 🔽 Decrement job vacancies (atomic operation to prevent race conditions)
            if ($job && $job->vacancies > 0) {
                // Use atomic decrement to prevent concurrent updates
                $job->decrement('vacancies');

                // Reload the model to get updated vacancy count
                $job->refresh();

                if ($job->vacancies <= 0) {
                    // Mark job as filled when all vacancies are taken
                    $job->status = 'filled';
                    $job->save();
                }
            }

        } else {
            // 1️⃣ Update status to failed (observer handles auto-archiving)
            $application->setStatus(ApplicationStatus::FAILED_EVALUATION);
            $application->save();
        }
    });

    // ✅ Send email *after* transaction finishes
    if ($validated['result'] === 'passed') {
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
        $application = Application::with('user', 'job')->findOrFail($applicationId);
        $user = $application->user;

        // Only promote if not already an employee
        if ($user->role !== 'employee') {
            $user->role = 'employee';
            $user->job_id = $application->job_id;
            $user->save();
        }

        // Mark application as hired
        $application->setStatus(ApplicationStatus::HIRED);
        $application->save();

        // Check if it's an AJAX request
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->full_name} has been promoted to employee."
            ], 200);
        }

        return redirect()->back()->with('success', "{$user->full_name} has been promoted to employee.");
    }
}
