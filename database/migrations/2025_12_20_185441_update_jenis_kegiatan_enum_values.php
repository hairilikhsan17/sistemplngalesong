<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Langkah 1: Ubah kolom dari enum menjadi string dulu
        // Karena MySQL/MariaDB tidak bisa update enum langsung jika nilai baru tidak ada di enum list
        Schema::table('laporan_karyawan', function (Blueprint $table) {
            $table->string('jenis_kegiatan')->nullable()->change();
        });

        // Langkah 2: Update data yang sudah ada dari format lama ke format baru
        // Hanya update jika ada data dengan nilai lama
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Perbaikan Meteran' WHERE jenis_kegiatan = 'Perbaikan KWH'");
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Perbaikan Sambungan Rumah' WHERE jenis_kegiatan = 'Pemeliharaan Pengkabelan'");
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Pemeriksaan Gardu' WHERE jenis_kegiatan = 'Pengecekan Gardu'");
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Jenis Kegiatan' WHERE jenis_kegiatan = 'Penanganan Gangguan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update data kembali ke format lama
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Perbaikan KWH' WHERE jenis_kegiatan = 'Perbaikan Meteran'");
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Pemeliharaan Pengkabelan' WHERE jenis_kegiatan = 'Perbaikan Sambungan Rumah'");
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Pengecekan Gardu' WHERE jenis_kegiatan = 'Pemeriksaan Gardu'");
        DB::statement("UPDATE laporan_karyawan SET jenis_kegiatan = 'Penanganan Gangguan' WHERE jenis_kegiatan = 'Jenis Kegiatan'");

        // Kembalikan ke enum dengan nilai lama
        Schema::table('laporan_karyawan', function (Blueprint $table) {
            $table->enum('jenis_kegiatan', [
                'Perbaikan KWH',
                'Pemeliharaan Pengkabelan',
                'Pengecekan Gardu',
                'Penanganan Gangguan'
            ])->nullable()->change();
        });
    }
};
