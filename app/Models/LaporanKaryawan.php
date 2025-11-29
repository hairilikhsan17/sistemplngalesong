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
        'alamat_tujuan',
        'jenis_kegiatan',
        'deskripsi_kegiatan',
        'waktu_mulai_kegiatan',
        'waktu_selesai_kegiatan',
        'durasi_waktu',
        'lokasi',
        'file_path',
        'kelompok_id',
    ];

    protected $casts = [
        'id' => 'string',
        'tanggal' => 'date',
        'kelompok_id' => 'string',
        'durasi_waktu' => 'decimal:2',
    ];

    /**
     * Get the kelompok that owns the laporan karyawan.
     */
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}
