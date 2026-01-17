# Hostinger Quick Start Guide

## 🚀 Fast Deployment Checklist

### Before Upload

- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build`
- [ ] Generate `APP_KEY` with `php artisan key:generate --show`
- [ ] Create database in Hostinger cPanel
- [ ] Prepare `.env` file with database credentials

### Upload Files

1. **Connect via FTP/SFTP** to `public_html/`
2. **Upload all files** (except `.env`, `node_modules`, `.git`)
3. **Create `.env` file** in `public_html/` with your settings

### Configure Server

1. **Set permissions:**
   - `storage/` → 755
   - `bootstrap/cache/` → 755
   - `storage/framework/` → 775
   - `storage/logs/` → 775

2. **Create `.htaccess`** in root (redirects to `public/`)

3. **Install dependencies:**
   ```bash
   cd public_html
   composer install --optimize-autoloader --no-dev
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

5. **Create storage link:**
   ```bash
   php artisan storage:link
   ```

6. **Cache for production:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Test

Visit `https://yourdomain.com` and verify:
- ✅ Login page loads
- ✅ Can log in with seeded credentials
- ✅ No errors in `storage/logs/laravel.log`

## 📋 Required .env Settings

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## 🔧 Common Issues

**500 Error?**
- Check file permissions
- Verify `.env` exists
- Check `APP_KEY` is set

**Database Error?**
- Verify credentials in `.env`
- Check database exists in cPanel
- Ensure user has permissions

**Assets Not Loading?**
- Upload `public/build/` folder
- Check `APP_URL` in `.env`

## 📞 Need Help?

See `HOSTINGER_DEPLOYMENT.md` for detailed instructions.


