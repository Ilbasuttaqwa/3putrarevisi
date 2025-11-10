<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CRITICAL FIX: Add jabatan column for role detection
     *
     * History:
     * - Old migration added 'role_karyawan' column
     * - Later migration dropped 'role_karyawan' (2025_10_23_163216)
     * - Now database has NO role column
     * - Application saves to 'jabatan' field → Column not found error
     *
     * This migration adds 'jabatan' column to store employee role:
     * - 'karyawan' (from employees table)
     * - 'karyawan_gudang' (from gudangs table)
     * - 'mandor' (from mandors table)
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Add jabatan column after nama_karyawan
            $table->string('jabatan')->nullable()->after('nama_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn('jabatan');
        });
    }
};
