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
        Schema::create('prediksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('jenis_prediksi', ['laporan_karyawan', 'job_pekerjaan']);
            $table->string('bulan_prediksi');
            $table->float('hasil_prediksi');
            $table->uuid('kelompok_id')->nullable();
            $table->timestamps();
            
            $table->foreign('kelompok_id')->references('id')->on('kelompok')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksi');
    }
};



