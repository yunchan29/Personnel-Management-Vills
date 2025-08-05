<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingEvaluation;
use App\Models\Application;
use App\Models\User;

class EvaluationController extends Controller
{
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

        $application = Application::with('user')->findOrFail($applicationId);

        $totalScore = $validated['knowledge_score']
                    + $validated['skill_score']
                    + $validated['participation_score']
                    + $validated['professionalism_score'];

        // ✅ Wrap everything in a transaction for safety
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

            // ✅ Update application status if passed
            if ($validated['result'] === 'passed') {
                $application->status = 'hired';
                $application->save();

                // ✅ Promote applicant to employee
                $user = $application->user;
                if ($user->role !== 'employee') {
                    $user->role = 'employee';
                    $user->job_id = $application->job_id;
                    $user->save();
                }
            }
        });

        return redirect()->back()->with('success', 'Evaluation submitted successfully.');
    }
}
