<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKaryawan extends Model
{
    use HasFactory;

    protected $table = 'laporan_karyawan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'hari',
        'tanggal',
        'nama',
        'instansi',
        'jabatan',
        'alamat_tujuan',
        'dokumentasi',
        'file_path',
        'kelompok_id',
    ];

    protected $casts = [
        'id' => 'string',
        'tanggal' => 'date',
        'kelompok_id' => 'string',
    ];

    /**
     * Get the kelompok that owns the laporan karyawan.
     */
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}



