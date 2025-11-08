# Authentication Security Enhancements

## Overview
This document outlines the security improvements implemented for the authentication flow in the Personnel Management System.

## Implemented Features

### 1. User Activity Tracking

**What it does:**
- Tracks when users last logged in
- Records the IP address of login attempts
- Monitors user activity throughout their session

**Database Fields Added:**
- `last_login_at` - Timestamp of the user's last successful login
- `last_login_ip` - IP address from which the user last logged in
- `last_activity_at` - Timestamp of the user's most recent activity

**Benefits:**
- Security auditing and compliance
- Detect inactive accounts
- Identify suspicious login patterns
- Show "last login" information to users

**Location:**
- Migration: `database/migrations/2025_11_07_184959_add_last_login_columns_to_users_table.php`
- Controller: `app/Http/Controllers/Auth/LoginController.php` (lines 51-56)
- Model: `app/Models/User.php` (lines 52-54, 90-91)

### 2. User Activity Middleware

**What it does:**
- Automatically updates the `last_activity_at` timestamp for authenticated users
- Updates every 5 minutes to prevent excessive database writes
- Enables idle session timeout functionality

**How it works:**
```php
// Updates last_activity_at only if > 5 minutes have passed
if (!$user->last_activity_at || $user->last_activity_at->diffInMinutes(now()) >= 5) {
    $user->update(['last_activity_at' => now()]);
}
```

**Benefits:**
- Enables idle timeout detection
- Provides accurate user activity metrics
- Minimal performance impact (throttled updates)

**Location:**
- `app/Http/Middleware/UserActivityMiddleware.php`
- Registered in: `bootstrap/app.php` (line 23)

### 3. Security Headers Middleware

**What it does:**
- Adds critical HTTP security headers to all responses
- Protects against common web vulnerabilities

**Headers Implemented:**

| Header | Value | Protection Against |
|--------|-------|-------------------|
| X-Frame-Options | SAMEORIGIN | Clickjacking attacks |
| X-Content-Type-Options | nosniff | MIME type sniffing |
| X-XSS-Protection | 1; mode=block | Cross-site scripting |
| Strict-Transport-Security | max-age=31536000 | SSL stripping (production only) |
| Referrer-Policy | strict-origin-when-cross-origin | Referrer leakage |
| Permissions-Policy | geolocation=(), microphone=(), camera=() | Unwanted browser features |
| Content-Security-Policy | Configured CSP | XSS and data injection |

**Benefits:**
- OWASP Top 10 protection
- Browser-level security enforcement
- Reduced attack surface

**Location:**
- `app/Http/Middleware/SecurityHeadersMiddleware.php`
- Registered globally in: `bootstrap/app.php` (line 20)

### 4. Enhanced Session Security

**Configuration Updates:**

**Development (.env):**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

**Production (.env.production.example):**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=60              # Reduced for security
SESSION_ENCRYPT=true             # Encrypted sessions
SESSION_SECURE_COOKIE=true       # HTTPS only
SESSION_HTTP_ONLY=true           # No JS access
SESSION_SAME_SITE=lax            # CSRF protection
```

**Benefits:**
- Prevents session hijacking
- Encrypts sensitive session data
- Reduces session lifetime for better security
- CSRF attack mitigation

**Location:**
- Configuration: `config/session.php`
- Example: `personnelManagement/.env.production.example` (lines 31-38)

---

## Existing Security Features (Already Implemented)

### 1. Login Attempt Tracking & Account Lockout
- **Max Attempts:** 5 failed logins
- **Lockout Duration:** 15 minutes
- **Features:** Records IP, user agent, timestamp
- **Location:** `app/Models/LoginAttempt.php`

### 2. Rate Limiting
- **Login:** 5 attempts per minute
- **Registration:** 3 attempts per minute
- **Password Reset:** 3 attempts per minute
- **Location:** `routes/web.php`

### 3. Role-Based Access Control
- Middleware prevents unauthorized access
- Aborts with 403 for invalid roles
- **Location:** `app/Http/Middleware/RoleMiddleware.php`

### 4. Secure Password Reset
- Token-based password reset
- Throttled to 3 attempts per minute
- **Location:** `app/Http/Controllers/Auth/ForgotPasswordController.php`

### 5. CSRF Protection
- Laravel's built-in CSRF protection
- Token validation on all POST/PUT/DELETE requests

---

## Usage Examples

### Display Last Login Information

In your dashboard or profile view:

```blade
@if(auth()->user()->last_login_at)
    <p>Last login: {{ auth()->user()->last_login_at->diffForHumans() }}</p>
    <p>From IP: {{ auth()->user()->last_login_ip }}</p>
@endif
```

### Check User Activity Status

```php
// Check if user has been inactive for more than 30 minutes
$isInactive = auth()->user()->last_activity_at
    && auth()->user()->last_activity_at->diffInMinutes(now()) > 30;

if ($isInactive) {
    // Force logout or show warning
}
```

### View User Activity in Admin Panel

```php
// Get recently active users
$activeUsers = User::where('last_activity_at', '>', now()->subMinutes(15))
    ->orderBy('last_activity_at', 'desc')
    ->get();

