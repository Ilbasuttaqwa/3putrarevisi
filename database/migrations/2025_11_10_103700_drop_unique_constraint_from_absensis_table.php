<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CRITICAL FIX: Remove unique constraint to enable double shift feature
     *
     * Business Requirement:
     * - Employees can work 2 half-day shifts at different pembibitans on the same date
     * - Validation is handled in application layer (AbsensiController RULE 1-4)
     * - Database constraint was blocking this core feature
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Drop the unique constraint that blocks double shifts
            // Constraint name: absensis_employee_id_tanggal_unique
            $table->dropUnique(['employee_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Restore the unique constraint
            // WARNING: This will prevent double shifts again
            $table->unique(['employee_id', 'tanggal']);
        });
    }
};
