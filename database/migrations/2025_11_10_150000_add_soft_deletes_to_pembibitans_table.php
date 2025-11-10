<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan soft delete untuk pembibitan agar rekaman absensi dan laporan gaji
     * tetap ada meskipun pembibitan sudah dihapus (untuk jejak historis).
     */
    public function up(): void
    {
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
