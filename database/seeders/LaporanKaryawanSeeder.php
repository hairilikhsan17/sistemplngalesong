<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LaporanKaryawanSeeder extends Seeder
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
        
        $totalLaporan = 0;
        
        // Generate data untuk periode Januari 2023 - Oktober 2025 (34 bulan)
        $startDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::create(2025, 10, 31);
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            foreach ($kelompoks as $kelompok) {
                // Get karyawan dari kelompok ini
                $karyawans = Karyawan::where('kelompok_id', $kelompok->id)->get();
                
                if ($karyawans->isEmpty()) {
                    // Jika tidak ada karyawan, gunakan nama default
                    $namaKaryawan = 'Karyawan ' . $kelompok->nama_kelompok;
                } else {
                    // Pilih karyawan random dari kelompok
                    $namaKaryawan = $karyawans->random()->nama;
                }
                
                // Setiap hari kerja (Senin-Jumat) ada kemungkinan laporan
                // Weekend (Sabtu-Minggu) lebih sedikit
                $dayOfWeek = $currentDate->dayOfWeek; // 0 = Minggu, 6 = Sabtu
                
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    // Hari kerja: 60-80% kemungkinan ada laporan
                    $shouldCreate = rand(1, 100) <= 75;
                } else {
                    // Weekend: 20-40% kemungkinan ada laporan
                    $shouldCreate = rand(1, 100) <= 30;
                }
                
                if ($shouldCreate) {
                    // Variasi jumlah laporan per hari (1-3 laporan)
                    $jumlahLaporan = rand(1, 3);
                    
                    for ($i = 0; $i < $jumlahLaporan; $i++) {
                        LaporanKaryawan::create([
                            'id' => Str::uuid(),
                            'hari' => $currentDate->locale('id')->dayName,
                            'tanggal' => $currentDate->copy(),
                            'nama' => $namaKaryawan,
                            'instansi' => 'PLN Unit Induk Distribusi Sulselrabar',
                            'jabatan' => 'Teknisi',
                            'alamat_tujuan' => $this->getRandomLokasi(),
                            'dokumentasi' => 'Seeder Data',
                            'kelompok_id' => $kelompok->id,
                        ]);
                        
                        $totalLaporan++;
                    }
                }
            }
            
            // Move to next day
            $currentDate->addDay();
        }
        
        $this->command->info("✅ Data laporan karyawan berhasil dibuat!");
        $this->command->info("   - Periode: Januari 2023 - Oktober 2025 (34 bulan)");
        $this->command->info("   - Total laporan: " . $totalLaporan);
        $this->command->info("   - Jumlah kelompok: " . $kelompoks->count());
        $this->command->info("   - Rata-rata per bulan: " . round($totalLaporan / 34, 2) . " laporan");
    }
    
    /**
     * Get random lokasi untuk alamat tujuan
     */
    private function getRandomLokasi(): string
    {
        $lokasis = [
            'Gardu Induk Galesong',
            'Gardu Distribusi Takalar',
            'Gardu Distribusi Gowa',
            'Jalan Poros Makassar-Takalar',
            'Kawasan Industri Galesong',
            'Pemukiman Warga Takalar',
            'Pasar Tradisional Galesong',
            'Sekolah Dasar Galesong',
            'Puskesmas Galesong',
            'Masjid Agung Galesong',
            'Pelabuhan Galesong',
            'Kawasan Wisata Pantai',
            'Perumahan Perumahan Galesong',
            'Pabrik Galesong',
            'SPBU Galesong',
        ];
        
        return $lokasis[array_rand($lokasis)];
    }
}
