<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;
use App\Models\Karyawan;
use App\Models\LaporanKaryawan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LaporanKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all kelompok
        $kelompoks = Kelompok::all();
        
        foreach ($kelompoks as $kelompok) {
            $this->generateLaporanData($kelompok);
        }
    }

    /**
     * Generate random laporan data for a kelompok
     */
    private function generateLaporanData($kelompok)
    {
        // Get karyawan dari kelompok ini
        $karyawans = Karyawan::where('kelompok_id', $kelompok->id)->get();
        
        if ($karyawans->isEmpty()) {
            return;
        }

        // Data acak untuk jenis kegiatan
        $jenisKegiatan = [
            'Perbaikan KWH',
            'Pemeliharaan Pengkabelan',
            'Pengecekan Gardu',
            'Penanganan Gangguan'
        ];

        // Data acak untuk instansi
        $instansiList = [
            'PLN Galesong',
            'PLN Cabang Takalar',
            'PLN Cabang Makassar',
            'PLN Cabang Gowa'
        ];

        // Data acak untuk alamat tujuan
        $alamatTujuanList = [
            'Jl. Poros Galesong, Takalar',
            'Jl. Raya Takalar - Galesong',
            'Jl. Poros Galesong KM 5',
            'Jl. Poros Galesong KM 10',
            'Jl. Poros Galesong KM 15',
            'Jl. Poros Galesong KM 20',
            'Jl. Raya Galesong - Sanrobone',
            'Jl. Poros Galesong - Bontomanai',
            'Jl. Poros Galesong - Bontotangnga',
            'Jl. Poros Galesong - Bontomarannu'
        ];

        // Data acak untuk lokasi
        $lokasiList = [
            'Gardu Induk Galesong',
            'Gardu Distribusi KM 5',
            'Gardu Distribusi KM 10',
            'Gardu Distribusi KM 15',
            'Gardu Distribusi KM 20',
            'Jaringan Kabel Galesong',
            'Jaringan Kabel Takalar',
            'Jaringan Kabel Sanrobone',
            'Jaringan Kabel Bontomanai',
            'Jaringan Kabel Bontotangnga'
        ];

        // Data deskripsi untuk Penanganan Gangguan
        $deskripsiGangguan = [
            'Pohon tumbang mengenai kabel listrik, dilakukan perbaikan dengan mengganti kabel yang rusak',
            'Kabel putus akibat cuaca buruk, dilakukan perbaikan dengan menyambung kabel baru',
            'Gardu mengalami gangguan listrik, dilakukan perbaikan komponen yang rusak',
            'Jaringan kabel mengalami korsleting, dilakukan perbaikan dengan mengganti kabel yang rusak',
            'Tiang listrik roboh, dilakukan perbaikan dengan memasang tiang baru',
            'Transformator mengalami gangguan, dilakukan perbaikan dengan mengganti komponen yang rusak',
            'Kabel listrik putus akibat hewan, dilakukan perbaikan dengan menyambung kabel baru',
            'Gardu distribusi mengalami gangguan, dilakukan perbaikan dengan mengganti komponen yang rusak'
        ];

        // Data deskripsi untuk jenis kegiatan lain (opsional)
        $deskripsiLain = [
            'Melakukan perbaikan dan pemeliharaan rutin',
            'Melakukan pengecekan dan perawatan berkala',
            'Melakukan inspeksi dan perbaikan komponen',
            'Melakukan pemeliharaan preventif',
            'Melakukan perbaikan dan penggantian komponen',
            'Melakukan pengecekan dan perawatan sistem',
            'Melakukan inspeksi dan perbaikan instalasi',
            'Melakukan pemeliharaan dan perbaikan rutin'
        ];

        // Generate data dari 1 Januari 2024 sampai 29 November 2025
        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2025, 11, 29);
        
        // Generate sekitar 50-100 laporan per karyawan
        $laporanPerKaryawan = rand(50, 100);
        
        foreach ($karyawans as $karyawan) {
            $laporanCount = 0;
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate) && $laporanCount < $laporanPerKaryawan) {
                // Random apakah akan membuat laporan di tanggal ini (sekitar 10-15% kemungkinan)
                if (rand(1, 100) <= 12) {
                    $hari = $this->getHariIndonesia($currentDate->dayOfWeek);
                    $jenisKegiatanSelected = $jenisKegiatan[array_rand($jenisKegiatan)];
                    
                    // Generate waktu mulai (antara 07:00 - 16:00)
                    $waktuMulaiJam = rand(7, 16);
                    $waktuMulaiMenit = rand(0, 59);
                    $waktuMulai = Carbon::createFromTime($waktuMulaiJam, $waktuMulaiMenit, 0);
                    
                    // Generate waktu selesai (1-8 jam setelah waktu mulai)
                    $durasiJam = rand(1, 8);
                    $durasiMenit = rand(0, 59);
                    $waktuSelesai = $waktuMulai->copy()->addHours($durasiJam)->addMinutes($durasiMenit);
                    
                    // Jika waktu selesai melewati tengah malam, set ke hari berikutnya
                    if ($waktuSelesai->hour >= 24) {
                        $waktuSelesai->subDay()->setTime(23, 59, 0);
                    }
                    
                    // Hitung durasi
                    $durasiWaktu = $waktuMulai->diffInMinutes($waktuSelesai) / 60;
                    
                    // Generate deskripsi
                    $deskripsiKegiatan = null;
                    if ($jenisKegiatanSelected === 'Penanganan Gangguan') {
                        $deskripsiKegiatan = $deskripsiGangguan[array_rand($deskripsiGangguan)];
                    } elseif (rand(1, 100) <= 30) { // 30% kemungkinan ada deskripsi untuk jenis lain
                        $deskripsiKegiatan = $deskripsiLain[array_rand($deskripsiLain)];
                    }
                    
                    LaporanKaryawan::create([
                        'id' => Str::uuid(),
                        'hari' => $hari,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'nama' => $karyawan->nama,
                        'instansi' => $instansiList[array_rand($instansiList)],
                        'alamat_tujuan' => $alamatTujuanList[array_rand($alamatTujuanList)],
                        'jenis_kegiatan' => $jenisKegiatanSelected,
                        'deskripsi_kegiatan' => $deskripsiKegiatan,
                        'waktu_mulai_kegiatan' => $waktuMulai->format('H:i:s'),
                        'waktu_selesai_kegiatan' => $waktuSelesai->format('H:i:s'),
                        'durasi_waktu' => round($durasiWaktu, 2),
                        'lokasi' => $lokasiList[array_rand($lokasiList)],
                        'file_path' => null, // Tidak ada file untuk data dummy
                        'kelompok_id' => $kelompok->id,
                        'created_at' => $currentDate->copy()->setTime(rand(8, 17), rand(0, 59), rand(0, 59)),
                        'updated_at' => $currentDate->copy()->setTime(rand(8, 17), rand(0, 59), rand(0, 59)),
                    ]);
                    
                    $laporanCount++;
                }
                
                // Pindah ke hari berikutnya
                $currentDate->addDay();
            }
        }
    }

    /**
     * Get hari dalam bahasa Indonesia
     */
    private function getHariIndonesia($dayOfWeek)
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
        
        return $hari[$dayOfWeek] ?? 'Senin';
    }
}




