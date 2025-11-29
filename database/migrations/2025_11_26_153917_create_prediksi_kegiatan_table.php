<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prediksi_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->uuid('kelompok_id');
            $table->string('jenis_kegiatan'); // perbaikan_kwh, pemeliharaan_pengkabelan, pengecekan_gardu, penanganan_gangguan
            $table->date('tanggal_prediksi'); // Tanggal besok (tomorrow)
            $table->float('prediksi_jam'); // Hasil prediksi dalam jam
            $table->float('mae')->nullable(); // Mean Absolute Error
            $table->float('mape')->nullable(); // Mean Absolute Percentage Error
            $table->datetime('waktu_generate'); // Waktu generate
            $table->json('params')->nullable(); // Simpan parameter alpha, beta, gamma
            $table->timestamps();
            
            $table->foreign('kelompok_id')->references('id')->on('kelompok')->onDelete('cascade');
            $table->index(['kelompok_id', 'tanggal_prediksi', 'jenis_kegiatan'], 'idx_prediksi_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksi_kegiatan');
    }
};
