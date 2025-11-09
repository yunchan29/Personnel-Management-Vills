<?php

namespace App\Observers;

use App\Models\Application;
use App\Enums\ApplicationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationObserver
{
    /**
     * Handle the Application "created" event.
     */
    public function created(Application $application): void
    {
        // Set default status if not set
        if (!$application->status) {
            $application->status = ApplicationStatus::PENDING;
            $application->saveQuietly(); // Save without triggering events again
        }
    }

    /**
     * Handle the Application "updated" event.
     * Automatically sync status with related tables (interview, training)
     */
    public function updated(Application $application): void
    {
        // Only proceed if status was changed
        if (!$application->wasChanged('status')) {
            return;
        }

        $newStatus = $application->status;
        $oldStatus = $application->getOriginal('status');

        Log::info("Application {$application->id} status changed from {$oldStatus} to {$newStatus}");

        // Sync with Interview table
        if ($newStatus === ApplicationStatus::FOR_INTERVIEW) {
            // When setting to for_interview, ensure interview record exists
            if ($application->interview) {
                DB::table('interviews')
                    ->where('application_id', $application->id)
                    ->update(['status' => 'scheduled']);
            }
        } elseif ($newStatus === ApplicationStatus::INTERVIEWED) {
            // When marking as interviewed, update interview status to completed
            if ($application->interview) {
                DB::table('interviews')
                    ->where('application_id', $application->id)
                    ->update(['status' => 'completed']);
            }
        } elseif ($newStatus === ApplicationStatus::FAILED_INTERVIEW) {
            // Mark interview as failed
            if ($application->interview) {
                DB::table('interviews')
                    ->where('application_id', $application->id)
                    ->update(['status' => 'failed']);
            }
        }

        // Sync with Training table
        if ($newStatus === ApplicationStatus::SCHEDULED_FOR_TRAINING) {
            // Ensure training schedule exists and is in scheduled status
            if ($application->trainingSchedule) {
                DB::table('training_schedules')
                    ->where('application_id', $application->id)
                    ->update(['status' => 'scheduled']);
            }
        } elseif ($newStatus === ApplicationStatus::TRAINED) {
            // Mark training as completed
            if ($application->trainingSchedule) {
                DB::table('training_schedules')
                    ->where('application_id', $application->id)
                    ->update(['status' => 'completed']);
            }
        }

        // Auto-archive failed applications
        if (in_array($newStatus, [
            ApplicationStatus::DECLINED,
            ApplicationStatus::FAILED_INTERVIEW,
            ApplicationStatus::FAILED_EVALUATION,
            ApplicationStatus::REJECTED
        ])) {
            if (!$application->is_archived) {
                $application->is_archived = true;
                $application->saveQuietly(); // Prevent infinite loop
            }
        }
    }

    /**
     * Handle the Application "deleted" event.
     */
    public function deleted(Application $application): void
    {
        // Clean up related records if needed
        // This is optional based on cascade delete settings
    }

    /**
     * Handle the Application "restored" event.
     */
    public function restored(Application $application): void
    {
        // Unarchive if restored
        if ($application->is_archived) {
            $application->is_archived = false;
            $application->saveQuietly();
        }
    }

    /**
     * Handle the Application "force deleted" event.
     */
    public function forceDeleted(Application $application): void
    {
        //
    }
}
