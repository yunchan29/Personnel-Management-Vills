<?php

/**
 * Timezone Diagnostic Script
 * Run this to diagnose timezone issues with archived records
 *
 * Usage: php check-timezone-issue.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=========================================\n";
echo "Timezone Diagnostic Report\n";
echo "=========================================\n\n";

// Show current timezone configuration
echo "Configuration:\n";
echo "  Laravel Timezone: " . config('app.timezone') . "\n";
echo "  PHP Timezone: " . date_default_timezone_get() . "\n";
echo "  Server Time: " . date('Y-m-d H:i:s T') . "\n";
echo "  Laravel now(): " . now()->format('Y-m-d H:i:s T') . "\n";
echo "  Laravel now() UTC: " . now()->utc()->format('Y-m-d H:i:s T') . "\n\n";

try {
    // Check archived applications
    $archivedApps = \App\Models\Application::where('is_archived', true)
        ->orderBy('archived_at', 'desc')
        ->limit(10)
        ->get();

    if ($archivedApps->isEmpty()) {
        echo "No archived applications found.\n";
        exit(0);
    }

    echo "Archived Applications Analysis:\n";
    echo str_repeat("-", 120) . "\n";
    printf(
        "%-5s %-20s %-25s %-25s %-15s %-20s\n",
        "ID",
        "Archived At",
        "Updated At",
        "Deletion Date",
        "Days Left",
        "Status"
    );
    echo str_repeat("-", 120) . "\n";

    $futureCount = 0;
    $expiredCount = 0;
    $normalCount = 0;

    foreach ($archivedApps as $app) {
        // Use the same logic as the view
        $archivedDate = $app->archived_at
            ? \Carbon\Carbon::parse($app->archived_at)
            : \Carbon\Carbon::parse($app->updated_at);

        $deletionDate = $archivedDate->copy()->addDays(30);
        $remainingDays = (int) now()->diffInDays($deletionDate, false) + 1;

        // Determine status
        $status = '';
        if ($archivedDate->isFuture()) {
            $status = '⚠ FUTURE!';
            $futureCount++;
        } elseif ($remainingDays < 0) {
            $status = 'EXPIRED';
            $expiredCount++;
        } elseif ($remainingDays <= 5) {
            $status = 'Warning';
            $normalCount++;
        } else {
            $status = 'Normal';
            $normalCount++;
        }

        printf(
            "%-5s %-20s %-25s %-25s %-15s %-20s\n",
            $app->id,
            $archivedDate->format('Y-m-d H:i:s'),
            $app->updated_at->format('Y-m-d H:i:s'),
            $deletionDate->format('Y-m-d H:i:s'),
            $remainingDays . ' days',
            $status
        );

        // Show detailed info for future dates
        if ($archivedDate->isFuture()) {
            echo "     → This record has archived_at in the FUTURE (impossible!)\n";
            echo "     → Difference from now: " . $archivedDate->diffForHumans(now(), true) . " ahead\n";
        }
    }

    echo str_repeat("-", 120) . "\n\n";

    // Summary
    echo "Summary:\n";
    echo "  Total archived: " . $archivedApps->count() . "\n";
    echo "  Future dates (PROBLEM): $futureCount\n";
    echo "  Expired (>30 days): $expiredCount\n";
    echo "  Normal: $normalCount\n\n";

    if ($futureCount > 0) {
        echo "⚠ PROBLEM DETECTED!\n";
        echo "  You have $futureCount record(s) with archived_at in the FUTURE.\n";
        echo "  This is caused by a timezone mismatch between:\n";
        echo "    - Your database stored timestamps\n";
        echo "    - Laravel's configured timezone\n";
        echo "    - Server timezone\n\n";

        echo "  RECOMMENDED FIX:\n";
        echo "  1. Set Laravel timezone to match your location in config/app.php:\n";
        echo "     'timezone' => 'Asia/Manila',  // Or your actual timezone\n\n";

        echo "  2. Or keep UTC and fix the backfill by running:\n";
        echo "     php artisan archive:fix-timezone-dates\n\n";

        echo "  See: TIMEZONE_FIX_GUIDE.md for detailed instructions\n\n";
    }

    // Check database timezone
    echo "Database Information:\n";
    $dbTime = \DB::selectOne("SELECT NOW() as current_time, @@session.time_zone as session_tz, @@global.time_zone as global_tz");
    echo "  Database NOW(): " . $dbTime->current_time . "\n";
    echo "  Session Timezone: " . $dbTime->session_tz . "\n";
    echo "  Global Timezone: " . $dbTime->global_tz . "\n\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
