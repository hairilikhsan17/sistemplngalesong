<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query
        $query = LaporanKaryawan::with('kelompok');
        
        // If user is karyawan, only show their group's reports
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }
        
        // Calculate statistics
        $totalLaporan = (clone $query)->count();
        $laporanHariIni = (clone $query)->whereDate('tanggal', today())->count();
        $laporanBulanIni = (clone $query)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        
        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        
        if ($request->filled('instansi')) {
            $query->where('instansi', 'like', '%' . $request->instansi . '%');
        }
        
        $laporans = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        
        $statistics = [
            'totalLaporan' => $totalLaporan,
            'laporanHariIni' => $laporanHariIni,
            'laporanBulanIni' => $laporanBulanIni,
        ];
        
        // Get karyawan from the same kelompok
        $karyawans = collect([]);
        if ($user->isKaryawan() && $user->kelompok_id) {
            $karyawans = Karyawan::where('kelompok_id', $user->kelompok_id)
                ->orderBy('nama', 'asc')
                ->get();
        }
        
        return view('dashboard.kelompok.laporan', compact('laporans', 'statistics', 'karyawans'));
    }
    
    public function getLaporans()
    {
        $user = Auth::user();
        $query = LaporanKaryawan::with('kelompok');
        
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }
        
        $laporans = $query->orderBy('created_at', 'desc')->get();
        return response()->json($laporans);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'jam_masuk' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan lainnya',
            'deskripsi_kegiatan' => 'nullable|string|required_if:jenis_kegiatan,Jenis Kegiatan lainnya',
            'waktu_mulai_kegiatan' => 'nullable|date_format:H:i',
            'waktu_selesai_kegiatan' => 'nullable|date_format:H:i',
            'alamat_tujuan' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'deskripsi_kegiatan.required_if' => 'Deskripsi Jenis Kegiatan lainnya wajib diisi ketika jenis kegiatan adalah Jenis Kegiatan lainnya.',
        ]);
        
        $user = Auth::user();
        
        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }
        
        // Hitung durasi waktu otomatis
        $durasiWaktu = 0;
        if ($request->waktu_mulai_kegiatan && $request->waktu_selesai_kegiatan) {
            try {
                $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_mulai_kegiatan);
                $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_selesai_kegiatan);
                
                // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
                if ($waktuSelesai->lt($waktuMulai)) {
                    $waktuSelesai->addDay();
                }
                
                $diffInMinutes = $waktuMulai->diffInMinutes($waktuSelesai);
                $durasiWaktu = round($diffInMinutes / 60, 2);
            } catch (\Exception $e) {
                $durasiWaktu = 0;
            }
        }
        
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan-dokumentasi', $fileName, 'public');
        }
        
        $laporan = LaporanKaryawan::create([
            'id' => Str::uuid(),
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'jam_masuk' => $request->jam_masuk,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'alamat_tujuan' => $request->alamat_tujuan,
            'file_path' => $filePath,
            'kelompok_id' => $user->kelompok_id,
        ]);
        
        return response()->json($laporan->load('kelompok'));
    }
    
    public function show($id)
    {
        $laporan = LaporanKaryawan::with('kelompok')->findOrFail($id);
        return response()->json($laporan);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'hari' => 'required|string',
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'jam_masuk' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan lainnya',
            'deskripsi_kegiatan' => 'nullable|string',
            'waktu_mulai_kegiatan' => 'nullable|date_format:H:i',
            'waktu_selesai_kegiatan' => 'nullable|date_format:H:i',
            'alamat_tujuan' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        
        // Validasi khusus: deskripsi wajib jika jenis kegiatan adalah Jenis Kegiatan lainnya
        if ($request->jenis_kegiatan === 'Jenis Kegiatan lainnya' && empty($request->deskripsi_kegiatan)) {
            return response()->json([
                'success' => false,
                'message' => 'Deskripsi Jenis Kegiatan lainnya wajib diisi ketika jenis kegiatan adalah Jenis Kegiatan lainnya.',
                'errors' => [
                    'deskripsi_kegiatan' => ['Deskripsi Jenis Kegiatan lainnya wajib diisi.']
                ]
            ], 422);
        }
        
        $laporan = LaporanKaryawan::findOrFail($id);
        
        // Hitung durasi waktu otomatis
        $durasiWaktu = 0;
        if ($request->waktu_mulai_kegiatan && $request->waktu_selesai_kegiatan) {
            try {
                $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_mulai_kegiatan);
                $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_selesai_kegiatan);
                
                // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
                if ($waktuSelesai->lt($waktuMulai)) {
                    $waktuSelesai->addDay();
                }
                
                $diffInMinutes = $waktuMulai->diffInMinutes($waktuSelesai);
                $durasiWaktu = round($diffInMinutes / 60, 2);
            } catch (\Exception $e) {
                $durasiWaktu = 0;
            }
        }
        
        $updateData = [
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'jam_masuk' => $request->jam_masuk,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'alamat_tujuan' => $request->alamat_tujuan,
        ];
        
        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
                Storage::disk('public')->delete($laporan->file_path);
            }
            
            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan-dokumentasi', $fileName, 'public');
            $updateData['file_path'] = $filePath;
        }
        
        $laporan->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui',
            'data' => $laporan->load('kelompok')
        ]);
    }
    
    public function destroy($id)
    {
        $laporan = LaporanKaryawan::findOrFail($id);
        
        // Delete file if exists
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
        }
        
        $laporan->delete();
        
        return response()->json(['success' => true]);
    }
    
    public function downloadFile($id)
    {
        $laporan = LaporanKaryawan::findOrFail($id);
        
        if (!$laporan->file_path || !Storage::disk('public')->exists($laporan->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
        
        return Storage::disk('public')->download($laporan->file_path);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $user = Auth::user();
        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $importedCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Structure: Hari, Tanggal, Nama, Instansi, Jam Masuk, Waktu Mulai, Jenis Kegiatan, Deskripsi, Waktu Selesai, Alamat, Dokumentasi
                // Index: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
                
                $data = [
                    'hari' => trim($row[0] ?? ''),
                    'tanggal' => trim($row[1] ?? ''),
                    'nama' => trim($row[2] ?? ''),
                    'instansi' => trim($row[3] ?? ''),
                    'jam_masuk' => trim($row[4] ?? ''),
                    'waktu_mulai_kegiatan' => trim($row[5] ?? ''),
                    'jenis_kegiatan' => trim($row[6] ?? ''),
                    'deskripsi_kegiatan' => trim($row[7] ?? ''),
                    'waktu_selesai_kegiatan' => trim($row[8] ?? ''),
                    'alamat_tujuan' => trim($row[9] ?? ''),
                ];

                // Basic validation
                if (!$data['hari'] || !$data['tanggal'] || !$data['nama'] || !$data['instansi'] || !$data['jam_masuk']) {
                    $errors[] = "Baris " . ($index + 2) . ": Data wajib (Hari, Tanggal, Nama, Instansi, Jam Masuk) tidak lengkap.";
                    continue;
                }

                // Normalize Jenis Kegiatan for comparison
                $jenisKegiatanLower = strtolower($data['jenis_kegiatan']);
                $isLainnya = ($jenisKegiatanLower === 'jenis kegiatan lainnya');

                // Validation for Deskripsi Kegiatan
                if ($isLainnya && empty($data['deskripsi_kegiatan'])) {
                    $errors[] = "Baris " . ($index + 2) . ": Deskripsi Kegiatan wajib diisi jika Jenis Kegiatan adalah 'Jenis Kegiatan lainnya'.";
                    continue;
                }

                // Map to exact enum values if possible
                $validJenis = [
                    'perbaikan meteran' => 'Perbaikan Meteran',
                    'perbaikan sambungan rumah' => 'Perbaikan Sambungan Rumah',
                    'pemeriksaan gardu' => 'Pemeriksaan Gardu',
                    'jenis kegiatan lainnya' => 'Jenis Kegiatan lainnya'
                ];
                
                if (isset($validJenis[$jenisKegiatanLower])) {
                    $data['jenis_kegiatan'] = $validJenis[$jenisKegiatanLower];
                }

                // Calculate Duration
                $durasiWaktu = 0;
                if ($data['waktu_mulai_kegiatan'] && $data['waktu_selesai_kegiatan']) {
                    try {
                        // Handle Excel time format or string format
                        $waktuMulai = \Carbon\Carbon::parse($data['waktu_mulai_kegiatan']);
                        $waktuSelesai = \Carbon\Carbon::parse($data['waktu_selesai_kegiatan']);
                        
                        if ($waktuSelesai->lt($waktuMulai)) {
                            $waktuSelesai->addDay();
                        }
                        
                        $diffInMinutes = $waktuMulai->diffInMinutes($waktuSelesai);
                        $durasiWaktu = round($diffInMinutes / 60, 2);
                    } catch (\Exception $e) {
                        $durasiWaktu = 0;
                    }
                }

                // Format times and dates for database
                try {
                    if ($data['waktu_mulai_kegiatan']) {
                        $data['waktu_mulai_kegiatan'] = \Carbon\Carbon::parse($data['waktu_mulai_kegiatan'])->format('H:i');
                    }
                    if ($data['waktu_selesai_kegiatan']) {
                        $data['waktu_selesai_kegiatan'] = \Carbon\Carbon::parse($data['waktu_selesai_kegiatan'])->format('H:i');
                    }
                    if ($data['tanggal']) {
                        // Try to parse various date formats (e.g., 01-01-2025 or 2025-01-01)
                        $data['tanggal'] = \Carbon\Carbon::parse($data['tanggal'])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": Format Tanggal atau Waktu tidak valid (" . $e->getMessage() . ").";
                    continue;
                }

                LaporanKaryawan::create([
                    'id' => Str::uuid(),
                    'hari' => $data['hari'],
                    'tanggal' => $data['tanggal'],
                    'nama' => $data['nama'],
                    'instansi' => $data['instansi'],
                    'jam_masuk' => $data['jam_masuk'],
                    'jenis_kegiatan' => $data['jenis_kegiatan'],
                    'deskripsi_kegiatan' => $data['deskripsi_kegiatan'],
                    'waktu_mulai_kegiatan' => $data['waktu_mulai_kegiatan'],
                    'waktu_selesai_kegiatan' => $data['waktu_selesai_kegiatan'],
                    'durasi_waktu' => $durasiWaktu,
                    'alamat_tujuan' => $data['alamat_tujuan'],
                    'kelompok_id' => $user->kelompok_id,
                ]);

                $importedCount++;
            }

            if ($importedCount === 0 && !empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengimport data.',
                    'errors' => $errors
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimport $importedCount data laporan.",
                'errors' => $errors // Include warnings if any
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Hari',
            'Tanggal',
            'Nama',
            'Instansi',
            'Jam Masuk',
            'Waktu Mulai Kegiatan',
            'Jenis Kegiatan',
            'Deskripsi Kegiatan',
            'Waktu Selesai Kegiatan',
            'Alamat Tujuan',
            'Dokumentasi'
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        // Add example row
        $example = [
            'Senin',
            '2025-01-01',
            'Nama Karyawan',
            'PLN Galesong',
            '08:00:00',
            '08:30:00',
            'Perbaikan Meteran',
            '',
            '09:30:00',
            'Alamat Contoh',
            ''
        ];
        foreach ($example as $index => $value) {
            $sheet->setCellValue([$index + 1, 2], $value);
        }

        // Auto-size columns
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'Template_Import_Laporan.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
