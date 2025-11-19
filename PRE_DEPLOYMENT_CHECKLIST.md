# Pre-Deployment Checklist

Complete this checklist before deploying to Hostinger to ensure a smooth deployment.

---

## Critical Security Tasks

### 1. Remove Sensitive Data from Repository

- [ ] Verify `.env` is in `.gitignore`
- [ ] Check no credentials in any committed files
- [ ] Search for hardcoded passwords: `git grep -i password`
- [ ] Search for API keys: `git grep -i api_key`
- [ ] Search for secrets: `git grep -i secret`

### 2. Review Environment Files

- [ ] `.env` is NOT tracked by git
- [ ] `.env.example` has no real credentials
- [ ] `.env.hostinger` template is ready
- [ ] All sensitive values use placeholders

### 3. Email Credentials Security

**⚠️ CRITICAL**: Your current `.env` has exposed Gmail credentials!

- [ ] Remove Gmail credentials from `.env`
- [ ] Create dedicated app password for production
- [ ] Never commit `.env` with real credentials
- [ ] Use environment variables for sensitive data

---

## Code Quality Checks

### 1. Test Locally

- [ ] Run: `php artisan test` (if tests exist)
- [ ] Test registration flow
- [ ] Test login/logout
- [ ] Test file uploads
- [ ] Test leave forms
- [ ] Test email notifications
- [ ] Test job applications

### 2. Build Verification

- [ ] Run: `npm run build` - succeeds without errors
- [ ] Run: `composer install --optimize-autoloader --no-dev` - succeeds
- [ ] Check no TypeScript/JavaScript errors
- [ ] Verify all assets compile

### 3. Database

- [ ] All migrations run successfully: `php artisan migrate:status`
- [ ] No pending migrations
- [ ] Foreign keys properly set up
- [ ] Indexes created for performance

### 4. Configuration

- [ ] Review `config/app.php` settings
- [ ] Review `config/database.php` settings
- [ ] Review `config/mail.php` settings
- [ ] Review `config/session.php` settings

---

## Files & Permissions

### 1. .gitignore Verification

Run this command to verify:
```bash
cd D:\Joel\Webpage\Personnel-Management-Vills\personnelManagement
cat .gitignore
```

Must include:
- [ ] `.env`
- [ ] `.env.backup`
- [ ] `.env.production`
- [ ] `/node_modules`
- [ ] `/public/build`
- [ ] `/vendor`
- [ ] `/storage/*.key`

### 2. Required Directories Exist

- [ ] `storage/app/public`
- [ ] `storage/framework/cache`
- [ ] `storage/framework/sessions`
- [ ] `storage/framework/views`
- [ ] `storage/logs`
- [ ] `bootstrap/cache`

---

## Hostinger Requirements

### 1. Account Information Gathered

- [ ] Hostinger hosting plan active
- [ ] Domain name ready
- [ ] cPanel access credentials
- [ ] FTP credentials
- [ ] SSH credentials (if available)

### 2. Database Prepared

- [ ] Database name decided
- [ ] Database username decided
- [ ] Strong database password generated (16+ chars)
- [ ] Credentials documented securely

### 3. Email Setup

- [ ] Email address created (e.g., noreply@yourdomain.com)
- [ ] Email password set
- [ ] SMTP settings noted
- [ ] Test email sent

---

## GitHub Setup

### 1. Repository Ready

- [ ] All code pushed to GitHub
- [ ] Repository is private (recommended for production apps)
- [ ] Latest code is on `main` branch
- [ ] No merge conflicts

### 2. Secrets Prepared

Have these values ready to add as GitHub Secrets:

- [ ] FTP_SERVER: `________________`
- [ ] FTP_USERNAME: `________________`
- [ ] FTP_PASSWORD: `________________`
- [ ] FTP_SERVER_DIR: `________________`
- [ ] SSH_HOST: `________________`
- [ ] SSH_USERNAME: `________________`
- [ ] SSH_PASSWORD: `________________`
- [ ] SSH_PORT: `________________`

---

## Production Configuration

### 1. .env.hostinger Template Ready

Review and customize `.env.hostinger`:

- [ ] `APP_NAME` - correct application name
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` - your actual domain
- [ ] `DB_*` - placeholders for database
- [ ] `MAIL_*` - placeholders for email
- [ ] All security settings enabled

### 2. Security Settings Verified

In `.env.hostinger` template:

- [ ] `SESSION_ENCRYPT=true`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_HTTP_ONLY=true`
- [ ] `LOG_LEVEL=error` (not debug)

---

## Application Features Check

### 1. Core Features Working

- [ ] User registration
- [ ] Email verification
- [ ] Login/logout
- [ ] Password reset
- [ ] Profile management
- [ ] File uploads (resumes, photos)

### 2. HR Features Working

- [ ] Job posting creation
- [ ] Application submission
- [ ] Leave form management
- [ ] Employee dashboard
- [ ] Notifications system
- [ ] Archive system

### 3. Admin Features Working

- [ ] HR Admin dashboard
- [ ] HR Staff dashboard
- [ ] Application management
- [ ] Interview scheduling
- [ ] Training schedules
- [ ] Report generation

---

## Performance & Optimization

### 1. Laravel Optimization

- [ ] Run: `php artisan config:cache`
- [ ] Run: `php artisan route:cache`
- [ ] Run: `php artisan view:cache`
- [ ] No errors from caching commands

