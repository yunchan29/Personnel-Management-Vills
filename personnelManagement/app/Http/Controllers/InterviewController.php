<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Mail\InterviewScheduleMail;
use App\Mail\InterviewRescheduleMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InterviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'user_id'        => 'required|exists:users,id',
            'start_time'     => 'required|date',
            'end_time'       => 'required|date|after:start_time',
        ]);

        $interview = Interview::where('application_id', $validated['application_id'])
            ->where('user_id', $validated['user_id'])
            ->first();

        $isReschedule = false;
        $sendMail = false;

        if ($interview) {
            // Compare formatted values without seconds
            $currentStart = optional($interview->start_time)->format('Y-m-d H:i');
            $currentEnd   = optional($interview->end_time)->format('Y-m-d H:i');

            $newStart = Carbon::parse($validated['start_time'])->format('Y-m-d H:i');
            $newEnd   = Carbon::parse($validated['end_time'])->format('Y-m-d H:i');

            if ($currentStart !== $newStart || $currentEnd !== $newEnd) {
                $isReschedule = true;

                $interview->update([
                    'start_time'     => $validated['start_time'],
                    'end_time'       => $validated['end_time'],
                    'rescheduled_at' => now(),
                    'scheduled_by'   => auth()->id(),
                    'status'         => 'rescheduled',
                ]);

                $sendMail = true;
            }
        } else {
            $interview = Interview::create([
                'application_id' => $validated['application_id'],
                'user_id'        => $validated['user_id'],
                'start_time'     => $validated['start_time'],
                'end_time'       => $validated['end_time'],
                'scheduled_by'   => auth()->id(),
                'status'         => 'scheduled',
            ]);

            $sendMail = true;
        }

        // Update application status
        $application = Application::find($validated['application_id']);
        if ($application) {
            $application->status = 'for_interview';
            $application->save();
        }

        // Load related models
        $interview->load(['application.job', 'applicant']);

        // Send email
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
