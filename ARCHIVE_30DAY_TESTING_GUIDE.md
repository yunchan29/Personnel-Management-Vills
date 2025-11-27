# Archive 30-Day Deletion Testing Guide

This guide shows you how to test the archive cleanup functionality.

## What Was Implemented

The archive feature now automatically deletes archived applications after 30 days using:
- **`archived_at` timestamp**: Tracks exactly when a record was archived
- **Scheduled command**: Runs daily at 2:00 AM to clean up expired archives
- **Manual command**: Can be run anytime for testing or manual cleanup

---

## Available Commands

### 1. `archive:cleanup`
Deletes archived applications older than 30 days.

```bash
cd personnelManagement
php artisan archive:cleanup
```

### 2. `archive:backfill-timestamps`
One-time command to set `archived_at` for existing archived records.

```bash
cd personnelManagement
php artisan archive:backfill-timestamps
```

---

## Testing Steps

### Step 1: Start Your Database
Make sure your MySQL/MariaDB server is running.

### Step 2: Run Migration
```bash
cd personnelManagement
php artisan migrate
```

You should see:
```
Migration table created successfully.
Migrating: 2025_11_27_163929_add_archived_at_to_applications_table
Migrated:  2025_11_27_163929_add_archived_at_to_applications_table
```

### Step 3: Backfill Existing Archives (if any)
```bash
php artisan archive:backfill-timestamps
```

Output example:
```
Backfilling archived_at timestamps for existing archived applications...
Successfully backfilled 5 archived application(s).
```

### Step 4: View Scheduled Tasks
```bash
php artisan schedule:list
```

You should see:
```
  0 2 * * *  php artisan archive:cleanup ............... Next Due: 1 day from now
```

### Step 5: Test Manual Cleanup
```bash
php artisan archive:cleanup
```

Output example:
```
Starting archived applications cleanup...
Deleted 0 archived application(s) older than 30 days.
```

---

## Testing with Mock Data

### Option A: Test with Database Tinker

1. **Create a test archived record**:
```bash
php artisan tinker
```

Then run:
```php
$app = \App\Models\Application::where('is_archived', true)->first();
if ($app) {
    $app->archived_at = now()->subDays(35); // 35 days ago
    $app->save();
    echo "Test record created with archived_at 35 days ago\n";
}
```

2. **Run cleanup command**:
```bash
php artisan archive:cleanup
```

Expected output:
```
Starting archived applications cleanup...
Deleted 1 archived application(s) older than 30 days.
```

### Option B: Test Directly in MySQL

1. **Find archived applications**:
```sql
SELECT id, user_id, job_id, is_archived, archived_at, updated_at
FROM applications
WHERE is_archived = 1;
```

2. **Manually set an old archived_at date**:
```sql
UPDATE applications
SET archived_at = DATE_SUB(NOW(), INTERVAL 35 DAY)
WHERE id = 123;  -- Replace with actual ID
```

3. **Run cleanup**:
```bash
php artisan archive:cleanup
```

4. **Verify deletion**:
```sql
SELECT id FROM applications WHERE id = 123;
-- Should return no results
```

---

## Testing the Scheduler

The scheduler runs tasks automatically. Here's how to test it:

### Option 1: Test Schedule Manually
```bash
php artisan schedule:run
```

This runs all scheduled tasks that are due right now. If it's not 2:00 AM, `archive:cleanup` won't run.

### Option 2: Test at Specific Time

Temporarily change the schedule in `routes/console.php`:

```php
// Change from:
Schedule::command('archive:cleanup')->dailyAt('02:00');

// To (runs every minute for testing):
Schedule::command('archive:cleanup')->everyMinute();
```

Then run:
```bash
php artisan schedule:run
```

**Don't forget to change it back!**

### Option 3: Set Up Real Cron (Production)

On your server, add this to crontab:
```bash
crontab -e
```

Add this line:
```bash
* * * * * cd /path/to/Personnel-Management-Vills/personnelManagement && php artisan schedule:run >> /dev/null 2>&1
```

---

## Verification Checklist

- [ ] Migration ran successfully
- [ ] `archived_at` column exists in `applications` table
- [ ] Commands appear in `php artisan list`
- [ ] Schedule appears in `php artisan schedule:list`
- [ ] Manual `archive:cleanup` command works
- [ ] Backfill command works (if you have existing archived records)
- [ ] Archive page shows correct countdown timers

---

## How It Works in Production

1. **User archives an application** → `archived_at` is set to current timestamp
2. **Every day at 2:00 AM** → Cron runs `php artisan schedule:run`
3. **Laravel checks schedules** → Sees `archive:cleanup` should run
4. **Cleanup command executes** → Deletes records where `archived_at <= 30 days ago`
5. **Users see updated archive page** → Old records are gone

---

## Troubleshooting

### "No connection could be made to the database"
- Start your MySQL/MariaDB server
- Check `.env` file for correct database credentials

### "Command not found"
- Make sure you're in the `personnelManagement` directory
- Run `php artisan list` to see all commands

### "Schedule not running"
- Check if cron job is set up correctly
- Run `php artisan schedule:run` manually to test
- Check cron logs: `grep CRON /var/log/syslog` (Linux)

### "Records not being deleted"
- Check if `archived_at` is set: `SELECT id, archived_at FROM applications WHERE is_archived = 1`
- Verify date calculation: Run `php artisan tinker` then `echo now()->subDays(30);`
- Run cleanup manually: `php artisan archive:cleanup`

---

## Files Modified

1. **Database**:
   - `database/migrations/2025_11_27_163929_add_archived_at_to_applications_table.php`

2. **Commands**:
   - `app/Console/Commands/DeleteExpiredArchivedApplications.php`
   - `app/Console/Commands/BackfillArchivedAtTimestamps.php`

3. **Scheduler**:
   - `routes/console.php`

4. **Controllers**:
   - `app/Http/Controllers/ArchiveController.php` (line 12-21)
   - `app/Http/Controllers/StaffArchiveController.php` (lines 120, 265, 329, 337, 350)

5. **Model**:
   - `app/Models/Application.php` (line 26)

6. **View**:
   - `resources/views/admins/shared/archive.blade.php` (lines 55-61)
