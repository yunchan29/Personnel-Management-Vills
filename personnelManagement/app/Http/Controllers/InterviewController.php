<?php

namespace App\Http\Controllers;
use App\Models\Interview;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Mail\InterviewScheduleMail;
use App\Mail\InterviewRescheduleMail;
use Illuminate\Support\Facades\Mail;

class InterviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
        ]);

        $interview = Interview::where('application_id', $validated['application_id'])
            ->where('user_id', $validated['user_id'])
            ->first();

        $isReschedule = false;
        $sendMail = false;

        if ($interview) {
            // Format both dates to 'Y-m-d H:i' to compare without seconds differences
            $currentScheduled = optional($interview->scheduled_at)->format('Y-m-d H:i');
            $newScheduled = \Carbon\Carbon::parse($validated['scheduled_at'])->format('Y-m-d H:i');

            if ($currentScheduled !== $newScheduled) {
                $isReschedule = true;

                $interview->update([
                    'scheduled_at' => $validated['scheduled_at'],
                    'rescheduled_at' => now(), // ✅ This stores when the change happens
                    'scheduled_by' => auth()->id(),
                    'status' => 'rescheduled',
                ]);

                $sendMail = true; // ✅ Only send email if date changed
            }
        } else {
            $interview = Interview::create([
                'application_id' => $validated['application_id'],
                'user_id' => $validated['user_id'],
                'scheduled_at' => $validated['scheduled_at'],
                'scheduled_by' => auth()->id(),
                'status' => 'scheduled',
            ]);

            $sendMail = true; // ✅ First time set, send email
        }

        // ✅ Update application status to 'for_interview' if needed
        $application = Application::find($validated['application_id']);
        if ($application) {
            $application->status = 'for_interview';
            $application->save();
        }

        // Eager load relationships
        $interview->load(['application.job', 'applicant']);

        // ✅ Send email only when needed
        if ($sendMail) {
            if ($isReschedule) {
                Mail::to($interview->applicant->email)->send(new InterviewRescheduleMail($interview));
            } else {
                Mail::to($interview->applicant->email)->send(new InterviewScheduleMail($interview));
            }
        }

        return response()->json(['message' => 'Interview set successfully.']);
    }
}
