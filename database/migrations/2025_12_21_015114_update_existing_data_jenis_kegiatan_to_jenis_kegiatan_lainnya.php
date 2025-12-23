<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update data yang sudah ada dari "Jenis Kegiatan" menjadi "Jenis Kegiatan lainnya"
     * Migration ini opsional - hanya diperlukan jika ada data lama yang masih menggunakan "Jenis Kegiatan"
     * Jika menggunakan seeder SetupKelompokDanDataSeeder, data lama akan dihapus dan dibuat ulang
     */
    public function up(): void
    {
        // Update data lama dari "Jenis Kegiatan" menjadi "Jenis Kegiatan lainnya"
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Jenis Kegiatan lainnya' WHERE jenis_kegiatan = 'Jenis Kegiatan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update kembali ke format lama (jika diperlukan rollback)
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Jenis Kegiatan' WHERE jenis_kegiatan = 'Jenis Kegiatan lainnya'");
    }
};
