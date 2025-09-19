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

class TrainingScheduleController extends Controller
{
    public function setTrainingDate(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time'   => 'required|date_format:H:i:s',
            'location'   => 'required|string|max:255',
        ]);

        $application = Application::with(['user', 'trainingSchedule'])->findOrFail($id);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $data = [
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'start_time'   => $request->start_time, // already in 24h format
            'end_time'     => $request->end_time,
            'location'     => $request->location,
            'scheduled_by' => auth()->id(),
        ];

        $existingSchedule = $application->trainingSchedule;

        if ($existingSchedule) {
            $existingSchedule->update(array_merge($data, ['status' => 'rescheduled']));
            $existingSchedule->load('application.user', 'application.job');

            Mail::to($application->user->email)->send(
                new TrainingScheduleRescheduledMail($existingSchedule)
            );
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

        $application->status = 'scheduled_for_training';
        $application->save();

        return response()->json([
            'success' => true,
            'message' => 'Training schedule set successfully.'
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
                $existingSchedule->update(array_merge($data, ['status' => 'rescheduled']));
                $existingSchedule->load('application.user', 'application.job');

                Mail::to($application->user->email)->send(
                    new TrainingScheduleRescheduledMail($existingSchedule)
                );
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
            $application->status = 'scheduled_for_training';
            $application->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk training schedules set successfully.'
        ]);
    }

}