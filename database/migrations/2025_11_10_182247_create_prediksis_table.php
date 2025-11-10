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
        // Table already created by fix_prediksis_table_structure migration
        // This migration is kept for reference but won't create the table again
        if (!Schema::hasTable('prediksis')) {
            Schema::create('prediksis', function (Blueprint $table) {
                $table->id();
                $table->string('bulan'); // format: YYYY-MM (contoh: 2025-05)
                $table->float('hasil_prediksi'); // hari atau jumlah laporan
                $table->float('akurasi')->nullable(); // persen
                $table->string('metode')->default('Holt-Winters');
                $table->json('params')->nullable(); // simpan alpha,beta,gamma,tipe,kelompok,bulan_target
                $table->timestamps();
                
                // Add index for faster queries
                $table->index('bulan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksis');
    }
};
