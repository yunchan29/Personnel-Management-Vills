# 🚀 Production Deployment Checklist

## Pre-Deployment

### 1. Database
- [ ] Run all migrations
  ```bash
  php artisan migrate --force
  ```
- [ ] Run login attempts migration
  ```bash
  php artisan migrate --path=/database/migrations/2025_11_07_030031_create_login_attempts_table.php
  ```
- [ ] Verify database connection works
- [ ] Create database backup

### 2. Environment Configuration
- [ ] Copy `.env.production.example` to `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false` ⚠️ CRITICAL
- [ ] Generate application key: `php artisan key:generate`
- [ ] Configure database credentials
- [ ] Set `SESSION_ENCRYPT=true` ⚠️ CRITICAL
- [ ] Set `SESSION_SECURE_COOKIE=true` ⚠️ CRITICAL
- [ ] Set `APP_URL` to your domain (with https://)
- [ ] Configure mail settings

### 3. Dependencies
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm install && npm run build`

### 4. Cache & Optimization
- [ ] Clear existing cache: `php artisan cache:clear`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`

### 5. Storage & Permissions
- [ ] Create storage link: `php artisan storage:link`
- [ ] Set permissions:
  ```bash
  chmod -R 755 storage
  chmod -R 755 bootstrap/cache
  chmod 600 .env
  ```

### 6. Security Verification
- [ ] Verify APP_DEBUG is false
- [ ] Verify SESSION_ENCRYPT is true
- [ ] Verify SESSION_SECURE_COOKIE is true
- [ ] Verify strong database password is set
- [ ] Verify .env file is not in version control
- [ ] Verify .env file permissions are 600

## Deployment

### 7. Web Server Configuration
- [ ] Point document root to `/public` directory
- [ ] Configure SSL certificate (Let's Encrypt recommended)
- [ ] Force HTTPS redirect
- [ ] Set proper PHP settings:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  memory_limit = 256M
  max_execution_time = 300
  ```

### 8. Scheduled Tasks (Cron)
- [ ] Add cron job for Laravel scheduler:
  ```cron
  * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
  ```

### 9. Queue Workers (if using queues)
- [ ] Set up queue worker with supervisor
- [ ] Configure worker restart on deployment

### 10. Firewall
- [ ] Allow port 80 (HTTP)
- [ ] Allow port 443 (HTTPS)
- [ ] Restrict database port (3306) to localhost only
- [ ] Restrict Redis port (6379) to localhost only

## Post-Deployment

### 11. Testing
- [ ] Test user registration
- [ ] Test login (successful and failed attempts)
- [ ] Test account lockout (5 failed attempts)
- [ ] Test password reset
- [ ] Test file uploads (resume, profile picture, documents)
- [ ] Test file downloads (verify authentication required)
- [ ] Test role-based access control
- [ ] Test email sending

### 12. Security Testing
- [ ] Verify error pages don't leak information
- [ ] Test that direct file URLs require authentication
- [ ] Verify CSRF protection on all forms
- [ ] Test rate limiting on login/registration
- [ ] Verify SQL injection protection
- [ ] Test XSS protection

### 13. Monitoring Setup
- [ ] Set up application monitoring (optional: Sentry, Bugsnag)
- [ ] Configure log rotation
- [ ] Set up uptime monitoring
- [ ] Configure database backup schedule
- [ ] Set up alerts for critical errors

### 14. Documentation
- [ ] Document deployment process
- [ ] Update administrator credentials
- [ ] Document backup/restore procedures
- [ ] Create incident response plan

## Ongoing Maintenance

### Daily
- [ ] Monitor error logs: `storage/logs/laravel.log`
- [ ] Check login attempt logs for suspicious activity
- [ ] Review unauthorized file access attempts

### Weekly
- [ ] Review security logs
- [ ] Check disk space
- [ ] Verify backups are running

### Monthly
- [ ] Update Laravel: `composer update`
- [ ] Update NPM packages: `npm update`
- [ ] Review and rotate logs
- [ ] Test backup restoration
- [ ] Security audit review

## Rollback Plan

In case of issues:

### Immediate Rollback
1. Restore previous code version
2. Restore database from backup (if needed)
3. Clear all caches: `php artisan cache:clear`
4. Re-cache config: `php artisan config:cache`

### Database Rollback
```bash
# If migration causes issues
php artisan migrate:rollback --step=1
```

## Emergency Contacts

- **System Administrator:** _________________
- **Database Administrator:** _________________
- **Lead Developer:** _________________
- **Security Contact:** _________________

## Deployment Log

| Date | Version | Deployed By | Notes |
|------|---------|-------------|-------|
|      |         |             |       |

---

## Quick Commands Reference

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Check application status
php artisan about

# Put site in maintenance mode
php artisan down --secret="your-secret-token"

# Bring site back up
php artisan up
```

---

**Last Updated:** 2025-11-07
**Version:** 1.0
