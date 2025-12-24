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

class SetupKelompokDanDataSeeder extends Seeder
{
    // Data kelompok dengan anggota
    private $kelompokData = [
        [
            'nama_kelompok' => 'Kelompok 1',
            'shift' => 'Shift 1',
            'username' => 'kelompok1',
            'password' => 'kelompok1123',
            'anggota' => [
                ['nama' => 'Andi Pratama'],
                ['nama' => 'Rizky Ramadhan']
            ]
        ],
        [
            'nama_kelompok' => 'Kelompok 2',
            'shift' => 'Shift 1',
            'username' => 'kelompok2',
            'password' => 'kelompok2123',
            'anggota' => [
                ['nama' => 'Ahmad Fauzan'],
                ['nama' => 'Muhammad Ilham']
            ]
        ],
        [
            'nama_kelompok' => 'Kelompok 3',
            'shift' => 'Shift 2',
            'username' => 'kelompok3',
            'password' => 'kelompok3123',
            'anggota' => [
                ['nama' => 'Rahmat Hidayat'],
                ['nama' => 'Aditya Saputra']
            ]
        ]
    ];

    // Data instansi realistis
    private $instansiList = [
        'PT. PLN (Persero) UID Sulselrabar',
        'PT. PLN (Persero) Rayon Galesong',
        'PT. PLN (Persero) Area Takalar',
        'PT. PLN (Persero) Area Gowa',
        'PT. PLN (Persero) Area Makassar',
        'PT. PLN (Persero) Area Maros',
        'PT. PLN (Persero) Area Pangkep',
        'PT. PLN (Persero) Area Jeneponto',
        'PT. PLN (Persero) Area Bantaeng',
        'PT. PLN (Persero) Area Bulukumba'
    ];

    // Alamat tujuan realistis
    private $alamatList = [
        'Bonto, Galesong',
        'Galesong Timur, Takalar',
        'Biringkassi, Galesong',
        'Mangarabombang, Takalar',
        'Galesong Kota',
        "Pa'lalakkang, Galesong",
        'Bontolempangan, Takalar',
        'Bonto-bontoa, Galesong',
        'Kalukuang, Galesong',
        'Jl. Ahmad Yani No. 45, Galesong, Takalar',
        'Jl. Poros Galesong No. 12, Galesong Utara',
        'Jl. Raya Takalar No. 88, Galesong Selatan',
        'Jl. Bontomanai No. 23, Galesong',
        'Jl. Tamalate No. 67, Galesong'
    ];

    // Lokasi realistis
    private $lokasiList = [
        'Lokasi A - Gardu Induk Galesong',
        'Lokasi B - Trafo Distribusi Utama',
        'Lokasi C - Jaringan Udara 20kV',
        'Lokasi D - Jaringan Kabel Bawah Tanah',
        'Lokasi E - Gardu Distribusi',
        'Lokasi F - Jaringan Tegangan Rendah',
        'Lokasi G - Pilar Trafo',
        'Lokasi H - Saluran Udara Tegangan Menengah',
        'Lokasi I - SUTT 70kV',
        'Lokasi J - Gardu Trafo Distribusi'
    ];

    // Jenis kegiatan dengan nama baru
    private $jenisKegiatanList = [
        'Perbaikan Meteran',
        'Perbaikan Sambungan Rumah',
        'Pemeriksaan Gardu',
        'Jenis Kegiatan lainnya'
    ];

