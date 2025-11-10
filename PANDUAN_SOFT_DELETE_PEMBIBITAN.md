# PANDUAN SOFT DELETE PEMBIBITAN & FIX FILTER TANGGAL

## 📋 Ringkasan Perubahan

Implementasi soft delete untuk pembibitan dan perbaikan filter tanggal di laporan gaji agar sistem production-ready untuk operasi 2 tahun kedepan.

### Masalah yang Diperbaiki:

1. ❌ **Filter tanggal mulai dan selesai tidak berfungsi** - data tidak muncul saat rekap 1 bulan
2. ❌ **Pembibitan terhapus permanen** - kehilangan jejak rekaman historis
3. ❌ **Absensi dan laporan gaji hilang** ketika pembibitan dihapus

### Solusi yang Diimplementasikan:

1. ✅ **Fix filter tanggal** - menggunakan scope `tanggalRange` yang benar
2. ✅ **Soft delete pembibitan** - pembibitan yang dihapus tidak hilang permanen
3. ✅ **Preserve historical data** - rekaman absensi dan laporan tetap ada dan bisa ditampilkan
4. ✅ **Dropdown filter bersih** - pembibitan yang sudah dihapus tidak muncul di dropdown

---

## 🔧 CARA INSTALASI

### STEP 1: Execute SQL Script

Jalankan script berikut di **HeidiSQL** atau **phpMyAdmin**:

```sql
-- Add deleted_at column untuk soft delete
ALTER TABLE `pembibitans`
ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;
```

**Verifikasi:**
```sql
SHOW COLUMNS FROM `pembibitans` LIKE 'deleted_at';
```

**Expected Output:**
```
+------------+-----------+------+-----+---------+-------+
| Field      | Type      | Null | Key | Default | Extra |
+------------+-----------+------+-----+---------+-------+
| deleted_at | timestamp | YES  |     | NULL    |       |
+------------+-----------+------+-----+---------+-------+
```

### STEP 2: Verify Code Changes

Pastikan semua file berikut sudah ter-update:

#### ✅ **app/Models/Pembibitan.php**
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembibitan extends Model
{
    use HasFactory, SoftDeletes;
    // ...
}
```

#### ✅ **app/Models/Absensi.php**
```php
public function pembibitan()
{
    return $this->belongsTo(Pembibitan::class, 'pembibitan_id')->withTrashed();
}
```

#### ✅ **app/Models/SalaryReport.php**
```php
public function pembibitan()
{
    return $this->belongsTo(Pembibitan::class)->withTrashed();
}
```

#### ✅ **app/Http/Controllers/SalaryReportController.php** (Line 51-53)
```php
$query = SalaryReport::periode($tahun, $bulan)
    ->tipeKaryawan($tipe)
    ->tanggalRange($tanggalMulai, $tanggalSelesai);
```

---

## 🧪 TESTING

### Test 1: Filter Tanggal di Laporan Gaji

1. Buka halaman **Laporan Gaji**
2. Pilih **Tahun** dan **Bulan**
3. Set **Tanggal Mulai**: 01-11-2025
4. Set **Tanggal Selesai**: 30-11-2025
5. Klik **Filter**

**Expected Result:**
- ✅ Data muncul sesuai range tanggal
- ✅ Laporan yang overlap dengan range ditampilkan

### Test 2: Soft Delete Pembibitan

**Scenario A: Delete Pembibitan**

1. Buka halaman **Master Pembibitan**
2. Pilih salah satu pembibitan (misal: "Pembibitan A")
3. Klik **Delete**
4. Konfirmasi delete

**Expected Result:**
- ✅ Pembibitan hilang dari list
- ✅ Pembibitan tidak muncul di dropdown form absensi
- ✅ Data absensi lama tetap ada di database

**Verify di Database:**
```sql
-- Cek pembibitan yang di-soft delete
SELECT id, judul, deleted_at FROM pembibitans WHERE deleted_at IS NOT NULL;

-- Cek absensi masih ada meskipun pembibitan dihapus
SELECT COUNT(*) FROM absensis WHERE pembibitan_id = [ID_PEMBIBITAN_YANG_DIHAPUS];
```

**Scenario B: View Historical Data**

1. Buka **Transaksi Absensi**
2. Filter absensi bulan lalu yang pakai pembibitan yang sudah dihapus
3. Lihat detail absensi

**Expected Result:**
- ✅ Data absensi tetap tampil lengkap
- ✅ Nama pembibitan tetap muncul (meskipun sudah dihapus)
- ✅ Lokasi dan kandang dari pembibitan tetap bisa ditampilkan

**Scenario C: Laporan Gaji Historical**

1. Buka **Laporan Gaji**
2. Pilih periode bulan lalu (sebelum pembibitan dihapus)
3. Lihat laporan karyawan yang kerja di pembibitan yang sudah dihapus

**Expected Result:**
- ✅ Laporan gaji tetap tampil
- ✅ Pembibitan tetap bisa ditampilkan di detail
- ✅ Total gaji dihitung dengan benar

### Test 3: Dropdown Filter

**Scenario: Pembibitan Deleted Tidak Muncul di Dropdown**

1. Delete pembibitan "Pembibitan A"
2. Buka form **Create Absensi**
3. Lihat dropdown **Pembibitan**

**Expected Result:**
- ✅ "Pembibitan A" tidak muncul di dropdown
- ✅ Hanya pembibitan aktif yang muncul

4. Buka **Laporan Gaji** → Filter **Pembibitan**

**Expected Result:**
- ✅ "Pembibitan A" tidak muncul di dropdown filter
- ✅ Tapi data lama tetap bisa ditampilkan jika sudah ada

---

## 🔍 VERIFICATION QUERIES

### Query 1: Cek Soft Delete Bekerja
```sql
-- Pembibitan yang masih aktif
SELECT id, judul, deleted_at
FROM pembibitans
WHERE deleted_at IS NULL;

