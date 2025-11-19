# Personnel Management System - Hostinger Deployment Guide

## Overview

This guide will walk you through deploying your Laravel Personnel Management System to Hostinger using GitHub Actions for automated deployments.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [GitHub Setup](#github-setup)
3. [Hostinger Setup](#hostinger-setup)
4. [GitHub Actions Secrets Configuration](#github-actions-secrets-configuration)
5. [First Deployment](#first-deployment)
6. [Post-Deployment Configuration](#post-deployment-configuration)
7. [Troubleshooting](#troubleshooting)
8. [Maintenance](#maintenance)

---

## Prerequisites

### On Your Local Machine
- Git installed and configured
- GitHub account with repository access
- All code committed and pushed to GitHub

### On Hostinger
- Active hosting plan (Premium or Business recommended)
- Access to:
  - cPanel
  - FTP credentials
  - SSH access (if available)
  - MySQL database

---

## GitHub Setup

### Step 1: Push Your Code to GitHub

```bash
# Navigate to your project
cd D:\Joel\Webpage\Personnel-Management-Vills

# Add all files (except .env which is already in .gitignore)
git add .

# Commit your changes
git commit -m "Prepare for Hostinger deployment"

# Push to GitHub
git push origin main
```

### Step 2: Verify .gitignore

Ensure your `.gitignore` includes:
```
.env
.env.backup
.env.production
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
```

**IMPORTANT**: Never commit `.env` file with credentials to GitHub!

---

## Hostinger Setup

### Step 1: Create MySQL Database

1. Login to **Hostinger cPanel**
2. Go to **MySQL Databases**
3. Create new database:
   - Database name: `u123456789_personnel` (example)
   - Username: `u123456789_admin` (example)
   - Password: Generate strong password
4. **Save these credentials** - you'll need them later

### Step 2: Get FTP Credentials

1. In cPanel, go to **FTP Accounts**
2. Use existing FTP account or create new one
3. Note down:
   - FTP Server: `ftp.yourdomain.com`
   - FTP Username: Your username
   - FTP Password: Your password
   - FTP Port: Usually 21

### Step 3: Determine Server Directory

Your files should be deployed to one of these locations:
- **Main domain**: `/public_html/`
- **Subdomain**: `/public_html/subdomain/`
- **Addon domain**: `/public_html/addondomain/`

**IMPORTANT**: You'll need to point the document root to the `/public` folder.

### Step 4: Configure Document Root

#### Option A: Main Domain
1. In cPanel, go to **Domains**
2. Edit your domain settings
3. Change document root to: `/public_html/public`

#### Option B: Subdomain
1. Create subdomain: `app.yourdomain.com`
2. Set document root to: `/public_html/app/public`
3. Deploy files to: `/public_html/app/`

### Step 5: PHP Configuration

1. In cPanel, go to **Select PHP Version**
2. Select **PHP 8.2 or higher**
3. Enable extensions:
   - [x] mbstring
   - [x] xml
   - [x] ctype
   - [x] json
   - [x] bcmath
   - [x] pdo_mysql
   - [x] zip
   - [x] curl
   - [x] gd
   - [x] fileinfo

---

## GitHub Actions Secrets Configuration

### Required Secrets

Go to your GitHub repository:
1. Click **Settings**
2. Click **Secrets and variables** > **Actions**
3. Click **New repository secret**

Add these secrets:

| Secret Name | Description | Example Value |
|------------|-------------|---------------|
| `FTP_SERVER` | Hostinger FTP server | `ftp.yourdomain.com` |
| `FTP_USERNAME` | FTP username | `username@yourdomain.com` |
| `FTP_PASSWORD` | FTP password | `your_ftp_password` |
| `FTP_SERVER_DIR` | Deployment directory | `/public_html/` or `/public_html/app/` |
| `SSH_HOST` | SSH host (if available) | `yourdomain.com` |
| `SSH_USERNAME` | SSH username | `your_ssh_user` |
| `SSH_PASSWORD` | SSH password | `your_ssh_password` |
| `SSH_PORT` | SSH port | `22` |

### How to Add Secrets

For each secret:
1. Click **New repository secret**
2. Enter **Name** (e.g., `FTP_SERVER`)
3. Enter **Value** (e.g., `ftp.yourdomain.com`)
4. Click **Add secret**

**Note**: If SSH is not available on your Hostinger plan, you'll need to manually run post-deployment commands.

---

## First Deployment

### Step 1: Manual .env Setup on Hostinger

Before running GitHub Actions, create `.env` file on Hostinger:

1. Login to **Hostinger File Manager**
2. Navigate to your deployment directory
3. Create file named `.env`
4. Copy contents from `.env.hostinger` template
5. Fill in your actual values:

```env
APP_NAME="Personnel Management System"
APP_ENV=production
APP_KEY=  # Leave empty for now
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_personnel
DB_USERNAME=u123456789_admin
DB_PASSWORD=your_actual_db_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Personnel Management System"

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
CACHE_STORE=database
QUEUE_CONNECTION=database
```

6. Save the file
7. Set permissions: `chmod 600 .env` (if SSH available)

### Step 2: Run Initial Setup Commands

If you have **SSH access**, connect and run:

```bash
# Navigate to your application
cd /home/yourusername/public_html

# Install Composer dependencies (if not using GitHub Actions)
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

If **NO SSH access**, you can use cPanel's **Terminal** or run commands via web.

### Step 3: Trigger GitHub Actions Deployment

1. Push any change to `main` branch:
```bash
git commit --allow-empty -m "Trigger deployment"
git push origin main
```

2. Or manually trigger from GitHub:
   - Go to **Actions** tab
   - Select **Deploy to Hostinger**
   - Click **Run workflow**

### Step 4: Monitor Deployment

1. Go to **Actions** tab in GitHub
2. Watch the deployment progress
3. Check for errors

---

## Post-Deployment Configuration

### 1. Verify Installation

Visit: `https://yourdomain.com`

You should see your application login page.

### 2. Set Up Cron Jobs (Required for Queue Processing)

1. In cPanel, go to **Cron Jobs**
2. Add new cron job:
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**:
   ```bash
   /usr/bin/php /home/yourusername/public_html/artisan schedule:run >> /dev/null 2>&1
   ```

This enables:
- Email queue processing
- Leave notification jobs
- Scheduled tasks

### 3. Test Email Functionality

1. Register a test account
2. Verify email is received
3. Test password reset

### 4. Configure SSL Certificate

1. In cPanel, go to **SSL/TLS Status**
2. Enable AutoSSL or install Let's Encrypt
3. Force HTTPS redirect

### 5. Security Headers

Add to `.htaccess` in `/public`:

```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Solution**:
1. Check `.env` file exists and has correct values
2. Run `php artisan config:clear`
3. Check PHP version is 8.2+
4. Check file permissions: `chmod -R 755 storage bootstrap/cache`
5. Check error logs in `storage/logs/laravel.log`

### Issue: Database Connection Failed

**Solution**:
1. Verify database credentials in `.env`
2. Check database exists in cPanel
3. Verify user has permissions
4. Try `DB_HOST=localhost` or `DB_HOST=127.0.0.1`

### Issue: Assets Not Loading

**Solution**:
1. Verify `npm run build` completed in GitHub Actions
2. Check `public/build` directory exists
3. Verify `APP_URL` in `.env` is correct

### Issue: Emails Not Sending

**Solution**:
1. Test SMTP credentials
2. Check queue is running
3. Verify `MAIL_FROM_ADDRESS` matches domain
4. Check `storage/logs/laravel.log`

### Issue: Storage Symlink Broken

**Solution**:
```bash
rm public/storage
php artisan storage:link
```

### Issue: GitHub Actions Deployment Fails

**Solution**:
1. Check all secrets are set correctly
2. Verify FTP credentials
3. Check FTP_SERVER_DIR path is correct
4. Review GitHub Actions logs

---

## Maintenance

### Updating Your Application

1. Make changes locally
2. Test thoroughly
3. Commit and push to GitHub:
```bash
git add .
git commit -m "Your update message"
git push origin main
```
4. GitHub Actions will automatically deploy

### Manual Deployment

If you need to manually deploy:

1. Build locally:
```bash
cd personnelManagement
composer install --optimize-autoloader --no-dev
npm run build
```

2. Upload via FTP:
   - Upload all files except `.env`
   - Don't overwrite `.env` on server

3. Run artisan commands via SSH:
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup Strategy

1. **Database Backups**:
   - Use cPanel backup tools
   - Or set up automated backups

2. **File Backups**:
   - Backup `storage/app` directory
   - Backup `.env` file

### Monitoring

1. Check `storage/logs/laravel.log` regularly
2. Monitor application performance
3. Set up uptime monitoring (e.g., UptimeRobot)

---

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY` generated
- [ ] `.env` file permissions set to 600
- [ ] Database password is strong
- [ ] SSL certificate installed
- [ ] HTTPS enforced
- [ ] File upload limits configured
- [ ] Firewall rules configured
- [ ] Regular backups enabled
- [ ] Error logging configured
- [ ] Session security enabled (`SESSION_ENCRYPT=true`)

---

## Support Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Hostinger Support**: https://www.hostinger.com/tutorials/
- **GitHub Actions Docs**: https://docs.github.com/en/actions

---

## Quick Reference Commands

### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Run Migrations
```bash
php artisan migrate --force
```

### Queue Management
```bash
# Check queue status
php artisan queue:work --once

# Process all pending jobs
php artisan queue:work --stop-when-empty
```

---

**Last Updated**: November 2025
