<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'hasil_prediksi',
        'akurasi',
        'metode',
        'params',
    ];

    protected $casts = [
        'hasil_prediksi' => 'float',
        'akurasi' => 'float',
        'params' => 'array',
    ];
}
