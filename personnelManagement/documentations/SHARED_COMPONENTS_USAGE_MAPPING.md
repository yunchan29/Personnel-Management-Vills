# Shared Components Usage Mapping Report

## Overview
Comprehensive mapping of which roles use each shared component in the Personnel Management application.

---

## Component Usage Summary

### 1. button.blade.php
**Status:** Limited Use  
**Used By:** Indirectly (button loading states)  
**Roles:** All (through internal references)

### 2. form/text-input.blade.php
**Status:** Unused  
**Used By:** None detected  
**Roles:** None

### 3. form/select-input.blade.php
**Status:** Unused  
**Used By:** None detected  
**Roles:** None

### 4. form/date-input.blade.php
**Status:** Unused  
**Used By:** None detected  
**Roles:** None

### 5. form/textarea-input.blade.php
**Status:** Unused  
**Used By:** None detected  
**Roles:** None

### 6. loading-overlay.blade.php
**Status:** Active  
**Used By:**
- layouts/applicantHome.blade.php (Line 22)
- layouts/employeeHome.blade.php (Line 28)  
**Roles:** Applicant, Employee

### 7. loading-spinner.blade.php
**Status:** Active  
**Used By:**
- components/shared/loading-overlay.blade.php (Line 5)
- components/shared/button.blade.php (Line 24)  
**Roles:** All (through loading-overlay and button)

### 8. modal.blade.php
**Status:** Unused  
**Used By:** None detected  
**Roles:** None

### 9. navbar.blade.php
**Status:** Active  
**Used By:**
- layouts/applicantHome.blade.php (Line 14)
- layouts/employeeHome.blade.php (Line 15)
- layouts/hrAdmin.blade.php (Line 16)
- layouts/hrStaff.blade.php (Line 15)  
**Roles:** Applicant, Employee, HR Admin, HR Staff

### 10. status-badge.blade.php
**Status:** Unused  
**Used By:** None detected  
**Roles:** None

### 11. licenses.blade.php
**Status:** Active  
**Used By:**
- users/files.blade.php (Line 103)  
**Roles:** Applicant, Employee

### 12. other-files.blade.php
**Status:** Active  
**Used By:**
- users/files.blade.php (Line 104)  
**Roles:** Applicant, Employee

### 13. personal-information.blade.php
**Status:** Active  
**Used By:**
- components/shared/profile.blade.php (Line 59)  
**Roles:** Applicant, Employee, HR Admin (via profile)

### 14. work-experience.blade.php
**Status:** Active  
**Used By:**
- components/shared/profile.blade.php (Line 63)  
**Roles:** Applicant, Employee, HR Admin (via profile)

### 15. sidebar.blade.php
**Status:** Active  
**Used By:**
- layouts/applicantHome.blade.php (Line 18)
- layouts/employeeHome.blade.php (Line 19)
- layouts/hrAdmin.blade.php (Line 20)
- layouts/hrStaff.blade.php (Line 19)  
**Roles:** Applicant, Employee, HR Admin, HR Staff

### 16. profile.blade.php
**Status:** Active  
**Used By:**
- users/profile.blade.php (Line 4)
- admins/hrAdmin/profile.blade.php (Line 4)  
**Roles:** Applicant, Employee, HR Admin

### 17. settings.blade.php
**Status:** Active  
**Used By:**
- users/settings.blade.php (Line 4)
- admins/shared/settings.blade.php (Line 4)  
**Roles:** Applicant, Employee, HR Admin, HR Staff

---

## Role-Based Component Usage Table

| Component | Applicant | Employee | HR Admin | HR Staff |
|-----------|:---------:|:--------:|:--------:|:--------:|
| button | - | - | - | - |
| form/text-input | - | - | - | - |
| form/select-input | - | - | - | - |
| form/date-input | - | - | - | - |
| form/textarea-input | - | - | - | - |
| loading-overlay | YES | YES | - | - |
| loading-spinner | YES | YES | YES | YES |
| modal | - | - | - | - |
| navbar | YES | YES | YES | YES |
| status-badge | - | - | - | - |
| licenses | YES | YES | - | - |
| other-files | YES | YES | - | - |
| personal-information | YES | YES | YES | - |
| work-experience | YES | YES | YES | - |
| sidebar | YES | YES | YES | YES |
| profile | YES | YES | YES | - |
| settings | YES | YES | YES | YES |
| **TOTAL** | **12/17** | **12/17** | **8/17** | **4/17** |

---

## Key Findings

### Actively Used (10 components):
1. navbar - All 4 roles
2. sidebar - All 4 roles
3. settings - All 4 roles
4. loading-spinner - All 4 roles (indirect)
5. profile - 3 roles
6. personal-information - 3 roles
7. work-experience - 3 roles
8. licenses - 2 roles
9. other-files - 2 roles
10. loading-overlay - 2 roles

### Unused (7 components):
- form/text-input
- form/select-input
- form/date-input
- form/textarea-input
- button (limited)
- modal
- status-badge

---
