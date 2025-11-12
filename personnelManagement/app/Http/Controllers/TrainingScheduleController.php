<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingSchedule;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrainingScheduleSetMail;
use App\Mail\TrainingScheduleRescheduledMail;
use Carbon\Carbon;
use App\Enums\ApplicationStatus;

class TrainingScheduleController extends Controller
{
    public function setTrainingDate(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time'   => ['required', 'date_format:H:i:s', function ($attribute, $value, $fail) use ($request) {
                // If start and end dates are the same, validate that end_time > start_time
                if ($request->start_date === $request->end_date) {
                    $startTime = \Carbon\Carbon::parse($request->start_time);
                    $endTime = \Carbon\Carbon::parse($value);

                    if ($endTime->lte($startTime)) {
                        $fail('The end time must be after the start time when training occurs on the same day.');
                    }
                }
            }],
            'location'   => 'required|string|max:255',
        ]);

        $application = Application::with(['user', 'trainingSchedule'])->findOrFail($id);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $data = [
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'start_time'   => $request->start_time, // 24h format
            'end_time'     => $request->end_time,
            'location'     => $request->location,
            'scheduled_by' => auth()->id(),
        ];

        $existingSchedule = $application->trainingSchedule;

        if ($existingSchedule) {
            // ✅ Check if anything actually changed
            $isChanged = (
                $existingSchedule->start_date->format('Y-m-d') !== $startDate->format('Y-m-d') ||
                $existingSchedule->end_date->format('Y-m-d')   !== $endDate->format('Y-m-d') ||
                $existingSchedule->start_time                   !== $request->start_time ||
                $existingSchedule->end_time                     !== $request->end_time ||
                $existingSchedule->location                     !== $request->location
            );

            $emailSent = true;
            if ($isChanged) {
                $existingSchedule->update(array_merge($data, ['status' => 'rescheduled']));
                $existingSchedule->load('application.user', 'application.job');

                try {
                    Mail::to($application->user->email)
                        ->send(new TrainingScheduleRescheduledMail($existingSchedule));
                } catch (\Exception $e) {
                    \Log::error('Failed to send training reschedule email: ' . $e->getMessage());
                    $emailSent = false;
                }
            }
            // else nothing changed, no email
        } else {
            $newSchedule = TrainingSchedule::create(array_merge($data, [
                'application_id' => $application->id,
                'user_id'        => $application->user_id,
                'remarks'        => null,
                'status'         => 'scheduled'
            ]));

            $newSchedule->load('application.user', 'application.job');

            $emailSent = true;
            try {
                Mail::to($application->user->email)
                    ->send(new TrainingScheduleSetMail($newSchedule));
            } catch (\Exception $e) {
                \Log::error('Failed to send training schedule email: ' . $e->getMessage());
                $emailSent = false;
            }
        }

        $application->setStatus(ApplicationStatus::SCHEDULED_FOR_TRAINING);
        $application->save();

        return response()->json([
            'success' => true,
            'message' => $emailSent
                ? 'Training schedule set successfully.'
                : 'Training schedule saved. Note: Email notification could not be sent.',
            'email_sent' => $emailSent
        ]);
    }

    public function bulkSetTraining(Request $request)
    {
        $request->validate([
            'applicants' => 'required|array|min:1',
            'applicants.*.application_id' => 'required|exists:applications,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time'   => 'required|date_format:H:i:s',
            'location'   => 'required|string|max:255',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        foreach ($request->applicants as $applicantData) {
            $application = Application::with(['user', 'trainingSchedule'])
                ->findOrFail($applicantData['application_id']);

            $data = [
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time,
                'location'     => $request->location,
                'scheduled_by' => auth()->id(),
            ];

            $existingSchedule = $application->trainingSchedule;

            if ($existingSchedule) {
                // Check if anything actually changed
                $isChanged = (
                    $existingSchedule->start_date->format('Y-m-d') !== $startDate->format('Y-m-d') ||
                    $existingSchedule->end_date->format('Y-m-d')   !== $endDate->format('Y-m-d')   ||
                    $existingSchedule->start_time                   !== $request->start_time        ||
                    $existingSchedule->end_time                     !== $request->end_time          ||
                    $existingSchedule->location                     !== $request->location
                );

                if ($isChanged) {
                    $existingSchedule->update(array_merge($data, ['status' => 'rescheduled']));
                    $existingSchedule->load('application.user', 'application.job');

                    Mail::to($application->user->email)->send(
                        new TrainingScheduleRescheduledMail($existingSchedule)
                    );
                }
                // else do nothing, no email sent
            } else {
                $newSchedule = TrainingSchedule::create(array_merge($data, [
                    'application_id' => $application->id,
                    'user_id'        => $application->user_id,
                    'remarks'        => null,
                    'status'         => 'scheduled'
                ]));

                $newSchedule->load('application.user', 'application.job');

                Mail::to($application->user->email)->send(
                    new TrainingScheduleSetMail($newSchedule)
                );
            }

            // update status
            $application->setStatus(ApplicationStatus::SCHEDULED_FOR_TRAINING);
            $application->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk training schedules set successfully.'
        ]);
    }

}