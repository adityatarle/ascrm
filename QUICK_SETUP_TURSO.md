# Quick Setup: Turso Database on Vercel

## Step 1: Get Turso Credentials

From your Turso dashboard, get:
- **Database URL**: `libsql://your-db-name-org.turso.io`
- **Auth Token**: (if required)

## Step 2: Set Vercel Environment Variables

Go to: **Vercel Dashboard → Your Project → Settings → Environment Variables**

Add these variables:

```env
# Database (Turso)
DB_CONNECTION=turso
TURSO_DATABASE_URL=libsql://your-db-name-org.turso.io
TURSO_AUTH_TOKEN=your-turso-auth-token

# Migration Security Token (generate a random string)
MIGRATION_TOKEN=generate-a-random-secure-token-here

# Laravel App
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_URL=https://your-app.vercel.app

# Cache & Session (use array for serverless)
CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
```

### Generate Migration Token

**Windows PowerShell:**
```powershell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
```

**Linux/Mac:**
```bash
openssl rand -hex 32
```

### Generate APP_KEY

**Local:**
```bash
php artisan key:generate --show
```

Copy the output and set as `APP_KEY` in Vercel.

## Step 3: Deploy

```bash
vercel --prod
```

## Step 4: Run Migrations

After deployment, call the migration endpoint:

```bash
# Replace with your actual URL and token
curl "https://your-app.vercel.app/api/migrate?token=your-migration-token"
```

Or use a browser:
```
https://your-app.vercel.app/api/migrate?token=your-migration-token
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Migrations completed successfully",
  "output": "..."
}
```

## Step 5: Run Seeders (Optional)

```bash
curl "https://your-app.vercel.app/api/seed?token=your-migration-token"
```

## Step 6: Verify

1. Check Turso dashboard - you should see all tables created
2. Visit your app URL
3. Test login functionality

## Troubleshooting

### "Database connection failed"
- ✅ Check `TURSO_DATABASE_URL` is correct
- ✅ Verify `DB_CONNECTION=turso` is set
- ✅ Check Turso dashboard for connection status

### "Unauthorized" when calling migrate endpoint
- ✅ Verify `MIGRATION_TOKEN` matches in Vercel env vars
- ✅ Check you're passing the token correctly

### Migrations fail
- ✅ Check the error output in the JSON response
- ✅ Verify all migrations are SQLite-compatible
- ✅ Check Turso logs in dashboard

## Local Development

For local development with MySQL, keep your `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aglichem_erp
DB_USERNAME=root
DB_PASSWORD=
```

Vercel will use Turso (SQLite) automatically when `DB_CONNECTION=turso` is set.

## Important Notes

1. **MySQL vs SQLite**: Your migrations work with both, but some MySQL-specific features may need adjustment
2. **Foreign Keys**: SQLite requires them to be enabled (already configured)
3. **Storage**: Use external storage (S3) for file uploads, not local storage
4. **Sessions**: Use cookie-based sessions (already configured) or database sessions

## Next Steps

✅ Set environment variables  
✅ Deploy  
✅ Run migrations  
✅ Run seeders  
✅ Test application  

For detailed information, see `TURSO_MIGRATION_GUIDE.md`