-- Pembibitan yang sudah dihapus (soft deleted)
SELECT id, judul, deleted_at
FROM pembibitans
WHERE deleted_at IS NOT NULL;
```

### Query 2: Cek Historical Data Preserved
```sql
-- Absensi dengan pembibitan yang sudah dihapus
SELECT
    a.id,
    a.nama_karyawan,
    a.tanggal,
    a.pembibitan_id,
    p.judul as pembibitan_nama,
    p.deleted_at as pembibitan_deleted
FROM absensis a
LEFT JOIN pembibitans p ON a.pembibitan_id = p.id
WHERE p.deleted_at IS NOT NULL
ORDER BY a.tanggal DESC
LIMIT 10;
```

### Query 3: Cek Salary Reports dengan Deleted Pembibitan
```sql
SELECT
    sr.id,
    sr.nama_karyawan,
    sr.tahun,
    sr.bulan,
    sr.pembibitan_id,
    p.judul as pembibitan_nama,
    p.deleted_at as pembibitan_deleted,
    sr.total_gaji
FROM salary_reports sr
LEFT JOIN pembibitans p ON sr.pembibitan_id = p.id
WHERE p.deleted_at IS NOT NULL
ORDER BY sr.tahun DESC, sr.bulan DESC
LIMIT 10;
```

---

## 📊 TECHNICAL DETAILS

### Soft Delete Implementation

**Model Changes:**
- `Pembibitan` model menggunakan `SoftDeletes` trait
- `delete()` method tidak menghapus record, hanya set `deleted_at` timestamp
- Query default otomatis exclude soft deleted records

**Relationship Changes:**
- `Absensi->pembibitan()` menggunakan `withTrashed()`
- `SalaryReport->pembibitan()` menggunakan `withTrashed()`
- Ini memastikan data historis tetap bisa di-load

**Controller Logic:**
- Dropdown pembibitan: `Pembibitan::get()` (otomatis exclude deleted)
- View absensi: `$absensi->pembibitan` (load termasuk deleted)
- View laporan: `$report->pembibitan` (load termasuk deleted)

### Filter Tanggal Fix

**Before (WRONG):**
```php
if ($tanggalMulai) {
    $query->where('tanggal_mulai', '>=', Carbon::parse($tanggalMulai));
}
if ($tanggalSelesai) {
    $query->where('tanggal_selesai', '<=', Carbon::parse($tanggalSelesai));
}
```
❌ Hanya menampilkan records yang fully contained dalam range

**After (CORRECT):**
```php
$query = SalaryReport::periode($tahun, $bulan)
    ->tipeKaryawan($tipe)
    ->tanggalRange($tanggalMulai, $tanggalSelesai);
```
✅ Menampilkan semua records yang overlap dengan range (menggunakan scope)

---

## 🎯 PRODUCTION READINESS CHECKLIST

- [x] Soft delete implemented untuk Pembibitan
- [x] Migration file dibuat
- [x] SQL script manual tersedia
- [x] Relasi updated dengan `withTrashed()`
- [x] Filter tanggal di laporan gaji fixed
- [x] Historical data preserved
- [x] Dropdown hanya tampilkan aktif
- [x] Testing scenarios documented
- [x] Verification queries provided

---

## 🚀 BENEFITS

### Untuk Operasi 2 Tahun Kedepan:

1. **Data Integrity** ✅
   - Tidak ada data hilang
   - Jejak historis lengkap
   - Audit trail tersedia

2. **Business Continuity** ✅
   - Pembibitan selesai bisa dihapus tanpa masalah
   - Laporan gaji bulan lalu tetap akurat
   - Rekaman absensi tetap utuh

3. **User Experience** ✅
   - Dropdown bersih (hanya aktif)
   - Historical reports tetap bisa diakses
   - Tidak ada data corruption

4. **Performance** ✅
   - Query otomatis filter deleted
   - Tidak perlu WHERE deleted_at IS NULL manual
   - Indexed properly dengan default Laravel behavior

---

## 📞 SUPPORT

Jika ada masalah atau pertanyaan:

1. Cek file ini terlebih dahulu
2. Jalankan verification queries
3. Pastikan migration sudah dijalankan
4. Cek log Laravel untuk error details

---

**Sistem ini sekarang production-ready untuk operasi 2 tahun kedepan!** 🎉
