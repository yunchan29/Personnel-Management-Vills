# Admin Views Organization

This folder contains all administrative blade views organized by role and shared functionality.

## Folder Structure

```
admins/
├── shared/          # Merged files used by both HR Admin and HR Staff
├── hrAdmin/         # HR Admin-specific files only
└── hrStaff/         # HR Staff-specific files only
```

---

## Shared Views (`shared/`)

These files are used by **both HR Admin and HR Staff** roles. They dynamically extend the appropriate layout based on the authenticated user's role.

### Files:
1. **settings.blade.php**
   - Purpose: Password change form for account security
   - Features: Update current password with validation
   - Identical for both roles

2. **leaveForm.blade.php**
   - Purpose: View and manage employee leave requests
   - Features: Tabbed interface (Pending, Approved, Declined), draggable modals
   - Role Differences:
     - HR Admin: Attachment viewer modal (iframe)
     - HR Staff: Simple attachment link (target="_blank")

3. **employees.blade.php**
   - Purpose: Display employees grouped by job posting
   - Features: Two-tab view (Job Postings, Employee List)
   - Identical for both roles

4. **archive.blade.php**
   - Purpose: Display and manage archived applicants/applications
   - Features: Restore and delete actions with SweetAlert confirmations
   - Role Differences:
     - HR Admin: Shows "Archived On" date, all applicants can be restored
     - HR Staff: Shows "Reason" column, only manually archived passed applicants can be restored

---

## HR Admin Only (`hrAdmin/`)

These files are **exclusive to HR Admin** role.

### Files:
1. **dashboard.blade.php**
   - Purpose: Analytics dashboard with statistics and charts
   - Features: Job/applicant/employee stats, line chart, pie chart for leave forms

2. **application.blade.php**
   - Purpose: Master application management page
   - Features: 4-tab interface (Job Postings, Applicants, Interview Schedule, Training Schedule)

3. **applicants.blade.php**
   - Purpose: Applicant table with bulk actions
   - Features: Approve/decline applicants, mass approve/decline, report generation

4. **profile.blade.php**
   - Purpose: HR Admin personal profile management
   - Features: Profile picture upload, personal information, work experience tabs

5. **jobPosting.blade.php**
   - Purpose: Create and manage job advertisements
   - Features: Search, filters, job listing display, job form modal

6. **interviewSchedule.blade.php**
   - Purpose: Schedule and manage applicant interviews
   - Features: Bulk interview scheduling, mass reschedule, status management

7. **trainingSchedule.blade.php**
   - Purpose: Schedule and manage training sessions
   - Features: Set training dates/times/location, bulk scheduling

---

## HR Staff Only (`hrStaff/`)

These files are **exclusive to HR Staff** role.

### Files:
1. **dashboard.blade.php**
   - Purpose: Simple dashboard with employee statistics
   - Features: Employee count cards, calendar widget

2. **perfEval.blade.php**
   - Purpose: Training evaluation and employee promotion
   - Features:
     - Performance evaluation scoring (Knowledge, Skill, Participation, Professionalism)
     - Contract signing scheduling
     - Contract period management (6 months / 1 year)
     - Requirements tracking and notification
     - Promote applicants to employees
     - Archive management

---

## Implementation Notes

### Dynamic Layout Extension
All shared files use dynamic layout extension:
```php
@extends(auth()->user()->role === 'HR Admin' ? 'layouts.hrAdmin' : 'layouts.hrStaff')
```

### Route Conditionals
For role-specific routes in shared views:
```php
{{ route(auth()->user()->role === 'HR Admin' ? 'hrAdmin.route' : 'hrStaff.route') }}
```

### Conditional Rendering
For role-specific features:
```php
@if(auth()->user()->role === 'HR Admin')
    <!-- HR Admin specific content -->
@else
    <!-- HR Staff specific content -->
@endif
```

---

## Benefits of This Organization

✅ **Reduced Code Duplication** - 4 files merged, ~30% reduction
✅ **Single Source of Truth** - Bug fixes apply to both roles automatically
✅ **Easier Maintenance** - Clear separation of shared vs role-specific code
✅ **Consistent UI/UX** - Shared components ensure consistency
✅ **Better Organization** - Clear folder structure indicates purpose

---

## Migration Guide

To use these new organized views, update your controllers to point to the new paths:

### Before:
```php
return view('hrAdmin.settings');
return view('hrStaff.settings');
```

### After:
```php
return view('admins.shared.settings');
return view('admins.shared.settings'); // Same for both!
```

### Role-Specific Views:
```php
// HR Admin
return view('admins.hrAdmin.dashboard');
return view('admins.hrAdmin.application');

// HR Staff
return view('admins.hrStaff.dashboard');
return view('admins.hrStaff.perfEval');
```

---

## File Count Summary

- **Shared Files:** 4
- **HR Admin Only:** 7
- **HR Staff Only:** 2
- **Total:** 13 files

**Previously:** 11 files in hrAdmin + 6 files in hrStaff = 17 files
**Now:** 13 files (4 merged pairs + 9 unique)
**Savings:** 4 duplicate files eliminated
