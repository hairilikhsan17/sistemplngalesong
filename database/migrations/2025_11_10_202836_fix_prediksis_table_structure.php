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
        // Drop existing table if it exists with old structure
        Schema::dropIfExists('prediksis');
        
        // Create new table with correct structure
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksis');
        
        // Recreate old structure if needed (for rollback)
        Schema::create('prediksis', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->date('bulan');
            $table->unsignedBigInteger('kelompok_id');
            $table->decimal('prediksi_waktu', 8, 2);
            $table->decimal('akurasi', 5, 2);
            $table->decimal('parameter_alpha', 3, 2);
            $table->decimal('parameter_beta', 3, 2);
            $table->decimal('parameter_gamma', 3, 2);
            $table->timestamps();
        });
    }
};
