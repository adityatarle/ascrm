# Setup Commands Summary

## Initial Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Publish package configurations
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan horizon:install
php artisan horizon:publish

# 6. Run migrations
php artisan migrate

# 7. Seed database
php artisan db:seed

# 8. Build assets
npm run build
```

## Development Commands

```bash
# Start development server
php artisan serve

# Start queue worker
php artisan queue:work

# Start Horizon (queue dashboard)
php artisan horizon

# Watch assets for development
npm run dev

# Run tests
php artisan test

# Format code
php artisan pint
```

## Production Commands

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

