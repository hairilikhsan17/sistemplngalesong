<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrediksiKegiatan extends Model
{
    use HasFactory;

    protected $table = 'prediksi_kegiatan';

    protected $fillable = [
        'kelompok_id',
        'jenis_kegiatan',
        'tanggal_prediksi',
        'prediksi_jam',
        'mae',
        'mape',
        'waktu_generate',
        'params',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'waktu_generate' => 'datetime',
        'prediksi_jam' => 'float',
        'mae' => 'float',
        'mape' => 'float',
        'params' => 'array',
    ];

    /**
     * Get the kelompok that owns the prediksi kegiatan.
     */
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}
