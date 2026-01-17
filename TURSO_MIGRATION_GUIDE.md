# Turso Database Migration Guide

This guide explains how to set up and run database migrations on Vercel using Turso (SQLite).

## What is Turso?

Turso is a serverless SQLite database that works great with Vercel. It's based on libSQL (a fork of SQLite) and provides:
- Global edge replication
- Low latency
- Serverless-friendly
- SQLite compatibility

## Setup Steps

### 1. Get Turso Database URL

From your Turso dashboard, get your database URL. It will look like:
```
libsql://your-database-name-org.turso.io
```

### 2. Set Environment Variables in Vercel

Go to your Vercel project → Settings → Environment Variables and add:

```env
# Database Configuration
DB_CONNECTION=turso
TURSO_DATABASE_URL=libsql://your-database-name-org.turso.io
TURSO_AUTH_TOKEN=your-auth-token-here

# Migration Security Token (generate a random string)
MIGRATION_TOKEN=your-secure-random-token-here

# Other Laravel Variables
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-app-key-here
APP_URL=https://your-app.vercel.app
```

### 3. Configure Database Connection

The `config/database.php` already includes a `turso` connection that uses:
- SQLite driver (compatible with Turso)
- WAL journal mode (better for concurrent access)
- Foreign key constraints enabled

### 4. Run Migrations

You have **three options** to run migrations:

#### Option A: Using the Migration Endpoint (Recommended)

After deploying, call the migration endpoint:

```bash
# Using curl
curl -X GET "https://your-app.vercel.app/api/migrate?token=your-migration-token"

# Or with header
curl -X GET "https://your-app.vercel.app/api/migrate" \
  -H "X-Migration-Token: your-migration-token"
```

**Response:**
```json
{
  "success": true,
  "message": "Migrations completed successfully",
  "output": "Migration output here..."
}
```

#### Option B: Using Vercel CLI

```bash
# Pull environment variables
vercel env pull .env.production

# Run migrations locally pointing to Turso
php artisan migrate --force --database=turso
```

#### Option C: Using Turso CLI

If you have Turso CLI installed:

```bash
# Connect to your database
turso db shell your-database-name

# Then run SQL commands manually (not recommended for migrations)
```

### 5. Run Seeders (Optional)

After migrations, you can seed the database:

```bash
# Using the migration endpoint (add seeder support)
# Or use Vercel CLI
php artisan db:seed --force --database=turso
```

## Important Notes

### MySQL vs SQLite Differences

Your migrations were written for MySQL, but Turso uses SQLite. Laravel handles most differences automatically, but watch out for:

1. **Data Types:**
   - MySQL `BIGINT` → SQLite `INTEGER`
   - MySQL `TEXT` → SQLite `TEXT` (same)
   - MySQL `VARCHAR(255)` → SQLite `TEXT`

2. **Foreign Keys:**
   - SQLite requires foreign keys to be enabled (already configured)
   - `onDelete('cascade')` works the same

3. **Indexes:**
   - Work the same in both

4. **Auto-increment:**
   - MySQL: `AUTO_INCREMENT`
   - SQLite: `AUTOINCREMENT` (Laravel handles this)

### Security

The migration endpoint is protected by a token. **Never commit your `MIGRATION_TOKEN` to git!**

Generate a secure token:
```bash
# On Linux/Mac
openssl rand -hex 32

# On Windows PowerShell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))
```

### Troubleshooting

#### Error: "Database connection failed"

1. Check `TURSO_DATABASE_URL` is set correctly
2. Verify `TURSO_AUTH_TOKEN` is set (if required)
3. Ensure `DB_CONNECTION=turso` is set

#### Error: "Migration failed"

1. Check the error output in the response
2. Verify all migrations are SQLite-compatible
3. Check Turso dashboard for connection issues

#### Error: "Unauthorized"

1. Verify `MIGRATION_TOKEN` matches in Vercel env vars
2. Check you're passing the token correctly in the request

### Testing Locally with Turso

1. Install Turso CLI:
   ```bash
   # macOS/Linux
   curl -sSfL https://get.tur.so/install.sh | bash
   
   # Or download from https://docs.tur.so/cli/installation
   ```

2. Create a local database:
   ```bash
   turso db create my-local-db
   turso db show my-local-db
   ```

3. Get the database URL and set in `.env`:
   ```env
   DB_CONNECTION=turso
   TURSO_DATABASE_URL=libsql://my-local-db-org.turso.io
   ```

4. Run migrations:
   ```bash
   php artisan migrate --force --database=turso
   ```

## Migration Endpoint Details

**URL:** `/api/migrate`  
**Method:** GET  
**Authentication:** Token-based (query param or header)

**Query Parameters:**
- `token` (optional if using header)

**Headers:**
- `X-Migration-Token` (optional if using query param)

**Response:**
```json
{
  "success": true|false,
  "message": "Status message",
  "output": "Artisan command output",
  "error": "Error message (if failed)"
}
```

## Next Steps

1. ✅ Set environment variables in Vercel
2. ✅ Deploy your application
3. ✅ Call the migration endpoint
4. ✅ Verify tables in Turso dashboard
5. ✅ (Optional) Run seeders
6. ✅ Test your application

## Additional Resources

- [Turso Documentation](https://docs.tur.so/)
- [Laravel SQLite Documentation](https://laravel.com/docs/database#sqlite-configuration)
- [Vercel Environment Variables](https://vercel.com/docs/concepts/projects/environment-variables)

