# Production Archive Fix Guide (Hostinger)

## Problem
After running the migration, archived records are "stuck at 30 days" because:
1. ❌ Old records have `archived_at = NULL` (migration doesn't populate existing data)
2. ❌ Cron job not set up (automatic cleanup never runs)
3. ❌ Records never get deleted

## Solution - 3 Steps

---

## Step 1: Check Current Status

SSH into your Hostinger server and run:

```bash
cd /path/to/your/project/personnelManagement
php check-archive-status.php
```

This will show you:
- How many archived records have NULL `archived_at`
- Which records are expired
- Sample data to verify the issue

**Expected Output:**
```
Total Archived Applications: 15

Breakdown:
  - WITH archived_at set: 0
  - WITHOUT archived_at (NULL): 15    ← THIS IS THE PROBLEM

⚠ WARNING: 15 archived record(s) don't have archived_at timestamp!
   FIX: Run this command:
   php artisan archive:backfill-timestamps
```

---

## Step 2: Backfill `archived_at` for Existing Records

Run this command to set `archived_at` for all existing archived records:

```bash
cd /path/to/your/project/personnelManagement
php artisan archive:backfill-timestamps
```

**What this does:**
- Finds all archived records with `archived_at = NULL`
- Sets `archived_at = updated_at` (best approximation we have)
- Now countdown timers will work correctly

**Expected Output:**
```
Backfilling archived_at timestamps for existing archived applications...
Successfully backfilled 15 archived application(s).
```

**Verify it worked:**
```bash
php check-archive-status.php
```

Should now show:
```
Breakdown:
  - WITH archived_at set: 15    ← FIXED!
  - WITHOUT archived_at (NULL): 0
```

---

## Step 3: Set Up Cron Job on Hostinger

### Option A: Using Hostinger Control Panel (Recommended)

1. **Log into Hostinger Control Panel**
   - Go to your hosting dashboard

2. **Navigate to Cron Jobs**
   - Advanced → Cron Jobs
   - OR Search for "Cron Jobs" in the control panel

3. **Add New Cron Job**
   - **Type**: Common Settings → Custom
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**:
     ```bash
     cd /home/uXXXXXX/domains/yourdomain.com/public_html/personnelManagement && php artisan schedule:run >> /dev/null 2>&1
     ```
     *(Replace `/home/uXXXXXX/domains/yourdomain.com/public_html` with your actual path)*

4. **Save**

**To find your path:**
```bash
pwd
# Output: /home/u123456789/domains/example.com/public_html/personnelManagement
```

### Option B: Using SSH

1. **Connect via SSH**

2. **Edit crontab**
```bash
crontab -e
```

3. **Add this line**
```bash
* * * * * cd /home/uXXXXXX/domains/yourdomain.com/public_html/personnelManagement && php artisan schedule:run >> /dev/null 2>&1
```

4. **Save and exit**
   - Press `Ctrl+X`, then `Y`, then `Enter`

5. **Verify cron job was added**
```bash
crontab -l
```

---

## Step 4: Verify Everything Works

### 4.1 Check Scheduled Tasks
```bash
cd /path/to/your/project/personnelManagement
php artisan schedule:list
```

**Expected Output:**
```
  0 2 * * *  php artisan archive:cleanup ............... Next Due: 1 day from now
```

### 4.2 Test Manual Cleanup
```bash
php artisan archive:cleanup
```

**Expected Output:**
```
Starting archived applications cleanup...
Deleted 0 archived application(s) older than 30 days.
```
*(0 is normal if no records are older than 30 days yet)*

### 4.3 Test with Fake Old Record

**Option 1: Via Tinker**
```bash
php artisan tinker
```

Then run:
```php
$app = \App\Models\Application::where('is_archived', true)->first();
if ($app) {
    $app->archived_at = now()->subDays(35);
    $app->save();
    echo "Test record created (35 days old)\n";
    exit;
}
echo "No archived records found\n";
exit;
```

**Option 2: Via MySQL**
```sql
-- Find an archived record
SELECT id, archived_at FROM applications WHERE is_archived = 1 LIMIT 1;

-- Set it to 35 days ago (replace ID)
UPDATE applications
SET archived_at = DATE_SUB(NOW(), INTERVAL 35 DAY)
WHERE id = 123;
```

**Now run cleanup:**
```bash
php artisan archive:cleanup
```

**Expected Output:**
```
Starting archived applications cleanup...
Deleted 1 archived application(s) older than 30 days.
```

### 4.4 Check the Archive Page
- Visit your production site
- Log in as HR Admin or HR Staff
- Go to Archive page
- Verify countdown shows correct days remaining

---

## Common Hostinger Issues & Solutions

### Issue 1: "Command not found: php"
**Solution:** Use full PHP path
```bash
# Find PHP path
which php
# Or try these common paths:
/usr/bin/php
/opt/alt/php82/usr/bin/php
/usr/local/bin/php
```

Update cron command:
```bash
* * * * * cd /home/uXXXXXX/domains/yourdomain.com/public_html/personnelManagement && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### Issue 2: "Permission denied"
**Solution:** Check file permissions
```bash
chmod +x artisan
chmod -R 755 storage bootstrap/cache
```

### Issue 3: Cron job not running
**Solution:** Check cron logs
```bash
# Hostinger cron logs location (varies):
tail -f /var/log/cron
# Or check email - Hostinger sends cron output to account email
```

### Issue 4: "Class not found" errors in cron
**Solution:** Make sure cron runs in the correct directory
```bash
# BAD:
* * * * * php artisan schedule:run

# GOOD:
* * * * * cd /full/path/to/personnelManagement && php artisan schedule:run
```

---

## Verification Checklist

After completing all steps, verify:

- [ ] Migration ran: `archived_at` column exists
- [ ] Backfill ran: Old records have `archived_at` populated
- [ ] Cron job added: Shows in `crontab -l`
- [ ] Commands exist: `php artisan list | grep archive`
- [ ] Schedule registered: `php artisan schedule:list` shows `archive:cleanup`
- [ ] Manual cleanup works: `php artisan archive:cleanup` runs without errors
- [ ] Archive page shows correct countdowns

---

## Monitoring in Production

### Daily Checks (First Week)
```bash
# Check if cleanup is running
php check-archive-status.php

# Check Laravel logs
tail -f storage/logs/laravel.log | grep -i archive
```

### Check Cron Execution
Hostinger sends cron output to your account email by default. Check for:
- Error emails from cron
- Success confirmations

### Disable Cron Emails (Optional)
If you don't want emails, update cron command:
```bash
* * * * * cd /path/to/personnelManagement && php artisan schedule:run >> /dev/null 2>&1
```
The `>> /dev/null 2>&1` already suppresses output.

---

## Quick Reference Commands

```bash
# Diagnostic
php check-archive-status.php

# Backfill existing records
php artisan archive:backfill-timestamps

# Manual cleanup
php artisan archive:cleanup

# List scheduled tasks
php artisan schedule:list

# Test schedule manually
php artisan schedule:run

# View cron jobs
crontab -l

# Edit cron jobs
crontab -e
```

---

## Timeline of What Happens

| Time | Event | Details |
|------|-------|---------|
| **Day 0** | Application archived | `is_archived=true`, `archived_at=2025-11-27 14:30` |
| **Day 1-29** | Visible in archive | Countdown shows: 29 days, 28 days... |
| **Day 30, 2:00 AM** | Auto-deletion | Cron runs `archive:cleanup` → Record deleted |
| **Day 30, 2:01 AM** | Removed from archive page | Record no longer exists |

---

## Support

If issues persist after following this guide:

1. Run diagnostic: `php check-archive-status.php`
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Verify cron is running: Check Hostinger email for cron output
4. Test manually: `php artisan archive:cleanup -v` (verbose mode)
