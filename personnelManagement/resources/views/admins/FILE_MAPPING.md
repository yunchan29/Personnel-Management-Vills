# File Mapping Reference

Quick reference guide showing where each original file is now located.

## Original hrAdmin Files → New Location

| Original Path | New Path | Type |
|--------------|----------|------|
| `hrAdmin/settings.blade.php` | `admins/shared/settings.blade.php` | **MERGED** |
| `hrAdmin/leaveForm.blade.php` | `admins/shared/leaveForm.blade.php` | **MERGED** |
| `hrAdmin/employees.blade.php` | `admins/shared/employees.blade.php` | **MERGED** |
| `hrAdmin/archive.blade.php` | `admins/shared/archive.blade.php` | **MERGED** |
| `hrAdmin/dashboard.blade.php` | `admins/hrAdmin/dashboard.blade.php` | **MOVED** |
| `hrAdmin/application.blade.php` | `admins/hrAdmin/application.blade.php` | **MOVED** |
| `hrAdmin/applicants.blade.php` | `admins/hrAdmin/applicants.blade.php` | **MOVED** |
| `hrAdmin/profile.blade.php` | `admins/hrAdmin/profile.blade.php` | **MOVED** |
| `hrAdmin/jobPosting.blade.php` | `admins/hrAdmin/jobPosting.blade.php` | **MOVED** |
| `hrAdmin/interviewSchedule.blade.php` | `admins/hrAdmin/interviewSchedule.blade.php` | **MOVED** |
| `hrAdmin/trainingSchedule.blade.php` | `admins/hrAdmin/trainingSchedule.blade.php` | **MOVED** |

---

## Original hrStaff Files → New Location

| Original Path | New Path | Type |
|--------------|----------|------|
| `hrStaff/settings.blade.php` | `admins/shared/settings.blade.php` | **MERGED** |
| `hrStaff/leaveForm.blade.php` | `admins/shared/leaveForm.blade.php` | **MERGED** |
| `hrStaff/employees.blade.php` | `admins/shared/employees.blade.php` | **MERGED** |
| `hrStaff/archive.blade.php` | `admins/shared/archive.blade.php` | **MERGED** |
| `hrStaff/dashboard.blade.php` | `admins/hrStaff/dashboard.blade.php` | **MOVED** |
| `hrStaff/perfEval.blade.php` | `admins/hrStaff/perfEval.blade.php` | **MOVED** |

---

## Controller Route Updates Required

### Shared Views (Both Roles Use Same File)

```php
// Settings
// OLD: return view('hrAdmin.settings');
// OLD: return view('hrStaff.settings');
// NEW: return view('admins.shared.settings');

// Leave Form
// OLD: return view('hrAdmin.leaveForm');
// OLD: return view('hrStaff.leaveForm');
// NEW: return view('admins.shared.leaveForm');

// Employees
// OLD: return view('hrAdmin.employees');
// OLD: return view('hrStaff.employees');
// NEW: return view('admins.shared.employees');

// Archive
// OLD: return view('hrAdmin.archive');
// OLD: return view('hrStaff.archive');
// NEW: return view('admins.shared.archive');
```

### HR Admin Specific Views

```php
// Dashboard
// OLD: return view('hrAdmin.dashboard');
// NEW: return view('admins.hrAdmin.dashboard');

// Application
// OLD: return view('hrAdmin.application');
// NEW: return view('admins.hrAdmin.application');

// Applicants
// OLD: return view('hrAdmin.applicants');
// NEW: return view('admins.hrAdmin.applicants');

// Profile
// OLD: return view('hrAdmin.profile');
// NEW: return view('admins.hrAdmin.profile');

// Job Posting
// OLD: return view('hrAdmin.jobPosting');
// NEW: return view('admins.hrAdmin.jobPosting');

// Interview Schedule
// OLD: return view('hrAdmin.interviewSchedule');
// NEW: return view('admins.hrAdmin.interviewSchedule');

// Training Schedule
// OLD: return view('hrAdmin.trainingSchedule');
// NEW: return view('admins.hrAdmin.trainingSchedule');
```

### HR Staff Specific Views

```php
// Dashboard
// OLD: return view('hrStaff.dashboard');
// NEW: return view('admins.hrStaff.dashboard');

// Performance Evaluation
// OLD: return view('hrStaff.perfEval');
// NEW: return view('admins.hrStaff.perfEval');
```

---

## Search & Replace Guide

Use your IDE's find-and-replace feature with these patterns:

### Pattern 1: Shared Views
```
Find: view\('hrAdmin\.(settings|leaveForm|employees|archive)'\)
Replace: view('admins.shared.$1')

Find: view\('hrStaff\.(settings|leaveForm|employees|archive)'\)
Replace: view('admins.shared.$1')
```

### Pattern 2: HR Admin Views
```
Find: view\('hrAdmin\.
Replace: view('admins.hrAdmin.
```

### Pattern 3: HR Staff Views
```
Find: view\('hrStaff\.
Replace: view('admins.hrStaff.
```

---

## Testing Checklist

After updating controllers, test the following:

### Shared Views (Test Both Roles)
- [ ] Settings page loads correctly for HR Admin
- [ ] Settings page loads correctly for HR Staff
- [ ] Leave form displays correctly for HR Admin (with iframe modal)
- [ ] Leave form displays correctly for HR Staff (with link)
- [ ] Employees page displays correctly for both roles
- [ ] Archive page displays correctly for both roles
  - [ ] HR Admin can restore all applicants
  - [ ] HR Staff can only restore manually archived passed applicants

### HR Admin Only
- [ ] Dashboard shows analytics and charts
- [ ] Application page with 4 tabs works
- [ ] Applicants table with bulk actions works
- [ ] Profile management works
- [ ] Job posting CRUD works
- [ ] Interview scheduling works
- [ ] Training scheduling works

### HR Staff Only
- [ ] Dashboard shows stats and calendar
- [ ] Performance evaluation scoring works
- [ ] Contract signing scheduling works
- [ ] Contract period management works
- [ ] Requirements tracking works
- [ ] Promote to employee works

---

## Rollback Instructions

If you need to revert to the original structure:

1. The original files are still in `resources/views/hrAdmin/` and `resources/views/hrStaff/`
2. Simply revert the controller changes
3. Delete the `resources/views/admins/` folder if desired

**Note:** Keep the `admins/` folder as the new standard. Only rollback if there are critical issues.
