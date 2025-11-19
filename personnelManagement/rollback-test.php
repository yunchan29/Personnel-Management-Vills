<?php

/**
 * Rollback Test Data Script
 *
 * This script will restore the database to the state before testing.
 * Run with: php artisan tinker rollback-test.php
 */

echo "=== ROLLBACK TEST DATA ===" . PHP_EOL . PHP_EOL;

// Load saved state
$rollbackFile = storage_path('app/test_rollback_data.json');

if (!file_exists($rollbackFile)) {
    echo "❌ Error: No rollback data found!" . PHP_EOL;
    echo "The rollback data file does not exist." . PHP_EOL;
    exit(1);
}

$data = json_decode(file_get_contents($rollbackFile), true);

echo "📂 Loading rollback data..." . PHP_EOL;
echo "-----------------------------------" . PHP_EOL . PHP_EOL;

// Rollback Application 11 (Ramon)
$app11 = App\Models\Application::find(11);
if ($app11) {
    $app11->status = $data['app_11_status'];
    $app11->contract_start = null;
    $app11->contract_end = null;
    $app11->save();

    $user = $app11->user;
    $user->role = $data['app_11_user_role'];
    $user->job_id = $data['app_11_user_job_id'];
    $user->save();

    echo "✅ Ramon Gonzales restored:" . PHP_EOL;
    echo "   - Status: " . $app11->status->label() . PHP_EOL;
    echo "   - User Role: " . $user->role . PHP_EOL;
    echo "   - Contracts cleared" . PHP_EOL . PHP_EOL;
}

// Rollback Isabel
$isabel = App\Models\Application::whereHas('user', fn($q) => $q->where('email', 'isabel.diaz7@applicant.com'))->first();
if ($isabel) {
    $isabel->status = $data['isabel_status'];
    $isabel->is_archived = $data['isabel_archived'];
    $isabel->save();

    echo "✅ Isabel Diaz restored:" . PHP_EOL;
    echo "   - Status: " . $isabel->status->label() . PHP_EOL;
    echo "   - Archived: " . ($isabel->is_archived ? 'Yes' : 'No') . PHP_EOL . PHP_EOL;
}

// Rollback Eduardo
$eduardo = App\Models\Application::whereHas('user', fn($q) => $q->where('email', 'eduardo.navarro10@applicant.com'))->first();
if ($eduardo) {
    $eduardo->status = $data['eduardo_status'];
    $eduardo->is_archived = $data['eduardo_archived'];
    $eduardo->save();

    echo "✅ Eduardo Navarro restored:" . PHP_EOL;
    echo "   - Status: " . $eduardo->status->label() . PHP_EOL;
    echo "   - Archived: " . ($eduardo->is_archived ? 'Yes' : 'No') . PHP_EOL . PHP_EOL;
}

// Rollback Job
$job = App\Models\Job::find($data['job_id']);
if ($job) {
    $job->vacancies = $data['job_vacancies'];
    $job->status = $data['job_status'];
    $job->save();

    echo "✅ Construction Worker Job restored:" . PHP_EOL;
    echo "   - Vacancies: " . $job->vacancies . PHP_EOL;
    echo "   - Status: " . $job->status . PHP_EOL . PHP_EOL;
}

echo "-----------------------------------" . PHP_EOL;
echo "🎉 ROLLBACK COMPLETE!" . PHP_EOL;
echo "All data has been restored to pre-test state." . PHP_EOL;
