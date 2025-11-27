<?php

/**
 * Archive Status Diagnostic Script
 * Run this to check the state of archived records in production
 *
 * Usage: php check-archive-status.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "Archive Status Diagnostic Report\n";
echo "=========================================\n\n";

try {
    $archivedApps = \App\Models\Application::where('is_archived', true)->get();

    echo "Total Archived Applications: " . $archivedApps->count() . "\n\n";

    if ($archivedApps->isEmpty()) {
        echo "✓ No archived applications found.\n";
        exit(0);
    }

    $withArchiveAt = $archivedApps->whereNotNull('archived_at')->count();
    $withoutArchiveAt = $archivedApps->whereNull('archived_at')->count();

    echo "Breakdown:\n";
    echo "  - WITH archived_at set: $withArchiveAt\n";
    echo "  - WITHOUT archived_at (NULL): $withoutArchiveAt\n\n";

    if ($withoutArchiveAt > 0) {
        echo "⚠ WARNING: $withoutArchiveAt archived record(s) don't have archived_at timestamp!\n";
        echo "   This means the countdown timer won't work correctly for these records.\n\n";
        echo "   FIX: Run this command:\n";
        echo "   php artisan archive:backfill-timestamps\n\n";
    }

    // Check for records older than 30 days
    $expiredCount = \App\Models\Application::where('is_archived', true)
        ->whereNotNull('archived_at')
        ->where('archived_at', '<=', now()->subDays(30))
        ->count();

    if ($expiredCount > 0) {
        echo "⚠ Found $expiredCount expired archived record(s) (older than 30 days)\n";
        echo "   These should be deleted by the scheduled task.\n\n";
        echo "   Manual cleanup: php artisan archive:cleanup\n\n";
    }

    // Show sample records
    echo "Sample Archived Records:\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-5s %-25s %-20s %-25s %-15s\n", "ID", "User Email", "Archived At", "Updated At", "Days Left");
    echo str_repeat("-", 100) . "\n";

    foreach ($archivedApps->take(10) as $app) {
        $archivedDate = $app->archived_at
            ? \Carbon\Carbon::parse($app->archived_at)
            : \Carbon\Carbon::parse($app->updated_at);

        $deletionDate = $archivedDate->copy()->addDays(30);
        $remainingDays = (int) now()->diffInDays($deletionDate, false) + 1;

        $archivedAtDisplay = $app->archived_at
            ? $app->archived_at->format('Y-m-d H:i:s')
            : 'NULL (using updated_at)';

        $daysLeftDisplay = $remainingDays < 0 ? 'EXPIRED' : $remainingDays . ' days';

        printf(
            "%-5s %-25s %-20s %-25s %-15s\n",
            $app->id,
            substr($app->user->email ?? 'N/A', 0, 24),
            substr($archivedAtDisplay, 0, 19),
            $app->updated_at->format('Y-m-d H:i:s'),
            $daysLeftDisplay
        );
    }

    if ($archivedApps->count() > 10) {
        echo "\n... and " . ($archivedApps->count() - 10) . " more record(s)\n";
    }

    echo "\n";
    echo "=========================================\n";
    echo "Next Steps:\n";
    echo "=========================================\n\n";

    if ($withoutArchiveAt > 0) {
        echo "1. Run backfill command:\n";
        echo "   php artisan archive:backfill-timestamps\n\n";
    }

    if ($expiredCount > 0) {
        echo "2. Test manual cleanup:\n";
        echo "   php artisan archive:cleanup\n\n";
    }

    echo "3. Set up cron job for automatic cleanup:\n";
    echo "   See: ARCHIVE_30DAY_TESTING_GUIDE.md\n\n";

    echo "4. Verify scheduled tasks:\n";
    echo "   php artisan schedule:list\n\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
