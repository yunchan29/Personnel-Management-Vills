# Deployment Files Overview

This document explains all the deployment-related files created for your Personnel Management System.

---

## Files Created

### 1. `.github/workflows/deploy-to-hostinger.yml`
**Purpose**: GitHub Actions workflow for automated deployment

**What it does**:
- Runs when you push to `main` branch
- Builds your Laravel app (Composer + NPM)
- Deploys files to Hostinger via FTP
- Runs post-deployment commands via SSH
- Optimizes for production

**How to use**:
1. Set up GitHub Secrets (see below)
2. Push to main branch
3. Watch deployment in Actions tab

### 2. `personnelManagement/.env.hostinger`
**Purpose**: Template for production environment variables

**What it does**:
- Contains all required environment variables
- Includes security settings for production
- Has detailed comments and instructions

**How to use**:
1. Copy this file's content
2. Create `.env` file on Hostinger server
3. Fill in actual values (database, email, etc.)

**⚠️ IMPORTANT**: Never commit this with real credentials!

### 3. `DEPLOYMENT_GUIDE.md`
**Purpose**: Complete deployment documentation

**What it includes**:
- Detailed step-by-step instructions
- Prerequisites checklist
- GitHub and Hostinger setup
- Troubleshooting guide
- Security checklist
- Maintenance procedures

**When to use**: Reference when setting up deployment or troubleshooting

### 4. `HOSTINGER_QUICK_SETUP.md`
**Purpose**: Quick reference checklist for deployment

**What it includes**:
- Condensed step-by-step checklist
- Time estimates for each step
- Quick troubleshooting fixes
- Emergency commands

**When to use**: During actual deployment for quick reference

### 5. `personnelManagement/deploy.sh`
**Purpose**: Manual deployment script for SSH

**What it does**:
- Puts app in maintenance mode
- Pulls latest code (if using Git on server)
- Installs dependencies
- Builds assets
- Runs migrations
- Optimizes caches
- Sets permissions

**How to use**:
```bash
# On Hostinger server via SSH
cd public_html
bash deploy.sh
```

### 6. `personnelManagement/public/.htaccess` (Updated)
**Purpose**: Apache configuration with security enhancements

**What was added**:
- Force HTTPS redirect
- Security headers (XSS, clickjacking protection)
- Block access to .env file
- Browser caching rules
- Compression settings
- PHP configuration tweaks

**Note**: Automatically deployed with your app

---

## GitHub Secrets Required

Set these in: **GitHub Repository → Settings → Secrets and variables → Actions**

| Secret Name | Description | Where to Get |
|------------|-------------|--------------|
| `FTP_SERVER` | FTP hostname | Hostinger cPanel → FTP Accounts |
| `FTP_USERNAME` | FTP username | Hostinger cPanel → FTP Accounts |
| `FTP_PASSWORD` | FTP password | Hostinger cPanel → FTP Accounts |
| `FTP_SERVER_DIR` | Deploy directory | Usually `/public_html/` or `/public_html/app/` |
| `SSH_HOST` | SSH hostname | Your domain name |
| `SSH_USERNAME` | SSH username | Hostinger cPanel → SSH Access |
| `SSH_PASSWORD` | SSH password | Hostinger cPanel → SSH Access |
| `SSH_PORT` | SSH port | Usually `22` |

---

## Deployment Workflow

### First Time Setup

1. **Complete Hostinger Setup** (see HOSTINGER_QUICK_SETUP.md)
   - Create database
   - Configure PHP
   - Set document root
   - Get credentials

2. **Configure GitHub Secrets**
   - Add all 8 secrets above

3. **Create .env on Hostinger**
   - Use .env.hostinger as template
   - Fill in actual credentials

4. **Trigger First Deployment**
   - Push to main or use manual trigger
   - Watch GitHub Actions

5. **Run Initial Commands** (via SSH or cPanel)
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   ```

6. **Set Up Cron Job**
   - Configure in cPanel
   - For queue processing

### Ongoing Deployments

1. Make changes locally
2. Test thoroughly
3. Commit and push to main:
   ```bash
   git add .
   git commit -m "Your changes"
   git push origin main
   ```
4. GitHub Actions automatically deploys
5. Verify deployment succeeded

---

## Quick Commands Reference

### Clear Caches
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

### Maintenance Mode
```bash
# Enable
php artisan down --retry=60

# Disable
php artisan up
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Test Queue
```bash
php artisan queue:work --once
```

---

## Directory Structure on Hostinger

```
/home/yourusername/
└── public_html/                    # Your deployment directory
    ├── app/                       # Laravel app directory
    ├── bootstrap/
    │   └── cache/                 # Needs 755 permissions
    ├── config/
    ├── database/
    ├── public/                    # Document root (set in cPanel)
    │   ├── .htaccess             # Updated with security
    │   ├── index.php
    │   └── build/                # Built assets
    ├── resources/
    ├── routes/
    ├── storage/                   # Needs 755 permissions
    │   ├── app/
    │   ├── framework/
    │   └── logs/
    ├── vendor/                    # Composer dependencies
    ├── .env                       # Production config (create manually)
    ├── composer.json
    ├── package.json
    └── artisan
```

---

## Security Checklist

Before going live:

- [ ] `APP_DEBUG=false` in .env
- [ ] `APP_ENV=production` in .env
- [ ] Strong `APP_KEY` generated
- [ ] `.env` file has 600 permissions
- [ ] Database password is strong (16+ characters)
- [ ] Email credentials are secure (use app password)
- [ ] SSL certificate installed
- [ ] HTTPS redirect working
- [ ] `SESSION_ENCRYPT=true`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] All GitHub secrets are set
- [ ] `.env` is in `.gitignore`
- [ ] No credentials in git history

---

## Troubleshooting

### GitHub Actions Fails

1. Check secrets are set correctly
2. Verify FTP credentials
3. Check FTP_SERVER_DIR path
4. Review Actions logs

### Application Shows 500 Error

1. Check `.env` exists on server
2. Run `php artisan config:clear`
3. Check storage permissions
4. Review `storage/logs/laravel.log`

### Database Connection Error

1. Verify database credentials in `.env`
2. Check database exists in cPanel
3. Try `DB_HOST=localhost` instead of `127.0.0.1`

### Assets Not Loading

1. Check `APP_URL` in `.env`
2. Run `php artisan config:clear`
3. Verify `public/build` directory exists

### Emails Not Sending

1. Verify SMTP credentials
2. Check email account exists
3. Test with `php artisan tinker`:
   ```php
   Mail::raw('Test', function($m) {
       $m->to('test@example.com')->subject('Test');
   });
   ```

---

## Support Resources

- **Detailed Guide**: [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)
- **Quick Setup**: [HOSTINGER_QUICK_SETUP.md](HOSTINGER_QUICK_SETUP.md)
- **Laravel Docs**: https://laravel.com/docs/11.x/deployment
- **Hostinger Tutorials**: https://www.hostinger.com/tutorials/
- **GitHub Actions**: https://docs.github.com/en/actions

---

## Next Steps

1. Read [HOSTINGER_QUICK_SETUP.md](HOSTINGER_QUICK_SETUP.md)
2. Follow Part 1: Hostinger cPanel Setup
3. Follow Part 2: GitHub Repository Setup
4. Follow Part 3-7 for deployment
5. Test your application
6. Start using automated deployments!

---

**Questions?** Check [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) for detailed answers.
