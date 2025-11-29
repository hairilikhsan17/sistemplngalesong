<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LaporanKaryawanHistorisSeeder extends Seeder
{
    // Data kelompok dan nama anggota
    private $kelompokData = [
        [
            'nama' => 'Kelompok 1',
            'nama_pendek' => 'Kelompok 1',
            'anggota' => ['Karyawan 1', 'Karyawan 2'],
            'shift' => 'Shift 1'
        ],
        [
            'nama' => 'Kelompok 2',
            'nama_pendek' => 'Kelompok 2',
            'anggota' => ['Karyawan 1', 'Karyawan 2'],
            'shift' => 'Shift 1'
        ],
        [
            'nama' => 'Kelompok 3',
            'nama_pendek' => 'Kelompok 3',
            'anggota' => ['Karyawan 1', 'Karyawan 2'],
            'shift' => 'Shift 2'
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
        'Bonto',
        'Galesong Timur',
        'Biringkassi',
        'Mangarabombang',
        'Galesong',
        "Pa'lalakkang",
        'Bontolempangan',
        'Bonto-bontoa',
        'Kalukuang',
        'Galesong Kota',
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

    // Detail kegiatan realistis
    private $perbaikanKWHList = [
        'Perbaikan kwh meter rusak, ganti komponen internal',
        'Perbaikan kwh meter error, kalibrasi ulang',
        'Perbaikan kwh meter mati, cek koneksi dan power',
        'Perbaikan kwh meter terbakar, ganti unit baru',
        'Perbaikan kwh meter tidak akurat, service komponen',
        'Perbaikan kwh meter display error, ganti display',
        'Perbaikan kwh meter putus kabel, perbaikan koneksi'
    ];

    private $pemeliharaanPengkabelanList = [
        'Pemeliharaan pengkabelan di saluran udara 20kV',
        'Pemeliharaan pengkabelan di jaringan bawah tanah',
        'Pemeliharaan pengkabelan di gardu distribusi',
        'Pemeliharaan pengkabelan di trafo distribusi',
        'Pemeliharaan pengkabelan di panel utama',
        'Pemeliharaan pengkabelan di terminal box',
        'Pemeliharaan pengkabelan di junction box',
        'Pemeliharaan pengkabelan di saluran tegangan rendah'
    ];

    private $pengecekanGarduList = [
        'Pengecekan gardu distribusi, cek kondisi trafo',
        'Pengecekan gardu induk, test isolasi dan grounding',
        'Pengecekan gardu trafo, inspeksi visual dan elektrik',
        'Pengecekan gardu panel, test sistem proteksi',
        'Pengecekan gardu distribusi, cek temperature dan beban',
        'Pengecekan gardu trafo, service rutin bulanan',
        'Pengecekan gardu induk, test kemampuan beban',
        'Pengecekan gardu panel, kalibrasi alat ukur'
    ];

    private $penangananGangguanList = [
        'Penanganan gangguan listrik padam, cek penyebab',
        'Penanganan gangguan tegangan drop, perbaikan jaringan',
        'Penanganan gangguan arus bocor, isolasi ulang',
        'Penanganan gangguan MCB trip, reset dan cek beban',
        'Penanganan gangguan korsleting, perbaikan kabel',
        'Penanganan gangguan trafo panas, pendinginan',
        'Penanganan gangguan kabel putus, perbaikan sambungan',
        'Penanganan gangguan gardu rusak, perbaikan komponen'
    ];

    // Pola waktu untuk setiap jenis kegiatan (dalam jam)
    private $waktuPerbaikanKWH = [2.8, 3.1, 2.9, 2.5, 3.2, 2.7, 3.0];
    private $waktuPemeliharaanPengkabelan = [4.2, 4.7, 3.8, 4.5, 4.0, 4.3, 3.9];
    private $waktuPengecekanGardu = [1.5, 1.3, 1.8, 1.2, 1.6, 1.4, 1.7];
    private $waktuPenangananGangguan = [3.9, 4.5, 3.6, 4.2, 3.8, 4.0, 4.3];

    // Lokasi A-J
    private $lokasiLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mulai membuat data historis laporan karyawan...');
        
        // Hapus semua data laporan sebelumnya
        LaporanKaryawan::truncate();
        $this->command->info('Data laporan sebelumnya telah dihapus.');
        
        // Buat atau ambil kelompok
        $kelompoks = [];
        foreach ($this->kelompokData as $data) {
            $kelompok = Kelompok::firstOrCreate(
                ['nama_kelompok' => $data['nama_pendek']],
                [
                    'id' => Str::uuid(),
                    'shift' => $data['shift']
                ]
            );
            $kelompoks[] = [
                'model' => $kelompok,
                'data' => $data
            ];
        }

        // Periode data: Januari 2024 sampai 27 November 2025
        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2025, 11, 27);
        
        $totalLaporan = 0;

        // Generate data untuk setiap kelompok
        foreach ($kelompoks as $kelompokInfo) {
            $kelompok = $kelompokInfo['model'];
            $kelompokNama = $kelompokInfo['data']['nama_pendek'];
            
            $this->command->info("\nMemproses " . $kelompok->nama_kelompok . "...");
            
            $currentDate = $startDate->copy();
            $lokasiCounter = 0; // Untuk lokasi konsisten A-J
            $fotoCounter = 1; // Untuk dokumentasi konsisten
            $kegiatanCounter = 0; // Counter untuk rotasi jenis kegiatan per kelompok
            
            // Counter untuk memilih waktu dari array
            $counterPerbaikanKWH = 0;
            $counterPemeliharaan = 0;
            $counterPengecekan = 0;
            $counterPenanganan = 0;

            while ($currentDate->lte($endDate)) {
                // Skip hari Minggu (tidak ada kegiatan)
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                    // Setiap hari ada 1 kegiatan (sesuai pola contoh)
                    $hari = $this->getHariIndonesia($currentDate->dayOfWeek);
                    
                    // Pilih jenis kegiatan secara rotasi (sesuai pola contoh)
                    $jenisKegiatanIndex = ($kegiatanCounter % 4);
                    $jenisKegiatan = $this->pilihJenisKegiatanRotasi($jenisKegiatanIndex, $counterPerbaikanKWH, $counterPemeliharaan, $counterPengecekan, $counterPenanganan);
                    
                    // Update counter
                    if ($jenisKegiatan['field'] === 'perbaikan_kwh') {
                        $counterPerbaikanKWH = ($counterPerbaikanKWH + 1) % count($this->waktuPerbaikanKWH);
                    } elseif ($jenisKegiatan['field'] === 'pemeliharaan_pengkabelan') {
                        $counterPemeliharaan = ($counterPemeliharaan + 1) % count($this->waktuPemeliharaanPengkabelan);
                    } elseif ($jenisKegiatan['field'] === 'pengecekan_gardu') {
                        $counterPengecekan = ($counterPengecekan + 1) % count($this->waktuPengecekanGardu);
                    } else {
                        $counterPenanganan = ($counterPenanganan + 1) % count($this->waktuPenangananGangguan);
                    }
                    
                    // Lokasi A-J berulang
                    $lokasiLabel = $this->lokasiLabels[$lokasiCounter % count($this->lokasiLabels)];
                    $lokasi = 'Lokasi ' . $lokasiLabel;
                    
                    // Pilih salah satu anggota kelompok untuk input laporan (karena satu kelompok, salah satu mereka saja yang input)
                    $anggotaKelompok = $kelompokInfo['data']['anggota'];
                    $namaKaryawan = $anggotaKelompok[array_rand($anggotaKelompok)];
                    
                    // Generate data laporan
                    $laporanData = [
                        'id' => Str::uuid(),
                        'hari' => $hari,
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'nama' => $namaKaryawan, // Gunakan nama salah satu anggota kelompok
                        'instansi' => 'PLN',
                        'alamat_tujuan' => $this->alamatList[array_rand($this->alamatList)],
                        'lokasi' => $lokasi,
                        'waktu_penyelesaian' => (int) round($jenisKegiatan['waktu']),
                        'dokumentasi' => 'foto' . $fotoCounter . '.jpg',
                        'kelompok_id' => $kelompok->id,
                    ];

                    // Isi hanya kolom kegiatan yang dikerjakan dengan nilai waktu
                    $laporanData[$jenisKegiatan['field']] = (string) $jenisKegiatan['waktu'];
                    
                    // Set kolom lain ke null
                    $otherFields = ['perbaikan_kwh', 'pemeliharaan_pengkabelan', 'pengecekan_gardu', 'penanganan_gangguan'];
                    foreach ($otherFields as $field) {
                        if ($field !== $jenisKegiatan['field']) {
                            $laporanData[$field] = null;
                        }
                    }

                    LaporanKaryawan::create($laporanData);
                    $totalLaporan++;
                    $kegiatanCounter++;
                    
                    $lokasiCounter++;
                    $fotoCounter++;
                }
                
                $currentDate->addDay();
            }
        }
        
        $this->command->info("\n\n✅ Selesai! Total " . number_format($totalLaporan, 0, ',', '.') . " laporan berhasil dibuat.");
        $this->command->info("📊 Periode: " . $startDate->format('d M Y') . " - " . $endDate->format('d M Y'));
    }

    private function pilihJenisKegiatanRotasi($index, $counterPerbaikan, $counterPemeliharaan, $counterPengecekan, $counterPenanganan): array
    {
        $kegiatan = [
            [
                'field' => 'perbaikan_kwh',
                'waktu' => $this->waktuPerbaikanKWH[$counterPerbaikan]
            ],
            [
                'field' => 'pemeliharaan_pengkabelan',
                'waktu' => $this->waktuPemeliharaanPengkabelan[$counterPemeliharaan]
            ],
            [
                'field' => 'pengecekan_gardu',
                'waktu' => $this->waktuPengecekanGardu[$counterPengecekan]
            ],
            [
                'field' => 'penanganan_gangguan',
                'waktu' => $this->waktuPenangananGangguan[$counterPenanganan]
            ]
        ];
        
        return $kegiatan[$index % 4];
    }


    private function getHariIndonesia(int $dayOfWeek): string
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
        
        return $hari[$dayOfWeek];
    }
}