### 2. Asset Optimization

- [ ] Run: `npm run build` produces optimized files
- [ ] Check `public/build/manifest.json` exists
- [ ] CSS files minified
- [ ] JS files minified

### 3. Database Optimization

- [ ] Indexes created on frequently queried columns
- [ ] Foreign keys properly defined
- [ ] No N+1 query issues (check with debugbar if available)

---

## Documentation Review

### 1. Deployment Docs Created

- [ ] `DEPLOYMENT_GUIDE.md` reviewed
- [ ] `HOSTINGER_QUICK_SETUP.md` reviewed
- [ ] `DEPLOYMENT_FILES_README.md` reviewed
- [ ] All steps understood

### 2. GitHub Actions Workflow

- [ ] `.github/workflows/deploy-to-hostinger.yml` reviewed
- [ ] Deployment steps understood
- [ ] Exclude patterns correct
- [ ] Post-deployment commands appropriate

---

## Final Verification

### 1. Local Environment Check

```bash
# Run these commands and verify success
cd D:\Joel\Webpage\Personnel-Management-Vills\personnelManagement

# Test build
npm run build

# Test composer
composer install --optimize-autoloader --no-dev

# Check migrations
php artisan migrate:status

# Clear caches
php artisan config:clear
php artisan cache:clear
```

All commands should run without errors:
- [ ] All commands successful

### 2. Git Status Clean

```bash
git status
```

- [ ] No uncommitted changes (or commit them)
- [ ] `.env` NOT showing in git status
- [ ] All important files committed

### 3. GitHub Actions Workflow Valid

- [ ] YAML syntax is valid (GitHub will show errors)
- [ ] All paths are correct
- [ ] Environment is 'ubuntu-latest'

---

## Hostinger-Specific Checks

### 1. PHP Requirements

Required PHP version: **8.2 or higher**

- [ ] Hostinger plan supports PHP 8.2+
- [ ] Required extensions available

### 2. Server Resources

Recommended minimum:
- [ ] 1 GB RAM
- [ ] 10 GB disk space
- [ ] MySQL 5.7+ or MySQL 8.0+

### 3. SSL Certificate

- [ ] Plan includes SSL certificate
- [ ] Or Let's Encrypt available

---

## Backup Plan

### 1. Local Backup

Before deploying:

- [ ] Database backup saved locally
- [ ] Files backup saved locally
- [ ] `.env` file backed up separately

### 2. Rollback Plan

If deployment fails:

- [ ] Know how to revert git commit
- [ ] Know how to disable GitHub Actions
- [ ] Know how to access Hostinger File Manager

---

## Post-Deployment Preparation

### 1. Monitoring Setup

- [ ] Know how to access `storage/logs/laravel.log`
- [ ] Know how to use cPanel Error Logs
- [ ] Uptime monitoring service ready (optional)

### 2. Cron Job Configuration

- [ ] Command prepared: `/usr/bin/php /path/to/artisan schedule:run`
- [ ] Schedule understood: `* * * * *`

### 3. Testing Plan

After deployment:

- [ ] Test registration
- [ ] Test login
- [ ] Test email
- [ ] Test file upload
- [ ] Test each major feature

---

## Security Pre-Flight

### 1. Credential Review

- [ ] No credentials in repository
- [ ] All passwords are strong (16+ characters)
- [ ] Email app passwords used (not account passwords)
- [ ] Database password is unique

### 2. Application Security

- [ ] `APP_DEBUG=false` for production
- [ ] HTTPS will be enforced
- [ ] `.env` file protected via `.htaccess`
- [ ] Session security enabled

### 3. File Security

- [ ] `.htaccess` updated with security headers
- [ ] Directory listing disabled
- [ ] Sensitive files blocked

---

## Communication

### 1. Stakeholders Informed

- [ ] Users notified of potential downtime
- [ ] Maintenance window scheduled (if needed)
- [ ] Support team ready

### 2. Deployment Timeline

- [ ] Estimated deployment time: ~90 minutes
- [ ] Best time to deploy identified
- [ ] Backup contact if issues arise

---

## Final Go/No-Go Decision

Review all sections above. All critical items must be checked.

### Critical Items (Must Complete):

1. **Security**
   - [ ] No credentials in git
   - [ ] `.env` in `.gitignore`
   - [ ] Production settings configured

2. **Testing**
   - [ ] Application works locally
   - [ ] Build succeeds
   - [ ] No critical bugs

3. **Hostinger**
   - [ ] Database created
   - [ ] Domain configured
   - [ ] Credentials available

4. **GitHub**
   - [ ] All secrets set
   - [ ] Code pushed
   - [ ] Workflow file ready

### Ready to Deploy?

- [ ] **ALL critical items checked**
- [ ] **Deployment docs read**
- [ ] **Backup created**
- [ ] **Ready to proceed**

---

## If Everything is Checked

You're ready to deploy!

**Next steps:**
1. Follow [HOSTINGER_QUICK_SETUP.md](HOSTINGER_QUICK_SETUP.md)
2. Start with Part 1: Hostinger cPanel Setup
3. Proceed through all parts systematically

**Good luck with your deployment!** 🚀

---

**Date Completed**: _______________

**Deployment By**: _______________

**Notes**: _______________
