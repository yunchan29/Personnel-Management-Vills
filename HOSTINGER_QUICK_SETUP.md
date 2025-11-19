# Hostinger Quick Setup Guide

This is a condensed, step-by-step checklist for deploying to Hostinger. For detailed instructions, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).

---

## Prerequisites Checklist

- [ ] GitHub repository is up to date
- [ ] Hostinger hosting account active
- [ ] Domain name configured
- [ ] SSL certificate ready (AutoSSL/Let's Encrypt)

---

## Part 1: Hostinger cPanel Setup (30 minutes)

### Step 1: Create MySQL Database

1. Login to **cPanel**
2. Go to **MySQL Databases**
3. Create database: `u123456789_personnel`
4. Create user: `u123456789_admin`
5. Set strong password
6. Add user to database (ALL PRIVILEGES)
7. **Write down credentials**:
   ```
   Database: ________________
   Username: ________________
   Password: ________________
   ```

### Step 2: Configure PHP

1. Go to **Select PHP Version**
2. Select **PHP 8.2** or higher
3. Enable extensions:
   - [x] mbstring
   - [x] xml
   - [x] pdo_mysql
   - [x] curl
   - [x] zip
   - [x] gd
   - [x] fileinfo
   - [x] json
   - [x] bcmath

### Step 3: Set Document Root

**For Main Domain:**
1. Go to **Domains**
2. Edit main domain
3. Set document root: `/public_html/public`

**For Subdomain (recommended):**
1. Create subdomain: `app.yourdomain.com`
2. Set document root: `/public_html/app/public`

### Step 4: Get FTP/SSH Credentials

**FTP:**
1. Go to **FTP Accounts**
2. Note credentials:
   ```
   Server: ________________
   Username: ________________
   Password: ________________
   ```

**SSH (if available):**
1. Check if SSH is enabled
2. Note credentials:
   ```
   Host: ________________
   Port: 22
   Username: ________________
   Password: ________________
   ```

### Step 5: Create Email Account (for notifications)

1. Go to **Email Accounts**
2. Create: `noreply@yourdomain.com`
3. Set strong password
4. **Write down credentials**:
   ```
   Email: ________________
   Password: ________________
   SMTP Host: smtp.hostinger.com
   SMTP Port: 587
   ```

---

## Part 2: GitHub Repository Setup (15 minutes)

### Step 1: Add GitHub Secrets

Go to repository → **Settings** → **Secrets and variables** → **Actions**

Add these secrets:

| Secret Name | Value |
|------------|-------|
| `FTP_SERVER` | `ftp.yourdomain.com` |
| `FTP_USERNAME` | Your FTP username |
| `FTP_PASSWORD` | Your FTP password |
| `FTP_SERVER_DIR` | `/public_html/` or `/public_html/app/` |
| `SSH_HOST` | Your domain |
| `SSH_USERNAME` | Your SSH username |
| `SSH_PASSWORD` | Your SSH password |
| `SSH_PORT` | `22` |

**Note**: If no SSH, you'll run commands manually.

### Step 2: Verify .gitignore

Ensure `.env` is in `.gitignore`:

```bash
cd D:\Joel\Webpage\Personnel-Management-Vills
cat personnelManagement/.gitignore | grep .env
```

Should show `.env` listed.

---

## Part 3: Create .env on Hostinger (10 minutes)

### Option A: Via File Manager

1. Login to **cPanel**
2. Go to **File Manager**
3. Navigate to deployment directory
4. Click **+ File**
5. Name it `.env`
6. Edit and paste (replace with your values):

```env
APP_NAME="Personnel Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_personnel
DB_USERNAME=u123456789_admin
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Personnel Management System"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

LOG_CHANNEL=stack
LOG_LEVEL=error
```

7. **Save** the file

### Option B: Via SSH

```bash
ssh username@yourdomain.com
cd public_html  # or public_html/app
nano .env
# Paste content above
# Press Ctrl+X, then Y, then Enter
```

---

## Part 4: First Deployment (20 minutes)

### Step 1: Run Initial Setup (SSH Required)

If you have SSH access:

```bash
# Connect to server
ssh username@yourdomain.com

# Navigate to directory
cd public_html  # or public_html/app

# You'll do this AFTER first GitHub Actions deployment
# For now, just note these commands
```

### Step 2: Deploy from GitHub

**Option A: Automatic (push to main)**
```bash
# On your local machine
cd D:\Joel\Webpage\Personnel-Management-Vills
git add .
git commit -m "Initial Hostinger deployment"
git push origin main
```

**Option B: Manual trigger**
1. Go to GitHub repository
2. Click **Actions** tab
3. Select **Deploy to Hostinger**
4. Click **Run workflow** → **Run workflow**

### Step 3: Monitor Deployment

1. Watch GitHub Actions progress
2. Wait for completion (5-10 minutes)
3. Check for errors

### Step 4: Run Post-Deployment Commands

**Via SSH:**
```bash
cd public_html  # or your directory

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 600 .env
```

**Via cPanel Terminal:**
- Same commands as above, run one by one

**No SSH? Manual Setup:**
- You may need Hostinger support to run these commands

---

## Part 5: Configure Cron Job (5 minutes)

### In cPanel → Cron Jobs

**Add new cron job:**

- **Common Settings**: Once Per Minute (* * * * *)
- **Command**:
  ```
  /usr/bin/php /home/yourusername/public_html/artisan schedule:run >> /dev/null 2>&1
  ```

Replace:
- `yourusername` with actual cPanel username
- `public_html` with actual path

**Or manually set:**
- Minute: `*`
- Hour: `*`
- Day: `*`
- Month: `*`
- Weekday: `*`

---

## Part 6: Enable SSL & HTTPS (5 minutes)

### Step 1: Install SSL

1. Go to **SSL/TLS Status**
2. Click **Run AutoSSL**
3. Wait for completion

### Step 2: Force HTTPS

Already configured in `.htaccess` - will automatically redirect.

---

## Part 7: Test Your Application (10 minutes)

### Checklist:

1. **Visit your site**: `https://yourdomain.com`
   - [ ] Login page loads
   - [ ] No errors displayed

2. **Test Registration**:
   - [ ] Register new account
   - [ ] Receive email verification

3. **Test Login**:
   - [ ] Login with credentials
   - [ ] Dashboard loads

4. **Test Email**:
   - [ ] Password reset works
   - [ ] Emails are received

5. **Check Logs**:
   - Via SSH: `tail -f storage/logs/laravel.log`
   - Via File Manager: Check `storage/logs/laravel.log`
   - [ ] No critical errors

6. **Test File Uploads**:
   - [ ] Profile picture upload
   - [ ] Resume upload
   - [ ] Files are accessible

---

## Troubleshooting Quick Fixes

### Issue: 500 Error

```bash
# Via SSH
php artisan config:clear
chmod -R 755 storage bootstrap/cache
```

### Issue: Database Connection Error

Check `.env`:
- `DB_HOST=localhost` (not 127.0.0.1)
- Verify database name, username, password
- Check database exists in cPanel

### Issue: Assets Not Loading

```bash
php artisan config:clear
php artisan cache:clear
```

Verify `APP_URL` in `.env` matches your domain.

### Issue: Emails Not Sending

1. Check SMTP credentials in `.env`
2. Verify email account exists
3. Check `storage/logs/laravel.log`

### Issue: Queue Not Processing

Verify cron job is set up correctly:
```bash
# Test manually
php artisan queue:work --once
```

---

## Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] Registration works
- [ ] Login works
- [ ] Email notifications work
- [ ] File uploads work
- [ ] SSL certificate active (https://)
- [ ] Cron job configured
- [ ] Error logging working
- [ ] Database connected
- [ ] Storage symlink created

---

## Daily Operations

### View Application Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear All Caches
```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

### Check Queue Status
```bash
php artisan queue:work --once
```

### Update Application
Just push to GitHub main branch - auto-deploys!

---

## Emergency Commands

### Put in Maintenance Mode
```bash
php artisan down
```

### Bring Back Online
```bash
php artisan up
```

### Rollback Last Migration
```bash
php artisan migrate:rollback
```

---

## Support Contacts

- **Hostinger Support**: Via cPanel chat or ticket
- **Laravel Docs**: https://laravel.com/docs
- **Repository Issues**: Check GitHub Actions logs

---

## Summary Timeline

| Task | Time | Status |
|------|------|--------|
| cPanel Setup | 30 min | ⬜ |
| GitHub Secrets | 15 min | ⬜ |
| Create .env | 10 min | ⬜ |
| Deploy | 20 min | ⬜ |
| Cron Setup | 5 min | ⬜ |
| SSL Setup | 5 min | ⬜ |
| Testing | 10 min | ⬜ |
| **Total** | **~95 min** | |

---

**Ready to deploy? Start with Part 1!**

For detailed explanations, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).
