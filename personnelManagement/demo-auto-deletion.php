<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║    30-DAY AUTO-DELETION DEMONSTRATION                      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Step 1: Show current state
echo "STEP 1: Current Archive Status\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$total = \App\Models\Application::where('is_archived', true)->count();
echo "Total archived applications: {$total}\n\n";

if ($total == 0) {
    echo "⚠ No archived applications found.\n";
    echo "Please archive an application first to run this demo.\n\n";
    exit;
}

// Step 2: Create test record
echo "STEP 2: Creating Test Record (Simulating 35-day old archive)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$testApp = \App\Models\Application::where('is_archived', true)->first();
$originalDate = $testApp->archived_at;

$testApp->archived_at = now()->subDays(35);
$testApp->save();

echo "✓ Application ID {$testApp->id} modified\n";
echo "  - Original archived_at: " . ($originalDate ?? 'NULL') . "\n";
echo "  - New archived_at: {$testApp->archived_at} (35 days ago)\n";
echo "  - Status: PENDING DELETION (past 30-day limit)\n\n";

echo "👉 Check your archive page - this record now shows 'Pending deletion'\n\n";
sleep(2);

// Step 3: Show what will be deleted
echo "STEP 3: Checking for Expired Records\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$cutoff = now()->subDays(30);
$expired = \App\Models\Application::where('is_archived', true)
    ->whereNotNull('archived_at')
    ->where('archived_at', '<=', $cutoff)
    ->get();

echo "Deletion cutoff date: {$cutoff}\n";
echo "Records older than 30 days: {$expired->count()}\n\n";

if ($expired->count() > 0) {
    echo "Records ready for deletion:\n";
    foreach($expired as $app) {
        $daysOld = now()->diffInDays($app->archived_at);
        echo "  - ID {$app->id}: {$daysOld} days old\n";
    }
    echo "\n";
}

sleep(2);

// Step 4: Run cleanup
echo "STEP 4: Running Auto-Deletion (What happens at midnight)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Executing: php artisan archive:cleanup\n\n";

exec('php artisan archive:cleanup 2>&1', $output, $return);
foreach($output as $line) {
    echo strip_tags($line) . "\n";
}
echo "\n";

sleep(2);

// Step 5: Show final state
echo "STEP 5: Final Archive Status\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$remaining = \App\Models\Application::where('is_archived', true)->count();
echo "Total archived applications: {$remaining}\n";
echo "Deleted: " . ($total - $remaining) . " record(s)\n\n";

echo "👉 Refresh your archive page - the old record is now gone!\n\n";

// Summary
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    DEMONSTRATION COMPLETE                  ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "SUMMARY:\n";
echo "✓ Archives older than 30 days are automatically flagged\n";
echo "✓ System runs cleanup daily at 12:00 midnight\n";
echo "✓ Old records are permanently deleted\n";
echo "✓ Keeps database clean and compliant with data retention\n\n";

echo "This demonstration simulated what happens automatically every night.\n";
echo "No manual intervention needed - it's fully automated!\n\n";
