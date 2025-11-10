# PANDUAN DEPLOYMENT KE CPANEL - PRODUCTION READY

## 📋 RINGKASAN CEPAT

**PERTANYAAN:** Apakah perlu `npm run build` sebelum zip untuk cPanel?

**JAWABAN:** **TIDAK PERLU** di local. Ada 2 opsi:
1. ✅ **Build di local** → include `public/build` dalam zip (RECOMMENDED)
2. ⚠️ **Build di cPanel** → perlu akses terminal SSH dan install npm

---

## 🚀 CARA 1: BUILD DI LOCAL (RECOMMENDED)

### Step 1: Build Assets di Local

```bash
# Install dependencies
npm install

# Build untuk production
npm run build
```

**Hasil:**
- Assets ter-compile akan ada di folder `public/build/`
- CSS dan JS sudah di-minify dan optimized

### Step 2: Persiapan File untuk Zip

**EXCLUDE folder berikut dari zip:**
```
node_modules/          ❌ JANGAN di-zip (besar sekali)
vendor/                ❌ JANGAN di-zip (install ulang di server)
.git/                  ❌ JANGAN di-zip
storage/logs/*.log     ❌ JANGAN di-zip
.env                   ⚠️ BUAT BARU di server
```

**INCLUDE folder berikut:**
```
public/build/          ✅ HARUS di-zip (hasil npm run build)
app/                   ✅
database/              ✅
resources/             ✅
routes/                ✅
config/                ✅
bootstrap/             ✅
storage/ (kosongkan logs) ✅
.env.example           ✅
composer.json          ✅
composer.lock          ✅
package.json           ✅
artisan                ✅
```

### Step 3: Cara Zip yang Benar

**Linux/Mac:**
```bash
cd /home/user/3putrarevisi

# Buat zip TANPA node_modules dan vendor
zip -r 3putrarevisi-production.zip . \
  -x "node_modules/*" \
  -x "vendor/*" \
  -x ".git/*" \
  -x "storage/logs/*" \
  -x ".env"
```

**Windows:**
1. Buka folder `3putrarevisi`
2. Delete atau exclude manual:
   - `node_modules/`
   - `vendor/`
   - `.git/`
   - `storage/logs/*.log`
3. Zip sisanya menggunakan WinRAR/7zip
4. **PASTIKAN** `public/build/` masuk dalam zip!

---

## 🔧 DEPLOYMENT DI CPANEL

### Step 1: Upload & Extract

1. Login cPanel → File Manager
2. Upload `3putrarevisi-production.zip` ke folder `public_html` atau folder custom
3. Extract zip file
4. Pindahkan isi folder ke root (jika perlu)

### Step 2: Install Composer Dependencies

**Via Terminal SSH (jika tersedia):**
```bash
cd ~/public_html/3putrarevisi
composer install --no-dev --optimize-autoloader
```

**Via cPanel Terminal/SSH Access:**
```bash
php composer.phar install --no-dev --optimize-autoloader
```

### Step 3: Setup Environment File

```bash
# Copy .env.example ke .env
cp .env.example .env

# Edit .env dengan File Manager cPanel
# Set database, APP_URL, dll
```

**Konfigurasi penting di `.env`:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domainanda.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Setup Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

### Step 6: Run Migrations

```bash
# Jika database baru (HATI-HATI!)
php artisan migrate --force

# Atau import SQL dump jika ada
```

**IMPORTANT: Jalankan SQL script untuk soft delete pembibitan:**
```sql
-- Di phpMyAdmin atau HeidiSQL
ALTER TABLE `pembibitans`
ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;
```

