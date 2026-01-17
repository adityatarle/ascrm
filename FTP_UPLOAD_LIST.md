# FTP Upload List - Complete File List

## 📦 Composer Dependencies (MUST RUN ON SERVER)
**IMPORTANT:** Run these commands on your server after uploading files:
```bash
composer install
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## 📦 NPM Dependencies (MUST RUN ON SERVER)
```bash
npm install
npm install chart.js --save
npm run build
```

## 🗂️ New Files Created

### Dashboard Files
- `app/Livewire/Dashboard/Dashboard.php` - Dashboard Livewire component
- `resources/views/livewire/dashboard/dashboard.blade.php` - Dashboard view

### Reports Files
- `app/Livewire/Reports/ReportsPage.php` - Reports Livewire component
- `resources/views/livewire/reports/reports-page.blade.php` - Reports page view
- `app/Http/Controllers/Reports/ReportExportController.php` - Export controller
- `resources/views/reports/export-pdf.blade.php` - PDF export template
- `resources/views/reports/partials/sales-pdf.blade.php` - Sales PDF partial
- `resources/views/reports/partials/orders-pdf.blade.php` - Orders PDF partial
- `resources/views/reports/partials/payments-pdf.blade.php` - Payments PDF partial
- `resources/views/reports/partials/products-pdf.blade.php` - Products PDF partial
- `resources/views/reports/partials/dealers-pdf.blade.php` - Dealers PDF partial
- `resources/views/reports/partials/dispatches-pdf.blade.php` - Dispatches PDF partial
- `resources/views/reports/partials/gst-pdf.blade.php` - GST PDF partial
- `resources/views/components/report-header.blade.php` - Report header component

## 📝 Modified Files

### Routes
- `routes/web.php` - Updated with dashboard and reports routes

### Styles
- `resources/scss/app.scss` - Added dashboard and reports styles, print styles

### JavaScript
- `resources/js/app.js` - Updated for Chart.js

### Layouts
- `resources/views/layouts/app.blade.php` - Updated sidebar with Masters menu
- `resources/views/components/layouts/app.blade.php` - Updated sidebar with Masters menu, added @stack('scripts')

### Configuration
- `composer.json` - Added new dependencies
- `package.json` - Added Chart.js dependency
- `config/dompdf.php` - DomPDF configuration (auto-generated)

## 📋 Complete Upload Checklist

### 1. Application Files (app/)
```
app/
├── Http/
│   └── Controllers/
│       └── Reports/
│           └── ReportExportController.php (NEW)
├── Livewire/
│   ├── Dashboard/
│   │   └── Dashboard.php (NEW)
│   └── Reports/
│       └── ReportsPage.php (NEW)
```

### 2. View Files (resources/views/)
```
resources/views/
├── components/
│   ├── layouts/
│   │   └── app.blade.php (MODIFIED)
│   └── report-header.blade.php (NEW)
├── layouts/
│   └── app.blade.php (MODIFIED)
├── livewire/
│   ├── dashboard/
│   │   └── dashboard.blade.php (NEW)
│   └── reports/
│       └── reports-page.blade.php (NEW)
└── reports/
    ├── export-pdf.blade.php (NEW)
    └── partials/
        ├── sales-pdf.blade.php (NEW)
        ├── orders-pdf.blade.php (NEW)
        ├── payments-pdf.blade.php (NEW)
        ├── products-pdf.blade.php (NEW)
        ├── dealers-pdf.blade.php (NEW)
        ├── dispatches-pdf.blade.php (NEW)
        └── gst-pdf.blade.php (NEW)
```

### 3. Assets (resources/)
```
resources/
├── js/
│   └── app.js (MODIFIED)
└── scss/
    └── app.scss (MODIFIED)
```

### 4. Configuration Files
```
routes/
└── web.php (MODIFIED)

composer.json (MODIFIED)
package.json (MODIFIED)
```

### 5. Build Files (After npm run build)
```
public/build/
├── assets/
│   ├── app-*.css (GENERATED - will be different names)
│   └── app-*.js (GENERATED - will be different names)
└── manifest.json (GENERATED)
```

## ⚠️ Important Notes

### Files NOT to Upload (Auto-generated)
- `vendor/` - Run `composer install` on server
- `node_modules/` - Run `npm install` on server
- `public/build/` - Run `npm run build` on server
- `.env` - Keep your existing .env file

### After Upload Steps (ON SERVER)

1. **Install Dependencies:**
   ```bash
   composer install
   composer require barryvdh/laravel-dompdf
   composer require maatwebsite/excel
   npm install
   npm install chart.js --save
   ```

2. **Publish DomPDF Config:**
   ```bash
   php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
   ```

3. **Build Assets:**
   ```bash
   npm run build
   ```

4. **Clear Caches:**
   ```bash
   php artisan view:clear
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

5. **Optimize (Production):**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 📁 Quick Upload Script (FTP)

### Upload These Directories:
1. `app/Livewire/Dashboard/` (entire folder)
2. `app/Livewire/Reports/` (entire folder)
3. `app/Http/Controllers/Reports/` (entire folder)
4. `resources/views/livewire/dashboard/` (entire folder)
5. `resources/views/livewire/reports/` (entire folder)
6. `resources/views/reports/` (entire folder)
7. `resources/views/components/` (entire folder)
8. `resources/js/` (entire folder)
9. `resources/scss/` (entire folder)

### Upload These Individual Files:
1. `routes/web.php`
2. `composer.json`
3. `package.json`
4. `resources/views/layouts/app.blade.php`
5. `resources/views/components/layouts/app.blade.php`

## 🔍 Verification Checklist

After upload, verify:
- [ ] Dashboard loads with charts
- [ ] Reports page shows all tabs
- [ ] Print functionality works
- [ ] PDF export works
- [ ] Excel export works
- [ ] Charts display correctly
- [ ] Sidebar shows Masters menu
- [ ] All filters work on reports page

## 📝 Summary

**Total New Files:** 13
**Total Modified Files:** 6
**New Directories:** 3 (Dashboard, Reports, Reports/Partials)

**Critical:** Make sure to run `composer install`, `npm install`, and `npm run build` on the server after uploading!

