# CRITICAL FIX - Double Shift & Role Detection
## Panduan Lengkap untuk Production

---

## 🔴 MASALAH YANG DITEMUKAN

### 1. Double Shift Tidak Bisa Disimpan
**Error:**
```
SQLSTATE[23000]: Integrity constraint violation: 1062
Duplicate entry 'budi-2025-11-01' for key 'absensis.unique_employee_date'
```

**Root Cause:**
- Database punya unique constraint: `unique_employee_date` pada kolom `(nama_karyawan, tanggal)`
- Constraint ini mencegah karyawan punya 2 absensi pada tanggal yang sama
- **Ini BLOCKING fitur utama aplikasi: double shift untuk setengah hari**

### 2. Role Gudang/Mandor Kosong di Index
**Problem:**
- Di halaman index absensis, kolom "Role" kosong untuk karyawan gudang dan mandor
- Hanya karyawan biasa yang tampil role-nya

**Root Cause:**
- Column `jabatan` belum ada di table `absensis`
- DataTables mencoba baca dari `$absensi->jabatan` tapi field tidak exist
- Fallback ke employee relationship, tapi gudang/mandor tidak punya employee_id

---

## ✅ SOLUSI LENGKAP

### STEP 1: Run SQL Script (WAJIB!)

**File:** `database/add_jabatan_column.sql`

#### Cara Jalankan di Laragon (Windows):

**Option A - HeidiSQL (Recommended):**
1. Buka **HeidiSQL** dari Laragon menu
2. Connect ke database Anda
3. Pilih database di panel kiri
4. Klik tab **"Query"** di atas
5. Buka file: `database/add_jabatan_column.sql`
6. Copy SEMUA content file tersebut
7. Paste ke query window
8. Klik tombol **"Execute"** (F9) atau icon ▶️
9. Tunggu sampai selesai

**Option B - phpMyAdmin:**
1. Buka browser: `http://localhost/phpmyadmin`
2. Pilih database Anda di panel kiri
3. Klik tab **"SQL"** di atas
4. Buka file: `database/add_jabatan_column.sql`
5. Copy SEMUA content file tersebut
6. Paste ke textarea
7. Klik tombol **"Go"**
8. Tunggu sampai selesai

#### Apa yang Dilakukan SQL Ini:

```sql
-- 1. Drop constraint yang block double shift
ALTER TABLE `absensis` DROP INDEX IF EXISTS `unique_employee_date`;
ALTER TABLE `absensis` DROP INDEX IF EXISTS `absensis_employee_id_tanggal_unique`;

-- 2. Add column jabatan untuk simpan role
ALTER TABLE `absensis` ADD COLUMN IF NOT EXISTS `jabatan` VARCHAR(255) NULL AFTER `nama_karyawan`;
```

#### Verifikasi SQL Berhasil:

Run query ini untuk cek:
```sql
-- Cek constraint sudah hilang (harusnya 0 rows)
SELECT CONSTRAINT_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_NAME = 'absensis'
  AND TABLE_SCHEMA = DATABASE()
  AND CONSTRAINT_NAME IN ('unique_employee_date', 'absensis_employee_id_tanggal_unique');

-- Cek column jabatan sudah ada (harusnya 1 row)
SHOW COLUMNS FROM `absensis` LIKE 'jabatan';
```

**Expected Result:**
- Query pertama: 0 rows (constraints sudah hilang)
- Query kedua: 1 row showing `jabatan | varchar(255) | YES | NULL`

---

### STEP 2: Uncomment Code di Controller

**File:** `app/Http/Controllers/AbsensiController.php`

**Line 1367** - Sekarang:
```php
// 'jabatan' => $employee->jabatan,  // ⚠️ DISABLED: Column doesn't exist yet
```

**Ubah jadi:**
```php
'jabatan' => $employee->jabatan ?? 'karyawan',  // ✅ ENABLED after SQL
```

**Cara Edit:**
1. Buka file `app/Http/Controllers/AbsensiController.php`
2. Go to line 1367
3. Hapus `//` di depan baris tersebut
4. Tambahkan ` ?? 'karyawan'` di akhir setelah `$employee->jabatan`
5. Save file

---

### STEP 3: Clear Cache Laravel

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

### STEP 4: Testing

#### Test 1: Double Shift - Setengah Hari
1. Buka form absensi: `/absensis/create`
2. Input **budi** - tanggal: 2025-11-01, status: setengah hari, pembibitan: #1
3. Klik **Simpan** → ✅ Berhasil
4. Input **budi** lagi (SAMA!) - tanggal: 2025-11-01 (SAMA!), status: setengah hari, pembibitan: #2 (BEDA!)
5. Klik **Simpan** → ✅ **Harusnya berhasil sekarang!**
6. Buka index → Filter tanggal 2025-11-01
7. ✅ Harusnya ada **2 baris** untuk budi:
   - Baris 1: Pembibitan #1, Gaji: 25,000
   - Baris 2: Pembibitan #2, Gaji: 25,000

#### Test 2: Role Detection
1. Buka form absensi
2. Input 3 karyawan berbeda:
   - **dimas** (employee) - role: karyawan
   - **anang** (gudang) - role: karyawan_gudang
   - **budi** (mandor) - role: mandor
