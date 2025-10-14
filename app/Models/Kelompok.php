<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompok';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama_kelompok',
        'shift',
        'avatar',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    /**
     * Get the users for the kelompok.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the karyawan for the kelompok.
     */
    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }

    /**
     * Get the laporan karyawan for the kelompok.
     */
    public function laporanKaryawan(): HasMany
    {
        return $this->hasMany(LaporanKaryawan::class);
    }

    /**
     * Get the job pekerjaan for the kelompok.
     */
    public function jobPekerjaan(): HasMany
    {
        return $this->hasMany(JobPekerjaan::class);
    }

    /**
     * Get the prediksi for the kelompok.
     */
    public function prediksi(): HasMany
    {
        return $this->hasMany(Prediksi::class);
    }
}



