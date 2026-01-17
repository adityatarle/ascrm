# Files to Upload to Hostinger

## ✅ Files/Folders to UPLOAD

Upload these to `public_html/` on Hostinger:

```
public_html/
├── app/                    ✅ Upload
├── bootstrap/              ✅ Upload
│   └── cache/              ✅ Upload (will be created if missing)
├── config/                 ✅ Upload
├── database/               ✅ Upload
│   ├── migrations/         ✅ Upload
│   ├── seeders/            ✅ Upload
│   └── factories/          ✅ Upload
├── public/                 ✅ Upload
│   ├── .htaccess          ✅ Upload
│   ├── index.php          ✅ Upload
│   ├── build/             ✅ Upload (after npm run build)
│   └── ...                ✅ Upload all
├── resources/              ✅ Upload
│   ├── css/               ✅ Upload
│   ├── js/                ✅ Upload
│   ├── scss/              ✅ Upload
│   ├── views/             ✅ Upload
│   └── ...                ✅ Upload all
├── routes/                 ✅ Upload
│   ├── api.php            ✅ Upload
│   ├── web.php            ✅ Upload
│   ├── auth.php           ✅ Upload
│   └── console.php        ✅ Upload
├── storage/                 ✅ Upload (structure only)
│   ├── app/               ✅ Upload (create if missing)
│   │   ├── public/        ✅ Upload (create if missing)
│   │   └── private/       ✅ Upload (create if missing)
│   ├── framework/         ✅ Upload (create if missing)
│   │   ├── cache/         ✅ Upload (create if missing)
│   │   ├── sessions/      ✅ Upload (create if missing)
│   │   └── views/         ✅ Upload (create if missing)
│   └── logs/              ✅ Upload (create if missing)
├── vendor/                 ✅ Upload (or install via SSH)
├── .htaccess               ✅ Upload (root level)
├── artisan                 ✅ Upload
├── composer.json           ✅ Upload
├── composer.lock           ✅ Upload
├── package.json            ✅ Upload (optional)
├── package-lock.json       ✅ Upload (optional)
└── README.md               ✅ Upload (optional)
```

## ❌ Files/Folders to EXCLUDE

**DO NOT upload these:**

```
├── .env                    ❌ Create new on server
├── .env.example            ❌ Optional (can upload)
├── .env.backup             ❌ Don't upload
├── .env.production         ❌ Don't upload
├── .git/                   ❌ Don't upload
├── .gitignore              ❌ Optional
├── .vercel/                ❌ Vercel-specific
├── vercel.json             ❌ Vercel-specific
├── node_modules/           ❌ Not needed
├── tests/                  ❌ Optional
├── .phpunit.cache/         ❌ Don't upload
├── .idea/                  ❌ IDE-specific
├── .vscode/                ❌ IDE-specific
├── storage/logs/*.log      ❌ Don't upload log files
└── storage/framework/cache/ ❌ Don't upload cache files
```

## 📝 Important Notes

### 1. Storage Folder Structure

Create these folders if they don't exist:
```
storage/
├── app/
│   ├── public/
│   └── private/
├── framework/
│   ├── cache/
│   ├── sessions/
│   └── views/
└── logs/
```

**After upload, set permissions:**
- `storage/` → 755
- `storage/framework/` → 775
- `storage/logs/` → 775

### 2. Vendor Folder

**Option A: Upload via FTP**
- Upload the entire `vendor/` folder (can be large, ~50-100MB)

**Option B: Install via SSH (Recommended)**
```bash
cd public_html
composer install --optimize-autoloader --no-dev
```

### 3. Public Build Folder

**Must upload after building:**
```bash
# Local: Build assets
npm run build

# Then upload public/build/ folder
```

### 4. .env File

**DO NOT upload your local `.env` file!**

Create a new `.env` on the server with:
- Production database credentials
- Production `APP_KEY`
- `APP_ENV=production`
- `APP_DEBUG=false`

### 5. Bootstrap Cache

The `bootstrap/cache/` folder should exist but can be empty. Laravel will create cache files automatically.

## 🚀 Upload Order (Recommended)

1. **First:** Upload folder structure (app/, config/, routes/, etc.)
2. **Second:** Upload `vendor/` (or install via SSH)
3. **Third:** Upload `public/` with built assets
4. **Fourth:** Upload `storage/` structure
5. **Fifth:** Create `.env` file on server
6. **Sixth:** Upload root `.htaccess`
7. **Seventh:** Set file permissions
8. **Eighth:** Run migrations and setup

## 📦 Compression Tips

If uploading via FTP is slow:

1. **Compress locally:**
   ```bash
   # Exclude unnecessary files
   tar -czf deploy.tar.gz \
     --exclude='.env' \
     --exclude='.git' \
     --exclude='node_modules' \
     --exclude='.vercel' \
     --exclude='tests' \
     app bootstrap config database public resources routes storage vendor artisan composer.json composer.lock .htaccess
   ```

2. **Upload the compressed file**

3. **Extract on server:**
   ```bash
   # Via SSH
   cd public_html
   tar -xzf deploy.tar.gz
   ```

## ✅ Verification After Upload

Check these files exist on server:
- [ ] `public_html/.htaccess` (root)
- [ ] `public_html/public/.htaccess`
- [ ] `public_html/public/index.php`
- [ ] `public_html/vendor/` (or install via SSH)
- [ ] `public_html/storage/` structure
- [ ] `public_html/.env` (created on server)
- [ ] `public_html/public/build/` (after npm build)

---

**Note:** File sizes can be large. Be patient during upload, especially for `vendor/` folder.


