<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kegiatan;
use App\Models\Kelompok;
use Carbon\Carbon;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all kelompok from database
        $kelompoks = Kelompok::all();
        
        if ($kelompoks->isEmpty()) {
            $this->command->warn('Tidak ada kelompok di database. Silakan jalankan PlnGalesongSeeder terlebih dahulu.');
            return;
        }
        
        $kelompokNames = $kelompoks->pluck('nama_kelompok')->toArray();
        
        // Generate data kegiatan untuk 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            foreach ($kelompokNames as $kelompokName) {
                // Setiap kelompok melakukan 2-5 kegiatan per bulan
                $jumlahKegiatan = rand(2, 5);
                
                for ($j = 0; $j < $jumlahKegiatan; $j++) {
                    // Tanggal mulai di awal-pertengahan bulan
                    $tanggalMulai = $month->copy()->startOfMonth()->addDays(rand(0, 15));
                    
                    // Durasi 2-10 hari
                    $durasi = rand(2, 10);
                    $tanggalSelesai = $tanggalMulai->copy()->addDays($durasi - 1);
                    
                    // Pastikan tanggal selesai tidak melebihi akhir bulan
                    if ($tanggalSelesai->gt($month->copy()->endOfMonth())) {
                        $tanggalSelesai = $month->copy()->endOfMonth();
                    }
                    
                    Kegiatan::create([
                        'kelompok' => $kelompokName,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        // Durasi akan dihitung otomatis oleh model
                    ]);
                }
            }
        }
        
        $this->command->info('Data kegiatan berhasil dibuat untuk ' . count($kelompokNames) . ' kelompok selama 12 bulan terakhir.');
    }
}
