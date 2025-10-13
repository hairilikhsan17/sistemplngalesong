<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis',
        'bulan',
        'kelompok_id',
        'prediksi_waktu',
        'akurasi',
        'parameter_alpha',
        'parameter_beta',
        'parameter_gamma'
    ];

    protected $casts = [
        'bulan' => 'date',
        'prediksi_waktu' => 'decimal:1',
        'akurasi' => 'decimal:2',
        'parameter_alpha' => 'decimal:2',
        'parameter_beta' => 'decimal:2',
        'parameter_gamma' => 'decimal:2'
    ];

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }
}