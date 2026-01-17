# Hostinger Deployment Checklist

Use this checklist to ensure a smooth deployment.

## Pre-Deployment (Local)

### Build & Optimize
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm ci`
- [ ] Run `npm run build`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Generate `APP_KEY` with `php artisan key:generate --show`
- [ ] Save the `APP_KEY` for `.env` file

### Prepare Files
- [ ] Create `.env.production` with Hostinger settings
- [ ] Verify all sensitive data is in `.env` (not hardcoded)
- [ ] Check `.gitignore` excludes sensitive files
- [ ] Create `.htaccess` for root directory

## Hostinger Setup

### Database
- [ ] Create MySQL database in cPanel
- [ ] Create database user
- [ ] Assign user to database with ALL PRIVILEGES
- [ ] Save database credentials (host, name, user, password)
- [ ] Note: Database name might be prefixed (e.g., `username_dbname`)

### FTP/SFTP Access
- [ ] Get FTP/SFTP credentials from Hostinger
- [ ] Test connection with FTP client
- [ ] Identify correct directory (`public_html/` for main domain)

## File Upload

### Files to Upload
- [ ] All application files (app/, bootstrap/, config/, etc.)
- [ ] `vendor/` folder (or install via SSH)
- [ ] `public/` folder with built assets
- [ ] `storage/` folder structure
- [ ] `database/` folder (migrations, seeders)
- [ ] `routes/` folder
- [ ] `resources/` folder
- [ ] Root `.htaccess` file

### Files to EXCLUDE
- [ ] `.env` (create new on server)
- [ ] `.env.example` (optional)
- [ ] `node_modules/` (not needed)
- [ ] `.git/` (not needed)
- [ ] `tests/` (optional)
- [ ] `.vercel/` (Vercel-specific)
- [ ] `vercel.json` (Vercel-specific)

## Server Configuration

### File Permissions
- [ ] Set `storage/` to 755
- [ ] Set `bootstrap/cache/` to 755
- [ ] Set `storage/framework/` to 775
- [ ] Set `storage/logs/` to 775
- [ ] Set `storage/app/` to 755

### Environment File
- [ ] Create `.env` file in `public_html/`
- [ ] Add `APP_KEY` (from local generation)
- [ ] Add database credentials
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_URL=https://yourdomain.com`
- [ ] Configure cache/session drivers
- [ ] Add mail configuration (if needed)
- [ ] Add Sanctum domains

### Web Server
- [ ] Create root `.htaccess` (redirects to `public/`)
- [ ] Verify `public/.htaccess` exists
- [ ] (Optional) Set document root to `public/` (VPS only)

## Dependencies & Setup

### Composer
- [ ] Install dependencies: `composer install --optimize-autoloader --no-dev`
- [ ] Verify `vendor/` folder exists

### Laravel Setup
- [ ] Run `php artisan storage:link`
- [ ] Verify symlink `public/storage` → `storage/app/public`
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan db:seed --force` (optional)
- [ ] Clear all caches: `config:clear`, `route:clear`, `view:clear`, `cache:clear`
- [ ] Rebuild caches: `config:cache`, `route:cache`, `view:cache`

## Security

### File Protection
- [ ] `.env` file is protected (via `.htaccess`)
- [ ] `.env` is not accessible via browser
- [ ] Sensitive files excluded from public access

### SSL/HTTPS
- [ ] SSL certificate installed
- [ ] Force HTTPS redirect (in `.htaccess`)
- [ ] Update `APP_URL` to use `https://`

### Application Security
- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secure
- [ ] File permissions set correctly

## Testing

### Basic Functionality
- [ ] Homepage loads without errors
- [ ] Login page accessible
- [ ] Can log in with seeded credentials
- [ ] Dashboard loads after login
- [ ] No 500 errors

### Database
- [ ] Database connection works
- [ ] Migrations ran successfully
- [ ] Seeders ran (if applicable)
- [ ] Data persists correctly

### Assets
- [ ] CSS files load correctly
- [ ] JavaScript files load correctly
- [ ] Images display correctly
- [ ] FontAwesome icons work
- [ ] Bootstrap styles applied

### Features
- [ ] User authentication works
- [ ] Role-based access works
- [ ] CRUD operations work
- [ ] File uploads work (if applicable)
- [ ] API endpoints work (if using)

### Logs
- [ ] Check `storage/logs/laravel.log` for errors
- [ ] No critical errors in logs
- [ ] Warnings are acceptable/resolved

## Post-Deployment

### Optimization
- [ ] All caches built (`config:cache`, `route:cache`, `view:cache`)
- [ ] OPcache enabled (if available)
- [ ] Gzip compression enabled (via `.htaccess`)

### Monitoring
- [ ] Set up error monitoring (optional)
- [ ] Check server resources usage
- [ ] Monitor database performance
- [ ] Set up backups

### Maintenance
- [ ] Document deployment process
- [ ] Save deployment credentials securely
- [ ] Set up cron jobs (if needed)
- [ ] Configure email notifications (if needed)

## Troubleshooting

### If Something Fails
- [ ] Check `storage/logs/laravel.log`
- [ ] Check cPanel error logs
- [ ] Verify file permissions
- [ ] Verify `.env` configuration
- [ ] Test database connection
- [ ] Clear all caches
- [ ] Rebuild caches

## Final Verification

- [ ] Application is accessible via domain
- [ ] All features working
- [ ] No errors in logs
- [ ] Performance is acceptable
- [ ] Mobile responsive
- [ ] SSL certificate active
- [ ] Backups configured

---

**✅ Deployment Complete!**

Save this checklist for future deployments.


