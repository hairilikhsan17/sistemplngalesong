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
        Schema::table('job_pekerjaan', function (Blueprint $table) {
            // Add hari column
            $table->string('hari')->nullable()->after('kelompok_id');
            
            // Change column types from integer to text for job descriptions
            $table->text('perbaikan_kwh')->change();
            $table->text('pemeliharaan_pengkabelan')->change();
            $table->text('pengecekan_gardu')->change();
            $table->text('penanganan_gangguan')->change();
            
            // Drop bulan_data column if it exists
            if (Schema::hasColumn('job_pekerjaan', 'bulan_data')) {
                $table->dropColumn('bulan_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_pekerjaan', function (Blueprint $table) {
            // Drop hari column
            $table->dropColumn('hari');
            
            // Revert column types back to integer
            $table->integer('perbaikan_kwh')->change();
            $table->integer('pemeliharaan_pengkabelan')->change();
            $table->integer('pengecekan_gardu')->change();
            $table->integer('penanganan_gangguan')->change();
            
            // Add back bulan_data column
            $table->string('bulan_data')->after('kelompok_id');
        });
    }
};
