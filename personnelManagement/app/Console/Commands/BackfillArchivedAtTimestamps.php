<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;

class BackfillArchivedAtTimestamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:backfill-timestamps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill archived_at timestamps for existing archived applications using updated_at';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Backfilling archived_at timestamps for existing archived applications...');

        // Get all archived applications without archived_at timestamp
        $applications = Application::where('is_archived', true)
            ->whereNull('archived_at')
            ->get();

        if ($applications->isEmpty()) {
            $this->info('No archived applications need backfilling.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($applications as $application) {
            // Use updated_at as the archived_at timestamp (best approximation we have)
            $application->archived_at = $application->updated_at;
            $application->save();
            $count++;
        }

        $this->info("Successfully backfilled {$count} archived application(s).");

        return Command::SUCCESS;
    }
}