// Get users who haven't logged in recently
$inactiveUsers = User::where('last_login_at', '<', now()->subDays(30))
    ->orWhereNull('last_login_at')
    ->get();
```

---

## Recommended Future Enhancements

### HIGH PRIORITY
1. **Two-Factor Authentication (2FA)**
   - Implement TOTP for admin roles
   - Use Laravel Fortify or similar package
   - Required for hrAdmin and hrStaff roles

2. **Login Notifications**
   - Email alerts for new device logins
   - Suspicious activity detection
   - Account lockout notifications

3. **Concurrent Session Management**
   - Limit active sessions per user
   - Allow users to view/revoke active sessions
   - Force logout from all devices option

### MEDIUM PRIORITY
4. **Enhanced Audit Logging**
   - Log all authentication events
   - Track role changes and permission updates
   - Store logs in separate table or service

5. **Device Fingerprinting**
   - Track device/browser information
   - Detect new devices
   - "Remember this device" feature

6. **IP Whitelist/Blacklist**
   - Admin-configurable IP restrictions
   - Block known malicious IPs
   - Whitelist trusted networks

### LOW PRIORITY
7. **Password Breach Detection**
   - Check passwords against haveibeenpwned API
   - Warn users of compromised passwords
   - Force password change on breach detection

8. **Session Timeout Warning**
   - JavaScript warning before session expires
   - Option to extend session
   - Graceful logout on timeout

---

## Testing the Implementation

### 1. Test Login Tracking
```bash
# Login to the application
# Check the database
SELECT id, email, last_login_at, last_login_ip, last_activity_at
FROM users
WHERE email = 'your@email.com';
```

### 2. Test Security Headers
```bash
# Check response headers
curl -I https://yourdomain.com
# Or use browser DevTools > Network tab
```

### 3. Test Activity Tracking
```bash
# Login and navigate through the app
# Wait 5 minutes
# Navigate again
# Check last_activity_at updates
```

### 4. Test Account Lockout
```bash
# Try logging in with wrong password 5 times
# Verify account is locked for 15 minutes
# Check login_attempts table
```

---

## Security Checklist for Production

Before deploying to production, ensure:

- [ ] `APP_DEBUG=false` in `.env`
- [ ] `SESSION_ENCRYPT=true` in `.env`
- [ ] `SESSION_SECURE_COOKIE=true` in `.env`
- [ ] Valid SSL certificate installed
- [ ] HTTPS enforced (no HTTP access)
- [ ] Strong database passwords set
- [ ] File permissions correct (755 for directories, 644 for files)
- [ ] `.env` file permissions set to 600
- [ ] Database backups scheduled
- [ ] Error logging configured (Sentry, LogRocket, etc.)
- [ ] Rate limiting tested and working
- [ ] Security headers verified in browser
- [ ] Login attempt tracking tested
- [ ] Activity tracking verified

---

## Troubleshooting

### Issue: Session expires too quickly
**Solution:** Increase `SESSION_LIFETIME` in `.env` (in minutes)

### Issue: Activity not updating
**Solution:** Ensure `UserActivityMiddleware` is registered in `bootstrap/app.php`

### Issue: Security headers not appearing
**Solution:** Clear config cache with `php artisan config:clear`

### Issue: Login tracking not working
**Solution:**
1. Verify migration ran: `php artisan migrate:status`
2. Check database columns exist
3. Clear cache: `php artisan cache:clear`

---

## Performance Considerations

1. **Activity Tracking:**
   - Updates throttled to every 5 minutes
   - Minimal database impact
   - Consider Redis for high-traffic sites

2. **Security Headers:**
   - Added at middleware level (minimal overhead)
   - Headers are small (< 1KB total)
   - Can be cached by CDN

3. **Login Tracking:**
   - Single database update on login
   - Indexed columns recommended for large datasets
   - Consider archiving old login attempts

---

## Maintenance Tasks

### Cleanup Old Login Attempts
Create a scheduled command to clean up old login attempts:

```php
// In app/Console/Kernel.php
$schedule->command('cleanup:login-attempts')->daily();
```

```php
// Create command: php artisan make:command CleanupLoginAttempts
public function handle()
{
    LoginAttempt::where('attempted_at', '<', now()->subDays(30))->delete();
    $this->info('Old login attempts cleaned up.');
}
```

### Monitor Failed Login Attempts
Regularly check for unusual patterns:

```sql
-- Failed attempts by IP
SELECT ip_address, COUNT(*) as attempts
FROM login_attempts
WHERE successful = 0
  AND attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY ip_address
HAVING attempts > 10;

-- Recent lockouts
SELECT email, COUNT(*) as failed_attempts, MAX(attempted_at) as last_attempt
FROM login_attempts
WHERE successful = 0
  AND attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY email
HAVING failed_attempts >= 5;
```

---

## Support & Resources

- **Laravel Security Documentation:** https://laravel.com/docs/security
- **OWASP Top 10:** https://owasp.org/www-project-top-ten/
- **Mozilla Security Headers:** https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers#security

---

## Change Log

| Date | Version | Changes |
|------|---------|---------|
| 2025-11-07 | 1.0.0 | Initial security enhancements implemented |

---

## Contributors

- Authentication security improvements implemented
- Documentation created and maintained

---

*Last Updated: 2025-11-07*
