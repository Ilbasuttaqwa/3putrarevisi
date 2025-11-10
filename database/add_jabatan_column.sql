-- ============================================================
-- CRITICAL FIX: Enable Double Shift + Role Detection
-- ============================================================
-- MASALAH:
-- 1. Double shift blocked by 'unique_employee_date' constraint
-- 2. Role gudang/mandor tidak tampil di index (kolom 'jabatan' belum ada)
--
-- SOLUSI:
-- 1. Drop SEMUA unique constraints yang block double shift
-- 2. Add column 'jabatan' untuk simpan role
--
-- CARA JALANKAN:
-- 1. Buka HeidiSQL atau phpMyAdmin
-- 2. Select database Anda
-- 3. Klik tab "SQL"
-- 4. Copy-paste SEMUA query di bawah ini
-- 5. Klik "Execute" / "Go"
-- ============================================================

-- ============================================================
-- STEP 1: Drop Unique Constraints (Enable Double Shift)
-- ============================================================

-- Drop constraint 1: unique_employee_date (nama_karyawan, tanggal)
-- This is THE MAIN BLOCKER for double shift feature!
ALTER TABLE `absensis`
DROP INDEX IF EXISTS `unique_employee_date`;

-- Drop constraint 2: absensis_employee_id_tanggal_unique (employee_id, tanggal)
-- This was from old migration
ALTER TABLE `absensis`
DROP INDEX IF EXISTS `absensis_employee_id_tanggal_unique`;

-- ============================================================
-- STEP 2: Add jabatan Column (Enable Role Detection)
-- ============================================================

-- Add jabatan column to store employee role
-- This will fix "Role kosong" issue for gudang and mandor
ALTER TABLE `absensis`
ADD COLUMN IF NOT EXISTS `jabatan` VARCHAR(255) NULL
AFTER `nama_karyawan`;

-- ============================================================
-- VERIFICATION QUERIES (Run these after above):
-- ============================================================

-- 1. Check constraints are removed:
-- SELECT * FROM information_schema.TABLE_CONSTRAINTS
-- WHERE TABLE_NAME = 'absensis' AND TABLE_SCHEMA = DATABASE();
-- Should NOT see 'unique_employee_date' or 'absensis_employee_id_tanggal_unique'

-- 2. Check jabatan column exists:
-- SHOW COLUMNS FROM `absensis` LIKE 'jabatan';
-- Should show: jabatan | varchar(255) | YES | NULL

-- ============================================================
-- EXPECTED RESULTS:
-- ============================================================
-- ✅ Karyawan bisa absen 2x per hari (setengah hari) di pembibitan berbeda
-- ✅ Role "karyawan gudang" dan "mandor" tampil di index
-- ✅ Validation tetap jalan (max 2 shift, beda pembibitan)
-- ============================================================
