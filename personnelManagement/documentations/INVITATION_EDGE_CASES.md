# Contract Signing Invitation - Edge Case Protection

## Overview
Comprehensive edge case protection has been implemented for the contract signing invitation system to prevent errors, spam, and invalid data.

## Backend Protections (ContractScheduleController.php)

### 1. **Time Format Validation**
- **Rule**: Time must follow H:MM AM/PM format (e.g., 9:30 AM)
- **Regex**: `/^(1[0-2]|[1-9]):[0-5][0-9] (AM|PM)$/`
- **Error**: "Invalid time format. Please use H:MM AM/PM format"

### 2. **Past DateTime Prevention**
- **Rule**: Combined date and time cannot be in the past
- **Check**: `$schedule->isPast()`
- **Error**: "The scheduled date and time cannot be in the past"

### 3. **Business Hours Validation**
- **Rule**: Contract signing must be between 6:00 AM - 5:00 PM
- **Check**: `$hour < 6 || $hour >= 17`
- **Error**: "Contract signing must be scheduled during business hours (6:00 AM - 5:00 PM)"

### 4. **Hired Status Check**
- **Rule**: Cannot send invitation to applicants already hired
- **Check**: `$application->status->value === 'hired'`
- **Error**: "This applicant has already been hired. Cannot send invitation"

### 5. **Archived Status Check**
- **Rule**: Cannot send invitation to archived applicants
- **Check**: `$application->status->value === 'archived'`
- **Error**: "This applicant has been archived. Cannot send invitation"

### 6. **Evaluation Requirement**
- **Rule**: Applicant must have passed training evaluation
- **Check**: `!$application->evaluation || $application->evaluation->result !== 'Passed'`
- **Error**: "Applicant must pass training evaluation before setting a contract signing schedule"

### 7. **Spam Protection**
- **Rule**: Maximum 5 invitations per applicant within 24 hours
- **Check**: Count invitations in last 24 hours
- **Error**: "Too many invitations sent recently. Please wait before sending another invitation"
- **HTTP Status**: 429 (Too Many Requests)

### 8. **Duplicate Prevention**
- **Rule**: Cannot send duplicate invitation for same date/time within 2 hours
- **Check**: Checks for existing invitation with same date/time in last 2 hours
- **Error**: "An invitation for this exact date and time was already sent recently"

## Frontend Protections (perfEval.blade.php)

### 1. **Pre-Send Warning**
- **Feature**: Warns if selected applicants already have invitations
- **Action**: Shows confirmation dialog before proceeding
- **Message**: "X of the selected applicants already have invitations. Do you want to send new invitations anyway?"

### 2. **Past DateTime Validation**
- **Rule**: Validates before sending request
- **Check**: Client-side datetime comparison
- **Message**: "The scheduled date and time cannot be in the past or current time"

### 3. **Business Hours Validation**
- **Rule**: Client-side validation of business hours
- **Check**: Converts 12-hour to 24-hour format and validates
- **Message**: "Contract signing must be scheduled during business hours (6:00 AM - 5:00 PM)"

### 4. **Detailed Error Reporting**
- **Feature**: Shows specific error messages for each failed invitation
- **Display**: List of applicant names with their specific error messages
- **Format**:
  ```
  Invitations sent successfully: 5
  Failed: 2

  Error Details:
  • John Doe: Too many invitations sent recently
  • Jane Smith: This applicant has been archived
  ```

### 5. **Empty Selection Check**
- **Rule**: Must select at least one applicant
- **Message**: "Please select at least one applicant"

## Error Handling Flow

```
User Action → Frontend Validation → Backend Validation → Database Check → Email Send → Success/Error Response
```

### Success Scenarios:
1. ✅ All validations pass → Invitation sent → Email sent → Success message
2. ⚠️ All validations pass → Invitation sent → Email failed → Warning message

### Error Scenarios:
1. ❌ Frontend validation fails → Immediate error message (no API call)
2. ❌ Backend validation fails → Detailed error message from server
3. ❌ Bulk send with mixed results → Shows success count + detailed error list

## Database Tracking

All invitation attempts are tracked in the `contract_invitations` table:
- `application_id`: Reference to applicant
- `sent_by`: HR staff who sent invitation
- `contract_date`: Scheduled date
- `contract_signing_time`: Scheduled time
- `email_sent`: Boolean flag for email delivery status
- `sent_at`: Timestamp of invitation

## Benefits

1. **Data Integrity**: Prevents invalid or inconsistent data
2. **User Experience**: Clear, specific error messages
3. **Spam Prevention**: Rate limiting prevents abuse
4. **Audit Trail**: All invitation attempts logged
5. **Business Logic**: Enforces business rules (hours, statuses, etc.)
6. **Email Reliability**: Graceful handling of email failures

## Testing Scenarios

### Should PASS ✅
- Tomorrow at 9:00 AM (business hours, future date)
- Tomorrow at 4:00 PM (last business hour)
- Applicant with "Passed" evaluation status
- First invitation to an applicant

### Should FAIL ❌
- Today at current time (past/current time)
- Tomorrow at 5:30 AM (before business hours)
- Tomorrow at 6:00 PM (after business hours)
- Applicant with "hired" status
- Applicant with "archived" status
- Applicant without evaluation
- 6th invitation within 24 hours
- Duplicate invitation within 2 hours

## HTTP Status Codes

- `200`: Success
- `400`: Bad Request (validation failed)
- `404`: Application not found
- `429`: Too Many Requests (spam protection triggered)
- `500`: Server error

## Future Enhancements

Consider implementing:
- [ ] Weekend/Holiday detection
- [ ] Time zone support
- [ ] SMS notifications
- [ ] Configurable business hours
- [ ] Admin override for spam protection
- [ ] Invitation expiration
- [ ] Applicant confirmation tracking