### Step 7: Optimize for Production

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --no-dev
```

### Step 8: Setup Public Directory

**Di cPanel → Domains → Setup subdomain/domain:**
- Document Root: `/home/username/public_html/3putrarevisi/public`

Atau buat `.htaccess` di root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## 🧪 VERIFIKASI DEPLOYMENT

### Checklist Production:

- [ ] Website bisa diakses
- [ ] Login berhasil (manager & admin)
- [ ] Master data muncul (Karyawan, Gudang, Mandor, dll)
- [ ] Transaksi Absensi berfungsi
- [ ] Laporan Gaji berfungsi
- [ ] Filter tanggal & reset button bekerja
- [ ] Soft delete pembibitan (test delete → cek data lama tetap ada)
- [ ] Bold text konsisten di semua halaman
- [ ] No error di browser console (F12)
- [ ] No error di `storage/logs/laravel.log`

---

## ⚠️ CARA 2: BUILD DI CPANEL (ALTERNATIF)

**Hanya jika cPanel punya akses terminal dan npm terinstall:**

```bash
cd ~/public_html/3putrarevisi

# Install npm dependencies
npm install

# Build production
npm run build

# Hapus node_modules setelah build
rm -rf node_modules
```

**KEKURANGAN:**
- ❌ Perlu akses SSH/Terminal
- ❌ cPanel harus punya Node.js installed
- ❌ Build bisa lambat di shared hosting
- ❌ Boros resource server

**Karena itu CARA 1 lebih RECOMMENDED!**

---

## 📊 CHECKLIST FILE YANG HARUS ADA DI ZIP

```
✅ 3putrarevisi/
   ✅ public/
      ✅ build/              ← HASIL npm run build (PENTING!)
      ✅ css/
      ✅ js/
      ✅ images/
      ✅ index.php
      ✅ .htaccess
   ✅ app/
   ✅ bootstrap/
   ✅ config/
   ✅ database/
      ✅ migrations/
      ✅ add_soft_delete_pembibitan.sql
   ✅ resources/
   ✅ routes/
   ✅ storage/ (tanpa logs)
   ✅ composer.json
   ✅ composer.lock
   ✅ package.json
   ✅ .env.example
   ✅ artisan
   ✅ PANDUAN_SOFT_DELETE_PEMBIBITAN.md

   ❌ node_modules/         ← JANGAN
   ❌ vendor/               ← JANGAN
   ❌ .git/                 ← JANGAN
   ❌ .env                  ← BUAT BARU DI SERVER
```

---

## 🔍 TROUBLESHOOTING COMMON ISSUES

### Issue 1: "500 Internal Server Error"

**Solusi:**
```bash
# Check storage permissions
chmod -R 775 storage bootstrap/cache

# Check .env configuration
cat .env

# Check logs
tail -f storage/logs/laravel.log
```

### Issue 2: "Assets not loading (CSS/JS 404)"

**Penyebab:** Folder `public/build/` tidak ter-upload

**Solusi:**
1. Verify `public/build/` ada di server
2. Re-run `npm run build` di local
3. Re-upload folder `public/build/`

### Issue 3: "Mix Manifest Not Found"

**Penyebab:** Vite build belum dijalankan

**Solusi:**
```bash
# Di local
npm run build

# Re-upload public/build/
```

### Issue 4: Database Connection Failed

**Solusi:**
```bash
# Check .env
DB_HOST=localhost         # bukan 127.0.0.1
DB_DATABASE=nama_database
DB_USERNAME=username
DB_PASSWORD=password

# Clear config cache
php artisan config:clear
php artisan config:cache
```

---

## 📞 SUPPORT

Jika ada masalah saat deployment:

1. **Check error logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check web server error log** (cPanel → Error Log)

3. **Enable debug temporarily** (ONLY for troubleshooting):
   ```env
   # .env
   APP_DEBUG=true
   ```
   **⚠️ JANGAN LUPA SET KEMBALI KE `false` SETELAH FIX!**

---

## ✅ KESIMPULAN

**LANGKAH FINAL SEBELUM ZIP:**

```bash
# 1. Build assets
npm run build

# 2. Verify public/build/ ada
ls -la public/build/

# 3. Zip tanpa node_modules & vendor
# (lihat Step 3 di atas)

# 4. Upload ke cPanel
# 5. Extract & setup (lihat deployment steps)

# 6. DONE! 🚀
```

---

**Sistem sudah production-ready untuk operasi 2 tahun kedepan!** 🎉
