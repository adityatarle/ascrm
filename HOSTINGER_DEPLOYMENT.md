# Hostinger Deployment Guide for AgriChemTech ERP

Complete step-by-step guide to deploy your Laravel 11 application on Hostinger hosting.

## Prerequisites

- ✅ Hostinger hosting account (Shared, VPS, or Cloud)
- ✅ Domain name configured
- ✅ FTP/SFTP credentials (or SSH access for VPS)
- ✅ MySQL database created in Hostinger
- ✅ PHP 8.2+ enabled (check in cPanel)

## Step 1: Prepare Your Project Locally

### 1.1 Build Production Assets

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 1.2 Optimize for Production

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 1.3 Generate Application Key

```bash
php artisan key:generate --show
```

**Save this key** - you'll need it for the `.env` file on Hostinger.

### 1.4 Create Production .env File

Create a `.env.production` file locally with your Hostinger settings:

```env
APP_NAME="AgriChemTech ERP"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (from Hostinger cPanel)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Cache & Session (use file for shared hosting)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# If you have Redis on Hostinger
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
# CACHE_DRIVER=redis
# SESSION_DRIVER=redis

# Mail Configuration (use Hostinger SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Sanctum
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
```

## Step 2: Create Database in Hostinger

### 2.1 Access cPanel

1. Log in to Hostinger control panel
2. Go to **Databases** → **MySQL Databases**

### 2.2 Create Database

1. Create a new database (e.g., `aglichem_erp`)
2. Create a database user
3. Assign the user to the database with **ALL PRIVILEGES**
4. **Save the credentials** - you'll need them for `.env`

**Note:** Hostinger usually prefixes database names with your username (e.g., `username_aglichem_erp`)

## Step 3: Upload Files to Hostinger

### 3.1 Files to Upload

Upload **ALL** files except:
- ❌ `.env` (create new on server)
- ❌ `node_modules/` (not needed)
- ❌ `.git/` (not needed)
- ❌ `tests/` (optional)

### 3.2 Upload Methods

#### Option A: Using FTP/SFTP Client (FileZilla, WinSCP, etc.)

1. **Connect to Hostinger:**
   - Host: `ftp.yourdomain.com` or IP from Hostinger
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Navigate to public_html:**
   - For main domain: `/public_html/`
   - For subdomain: `/public_html/subdomain/`

3. **Upload Structure:**
   ```
   public_html/
   ├── .htaccess (root level - see Step 4)
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── public/
   │   ├── .htaccess
   │   ├── index.php
   │   └── ...
   ├── resources/
   ├── routes/
   ├── storage/
   ├── vendor/
   └── ... (all other Laravel files)
   ```

#### Option B: Using cPanel File Manager

1. Log in to cPanel
2. Go to **File Manager**
3. Navigate to `public_html`
4. Upload a ZIP file of your project
5. Extract it in `public_html`

#### Option C: Using SSH (VPS/Cloud Plans)

```bash
# Connect via SSH
ssh username@your-server-ip

# Navigate to public_html
cd public_html

# Use git or rsync to upload
git clone your-repo-url .
# OR
rsync -avz /local/path/ username@server:/home/username/public_html/
```

## Step 4: Configure Web Server

### 4.1 Create Root .htaccess

Create `.htaccess` in the **root** of `public_html` (not in `public/`):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public directory
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 4.2 Update public/index.php

The `public/index.php` should already be correct, but verify it points to:

