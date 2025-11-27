<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use Carbon\Carbon;

class FixArchiveTimezoneDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:fix-timezone-dates {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix archived_at dates that are in the future due to timezone issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $this->info('Checking for archived applications with timezone issues...');

        // Find archived applications with future dates or null archived_at
        $applications = Application::where('is_archived', true)->get();

        $futureCount = 0;
        $nullCount = 0;
        $fixedCount = 0;

        foreach ($applications as $app) {
            $needsFix = false;
            $reason = '';

            // Check if archived_at is null
            if ($app->archived_at === null) {
                $needsFix = true;
                $reason = 'archived_at is NULL';
                $nullCount++;

                if (!$dryRun) {
                    $app->archived_at = $app->updated_at;
                    $app->save();
                    $fixedCount++;
                }

                $this->line("  ID {$app->id}: {$reason} → Set to updated_at ({$app->updated_at})");
            }
            // Check if archived_at is in the future
            elseif (Carbon::parse($app->archived_at)->isFuture()) {
                $needsFix = true;
                $reason = 'archived_at is in the FUTURE';
                $futureCount++;

                if (!$dryRun) {
                    // If updated_at is also in the future, use current time
                    if (Carbon::parse($app->updated_at)->isFuture()) {
                        $app->archived_at = now();
                        $this->line("  ID {$app->id}: Both dates in future → Set to now()");
                    } else {
                        $app->archived_at = $app->updated_at;
                        $this->line("  ID {$app->id}: {$reason} → Set to updated_at ({$app->updated_at})");
                    }
                    $app->save();
                    $fixedCount++;
                } else {
                    $this->line("  ID {$app->id}: {$reason} (would fix)");
                }
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->line("  Total archived applications: {$applications->count()}");
        $this->line("  Records with NULL archived_at: {$nullCount}");
        $this->line("  Records with FUTURE archived_at: {$futureCount}");

        if ($dryRun) {
            $this->warn("  Would fix: " . ($nullCount + $futureCount) . " record(s)");
            $this->newLine();
            $this->info('Run without --dry-run to apply fixes:');
            $this->comment('  php artisan archive:fix-timezone-dates');
        } else {
            $this->info("  Fixed: {$fixedCount} record(s)");
        }

        return Command::SUCCESS;
    }
}
