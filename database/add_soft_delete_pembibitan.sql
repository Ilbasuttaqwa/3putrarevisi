-- =====================================================================
-- ADD SOFT DELETE TO PEMBIBITANS TABLE
-- =====================================================================
-- Menambahkan soft delete untuk pembibitan agar rekaman absensi dan
-- laporan gaji tetap ada meskipun pembibitan sudah dihapus.
--
-- CARA EKSEKUSI:
-- 1. Buka HeidiSQL atau phpMyAdmin
-- 2. Pilih database yang sesuai
-- 3. Copy-paste script ini dan execute
-- =====================================================================

-- Add deleted_at column untuk soft delete
ALTER TABLE `pembibitans`
ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`;

-- Verifikasi perubahan
SHOW COLUMNS FROM `pembibitans` LIKE 'deleted_at';

-- ✅ SELESAI: Sekarang pembibitan yang dihapus tidak hilang permanen
--    dan rekaman absensi tetap bisa menampilkan data pembibitan yang sudah dihapus
