# Leave Form Submission - Backend & Frontend Synchronization

## Overview
Complete synchronization between backend API responses and frontend handling for leave form submission with proper error handling, validation, and success flows.

---

## Backend Implementation

### File: `LeaveFormController.php`

#### **Method: `store(Request $request)`**

### Response Structure

#### 1. **Success Response (200)**
```json
{
    "success": true,
    "message": "Leave form submitted successfully.",
    "leave_form": {
        "id": 123,
        "leave_type": "Sick Leave",
        "date_range": "12/25/2024 - 12/27/2024",
        "status": "Pending"
    }
}
```

#### 2. **Validation Error Response (422)**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "attachment": ["The attachment field is required."],
        "date_range": ["The leave start date cannot be in the past."]
    }
}
```

#### 3. **Server Error Response (500)**
```json
{
    "success": false,
    "message": "An error occurred while submitting your leave request.",
    "error": "Detailed error message"
}
```

### Backend Logic Flow

```php
try {
    // 1. Validate request
    $validated = $request->validate([...]);

    // 2. Store file
    $path = $request->file('attachment')->store('leave_forms', 'public');

    // 3. Create leave form
    $leaveForm = LeaveForm::create([...]);

    // 4. Send notifications to HR
    foreach ($hrUsers as $hrUser) {
        $hrUser->notify(new NewLeaveRequestNotification($leaveForm, $employee));
    }

    // 5. Return appropriate response
    if (AJAX) {
        return JSON response
    } else {
        return redirect with session
    }

} catch (ValidationException $e) {
    // Handle validation errors
    if (AJAX) {
        return JSON with errors (422)
    } else {
        throw $e  // Laravel handles normally
    }

} catch (Exception $e) {
    // Handle server errors
    if (AJAX) {
        return JSON with error (500)
    } else {
        return redirect with error
    }
}
```

### Detection of AJAX Requests
```php
if ($request->wantsJson() || $request->ajax()) {
    // Return JSON
}
```

This checks:
- `wantsJson()`: Checks if `Accept: application/json` header is present
- `ajax()`: Checks if `X-Requested-With: XMLHttpRequest` header is present

---

## Frontend Implementation

### File: `leaveForm.blade.php`

### Request Headers
```javascript
headers: {
    'X-Requested-With': 'XMLHttpRequest',  // Identifies as AJAX
    'Accept': 'application/json'           // Requests JSON response
}
```

### Response Handling Flow

```javascript
async submitForm(event) {
    // 1. Close submit modal
    this.showSubmitModal = false;
    await delay(200ms);

    // 2. Show loading overlay
    this.isSubmitting = true;

    try {
        // 3. Submit via AJAX
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { AJAX + JSON headers }
        });

        // 4. Parse JSON response
        const data = await response.json();

        // 5. Handle by status code
        if (200-299) {
            if (data.success) {
                ✓ Show success modal
            } else {
                ⚠ Reload (unexpected format)
            }
        }
        else if (422) {
            ✗ Validation errors → Reload
        }
        else {
            ✗ Server error → Reload
        }

    } catch (error) {
        ✗ Network error → Reload
    }
}
```

---

## Complete Flow Diagram

```
┌────────────────────────────────────────────────────────────────┐
│                     SUBMIT LEAVE FORM                          │
└────────────────────────────────────────────────────────────────┘

FRONTEND                          BACKEND
────────                          ───────

1. User fills form
   ↓
2. Click "Submit Request"
   ↓
3. Close submit modal (200ms)
   ↓
4. Show loading overlay
   ↓
5. Send AJAX request  ───────────→ 6. Receive request
   Headers:                           Check: AJAX? JSON?
   - X-Requested-With                 ↓
   - Accept: application/json     7. Try validation
                                      ↓
                                  ┌───┴───────────────────┐
                                  │                       │
                              VALID?                  INVALID?
                                  │                       │
                                  ↓                       ↓
                          8. Store file          ValidationException
                          9. Create record               ↓
                         10. Notify HR          Return JSON (422)
                         11. Return success     { success: false,
                             JSON (200)           errors: {...} }
                             { success: true }          │
                                  │                       │
   ┌──────────────────────────────┴───────────────────────┘
   ↓
12. Parse JSON response
    ↓
    ┌────────┴──────────┬──────────────┬──────────────┐
    ↓                   ↓              ↓              ↓
  200 OK           422 Error      500 Error    Network Error
data.success=true  Validation    Server Error   Connection
    ↓                   ↓              ↓              ↓
13. Wait 500ms      Hide loading   Hide loading   Hide loading
14. Hide loading        ↓              ↓              ↓
15. Wait 200ms      Wait 300ms     Wait 300ms     Wait 300ms
16. Show SUCCESS        ↓              ↓              ↓
    MODAL           RELOAD PAGE    RELOAD PAGE    RELOAD PAGE
    ↓               (show errors)  (show error)   (show error)
17. User clicks OK
    ↓
18. Close modal (300ms)
    ↓
19. RELOAD PAGE
    (show new leave request)
