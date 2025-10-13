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
        Schema::create('job_pekerjaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('perbaikan_kwh')->default(0);
            $table->integer('pemeliharaan_pengkabelan')->default(0);
            $table->integer('pengecekan_gardu')->default(0);
            $table->integer('penanganan_gangguan')->default(0);
            $table->string('lokasi');
            $table->uuid('kelompok_id');
            $table->string('bulan_data');
            $table->date('tanggal');
            $table->integer('waktu_penyelesaian')->default(0);
            $table->timestamps();
            
            $table->foreign('kelompok_id')->references('id')->on('kelompok')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_pekerjaan');
    }
};



