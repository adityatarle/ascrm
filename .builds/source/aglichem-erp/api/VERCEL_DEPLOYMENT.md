# Vercel Deployment Guide for AgriChemTech ERP

This guide will help you deploy your Laravel 11 application on Vercel.

## Prerequisites

1. A Vercel account (sign up at [vercel.com](https://vercel.com))
2. Vercel CLI installed (`npm i -g vercel`)
3. Your Laravel project ready for deployment

## Important Notes

⚠️ **Laravel on Vercel Limitations:**
- Vercel is serverless, so some Laravel features may have limitations
- File storage should use external services (S3, etc.) instead of local storage
- Queue workers need external services (Redis, database queues, or external queue service)
- Sessions should use database or Redis, not file-based
- WebSockets are not supported (use external services if needed)

## Step 1: Environment Variables

Before deploying, you need to set up environment variables in Vercel:

### Required Environment Variables

```env
APP_NAME="AgriChemTech ERP"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.vercel.app

# Database (Use Vercel Postgres or external MySQL)
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Redis (Use Upstash Redis or external Redis)
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

# Cache and Session
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Sanctum
SANCTUM_STATEFUL_DOMAINS=your-app.vercel.app,your-custom-domain.com

# Filesystem (Use S3 or similar)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=your-region
AWS_BUCKET=your-bucket
```

### How to Set Environment Variables in Vercel

1. Go to your project settings in Vercel dashboard
2. Navigate to "Environment Variables"
3. Add each variable for Production, Preview, and Development environments

## Step 2: Generate APP_KEY

Generate your application key locally:

```bash
php artisan key:generate --show
```

Copy the generated key and add it as `APP_KEY` in Vercel environment variables.

## Step 3: Database Setup

### Option A: Use Vercel Postgres (Recommended for Vercel)

1. Create a Postgres database in Vercel
2. Update your `.env` to use Postgres:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=your-vercel-postgres-host
   DB_PORT=5432
   DB_DATABASE=your-database
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```

### Option B: Use External MySQL

Use services like:
- PlanetScale
- Railway
- AWS RDS
- DigitalOcean Managed Database

## Step 4: Redis Setup

Use external Redis services:
- **Upstash Redis** (Recommended for Vercel)
- **Redis Cloud**
- **AWS ElastiCache**

## Step 5: Storage Configuration

Since Vercel is serverless, you cannot use local file storage. Configure S3 or similar:

1. Update `config/filesystems.php` to use S3
2. Set up AWS S3 bucket or compatible service
3. Add AWS credentials to Vercel environment variables

## Step 6: Deploy to Vercel

### Using Vercel CLI

1. Install Vercel CLI (if not already installed):
   ```bash
   npm i -g vercel
   ```

2. Login to Vercel:
   ```bash
   vercel login
   ```

3. Deploy:
   ```bash
   vercel
   ```

4. For production deployment:
   ```bash
   vercel --prod
   ```

### Using GitHub Integration

1. Push your code to GitHub
2. Import your repository in Vercel dashboard
3. Vercel will automatically detect the `vercel.json` configuration
4. Add environment variables in project settings
5. Deploy

## Step 7: Post-Deployment Steps

After deployment, you need to run migrations and seeders:

### Option A: Using Vercel CLI

```bash
vercel env pull .env.production
php artisan migrate --force
php artisan db:seed --force
```

### Option B: Using Vercel Functions

Create a one-time migration function or use Vercel's serverless functions to run migrations.

### Option C: Manual Database Setup

Run migrations manually on your database server or use a database management tool.

## Step 8: Configure Custom Domain (Optional)

1. Go to your project settings in Vercel
2. Navigate to "Domains"
3. Add your custom domain
4. Update `APP_URL` and `SANCTUM_STATEFUL_DOMAINS` environment variables

## Step 9: Update Storage Configuration

Update `config/filesystems.php` to ensure public storage uses S3:

```php
'disks' => [
    'public' => [
        'driver' => 's3',
        'root' => 'storage',
        // ... S3 configuration
    ],
],
```

## Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
- Check environment variables are set correctly
- Verify `APP_KEY` is set
- Check database connection
- Review Vercel function logs

### Issue: Storage Not Working

**Solution:**
- Ensure S3 or external storage is configured
- Update `FILESYSTEM_DISK` environment variable
- Check AWS credentials

### Issue: Sessions Not Persisting

**Solution:**
- Use Redis or database sessions
- Update `SESSION_DRIVER` to `redis` or `database`
- Ensure Redis connection is working

### Issue: Queue Jobs Not Running

**Solution:**
- Use external queue service
- Configure `QUEUE_CONNECTION` to use Redis or database
- Set up a separate worker service (not on Vercel)

## Build Configuration

The `vercel.json` file is configured to:
- Install PHP and Node dependencies
- Build assets using Vite
- Route all requests through Laravel
- Serve static assets efficiently
- Use PHP 8.2 runtime

## Monitoring

- Check Vercel function logs in the dashboard
- Monitor database connections
- Set up error tracking (Sentry, Bugsnag, etc.)
- Monitor Redis usage

## Performance Optimization

1. **Enable Caching:**
   - Use Redis for caching
   - Enable OPcache if available
   - Cache routes and config in production

2. **Asset Optimization:**
   - Assets are built during deployment
   - Use CDN for static assets (Vercel provides this automatically)

3. **Database Optimization:**
   - Use connection pooling
   - Enable query caching
   - Optimize database indexes

## Security

1. **Environment Variables:**
   - Never commit `.env` file
   - Use Vercel's environment variables
   - Rotate keys regularly

2. **HTTPS:**
   - Vercel provides HTTPS by default
   - Ensure `APP_URL` uses `https://`

3. **CORS:**
   - Configure CORS in `config/cors.php`
   - Update `SANCTUM_STATEFUL_DOMAINS`

## Support

For issues specific to:
- **Vercel**: Check [Vercel Documentation](https://vercel.com/docs)
- **Laravel**: Check [Laravel Documentation](https://laravel.com/docs)
- **This Project**: Open an issue on the repository

## Additional Resources

- [Vercel PHP Runtime](https://vercel.com/docs/concepts/functions/serverless-functions/runtimes/php)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Vercel Environment Variables](https://vercel.com/docs/concepts/projects/environment-variables)