```

---

## Error Handling Matrix

| Scenario | Backend Response | Frontend Action | User Sees |
|----------|-----------------|-----------------|-----------|
| **Success** | 200 + `success: true` | Show success modal | ✓ Green success modal → Reload |
| **Missing field** | 422 + validation errors | Reload page | ✗ Red validation toast |
| **Invalid date** | 422 + validation errors | Reload page | ✗ Red validation toast |
| **File too large** | 422 + validation errors | Reload page | ✗ Red validation toast |
| **Server error** | 500 + error message | Reload page | ✗ Red error toast |
| **Network down** | Exception | Reload page | ✗ Red error toast |
| **DB error** | 500 + error message | Reload page | ✗ Red error toast |

---

## Validation Rules

### Attachment
- **Required**: Yes
- **Type**: File
- **Formats**: PDF, JPG, JPEG, PNG
- **Max Size**: 2MB (2048 KB)

### Leave Type
- **Required**: Yes
- **Type**: String
- **Options**:
  - Sick Leave
  - Vacation Leave
  - Emergency Leave
  - Maternity Leave
  - Paternity Leave
  - Special Leave

### Date Range
- **Required**: Yes
- **Format**: "MM/DD/YYYY - MM/DD/YYYY"
- **Rules**:
  - Must contain start and end date
  - Start date ≥ today (cannot be in past)
  - End date ≥ Start date

### About (Optional)
- **Required**: No
- **Type**: String/Text

---

## State Management

### Frontend States

```javascript
{
    showSubmitModal: boolean,    // Submit form modal visibility
    showSuccessModal: boolean,   // Success modal visibility
    isSubmitting: boolean,       // Loading overlay visibility
    selectedForm: object|null,   // Currently selected leave form
    leaveForms: array           // List of all leave forms
}
```

### State Transitions

```
IDLE STATE
  ↓ (User clicks "Submit New Request")
showSubmitModal = true
  ↓ (User clicks "Submit Request")
showSubmitModal = false
  ↓ (After 200ms)
isSubmitting = true
  ↓ (After AJAX success + 500ms)
isSubmitting = false
  ↓ (After 200ms)
showSuccessModal = true
  ↓ (User clicks "Got it, thanks!")
showSuccessModal = false
  ↓ (After 300ms)
RELOAD PAGE
```

---

## Console Logging

### Success
```javascript
// No console logs on success
```

### Validation Errors
```javascript
console.error('Validation errors:', data);
// data = { success: false, errors: {...} }
```

### Server Errors
```javascript
console.error('Server error:', data);
// data = { success: false, message: "...", error: "..." }
```

### Network Errors
```javascript
console.error('Network or submission error:', error);
// error = JavaScript Error object
```

### Unexpected Response
```javascript
console.warn('Unexpected response format:', data);
```

---

## Timing Configuration

| Event | Duration | Purpose |
|-------|----------|---------|
| Submit modal close | 200ms | Smooth exit animation |
| Loading minimum | 500ms | Prevent UI flash |
| Loading → Success | 200ms | Visual transition gap |
| Success modal close | 300ms | Smooth dismissal |
| Error → Reload | 300ms | Allow loading to hide |

**Total Success Flow**: ~1.5 seconds
**Total Error Flow**: ~0.5 seconds

---

## Testing Scenarios

### ✅ Happy Path
1. Fill all required fields correctly
2. Select valid date range
3. Upload valid file
4. Click submit
5. **Expected**: Loading → Success modal → Reload

### ❌ Validation Errors
1. Leave required field empty
2. Click submit
3. **Expected**: Loading → Reload with error toast

### ❌ Invalid Date
1. Select past date as start date
2. Click submit
3. **Expected**: Loading → Reload with date error

### ❌ Large File
1. Upload file > 2MB
2. Click submit
3. **Expected**: Loading → Reload with file size error

### ❌ Network Failure
1. Disconnect internet
2. Click submit
3. **Expected**: Loading → Reload with error

---

## Backward Compatibility

### Non-AJAX Requests
If a request is made WITHOUT AJAX headers:
- Backend returns standard redirect response
- Session flash messages are used
- Page reloads normally
- No JSON responses

This ensures the form still works if:
- JavaScript is disabled
- Browser doesn't support fetch API
- Old form submission method is used

---

## Security Considerations

### CSRF Protection
- All requests include `@csrf` token
- Laravel validates token automatically
- Invalid tokens return 419 error

### File Upload Security
- Only allowed MIME types
- Max file size enforced
- Stored in secure directory (`storage/app/public/leave_forms`)
- Unique filenames prevent overwrites

### Authorization
- Only authenticated users can submit
- User ID from `Auth::id()` (cannot be spoofed)
- Leave forms tied to user account

---

## Key Synchronization Points

✅ **Request Detection**: Both check AJAX headers
✅ **Response Format**: JSON for AJAX, redirect for normal
✅ **Status Codes**: 200 (success), 422 (validation), 500 (error)
✅ **Error Structure**: Consistent `{ success, message, errors }` format
✅ **Content Type**: `application/json` for all AJAX responses
✅ **State Management**: Frontend states match backend responses
✅ **Timing**: Appropriate delays for smooth UX
✅ **Fallback**: Page reload on any unexpected state

The backend and frontend are now **fully synchronized** and handle all edge cases gracefully!
