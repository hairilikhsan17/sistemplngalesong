<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\LaporanKaryawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JanuariOktober2025Seeder extends Seeder
{
    // Data kelompok dengan anggota
    private $kelompokData = [
        [
            'nama_kelompok' => 'Kelompok 1',
            'shift' => 'Shift 1',
            'username' => 'kelompok1',
            'password' => 'kelompok1123',
            'anggota' => [['nama' => 'Andi Pratama'], ['nama' => 'Rizky Ramadhan']]
        ],
        [
            'nama_kelompok' => 'Kelompok 2',
            'shift' => 'Shift 1',
            'username' => 'kelompok2',
            'password' => 'kelompok2123',
            'anggota' => [['nama' => 'Ahmad Fauzan'], ['nama' => 'Muhammad Ilham']]
        ],
        [
            'nama_kelompok' => 'Kelompok 3',
            'shift' => 'Shift 2',
            'username' => 'kelompok3',
            'password' => 'kelompok3123',
            'anggota' => [['nama' => 'Rahmat Hidayat'], ['nama' => 'Aditya Saputra']]
        ]
    ];

    private $instansiList = [
        'PT. PLN (Persero) UID Sulselrabar',
        'PT. PLN (Persero) Rayon Galesong',
        'PT. PLN (Persero) Area Takalar',
        'PT. PLN (Persero) Area Gowa',
        'PT. PLN (Persero) Area Makassar'
    ];

    private $alamatList = [
        'Bonto, Galesong',
        'Galesong Timur, Takalar',
        'Biringkassi, Galesong',
        'Mangarabombang, Takalar',
        'Galesong Kota'
    ];

    private $jamMasukList = ['07:30', '08:00', '08:30'];

    private $jenisKegiatanList = [
        'Perbaikan Meteran',
        'Perbaikan Sambungan Rumah',
        'Pemeriksaan Gardu',
        'Jenis Kegiatan lainnya'
    ];

    private $deskripsiKegiatan = [
        'Perbaikan Meteran' => 'Perbaikan meteran rusak, ganti komponen internal',
        'Perbaikan Sambungan Rumah' => 'Perbaikan sambungan rumah di trafo distribusi',
        'Pemeriksaan Gardu' => 'Pemeriksaan gardu distribusi, cek kondisi trafo',
        'Jenis Kegiatan lainnya' => 'Gangguan listrik di kompleks perumahan, perbaikan panel'
    ];

    private $hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function run(): void
    {
        $this->command->info('=== Memulai Seeder Data Januari - Oktober 2025 ===');
        
        // Pastikan kelompok ada
        foreach ($this->kelompokData as $data) {
            $kelompok = Kelompok::where('nama_kelompok', $data['nama_kelompok'])->first();
            
            if (!$kelompok) {
                $this->command->info("Membuat {$data['nama_kelompok']} (belum ada)...");
                
                $kelompok = Kelompok::create([
                    'id' => Str::uuid(),
                    'nama_kelompok' => $data['nama_kelompok'],
                    'shift' => $data['shift'],
                ]);
                
                User::create([
                    'id' => Str::uuid(),
                    'username' => $data['username'],
                    'password' => Hash::make($data['password']),
                    'role' => 'karyawan',
                    'kelompok_id' => $kelompok->id,
                ]);
                
                foreach ($data['anggota'] as $anggota) {
                    Karyawan::create([
                        'id' => Str::uuid(),
                        'nama' => $anggota['nama'],
                        'kelompok_id' => $kelompok->id,
                    ]);
                }
            }
        }

        $kelompoks = Kelompok::with('karyawan')->get();
        
        if ($kelompoks->isEmpty()) {
            $this->command->error('Tidak ada kelompok ditemukan. Jalankan SetupKelompokDanDataSeeder terlebih dahulu.');
            return;
        }

        $totalLaporan = 0;

        // Loop dari Bulan 1 (Januari) sampai Bulan 10 (Oktober)
        for ($month = 1; $month <= 10; $month++) {
            $startDate = Carbon::create(2025, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
            
            $this->command->info("Memproses Bulan {$month} (2025-{$month})...");

            foreach ($kelompoks as $kelompok) {
                $karyawans = $kelompok->karyawan;
                if ($karyawans->isEmpty()) continue;

                $currentDate = $startDate->copy();
                
                while ($currentDate->lte($endDate)) {
                    // Hari kerja (Senin-Jumat)
                    if ($currentDate->dayOfWeek >= 1 && $currentDate->dayOfWeek <= 5) {
                        $jumlahLaporan = rand(1, 2);
                        
                        for ($i = 0; $i < $jumlahLaporan; $i++) {
                            $karyawan = $karyawans->random();
                            $jenisKegiatan = $this->jenisKegiatanList[array_rand($this->jenisKegiatanList)];
                            
                            // Generate waktu (durasi bervariasi per bulan untuk simulasi tren/musiman)
                            // Kita buat durasi sedikit meningkat setiap bulannya untuk simulasi tren
                            $baseDuration = 60 + ($month * 5); 
                            $waktuMulai = Carbon::createFromTime(rand(8, 10), rand(0, 59), 0);
                            $waktuSelesai = $waktuMulai->copy()->addMinutes($baseDuration + rand(-20, 40));
                            
                            // HITUNG DALAM JAM (Bagi 60) agar sesuai dengan aplikasi
                            $durasi = $waktuMulai->diffInMinutes($waktuSelesai) / 60;

                            LaporanKaryawan::create([
                                'id' => Str::uuid(),
                                'hari' => $this->hariList[$currentDate->dayOfWeek],
                                'tanggal' => $currentDate->format('Y-m-d'),
                                'nama' => $karyawan->nama,
                                'instansi' => $this->instansiList[array_rand($this->instansiList)],
                                'jam_masuk' => $this->jamMasukList[array_rand($this->jamMasukList)],
                                'jenis_kegiatan' => $jenisKegiatan,
                                'deskripsi_kegiatan' => $this->deskripsiKegiatan[$jenisKegiatan],
                                'waktu_mulai_kegiatan' => $waktuMulai->format('H:i:s'),
                                'waktu_selesai_kegiatan' => $waktuSelesai->format('H:i:s'),
                                'durasi_waktu' => round($durasi, 2), 
                                'alamat_tujuan' => $this->alamatList[array_rand($this->alamatList)],
                                'kelompok_id' => $kelompok->id,
                            ]);
                            
                            $totalLaporan++;
                        }
                    }
                    $currentDate->addDay();
                }
            }
        }
        
        $this->command->info("=== Seeder Selesai. Total Laporan: {$totalLaporan} ===");
    }
}