    // Deskripsi kegiatan realistis
    private $deskripsiKegiatan = [
        'Perbaikan Meteran' => [
            'Perbaikan meteran rusak, ganti komponen internal',
            'Perbaikan meteran error, kalibrasi ulang',
            'Perbaikan meteran mati, cek koneksi dan power',
            'Perbaikan meteran terbakar, ganti unit baru',
            'Perbaikan meteran tidak akurat, service komponen',
            'Perbaikan meteran display error, ganti display',
            'Perbaikan meteran putus kabel, perbaikan koneksi'
        ],
        'Perbaikan Sambungan Rumah' => [
            'Perbaikan sambungan rumah di saluran udara 20kV',
            'Perbaikan sambungan rumah di jaringan bawah tanah',
            'Perbaikan sambungan rumah di gardu distribusi',
            'Perbaikan sambungan rumah di trafo distribusi',
            'Perbaikan sambungan rumah di panel utama',
            'Perbaikan sambungan rumah di terminal box',
            'Perbaikan sambungan rumah di junction box',
            'Perbaikan sambungan rumah di saluran tegangan rendah'
        ],
        'Pemeriksaan Gardu' => [
            'Pemeriksaan gardu distribusi, cek kondisi trafo',
            'Pemeriksaan gardu induk, test isolasi dan grounding',
            'Pemeriksaan gardu trafo, inspeksi visual dan elektrik',
            'Pemeriksaan gardu panel, test sistem proteksi',
            'Pemeriksaan gardu distribusi, cek temperature dan beban',
            'Pemeriksaan gardu trafo, service rutin bulanan',
            'Pemeriksaan gardu induk, test kemampuan beban',
            'Pemeriksaan gardu panel, kalibrasi alat ukur'
        ],
        'Jenis Kegiatan lainnya' => [
            'Pohon tumbang mengenai kabel listrik di Jl. Poros Galesong, dilakukan perbaikan dengan mengganti kabel yang rusak',
            'Gangguan listrik di RT 05 akibat petir, dilakukan pengecekan dan perbaikan instalasi',
            'Kabel putus di saluran udara akibat angin kencang, dilakukan perbaikan dengan mengganti kabel baru',
            'Gardu terbakar di Jl. Ahmad Yani, dilakukan pemadaman dan perbaikan komponen yang rusak',
            'Gangguan listrik di kompleks perumahan, dilakukan pengecekan dan perbaikan panel distribusi',
            'Kabel bawah tanah rusak akibat banjir, dilakukan penggantian kabel dan perbaikan instalasi',
            'Trafo overload di gardu distribusi, dilakukan pengecekan beban dan perbaikan sistem',
            'Gangguan listrik akibat hewan masuk ke gardu, dilakukan pembersihan dan perbaikan'
        ]
    ];

    // Hari dalam bahasa Indonesia
    private $hariList = [
        'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Memulai Setup Kelompok dan Data ===');
        
        // Hapus data lama jika ada (urutan penting karena foreign key)
        $this->command->info('Menghapus data lama...');
        LaporanKaryawan::truncate();
        Karyawan::truncate();
        // Hapus user karyawan saja, biarkan atasan tetap ada
        User::where('role', 'karyawan')->delete();
        // Hapus kelompok setelah semua data yang mereferensikannya dihapus
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kelompok::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Buat user atasan terlebih dahulu
        $this->command->info('Membuat user atasan...');
        
        // Cek apakah atasan sudah ada, jika ada update passwordnya
        $atasan = User::where('username', 'atasan')->where('role', 'atasan')->first();
        if ($atasan) {
            $atasan->password = Hash::make('atasan123');
            $atasan->save();
            $this->command->info('✓ User atasan sudah ada, password diupdate');
        } else {
            // Cek jika ada user admin lama, ubah jadi atasan
            $oldAdmin = User::where('username', 'admin')->where('role', 'atasan')->first();
            if ($oldAdmin) {
                $oldAdmin->username = 'atasan';
                $oldAdmin->password = Hash::make('atasan123');
                $oldAdmin->save();
                $this->command->info('✓ User admin lama diubah menjadi atasan');
            } else {
                User::create([
                    'id' => Str::uuid(),
                    'username' => 'atasan',
                    'password' => Hash::make('atasan123'),
                    'role' => 'atasan',
                    'kelompok_id' => null,
                ]);
                $this->command->info('✓ User atasan berhasil dibuat');
            }
        }
        
        $kelompoks = [];
        
        // Buat kelompok, user, dan karyawan
        foreach ($this->kelompokData as $data) {
            $this->command->info("Membuat {$data['nama_kelompok']}...");
            
            // Buat kelompok
            $kelompok = Kelompok::create([
                'id' => Str::uuid(),
                'nama_kelompok' => $data['nama_kelompok'],
                'shift' => $data['shift'],
            ]);
            
            // Buat user login
            User::create([
                'id' => Str::uuid(),
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'role' => 'karyawan',
                'kelompok_id' => $kelompok->id,
            ]);
            
            // Buat karyawan
            $karyawans = [];
            foreach ($data['anggota'] as $anggota) {
                $karyawan = Karyawan::create([
                    'id' => Str::uuid(),
                    'nama' => $anggota['nama'],
                    'kelompok_id' => $kelompok->id,
                ]);
                $karyawans[] = $karyawan;
            }
            
            $kelompoks[] = [
                'kelompok' => $kelompok,
                'karyawans' => $karyawans
            ];
            
            $this->command->info("✓ {$data['nama_kelompok']} berhasil dibuat dengan " . count($karyawans) . " anggota");
        }
        
