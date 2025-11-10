<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CRITICAL FIX: Remove ALL unique constraints to enable double shift feature
     *
     * Business Requirement:
     * - Employees can work 2 half-day shifts at different pembibitans on the same date
     * - Validation is handled in application layer (AbsensiController RULE 1-4)
     * - Database constraints were blocking this core feature
     *
     * Constraints to remove:
     * 1. unique_employee_date (nama_karyawan, tanggal) - THE MAIN BLOCKER
     * 2. absensis_employee_id_tanggal_unique (employee_id, tanggal) - Legacy
     */
    public function up(): void
    {
        // Drop constraint 1: unique_employee_date (nama_karyawan, tanggal)
        // This is the MAIN blocker from migration 2025_10_23_210000
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('unique_employee_date');
        });

        // Drop constraint 2: absensis_employee_id_tanggal_unique (employee_id, tanggal)
        // Legacy constraint from migration 2025_10_14_100200
        // Use separate Schema call to handle gracefully if doesn't exist
        try {
            Schema::table('absensis', function (Blueprint $table) {
                $table->dropUnique(['employee_id', 'tanggal']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist, ignore
            \Log::info('Legacy constraint absensis_employee_id_tanggal_unique not found, skipping');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Restore unique_employee_date constraint
            // WARNING: This will prevent double shifts again
            $table->unique(['nama_karyawan', 'tanggal'], 'unique_employee_date');
        });
    }
};
