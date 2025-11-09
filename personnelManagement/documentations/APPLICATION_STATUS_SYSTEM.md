# Application Status System Documentation

## Overview

This document describes the unified application status system implemented across the Personnel Management System. The system uses a centralized enum to ensure consistency and automatic synchronization across all related tables (applications, interviews, training schedules).

## Status Enum

All application statuses are defined in `App\Enums\ApplicationStatus` as an enum. This provides:
- Type safety
- Single source of truth
- Automatic IDE autocomplete
- Helper methods for status checking

## Application Status Flow

### 1. Initial Application Stage
**Statuses:** `pending`, `to_review`

- `pending` - New application submitted by applicant
- `to_review` - Application is under HR review

**View:** Applicants Tab (`applicants.blade.php`)
- Only shows applications that need approval/decline
- Applications with these statuses need HR action

### 2. Application Decision
**Statuses:** `approved`, `declined`

- `approved` - Application approved, ready for interview scheduling
- `declined` - Application rejected (auto-archived)

**Actions:**
- Approved applications move to Interview Tab
- Declined applications are automatically archived

### 3. Interview Stage
**Statuses:** `for_interview`, `interviewed`, `failed_interview`

- `for_interview` - Interview scheduled
- `interviewed` - Interview completed successfully
- `failed_interview` - Interview failed (auto-archived)

**View:** Interview Schedule Tab (`interviewSchedule.blade.php`)
- Shows approved applications and those scheduled for interview

**Automatic Synchronization:**
- When status changes to `for_interview`, interview record status becomes "scheduled"
- When status changes to `interviewed`, interview record status becomes "completed"
- When status changes to `failed_interview`, interview record status becomes "failed"

### 4. Training Stage
**Statuses:** `scheduled_for_training`, `in_training`, `trained`

- `scheduled_for_training` - Training schedule set
- `in_training` - Currently undergoing training
- `trained` - Training completed successfully

**View:** Training Schedule Tab (`trainingSchedule.blade.php`)
- Shows interviewed applications ready for training

**Automatic Synchronization:**
- When status changes to `scheduled_for_training`, training schedule status becomes "scheduled"
- When status changes to `trained`, training schedule status becomes "completed"

### 5. Evaluation Stage
**Statuses:** `for_evaluation`, `passed_evaluation`, `failed_evaluation`

- `for_evaluation` - Ready for performance evaluation
- `passed_evaluation` - Evaluation passed, ready for hiring
- `failed_evaluation` - Evaluation failed (auto-archived)

**View:** Performance Evaluation Tab (`perfEval.blade.php`)
- Shows trained applications ready for evaluation

### 6. Final Stage
**Statuses:** `hired`, `rejected`

- `hired` - Applicant promoted to employee
- `rejected` - Final rejection at any stage

## Status Badge Colors

Each status has an associated badge color class for UI consistency:

| Status | Color Class |
|--------|-------------|
| `pending`, `to_review` | Gray (bg-gray-100 text-gray-800) |
| `approved` | Green (bg-green-100 text-green-800) |
| `declined`, `rejected` | Red (bg-red-100 text-red-800) |
| `for_interview` | Yellow (bg-yellow-100 text-yellow-800) |
| `interviewed` | Blue (bg-blue-100 text-blue-800) |
| `failed_interview`, `failed_evaluation` | Red (bg-red-100 text-red-800) |
| `scheduled_for_training`, `in_training` | Blue (bg-blue-100 text-blue-800) |
| `trained`, `for_evaluation` | Purple (bg-purple-100 text-purple-800) |
| `passed_evaluation` | Green (bg-green-100 text-green-800) |
| `hired` | Green (bg-green-100 text-green-800) |

## Automatic Synchronization

The `ApplicationObserver` automatically handles status synchronization:

### Interview Table Sync
```php
// When application status changes to for_interview
interviews.status = 'scheduled'

// When application status changes to interviewed
interviews.status = 'completed'

// When application status changes to failed_interview
interviews.status = 'failed'
```

### Training Table Sync
```php
// When application status changes to scheduled_for_training
training_schedules.status = 'scheduled'

// When application status changes to trained
training_schedules.status = 'completed'
```

### Auto-Archiving
Applications are automatically archived when status changes to:
- `declined`
- `failed_interview`
- `failed_evaluation`
- `rejected`

