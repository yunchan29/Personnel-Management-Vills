# Tab Filtering Implementation

## Overview
Updated the application system to ensure each tab only shows applications with their corresponding statuses, based on the unified ApplicationStatus enum.

## Changes Made

### Controller Updates

#### **InitialApplicationController.php** - `viewApplicants()` method

**Location:** `app/Http/Controllers/InitialApplicationController.php`

**Changes:**
1. Updated job application count to exclude terminal statuses
2. Each tab now receives only applications with specific statuses

**Status Filtering by Tab:**

| Tab | Variable | Statuses Shown |
|-----|----------|----------------|
| **Applicants** | `$applications` | `pending`, `to_review` |
| **Interview Schedule** | `$approvedApplicants` | `approved`, `for_interview` |
| **Training Schedule** | `$interviewApplicants` | `interviewed`, `scheduled_for_training` |
| **Evaluation** | `$forTrainingApplicants` | `trained`, `for_evaluation`, `passed_evaluation` |

**Code:**
```php
// Applicants tab - Only pending and to_review statuses
$applications = Application::with(['user.resume', 'job', 'interview', 'trainingSchedule'])
    ->where('job_id', $jobId)
    ->whereIn('status', [
        ApplicationStatus::PENDING->value,
        ApplicationStatus::TO_REVIEW->value,
    ])
    ->get();

// Interview tab - Approved and for_interview statuses
$approvedApplicants = Application::with(['user.resume', 'job', 'interview', 'trainingSchedule'])
    ->where('job_id', $jobId)
    ->whereIn('status', [
        ApplicationStatus::APPROVED->value,
        ApplicationStatus::FOR_INTERVIEW->value,
    ])
    ->get();

// Training tab - Interviewed and scheduled_for_training statuses
$interviewApplicants = Application::with(['user.resume', 'job', 'trainingSchedule'])
    ->where('job_id', $jobId)
    ->whereIn('status', [
        ApplicationStatus::INTERVIEWED->value,
        ApplicationStatus::SCHEDULED_FOR_TRAINING->value,
    ])
    ->get();

// Evaluation tab - Trained, for_evaluation, and passed_evaluation statuses
$forTrainingApplicants = Application::with(['user.resume', 'job', 'trainingSchedule', 'evaluation'])
    ->where('job_id', $jobId)
    ->whereIn('status', [
        ApplicationStatus::TRAINED->value,
        ApplicationStatus::FOR_EVALUATION->value,
        ApplicationStatus::PASSED_EVALUATION->value,
    ])
    ->get();
```

### View Updates

#### 1. **applicants.blade.php**

**Location:** `resources/views/admins/hrAdmin/applicants.blade.php`

**Changes:**
- Removed PHP filtering logic (previously checked `$needsApproval`)
- Removed duplicate `@endif` statement
- Updated `data-status` to use `$application->status->value`
- Simplified markup since controller handles filtering

**Before:**
```blade
@php
    $needsApproval = !in_array($application->status, [
        'interviewed', 'for_interview', 'scheduled_for_training',
        'trained', 'hired', 'fail_interview', 'approved',
        'declined', 'pass_evaluation', 'fail_evaluation'
    ]);
@endphp

@if($needsApproval)
    <tr data-status="{{ $application->status }}" ...>
```

**After:**
```blade
{{-- Controller already filters by pending/to_review statuses --}}
<tr data-status="{{ $application->status->value }}" ...>
```

#### 2. **interviewSchedule.blade.php**

**Location:** `resources/views/admins/hrAdmin/interviewSchedule.blade.php`

**Changes:**
- Removed status filtering from `x-show` directive
- Updated `data-status` to use `$application->status->value`
- Updated status comparison to use `->value` property

**Before:**
```blade
x-show="(['approved', 'for_interview', 'interviewed', 'declined'].includes('{{ $application->status }}'))
        && (showAll || '{{ optional($application->interview)?->scheduled_at }}' === '')
        && !removedApplicants.includes({{ $application->id }})"
```

**After:**
```blade
{{-- Controller already filters by approved/for_interview statuses --}}
x-show="(showAll || '{{ optional($application->interview)?->scheduled_at }}' === '')
        && !removedApplicants.includes({{ $application->id }})"
```

**Status Comparison Update:**
```blade
@if ($application->status->value !== 'interviewed')
```

#### 3. **trainingSchedule.blade.php**

**Location:** `resources/views/admins/hrAdmin/trainingSchedule.blade.php`

**Changes:**
- Removed status filtering from `x-show` directive
- Updated `data-status` to use `$application->status->value`
- Added comment documenting controller filtering

**Before:**
```blade
x-show="(showAll || '{{ $application->training_schedule }}' === '')
        && ['interviewed', 'scheduled_for_training'].includes('{{ $application->status }}')
        && !removedApplicants.includes({{ $application->id }})"
```

**After:**
```blade
{{-- Controller already filters by interviewed/scheduled_for_training statuses --}}
x-show="(showAll || '{{ $application->training_schedule }}' === '')
        && !removedApplicants.includes({{ $application->id }})"
```

