-- ============================================================
-- CRITICAL FIX: Add 'jabatan' column to absensis table
-- ============================================================
-- Run this SQL in your database:
-- 1. Open phpMyAdmin or HeidiSQL (Laragon)
-- 2. Select your database
-- 3. Click "SQL" tab
-- 4. Copy-paste BOTH queries below
-- 5. Click "Go" / "Execute"
-- ============================================================

-- Step 1: Drop unique constraint (enable double shift)
ALTER TABLE `absensis`
DROP INDEX IF EXISTS `absensis_employee_id_tanggal_unique`;

-- Step 2: Add jabatan column (enable role detection)
ALTER TABLE `absensis`
ADD COLUMN `jabatan` VARCHAR(255) NULL
AFTER `nama_karyawan`;

-- ============================================================
-- VERIFICATION:
-- After running above SQL, verify with:
-- SHOW COLUMNS FROM `absensis`;
--
-- You should see:
-- - jabatan | varchar(255) | YES | NULL
-- ============================================================
