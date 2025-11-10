<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gaji_pokok',
        'jabatan',
        'kandang_id',
        'lokasi_id',
        'lokasi_kerja',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
    ];

    /**
     * Get the kandang that the employee belongs to.
     */
    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    /**
     * Get the lokasi that the employee belongs to.
     */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Get the kandangs for the employee (many-to-many for additional assignments).
     */
    public function kandangs()
    {
        return $this->belongsToMany(Kandang::class, 'kandang_employee');
    }

    /**
     * Get the absensis for the employee.
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Accessor untuk mendapatkan jabatan (sesuai ERD)
     * FIXED: Safe access to avoid "Undefined array key" errors
     */
    public function getJabatanAttribute($value)
    {
        // Return value if exists
        if (!empty($value)) {
            return $value;
        }

        // Fallback: Try 'role' column (for databases before migration)
        // Use attributes array to safely access without triggering accessor loop
        $role = $this->attributes['role'] ?? null;
        if (!empty($role)) {
            return $role;
        }

        // Default fallback
        return 'karyawan';
    }

    /**
     * Accessor untuk mengecek apakah mandor
     */
    public function getIsMandorAttribute()
    {
        return $this->jabatan === 'mandor';
    }

    /**
     * Accessor untuk mengecek apakah gudang kandang
     */
    public function getIsGudangKandangAttribute()
    {
        return $this->jabatan === 'gud_kandang';
    }
}
