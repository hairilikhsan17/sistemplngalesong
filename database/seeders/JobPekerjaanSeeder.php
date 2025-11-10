<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPekerjaan;
use App\Models\Kelompok;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobPekerjaanSeeder extends Seeder
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
        
        $totalJob = 0;
        
        // Generate data untuk periode Januari 2023 - Oktober 2025 (34 bulan)
        // Generate per bulan untuk efisiensi
        $startYear = 2023;
        $startMonth = 1;
        $endYear = 2025;
        $endMonth = 10;
        
        // Loop dari Jan 2024 sampai Okt 2025
        for ($year = $startYear; $year <= $endYear; $year++) {
            $monthStart = ($year == $startYear) ? $startMonth : 1;
            $monthEnd = ($year == $endYear) ? $endMonth : 12;
            
            for ($month = $monthStart; $month <= $monthEnd; $month++) {
                $currentMonth = Carbon::create($year, $month, 1);
                
                foreach ($kelompoks as $kelompok) {
                    // Setiap kelompok melakukan 3-8 job per bulan
                    $jumlahJob = rand(3, 8);
                    
                    for ($j = 0; $j < $jumlahJob; $j++) {
                        // Tanggal random dalam bulan tersebut
                        $tanggal = $currentMonth->copy()->addDays(rand(0, $currentMonth->daysInMonth - 1));
                        
                        // Generate nilai untuk setiap jenis pekerjaan
                        $perbaikanKwh = rand(0, 5);
                        $pemeliharaanPengkabelan = rand(0, 4);
                        $pengecekanGardu = rand(0, 6);
                        $penangananGangguan = rand(0, 3);
                        
                        // Pastikan minimal ada 1 jenis pekerjaan
                        if ($perbaikanKwh == 0 && $pemeliharaanPengkabelan == 0 && 
                            $pengecekanGardu == 0 && $penangananGangguan == 0) {
                            $pengecekanGardu = rand(1, 3);
                        }
                        
                        // Waktu penyelesaian berdasarkan jenis pekerjaan
                        // Semakin banyak pekerjaan, semakin lama waktu penyelesaian
                        $totalPekerjaan = $perbaikanKwh + $pemeliharaanPengkabelan + 
                                         $pengecekanGardu + $penangananGangguan;
                        
                        // Waktu penyelesaian: 1-2 hari per jenis pekerjaan, minimal 2 hari
                        $waktuPenyelesaian = max(2, $totalPekerjaan * rand(1, 2));
                        
                        // Maksimal 15 hari
                        if ($waktuPenyelesaian > 15) {
                            $waktuPenyelesaian = 15;
                        }
                        
                        JobPekerjaan::create([
                            'id' => Str::uuid(),
                            'perbaikan_kwh' => $perbaikanKwh,
                            'pemeliharaan_pengkabelan' => $pemeliharaanPengkabelan,
                            'pengecekan_gardu' => $pengecekanGardu,
                            'penanganan_gangguan' => $penangananGangguan,
                            'lokasi' => $this->getRandomLokasi(),
                            'kelompok_id' => $kelompok->id,
                            'tanggal' => $tanggal,
                            'hari' => $tanggal->locale('id')->dayName,
                            'waktu_penyelesaian' => $waktuPenyelesaian,
                        ]);
                        
                        $totalJob++;
                    }
                }
            }
        }
        
        $this->command->info("✅ Data job pekerjaan berhasil dibuat!");
        $this->command->info("   - Periode: Januari 2023 - Oktober 2025 (34 bulan)");
        $this->command->info("   - Total job: " . $totalJob);
        $this->command->info("   - Jumlah kelompok: " . $kelompoks->count());
        $this->command->info("   - Rata-rata per bulan: " . round($totalJob / 34, 2) . " job");
    }
    
    /**
     * Get random lokasi untuk job pekerjaan
     */
    private function getRandomLokasi(): string
    {
        $lokasis = [
            'Gardu Induk Galesong',
            'Gardu Distribusi Takalar',
            'Gardu Distribusi Gowa',
            'Jalan Poros Makassar-Takalar KM 45',
            'Jalan Poros Makassar-Takalar KM 50',
            'Kawasan Industri Galesong',
            'Pemukiman Warga Takalar',
            'Pasar Tradisional Galesong',
            'Sekolah Dasar Galesong',
            'Puskesmas Galesong',
            'Masjid Agung Galesong',
            'Pelabuhan Galesong',
            'Kawasan Wisata Pantai Galesong',
            'Perumahan Galesong Baru',
            'Pabrik Galesong',
            'SPBU Galesong',
            'Rumah Sakit Galesong',
            'Kantor Camat Galesong',
            'Terminal Galesong',
            'Stasiun Galesong',
        ];
        
        return $lokasis[array_rand($lokasis)];
    }
}
