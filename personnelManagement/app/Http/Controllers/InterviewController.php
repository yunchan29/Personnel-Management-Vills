<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Mail\InterviewScheduleMail;
use App\Mail\InterviewRescheduleMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class InterviewController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'application_id' => 'required|exists:applications,id',
                'user_id'        => 'required|exists:users,id',
                'scheduled_at'   => 'required|date',
            ]);

            $interview = Interview::where('application_id', $validated['application_id'])
                ->where('user_id', $validated['user_id'])
                ->first();

            $isReschedule = false;
            $sendMail = false;

            $newScheduled = Carbon::parse($validated['scheduled_at'])->format('Y-m-d H:i');

            if ($interview) {
                // Compare without seconds
                $currentScheduled = optional($interview->scheduled_at)->format('Y-m-d H:i');

                if ($currentScheduled !== $newScheduled) {
                    $isReschedule = true;

                    $interview->update([
                        'scheduled_at'   => $validated['scheduled_at'],
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
                    'scheduled_at'   => $validated['scheduled_at'],
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

            // Eager load relations
            $interview->load(['application.job', 'applicant']);

            // 🔹 Send email only if schedule changed or new
            if ($sendMail) {
                $mailClass = $isReschedule ? InterviewRescheduleMail::class : InterviewScheduleMail::class;
                Mail::to(optional($interview->applicant)->email)->send(new $mailClass($interview));
            }

            return response()->json(['message' => 'Interview set successfully.']);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function bulkStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
                'applicants'   => 'required|array',
                'applicants.*.application_id' => 'required|integer|exists:applications,id',
                'applicants.*.user_id'        => 'required|integer|exists:users,id',
            ]);

            $scheduledAt = $validated['scheduled_at'];
            $scheduledBy = auth()->id();

            foreach ($validated['applicants'] as $applicant) {
                $interview = Interview::where('application_id', $applicant['application_id'])
                    ->where('user_id', $applicant['user_id'])
                    ->first();

                $isReschedule = false;
                $sendMail = false;

                if ($interview) {
                    // Compare without seconds
                    $currentScheduled = optional($interview->scheduled_at)->format('Y-m-d H:i');
                    $newScheduled = \Carbon\Carbon::parse($scheduledAt)->format('Y-m-d H:i');

                    if ($currentScheduled !== $newScheduled) {
                        $isReschedule = true;

                        $interview->update([
                            'scheduled_at'   => $scheduledAt,
                            'rescheduled_at' => now(),
                            'scheduled_by'   => $scheduledBy,
                            'status'         => 'rescheduled',
                        ]);

                        $sendMail = true;
                    }
                } else {
                    $interview = Interview::create([
                        'application_id' => $applicant['application_id'],
                        'user_id'        => $applicant['user_id'],
                        'scheduled_at'   => $scheduledAt,
                        'scheduled_by'   => $scheduledBy,
                        'status'         => 'scheduled',
                    ]);

                    $sendMail = true;
                }

                // Update application status
                $application = Application::find($applicant['application_id']);
                if ($application) {
                    $application->status = 'for_interview';
                    $application->save();
                }

                // Eager load relations
                $interview->load(['application.job', 'applicant']);

                // Send email only when needed
                if ($sendMail) {
                    if ($isReschedule) {
                        \Mail::to(optional($interview->applicant)->email)
                            ->send(new \App\Mail\InterviewRescheduleMail($interview));
                    } else {
                        \Mail::to(optional($interview->applicant)->email)
                            ->send(new \App\Mail\InterviewScheduleMail($interview));
                    }
                }
            }

            return response()->json(['success' => true], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkReschedule(Request $request)
    {
        try {
            $validated = $request->validate([
                'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
                'applicants'   => 'required|array',
                'applicants.*.application_id' => 'required|integer|exists:applications,id',
                'applicants.*.user_id'        => 'required|integer|exists:users,id',
            ]);

            $scheduledAt = $validated['scheduled_at'];
            $scheduledBy = auth()->id();

            foreach ($validated['applicants'] as $applicant) {
                $interview = Interview::where('application_id', $applicant['application_id'])
                    ->where('user_id', $applicant['user_id'])
                    ->first();

                if ($interview) {
                    // 🔹 Only update if the scheduled_at is different
                    if ($interview->scheduled_at != $scheduledAt) {
                        $interview->update([
                            'scheduled_at'   => $scheduledAt,
                            'rescheduled_at' => now(),
                            'scheduled_by'   => $scheduledBy,
                            'status'         => 'rescheduled',
                        ]);

                        // Eager load relations
                        $interview->load(['application.job', 'applicant']);

                        // Update application status just to be sure
                        $application = Application::find($applicant['application_id']);
                        if ($application) {
                            $application->status = 'for_interview';
                            $application->save();
                        }

                        // Send reschedule email
                        \Mail::to(optional($interview->applicant)->email)
                            ->send(new \App\Mail\InterviewRescheduleMail($interview));
                    }
                }
            }

            return response()->json(['success' => true], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
