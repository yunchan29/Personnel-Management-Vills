# Controller & Route Alignment Check

**Date:** 2025-11-08
**Status:** ⚠️ **NOT ALIGNED - Updates Required**

---

## Summary

The new `admins/` and `users/` folder structure has been created, but **controllers and routes are still pointing to the old folder structure**. You need to update them before deleting the old files.

---

## Current State

### ✅ Users Folder
- **Location:** `resources/views/users/`
- **Files:** dashboard.blade.php, application.blade.php, files.blade.php, profile.blade.php, settings.blade.php
- **Status:** ✅ **Already aligned** (controllers are using `users.*`)

### ⚠️ Admin Folders (NOT ALIGNED)
- **Old Location:** `resources/views/hrAdmin/` and `resources/views/hrStaff/`
- **New Location:** `resources/views/admins/` (shared, hrAdmin, hrStaff subfolders)
- **Status:** ❌ **NOT aligned** - Controllers still reference old paths

---

## Required Updates

### 1. Settings Routes (3 locations)

**File:** `routes/web.php`

#### Lines to Update:

**Line 61 (Applicant):**
```php
// Current:
Route::get('/settings', fn () => view('users.settings'))->name('settings');
// ✅ Already correct - no change needed
```

**Line 97 (Employee):**
```php
// Current:
Route::get('/settings', function () {return view('users.settings');})->name('settings');
// ✅ Already correct - no change needed
```

**Line 179 (HR Admin):**
```php
// Current:
Route::get('/settings', fn() => view('hrAdmin.settings'))->name('settings');

// Update to:
Route::get('/settings', fn() => view('admins.shared.settings'))->name('settings');
```

**Lines 275-277 (HR Staff):**
```php
// Current:
Route::get('/settings', function () {
    return view('hrStaff.settings');
})->name('settings');

// Update to:
Route::get('/settings', function () {
    return view('admins.shared.settings');
})->name('settings');
```

---

### 2. LeaveFormController

**File:** `app/Http/Controllers/LeaveFormController.php`
**Lines:** 20-22

```php
// Current:
$view = $user->role === 'hrAdmin'
    ? 'hrAdmin.leaveForm'
    : 'hrStaff.leaveForm';

// Update to:
$view = 'admins.shared.leaveForm';
```

---

### 3. EmployeeController

**File:** `app/Http/Controllers/EmployeeController.php`

**Line 28:**
```php
// Current:
return view("$role.employees", [

// Update to:
return view("admins.shared.employees", [
```

**Line 79:**
```php
// Current:
return view('hrStaff.perfEval', [

// Update to:
return view('admins.hrStaff.perfEval', [
```

---

### 4. ArchiveController

**File:** `app/Http/Controllers/ArchiveController.php`
**Line 23:**

```php
// Current:
return view('hrAdmin.archive', compact('applications'));

// Update to:
return view('admins.shared.archive', compact('applications'));
```

---

### 5. StaffArchiveController

**File:** `app/Http/Controllers/StaffArchiveController.php`
**Line 23:**

```php
// Current:
return view('hrStaff.archive', compact('applications'));

// Update to:
return view('admins.shared.archive', compact('applications'));
```

---

### 6. DashboardChartController

**File:** `app/Http/Controllers/DashboardChartController.php`
**Line 123:**

```php
// Current:
return view('hrAdmin.dashboard', compact('chartData', 'stats', 'leaveData'));

// Update to:
return view('admins.hrAdmin.dashboard', compact('chartData', 'stats', 'leaveData'));
```

---

### 7. InitialApplicationController

**File:** `app/Http/Controllers/InitialApplicationController.php`

**Line 63:**
```php
// Current:
return view('hrAdmin.application', compact('jobs', 'applications', 'companies'));

// Update to:
return view('admins.hrAdmin.application', compact('jobs', 'applications', 'companies'));
```

**Line 106:**
```php
// Current:
return view('hrAdmin.application', [

// Update to:
return view('admins.hrAdmin.application', [
```

---

### 8. JobController

**File:** `app/Http/Controllers/JobController.php`
**Line 83:**

```php
// Current:
return view('hrAdmin.jobPosting', compact('jobs', 'companies'));

// Update to:
return view('admins.hrAdmin.jobPosting', compact('jobs', 'companies'));
```