## Usage in Controllers

### Setting Status
```php
use App\Enums\ApplicationStatus;

// Using enum directly
$application->setStatus(ApplicationStatus::APPROVED);
$application->save();

// Using string (automatically converted)
$application->setStatus('approved');
$application->save();
```

### Checking Status
```php
// Check if needs approval
if ($application->needsApproval()) {
    // Show in applicants tab
}

// Check if in interview stage
if ($application->isInInterview()) {
    // Show in interview tab
}

// Check if terminal/completed
if ($application->isTerminal()) {
    // Process complete
}

// Check if failed
if ($application->status->isFailed()) {
    // Handle failed application
}
```

### Getting Status Information
```php
// Get human-readable label
$label = $application->status_label;  // "Approved"

// Get badge CSS class
$badgeClass = $application->status_badge_class; // "bg-green-100 text-green-800"
```

## Usage in Views

### Display Status Badge
```blade
<span class="px-2 py-1 rounded-full text-xs font-medium {{ $application->status_badge_class }}">
    {{ $application->status_label }}
</span>
```

### Filter by Status
```blade
@php
    use App\Enums\ApplicationStatus;
@endphp

@foreach($applications as $application)
    @if($application->needsApproval())
        <!-- Show in applicants tab -->
    @endif
@endforeach
```

## Tab-Specific Filtering

### Applicants Tab
Shows only applications needing approval:
```php
$needsApproval = !in_array($application->status, [
    'interviewed',
    'for_interview',
    'scheduled_for_training',
    'trained',
    'hired',
    'fail_interview',
    'approved',
    'declined',
    'pass_evaluation',
    'fail_evaluation'
]);
```

### Interview Tab
Shows applications ready for/in interview:
```php
ApplicationStatus::interviewStatuses(); // ['approved', 'for_interview']
```

### Training Tab
Shows applications ready for/in training:
```php
ApplicationStatus::trainingStatuses(); // ['interviewed', 'scheduled_for_training']
```

### Evaluation Tab
Shows applications ready for/in evaluation:
```php
ApplicationStatus::evaluationStatuses(); // ['trained', 'for_evaluation']
```

## Email Notifications

Status changes trigger automatic email notifications:

| Status Change | Email Sent |
|---------------|------------|
| `pending` → `approved` | ApprovedLetterMail |
| `pending` → `declined` | DeclinedLetterMail |
| `for_interview` → `interviewed` | PassInterviewMail |
| `for_interview` → `failed_interview` | FailInterviewMail |
| `for_evaluation` → `passed_evaluation` | PassedEvaluationMail |
| `for_evaluation` → `failed_evaluation` | FailedEvaluationMail |

## Migration

The migration `2025_11_09_030309_update_applications_status_for_unified_enum.php` normalizes all existing status values to the new enum format:

**Old → New Mappings:**
- `Pending` → `pending`
- `To Review` → `to_review`
- `fail_interview` → `failed_interview`
- `passed` → `passed_evaluation`
- `failed` → `failed_evaluation`

## Best Practices

1. **Always use the enum** when setting statuses in code
2. **Use helper methods** (`needsApproval()`, `isInInterview()`, etc.) instead of direct comparisons
3. **Let the observer handle** synchronization - don't manually update interview/training statuses
4. **Use status_label** and **status_badge_class** attributes in views for consistency
5. **Never bypass the enum** - all status strings should be defined in the enum

## Troubleshooting

### Status not synchronizing
- Check that `ApplicationObserver` is registered in `AppServiceProvider`
- Ensure you're using `$application->save()` not `DB::update()`
- Verify the observer's `updated()` method is firing

### Incorrect status values in database
- Run the migration again: `php artisan migrate:refresh --path=/database/migrations/2025_11_09_030309_update_applications_status_for_unified_enum.php`
- Check for direct DB updates bypassing the model

### Status not showing in views
- Verify you're using `$application->status_label` not `$application->status`
- Check that the Application model cast is in place: `'status' => ApplicationStatus::class`

## Future Enhancements

Potential improvements to the status system:
1. Status history tracking (log all status changes)
2. Status transition validation (prevent invalid status changes)
3. Workflow automation (automatic progression through stages)
4. Custom status notifications per company
5. Status-based permissions (who can change which statuses)
