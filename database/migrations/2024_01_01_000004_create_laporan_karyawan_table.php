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
        Schema::create('laporan_karyawan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('hari');
            $table->date('tanggal');
            $table->string('nama');
            $table->string('instansi');
            $table->string('jabatan');
            $table->string('alamat_tujuan');
            $table->string('dokumentasi')->nullable();
            $table->uuid('kelompok_id');
            $table->timestamps();
            
            $table->foreign('kelompok_id')->references('id')->on('kelompok')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_karyawan');
    }
};