        // Generate data laporan dari Januari 2025 sampai Desember 2025 (sampai hari ini)
        $this->command->info('Membuat data laporan dari Januari 2025 sampai sekarang...');
        
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::now(); // Sampai hari ini
        
        $totalLaporan = 0;
        
        foreach ($kelompoks as $kelompokInfo) {
            $kelompok = $kelompokInfo['kelompok'];
            $karyawans = $kelompokInfo['karyawans'];
            
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Setiap hari kerja (Senin-Jumat) ada kemungkinan 1-3 laporan
                $hariNama = $this->hariList[$currentDate->dayOfWeek];
                
                if ($currentDate->dayOfWeek >= 1 && $currentDate->dayOfWeek <= 5) {
                    // Hari kerja - lebih banyak aktivitas
                    $jumlahLaporan = rand(1, 3);
                } else {
                    // Weekend - lebih sedikit aktivitas
                    $jumlahLaporan = rand(0, 1);
                }
                
                for ($i = 0; $i < $jumlahLaporan; $i++) {
                    // Pilih karyawan secara acak
                    $karyawan = $karyawans[array_rand($karyawans)];
                    
                    // Pilih jenis kegiatan
                    $jenisKegiatan = $this->jenisKegiatanList[array_rand($this->jenisKegiatanList)];
                    
                    // Generate waktu mulai dan selesai
                    $waktuMulai = Carbon::createFromTime(rand(7, 9), rand(0, 59), 0);
                    $waktuSelesai = $waktuMulai->copy()->addHours(rand(2, 6))->addMinutes(rand(0, 59));
                    
                    // Hitung durasi
                    $durasi = $waktuMulai->diffInMinutes($waktuSelesai) / 60;
                    
                    // Generate deskripsi
                    $deskripsi = '';
                    if ($jenisKegiatan === 'Jenis Kegiatan lainnya') {
                        $deskripsi = $this->deskripsiKegiatan[$jenisKegiatan][array_rand($this->deskripsiKegiatan[$jenisKegiatan])];
                    } elseif (isset($this->deskripsiKegiatan[$jenisKegiatan])) {
                        $deskripsi = $this->deskripsiKegiatan[$jenisKegiatan][array_rand($this->deskripsiKegiatan[$jenisKegiatan])];
                    }
                    
                    // Buat laporan
                    LaporanKaryawan::create([
                        'id' => Str::uuid(),
                        'hari' => $hariNama,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'nama' => $karyawan->nama,
                        'instansi' => $this->instansiList[array_rand($this->instansiList)],
                        'alamat_tujuan' => $this->alamatList[array_rand($this->alamatList)],
                        'jenis_kegiatan' => $jenisKegiatan,
                        'deskripsi_kegiatan' => $deskripsi,
                        'waktu_mulai_kegiatan' => $waktuMulai->format('H:i:s'),
                        'waktu_selesai_kegiatan' => $waktuSelesai->format('H:i:s'),
                        'durasi_waktu' => round($durasi, 2),
                        'lokasi' => $this->lokasiList[array_rand($this->lokasiList)],
                        'file_path' => null,
                        'kelompok_id' => $kelompok->id,
                    ]);
                    
                    $totalLaporan++;
                }
                
                $currentDate->addDay();
            }
            
            $this->command->info("✓ Data laporan untuk {$kelompok->nama_kelompok} selesai");
        }
        
        $this->command->info("=== Setup Selesai ===");
        $this->command->info("Total Kelompok: " . count($kelompoks));
        $this->command->info("Total Karyawan: " . Karyawan::count());
        $this->command->info("Total Laporan: {$totalLaporan}");
        $this->command->info("");
        $this->command->info("=== Informasi Login ===");
        $this->command->info("");
        $this->command->info("=== Atasan ===");
        $this->command->info("Username: atasan");
        $this->command->info("Password: atasan123");
        $this->command->info("");
        $this->command->info("=== Kelompok ===");
        foreach ($this->kelompokData as $data) {
            $this->command->info("Username: {$data['username']}");
            $this->command->info("Password: {$data['password']}");
            $this->command->info("");
        }
    }
}
// class Desember2025Seeder extends Seeder