```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

### 4.3 Set Document Root (if possible)

**For VPS/Cloud plans**, you can set the document root to `public_html/public`:
- In cPanel: **Domains** → **Your Domain** → **Document Root** → Set to `public_html/public`

**For Shared Hosting**, use the `.htaccess` redirect method above.

## Step 5: Set File Permissions

### 5.1 Using cPanel File Manager

1. Right-click on `storage/` folder → **Change Permissions** → Set to **755**
2. Right-click on `bootstrap/cache/` folder → **Change Permissions** → Set to **755**
3. Right-click on `storage/app/` → **Change Permissions** → Set to **755**
4. Right-click on `storage/framework/` → **Change Permissions** → Set to **755**
5. Right-click on `storage/logs/` → **Change Permissions** → Set to **755**

### 5.2 Using SSH (if available)

```bash
cd public_html
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/framework storage/logs
```

## Step 6: Create .env File on Server

### 6.1 Create .env File

**Option A: Using cPanel File Manager**
1. Navigate to `public_html`
2. Create new file named `.env`
3. Paste your production `.env` content (from Step 1.4)

**Option B: Using SSH**
```bash
cd public_html
nano .env
# Paste your .env content
# Save: Ctrl+X, then Y, then Enter
```

### 6.2 Verify .env File

Make sure `.env` contains:
- ✅ Correct database credentials
- ✅ `APP_KEY` generated
- ✅ `APP_URL` set to your domain
- ✅ `APP_DEBUG=false` for production

## Step 7: Install Dependencies on Server

### 7.1 Using SSH (Recommended)

```bash
cd public_html
composer install --optimize-autoloader --no-dev
```

### 7.2 Using cPanel Terminal (if available)

Same commands as SSH.

### 7.3 Manual Upload

If SSH is not available:
1. Install Composer locally
2. Run `composer install --optimize-autoloader --no-dev`
3. Upload the `vendor/` folder via FTP

## Step 8: Create Storage Link

### 8.1 Using SSH

```bash
cd public_html
php artisan storage:link
```

### 8.2 Manual Link (if SSH not available)

Create a symbolic link from `public/storage` to `storage/app/public`:
- In cPanel File Manager, create a symlink
- Or upload files directly to `public/storage/`

## Step 9: Run Migrations

### 9.1 Using SSH (Recommended)

```bash
cd public_html
php artisan migrate --force
php artisan db:seed --force
```

### 9.2 Using cPanel Terminal

Same commands as SSH.

### 9.3 Using Artisan Tinker (Alternative)

If migrations fail, you can run them via a temporary route (remove after use):

Create `routes/web.php` temporary route:
```php
Route::get('/run-migrations', function() {
    Artisan::call('migrate', ['--force' => true]);
    return Artisan::output();
})->middleware('auth'); // Protect with password
```

**⚠️ Remove this route immediately after running migrations!**

## Step 10: Verify Installation

### 10.1 Check Application

1. Visit `https://yourdomain.com`
2. You should see the login page
3. Try logging in with seeded credentials

### 10.2 Check Logs

If there are errors, check:
- `storage/logs/laravel.log`
- cPanel Error Logs

### 10.3 Common Issues

**500 Internal Server Error:**
- Check file permissions
- Verify `.env` file exists and is correct
- Check `APP_KEY` is set
- Verify database credentials

**Database Connection Error:**
- Verify database credentials in `.env`
- Check database user has proper permissions
- Ensure database exists

**Storage Not Working:**
- Run `php artisan storage:link`
- Check `storage/` folder permissions (755)
- Verify `public/storage` symlink exists

## Step 11: Security Hardening

### 11.1 Hide .env File

Ensure `.htaccess` in root prevents access to `.env`:

```apache
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### 11.2 Disable Directory Listing

Already in `public/.htaccess`, but verify:

```apache
Options -Indexes
```

### 11.3 Update .gitignore

Ensure sensitive files are not tracked:
- `.env`
- `storage/logs/*.log`
- `vendor/`

## Step 12: Post-Deployment Tasks

### 12.1 Clear All Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

Then rebuild:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 12.2 Set Up Cron Jobs (if using queues)

In cPanel → **Cron Jobs**, add:

```bash
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### 12.3 Enable SSL (if not already)

1. In cPanel → **SSL/TLS Status**
2. Enable SSL for your domain
3. Force HTTPS redirect in `.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Step 13: Test Everything

✅ **Test Checklist:**
- [ ] Homepage loads
- [ ] Login works
- [ ] Database connection works
- [ ] File uploads work (if applicable)
- [ ] API endpoints work (if using)
- [ ] All routes accessible
- [ ] No 500 errors in logs
- [ ] SSL certificate active
- [ ] Mobile responsive

## Troubleshooting

### Issue: "Class not found" errors

**Solution:**
- Run `composer dump-autoload`
- Verify `vendor/` folder is uploaded
- Check file permissions

### Issue: "Permission denied" errors

**Solution:**
- Set `storage/` and `bootstrap/cache/` to 755
- Set `storage/framework/` and `storage/logs/` to 775
- Check file ownership

### Issue: "No application encryption key"

**Solution:**
- Verify `.env` file exists
- Check `APP_KEY` is set in `.env`
- Run `php artisan key:generate` on server

### Issue: "SQLSTATE[HY000] [2002] Connection refused"

**Solution:**
- Verify database host (usually `localhost` for Hostinger)
- Check database credentials
- Ensure database user has proper permissions

### Issue: Assets not loading (CSS/JS)

**Solution:**
- Run `npm run build` locally and upload `public/build/`
- Check `APP_URL` in `.env`
- Verify file permissions on `public/` folder

## Support Resources

- **Hostinger Support:** https://www.hostinger.com/contact
- **Laravel Documentation:** https://laravel.com/docs
- **cPanel Documentation:** https://docs.cpanel.net

## Quick Reference Commands

```bash
# SSH into server
ssh username@your-server-ip

# Navigate to project
cd public_html

# Run migrations
php artisan migrate --force

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link

# Check Laravel version
php artisan --version
```

---

**🎉 Congratulations!** Your Laravel application should now be live on Hostinger!


