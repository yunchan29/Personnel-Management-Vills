<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;

class DeleteExpiredArchivedApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete archived applications older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting archived applications cleanup...');

        // Delete archived applications older than 30 days
        $deletedCount = Application::where('is_archived', true)
            ->whereNotNull('archived_at')
            ->where('archived_at', '<=', now()->subDays(30))
            ->delete();

        $this->info("Deleted {$deletedCount} archived application(s) older than 30 days.");

        return Command::SUCCESS;
    }
}