---

## Files That Need Updates

| Controller/Route File | Lines | Views to Update |
|----------------------|-------|-----------------|
| `routes/web.php` | 179, 275-277 | settings (2 instances) |
| `LeaveFormController.php` | 20-22 | leaveForm |
| `EmployeeController.php` | 28, 79 | employees, perfEval |
| `ArchiveController.php` | 23 | archive |
| `StaffArchiveController.php` | 23 | archive |
| `DashboardChartController.php` | 123 | dashboard |
| `InitialApplicationController.php` | 63, 106 | application (2 instances) |
| `JobController.php` | 83 | jobPosting |

**Total Updates Needed:** 13 view references across 8 files

---

## Users Folder Status

### ✅ Already Aligned (No Changes Needed)

The following controllers are already using the `users.*` view path:

- `File201Controller.php` → `users.files` ✅
- `ApplicantJobController.php` → `users.dashboard`, `applicant.applications` ⚠️
- `ResumeController.php` → `users.application` ✅
- `UserController.php` → `users.profile` ✅

**Note:** `ApplicantJobController.php` line 162 references `applicant.applications` which doesn't exist in the current structure. This may need investigation.

---

## Recommendation

### ⚠️ DO NOT DELETE OLD FILES YET

**Before deleting** `hrAdmin/` and `hrStaff/` folders:

1. ✅ Update all 13 view references in controllers/routes (see above)
2. ✅ Test all routes as HR Admin
3. ✅ Test all routes as HR Staff
4. ✅ Verify all pages load correctly
5. ✅ Check for any blade `@include` or `@extends` references in other files
6. ✅ Search for any hardcoded view paths in JavaScript files

**After confirming everything works:**
- Delete `resources/views/hrAdmin/` folder
- Delete `resources/views/hrStaff/` folder
- Keep `resources/views/users/` folder (already correct)

---

## Quick Fix Script

You can use this find-and-replace in your IDE:

```regex
Find: view\('hrAdmin\.settings'\)
Replace: view('admins.shared.settings')

Find: view\('hrStaff\.settings'\)
Replace: view('admins.shared.settings')

Find: view\('hrAdmin\.leaveForm'\)
Replace: view('admins.shared.leaveForm')

Find: view\('hrStaff\.leaveForm'\)
Replace: view('admins.shared.leaveForm')

Find: view\('hrAdmin\.employees'\)
Replace: view('admins.shared.employees')

Find: view\('hrStaff\.employees'\)
Replace: view('admins.shared.employees')

Find: view\('hrAdmin\.archive'\)
Replace: view('admins.shared.archive')

Find: view\('hrStaff\.archive'\)
Replace: view('admins.shared.archive')

Find: view\('hrAdmin\.dashboard'\)
Replace: view('admins.hrAdmin.dashboard')

Find: view\('hrAdmin\.application'\)
Replace: view('admins.hrAdmin.application')

Find: view\('hrAdmin\.jobPosting'\)
Replace: view('admins.hrAdmin.jobPosting')

Find: view\('hrStaff\.perfEval'\)
Replace: view('admins.hrStaff.perfEval')

Find: view\("\$role\.employees"
Replace: view('admins.shared.employees'
```

---

## Testing Checklist

After making updates, verify:

- [ ] HR Admin can access dashboard
- [ ] HR Admin can access settings
- [ ] HR Admin can access leave forms
- [ ] HR Admin can access employees
- [ ] HR Admin can access archive
- [ ] HR Admin can access applications
- [ ] HR Admin can access job posting
- [ ] HR Staff can access dashboard
- [ ] HR Staff can access settings
- [ ] HR Staff can access leave forms
- [ ] HR Staff can access employees
- [ ] HR Staff can access archive
- [ ] HR Staff can access performance evaluation
- [ ] Applicants can access their dashboard
- [ ] Applicants can access settings
- [ ] Employees can access their dashboard
- [ ] Employees can access settings

---

## Conclusion

**Status:** ❌ Routes/Controllers NOT aligned with new structure
**Action Required:** Update 13 view references before deleting old files
**Estimated Time:** 10-15 minutes
**Risk Level:** Low (old files remain as backup)
