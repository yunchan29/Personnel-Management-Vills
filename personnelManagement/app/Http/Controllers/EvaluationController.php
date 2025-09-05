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

        DB::transaction(function () use ($application, $validated, $totalScore) {
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

            $user = $application->user;
            $job = $application->job;

            if ($validated['result'] === 'passed') {
                // ✅ Update application status to "passed" (manual promotion will set "hired")
                $application->status = 'passed';
                $application->save();

                // 🔽 Decrement job vacancies
                if ($job && $job->vacancies > 0) {
                    $job->vacancies -= 1;

                    // Optional: close job if no vacancies left
                    if ($job->vacancies <= 0) {
                        $job->status = 'closed';
                    }

                    $job->save();
                }

                // ✅ Send passed email
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
            } else {
                // ✅ If failed, keep status as is but send fail email
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
            }
        });

        return redirect()->back()->with('success', 'Evaluation submitted successfully.');
    }

    /**
     * Promote an applicant to employee manually via "Add" button
     */
    public function promoteApplicant($applicationId)
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
        $application->status = 'hired';
        $application->save();

        return redirect()->back()->with('success', "{$user->full_name} has been promoted to employee.");
    }
}
