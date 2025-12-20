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
            $table->string('alamat_tujuan');
            $table->string('jenis_kegiatan')->nullable();
            $table->text('deskripsi_kegiatan')->nullable();
            $table->time('waktu_mulai_kegiatan')->nullable();
            $table->time('waktu_selesai_kegiatan')->nullable();
            $table->decimal('durasi_waktu', 5, 2)->default(0)->comment('Durasi dalam jam, dihitung otomatis');
            $table->string('lokasi')->nullable();
            $table->string('file_path')->nullable()->comment('Path untuk foto/file dokumentasi');
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
        Schema::dropIfExists('laporan_karyawan');
    }
};
