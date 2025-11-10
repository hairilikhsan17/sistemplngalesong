<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelompok',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->tanggal_mulai && $model->tanggal_selesai) {
                $model->durasi = Carbon::parse($model->tanggal_mulai)
                    ->diffInDays(Carbon::parse($model->tanggal_selesai)) + 1;
            }
        });

        static::updating(function ($model) {
            if ($model->tanggal_mulai && $model->tanggal_selesai) {
                $model->durasi = Carbon::parse($model->tanggal_mulai)
                    ->diffInDays(Carbon::parse($model->tanggal_selesai)) + 1;
            }
        });
    }
}
