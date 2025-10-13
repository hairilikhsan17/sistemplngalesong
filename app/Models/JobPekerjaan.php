<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'job_pekerjaan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'perbaikan_kwh',
        'pemeliharaan_pengkabelan',
        'pengecekan_gardu',
        'penanganan_gangguan',
        'lokasi',
        'kelompok_id',
        'hari',
        'tanggal',
        'waktu_penyelesaian',
    ];

    protected $casts = [
        'id' => 'string',
        'perbaikan_kwh' => 'string',
        'pemeliharaan_pengkabelan' => 'string',
        'pengecekan_gardu' => 'string',
        'penanganan_gangguan' => 'string',
        'waktu_penyelesaian' => 'integer',
        'tanggal' => 'date',
        'kelompok_id' => 'string',
    ];

    /**
     * Get the kelompok that owns the job pekerjaan.
     */
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}



