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
            'training_schedule' => 'required|string',
        ]);

        $application = Application::with(['user', 'trainingSchedule'])->findOrFail($id);

        // Parse the date range (MM/DD/YYYY - MM/DD/YYYY)
        [$start, $end] = explode(' - ', $request->training_schedule);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($start));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($end));

        $existingSchedule = $application->trainingSchedule;

        if ($existingSchedule) {
            // Reschedule
            $existingSchedule->update([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'scheduled_by' => auth()->id(),
                'scheduled_at' => now(),
                'status' => 'rescheduled',
            ]);

            Mail::to($application->user->email)->send(
                new TrainingScheduleRescheduledMail($existingSchedule)
            );
        } else {
            // Initial schedule
            $newSchedule = TrainingSchedule::create([
                'application_id' => $application->id,
                'user_id' => $application->user_id,
                'scheduled_by' => auth()->id(),
                'scheduled_at' => now(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remarks' => null,
                'status' => 'scheduled',
            ]);

            Mail::to($application->user->email)->send(
                new TrainingScheduleSetMail($newSchedule)
            );
        }

        // Update application status
        $application->status = 'scheduled_for_training';
        $application->save();

        return response()->json(['message' => 'Training schedule set successfully.']);
    }
}