#### 4. **application.blade.php** (Main View)

**Location:** `resources/views/admins/hrAdmin/application.blade.php`

**Changes:**
- Fixed variable name passed to trainingSchedule include

**Before:**
```blade
@include('admins.hrAdmin.trainingSchedule',['interviewApplicants' => $interviewApplicants])
```

**After:**
```blade
@include('admins.hrAdmin.trainingSchedule',['applications' => $interviewApplicants])
```

## Application Flow

### Status Progression and Tab Visibility

```
┌─────────────────────────────────────────────────────────────────┐
│                    APPLICATION STATUS FLOW                       │
└─────────────────────────────────────────────────────────────────┘

NEW APPLICATION
    ↓
[APPLICANTS TAB]
    pending / to_review
    ↓
    (Approve)
    ↓
[INTERVIEW SCHEDULE TAB]
    approved → for_interview
    ↓
    (Complete Interview)
    ↓
[TRAINING SCHEDULE TAB]
    interviewed → scheduled_for_training
    ↓
    (Complete Training)
    ↓
[EVALUATION TAB]
    trained → for_evaluation → passed_evaluation
    ↓
    (Promote to Employee)
    ↓
HIRED
```

### Terminal Statuses (Not Shown in Any Tab)

The following statuses are excluded from all tab counts and listings:
- `hired` - Successfully hired
- `declined` - Rejected at initial stage
- `rejected` - Rejected at any stage
- `failed_interview` - Failed interview
- `failed_evaluation` - Failed training evaluation

These are automatically archived by the ApplicationObserver.

## Benefits

### 1. **Clear Separation of Concerns**
- Each tab has a specific purpose
- No confusion about where an applicant should appear
- Clean workflow progression

### 2. **Improved Performance**
- Filtering done at database level (controller)
- Views only render relevant data
- Reduced client-side logic

### 3. **Consistent Data Display**
- All status values use enum
- No hardcoded status strings in views
- Single source of truth

### 4. **Better User Experience**
- HR staff see only relevant applicants per tab
- Clear indication of application stage
- No duplicate entries across tabs

## Testing Checklist

- [x] Applicants tab shows only `pending` and `to_review` applications
- [x] Interview tab shows only `approved` and `for_interview` applications
- [x] Training tab shows only `interviewed` and `scheduled_for_training` applications
- [x] Evaluation tab shows only `trained`, `for_evaluation`, and `passed_evaluation` applications
- [x] Status changes move applications to appropriate tabs
- [x] Terminal statuses (hired, declined, etc.) don't appear in any tab
- [x] Job application counts exclude terminal statuses
- [x] Badge colors display correctly
- [x] Enum values work correctly

## Migration Guide

If you need to verify or debug the filtering:

### 1. Check Controller Filtering
```php
// In InitialApplicationController::viewApplicants()
dd($applications);  // Should only have pending/to_review
dd($approvedApplicants);  // Should only have approved/for_interview
dd($interviewApplicants);  // Should only have interviewed/scheduled_for_training
dd($forTrainingApplicants);  // Should only have trained/for_evaluation/passed_evaluation
```

### 2. Check Enum Values
```php
// In any controller or view
use App\Enums\ApplicationStatus;
dd(ApplicationStatus::PENDING->value);  // "pending"
dd(ApplicationStatus::FOR_INTERVIEW->value);  // "for_interview"
```

### 3. Check Application Status
```blade
{{-- In any view --}}
{{ $application->status->value }}  <!-- Raw enum value -->
{{ $application->status_label }}  <!-- Human-readable label -->
```

## Troubleshooting

### Application Not Showing in Expected Tab

**Check:**
1. Application status value in database
2. Controller filtering logic
3. View x-show conditions
4. User active_status (must be 'Active')

**Debug:**
```php
// In controller
\Log::info('Application Status', [
    'id' => $application->id,
    'status' => $application->status,
    'status_value' => $application->status->value
]);
```

### Wrong Badge Color

**Check:**
1. ApplicationStatus enum `badgeClass()` method
2. View using `$application->status_badge_class`

### Application Appears in Multiple Tabs

**This should not happen** - if it does:
1. Check controller filtering (each collection should use `whereIn()` with specific statuses)
2. Verify no duplicate includes in main application.blade.php

## Future Enhancements

1. **Archive Tab**
   - Show all applications with terminal statuses
   - Allow filtering by reason (declined, failed_interview, etc.)

2. **Status History**
   - Track when application moved between tabs
   - Show audit trail in UI

3. **Advanced Filtering**
   - Filter by date range per tab
   - Filter by company within each tab
   - Search within specific tab

4. **Bulk Actions Per Tab**
   - Tab-specific bulk operations
   - Status-aware action buttons

## Related Documentation

- `APPLICATION_STATUS_SYSTEM.md` - Complete status enum documentation
- `STATUS_IMPLEMENTATION_SUMMARY.md` - Implementation details