3. Klik **Simpan** → ✅ Berhasil
4. Buka index → Filter tanggal hari ini
5. ✅ Kolom "Role" harusnya tampil:
   - dimas: **"karyawan kandang"**
   - anang: **"karyawan gudang"**
   - budi: **"mandor"**

#### Test 3: Validation Masih Kerja
**Test 3a: Max 2 Shifts**
- Coba input shift ke-3 untuk karyawan yang sudah 2 shift
- ❌ Harusnya ditolak: "sudah memiliki 2 shift pada [tanggal]"

**Test 3b: Same Pembibitan**
- Karyawan sudah setengah hari di pembibitan #1
- Coba input setengah hari lagi di pembibitan #1 (SAMA!)
- ❌ Harusnya ditolak: "sudah absen setengah hari di [nama pembibitan]"

**Test 3c: Full Day Block**
- Input karyawan: full day
- Coba input lagi: setengah hari di pembibitan lain
- ❌ Harusnya ditolak: "sudah absen Full Day pada [tanggal]"

---

## 📊 TECHNICAL DETAILS

### Database Changes:

**Before:**
```sql
-- Constraints blocking double shift:
unique_employee_date (nama_karyawan, tanggal)  -- Blocks same name, same date
absensis_employee_id_tanggal_unique (employee_id, tanggal)  -- Blocks same ID, same date

-- Missing column:
No 'jabatan' column in absensis table
```

**After:**
```sql
-- Constraints removed:
✅ unique_employee_date - DROPPED
✅ absensis_employee_id_tanggal_unique - DROPPED

-- New column added:
✅ jabatan VARCHAR(255) NULL
```

### Application Logic:

**Validation Rules (in AbsensiController):**
- ✅ RULE 1: Max 2 shifts per day
- ✅ RULE 2: Full day → No additional shifts
- ✅ RULE 3: Can't add full day if already has shift
- ✅ RULE 4: Half-day shifts must be at different pembibitans

**Role Storage:**
- Employee table → 'jabatan' column exists → direct read
- Gudang table → No 'jabatan' column → hardcoded 'karyawan_gudang' in code
- Mandor table → No 'jabatan' column → hardcoded 'mandor' in code
- Absensis table → 'jabatan' column added → stores role for ALL types

**DataTables Display:**
```php
// Priority 1: Read from stored jabatan field
if (!empty($absensi->jabatan)) {
    return match($absensi->jabatan) {
        'karyawan' => 'karyawan kandang',
        'karyawan_gudang' => 'karyawan gudang',
        'mandor' => 'mandor',
        default => $absensi->jabatan
    };
}

// Priority 2: Fallback to employee relationship (old records)
if ($absensi->employee && $absensi->employee->jabatan) {
    // ... same logic
}

return '-';
```

---

## 🚨 TROUBLESHOOTING

### Error: "Column 'jabatan' doesn't exist"
**Cause:** SQL script belum dijalankan atau gagal
**Fix:**
1. Run SQL script lagi
2. Verify dengan: `SHOW COLUMNS FROM absensis LIKE 'jabatan';`

### Error: "Duplicate entry 'nama-tanggal'"
**Cause:** Constraint belum di-drop atau SQL hanya drop sebagian
**Fix:**
1. Run SQL script lagi (safe to run multiple times)
2. Verify dengan query verification di atas

### Role masih kosong di index
**Cause:**
1. SQL belum dijalankan, atau
2. Line 1367 belum di-uncomment, atau
3. Cache belum di-clear

**Fix:**
1. Run SQL → Uncomment line 1367 → Clear cache → Test

### Double shift masih ditolak
**Cause:** Constraint masih ada di database
**Fix:**
1. Check constraints:
   ```sql
   SHOW INDEX FROM absensis;
   ```
2. Manual drop jika masih ada:
   ```sql
   ALTER TABLE absensis DROP INDEX unique_employee_date;
   ```

---

## ✅ EXPECTED FINAL STATE

### Database:
- ✅ No unique constraints blocking double shift
- ✅ Column 'jabatan' exists in absensis table
- ✅ Old data still works (jabatan can be NULL)

### Application:
- ✅ Form submission works for all employee types
- ✅ Double shift works (2x setengah hari, beda pembibitan)
- ✅ Role display works for karyawan, gudang, mandor
- ✅ Validation rules enforced in code
- ✅ No database errors
- ✅ Production-ready

### User Experience:
- ✅ Can assign employee to 2 different pembibitans same day
- ✅ Each shift counted separately (2x 25,000 = 50,000)
- ✅ Role clearly shown in index table
- ✅ Clear error messages when validation fails
- ✅ Fast performance (batch loading, bulk insert)

---

## 📞 SUPPORT

Jika setelah mengikuti panduan ini masih ada masalah:

1. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Check database:**
   ```sql
   -- See all constraints
   SHOW INDEX FROM absensis;

   -- See all columns
   SHOW COLUMNS FROM absensis;
   ```

3. **Test specific query:**
   ```sql
   -- Try insert manually
   INSERT INTO absensis (nama_karyawan, tanggal, status, jabatan, created_at, updated_at)
   VALUES ('test', '2025-11-10', 'setengah_hari', 'karyawan', NOW(), NOW()),
          ('test', '2025-11-10', 'setengah_hari', 'karyawan', NOW(), NOW());

   -- Should work without error
   ```

---

**Last Updated:** 2025-11-10
**Version:** 1.0
**Status:** Production-Ready ✅
