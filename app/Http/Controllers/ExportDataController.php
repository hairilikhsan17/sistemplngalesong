<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportDataController extends Controller
{
    /**
     * Display export data page
     */
    public function index()
    {
        try {
            $kelompoks = Kelompok::withCount(['karyawan'])->get();
            
            // Add laporan count for each kelompok
            foreach ($kelompoks as $kelompok) {
                $kelompok->laporan_count = DB::table('laporan_karyawan')->where('kelompok_id', $kelompok->id)->count();
            }
            
            $totalData = [
                'kelompok' => Kelompok::count(),
                'karyawan' => DB::table('karyawan')->count(),
                'laporan' => DB::table('laporan_karyawan')->count()
            ];
            
            return view('dashboard.atasan.export-data', compact('kelompoks', 'totalData'));
            
        } catch (\Exception $e) {
            return view('dashboard.atasan.export-data', [
                'kelompoks' => collect(),
                'totalData' => [
                    'kelompok' => 0,
                    'karyawan' => 0,
                    'laporan' => 0
                ]
            ]);
        }
    }

    /**
     * Export all data kelompok - Dinamis otomatis menyesuaikan jumlah kelompok
     */
    public function exportAllData()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Check if user is atasan (admin)
            if ($user->role !== 'atasan') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get all data - DINAMIS: Otomatis mengambil semua kelompok yang ada
            // Urutkan berdasarkan nama_kelompok untuk konsistensi
            $kelompoks = Kelompok::orderBy('nama_kelompok', 'asc')->get();
            $karyawans = Karyawan::all();
            $laporanKaryawans = LaporanKaryawan::with(['kelompok'])->get();
            // Job Pekerjaan tidak digunakan karena hanya ada input laporan
            $jobPekerjaans = collect(); // Empty collection
            $spreadsheet = new Spreadsheet();
            
            // Export semua data kelompok dengan struktur DINAMIS
            // Otomatis menyesuaikan dengan jumlah kelompok yang ada
            $this->exportAllKelompokDataToExcel($spreadsheet, $kelompoks, $karyawans, $laporanKaryawans, $jobPekerjaans);

            $filename = 'PLN_Galesong_Semua_Data_Kelompok_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return $this->downloadExcel($spreadsheet, $filename);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal export data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export data by kelompok
     */
    public function exportByKelompok(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Check if user is atasan (admin)
            if ($user->role !== 'atasan') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $kelompokId = $request->input('kelompok_id');
            
            if (!$kelompokId) {
                return response()->json(['error' => 'Kelompok ID required'], 400);
            }

            $kelompok = Kelompok::findOrFail($kelompokId);
            
            // Get data for this kelompok
            $karyawans = Karyawan::where('kelompok_id', $kelompokId)->get();
            $laporanKaryawans = LaporanKaryawan::where('kelompok_id', $kelompokId)->get();
            // Job Pekerjaan tidak digunakan karena hanya ada input laporan
            $jobPekerjaans = collect(); // Empty collection
            $spreadsheet = new Spreadsheet();
            
            // Export data kelompok dengan struktur yang sama seperti export karyawan
            $this->exportKelompokDataToExcel($spreadsheet, $kelompok, $karyawans, $laporanKaryawans, $jobPekerjaans);

            $filename = 'PLN_Galesong_' . str_replace(' ', '_', $kelompok->nama_kelompok) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return $this->downloadExcel($spreadsheet, $filename);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal export data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get kelompok list for API
     */
    public function getKelompokList()
    {
        try {
            $kelompoks = Kelompok::withCount(['karyawan'])->get();
            
            // Add laporan count for each kelompok
            foreach ($kelompoks as $kelompok) {
                $kelompok->laporan_count = DB::table('laporan_karyawan')->where('kelompok_id', $kelompok->id)->count();
            }
            
            return response()->json([
                'success' => true,
                'kelompok' => $kelompoks
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data kelompok: ' . $e->getMessage()], 500);
        }
    }

    // Helper methods for Excel export with styling
    
    private function exportKelompokSheet($spreadsheet, $kelompoks)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kelompok');
        
        // Header
        $sheet->setCellValue('A1', 'DATA KELOMPOK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        
        // Headers
        $headers = ['No', 'ID Kelompok', 'Nama Kelompok', 'Shift', 'Jumlah Karyawan', 'Jumlah Laporan', 'Created At'];
        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        
        // Data
        $row = 4;
        $no = 1;
        foreach ($kelompoks as $kelompok) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $kelompok->id);
            $sheet->setCellValue('C' . $row, $kelompok->nama_kelompok);
            $sheet->setCellValue('D' . $row, $kelompok->shift);
            $sheet->setCellValue('E' . $row, $kelompok->karyawan->count());
            $sheet->setCellValue('F' . $row, DB::table('laporan_karyawan')->where('kelompok_id', $kelompok->id)->count());
            $sheet->setCellValue('G' . $row, $kelompok->created_at->format('Y-m-d H:i:s'));
            $row++;
            $no++;
        }
        
        // Auto size dan border
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $this->setTableBorders($sheet, 'A3:G' . ($row - 1));
    }
    
    private function exportKaryawanSheet($spreadsheet, $karyawans, $kelompoks)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Data Karyawan');
        
        // Header
        $sheet->setCellValue('A1', 'DATA KARYAWAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('10B981');
        
        // Headers
        $headers = ['No', 'ID Karyawan', 'Nama', 'ID Kelompok', 'Nama Kelompok', 'Status', 'Created At'];
        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        
        // Data
        $row = 4;
        $no = 1;
        foreach ($karyawans as $karyawan) {
            $kelompok = $kelompoks->find($karyawan->kelompok_id);
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $karyawan->id);
            $sheet->setCellValue('C' . $row, $karyawan->nama);
            $sheet->setCellValue('D' . $row, $karyawan->kelompok_id);
            $sheet->setCellValue('E' . $row, $kelompok->nama_kelompok ?? '-');
            $sheet->setCellValue('F' . $row, $karyawan->status ?? 'Aktif');
            $sheet->setCellValue('G' . $row, $karyawan->created_at ? date('Y-m-d H:i:s', strtotime($karyawan->created_at)) : '-');
            $row++;
            $no++;
        }
        
        // Auto size dan border
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $this->setTableBorders($sheet, 'A3:G' . ($row - 1));
    }
    
    private function exportLaporanKaryawanSheet($spreadsheet, $laporanKaryawans)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Data Laporan Karyawan');
        
        // Header
        $sheet->setCellValue('A1', 'DATA LAPORAN KARYAWAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('3B82F6');
        
        // Headers
        $headers = ['No', 'ID Laporan', 'Hari', 'Tanggal', 'Nama', 'Kelompok', 'Instansi', 'Jabatan', 'Alamat Tujuan', 'Dokumentasi', 'Created At'];
        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A3:K3')->getFont()->setBold(true);
        $sheet->getStyle('A3:K3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        
        // Data
        $row = 4;
        $no = 1;
        foreach ($laporanKaryawans as $laporan) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $laporan->id);
            $sheet->setCellValue('C' . $row, $laporan->hari);
            $sheet->setCellValue('D' . $row, $laporan->tanggal->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $laporan->nama);
            $sheet->setCellValue('F' . $row, $laporan->kelompok->nama_kelompok ?? '-');
            $sheet->setCellValue('G' . $row, $laporan->instansi);
            $sheet->setCellValue('H' . $row, $laporan->jabatan ?? '-');
            $sheet->setCellValue('I' . $row, $laporan->alamat_tujuan);
            $sheet->setCellValue('J' . $row, $laporan->dokumentasi ?? '-');
            $sheet->setCellValue('K' . $row, $laporan->created_at->format('Y-m-d H:i:s'));
            $row++;
            $no++;
        }
        
        // Auto size dan border
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $this->setTableBorders($sheet, 'A3:K' . ($row - 1));
    }
    
    private function exportJobPekerjaanSheet($spreadsheet, $jobPekerjaans)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Data Job Pekerjaan');
        
        // Header
        $sheet->setCellValue('A1', 'DATA JOB PEKERJAAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('8B5CF6');
        
        // Headers
        $headers = ['No', 'ID Job', 'Tanggal', 'Hari', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Waktu (jam)', 'Kelompok', 'Created At'];
        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A3:L3')->getFont()->setBold(true);
        $sheet->getStyle('A3:L3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        
        // Data
        $row = 4;
        $no = 1;
        foreach ($jobPekerjaans as $job) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $job->id);
            $sheet->setCellValue('C' . $row, $job->tanggal->format('Y-m-d'));
            $sheet->setCellValue('D' . $row, $job->hari);
            $sheet->setCellValue('E' . $row, $job->perbaikan_kwh);
            $sheet->setCellValue('F' . $row, $job->pemeliharaan_pengkabelan);
            $sheet->setCellValue('G' . $row, $job->pengecekan_gardu);
            $sheet->setCellValue('H' . $row, $job->penanganan_gangguan);
            $sheet->setCellValue('I' . $row, $job->lokasi);
            $sheet->setCellValue('J' . $row, $job->waktu_penyelesaian);
            $sheet->setCellValue('K' . $row, $job->kelompok->nama_kelompok ?? '-');
            $sheet->setCellValue('L' . $row, $job->created_at->format('Y-m-d H:i:s'));
            $row++;
            $no++;
        }
        
        // Auto size dan border
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $this->setTableBorders($sheet, 'A3:L' . ($row - 1));
    }
    
    private function exportKelompokDataToExcel($spreadsheet, $kelompok, $karyawans, $laporanKaryawans, $jobPekerjaans)
    {
        // Sheet 1: Data Kelompok Lengkap
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kelompok ' . $kelompok->nama_kelompok);
        
        // Header Info Kelompok
        $sheet->setCellValue('A1', 'DATA KELOMPOK: ' . strtoupper($kelompok->nama_kelompok));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        
        $sheet->setCellValue('A2', 'Shift: ' . $kelompok->shift);
        $sheet->setCellValue('A3', 'Jumlah Karyawan: ' . $karyawans->count());
        $sheet->setCellValue('A4', 'Jumlah Laporan: ' . $laporanKaryawans->count());
        $sheet->setCellValue('A5', 'Tanggal Export: ' . now()->format('Y-m-d H:i:s'));
        
        // Tabel 1: Input Laporan (dimulai dari baris 7)
        $startRowLaporan = 7;
        $sheet->setCellValue('A' . $startRowLaporan, 'TABEL 1: INPUT LAPORAN');
        $sheet->getStyle('A' . $startRowLaporan)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $startRowLaporan)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('3B82F6');
        
        // Header tabel laporan - sesuai dengan field yang ada di database
        $laporanHeaders = ['No', 'Hari/Tanggal', 'Nama', 'Instansi', 'Alamat Tujuan', 'Waktu Mulai Kegiatan', 'Jenis Kegiatan', 'Deskripsi Kegiatan', 'Waktu Selesai Kegiatan', 'Durasi Waktu', 'Lokasi', 'Dokumentasi'];
        $col = 'A';
        $headerRow = $startRowLaporan + 1;
        foreach ($laporanHeaders as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $col++;
        }
        
        // Style header tabel laporan
        $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        
        // Data laporan
        $row = $headerRow + 1;
        $no = 1;
        foreach ($laporanKaryawans as $laporan) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $laporan->hari . ' / ' . $laporan->tanggal->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $laporan->nama);
            $sheet->setCellValue('D' . $row, $laporan->instansi);
            $sheet->setCellValue('E' . $row, $laporan->alamat_tujuan);
            $sheet->setCellValue('F' . $row, $laporan->waktu_mulai_kegiatan ? Carbon::parse($laporan->waktu_mulai_kegiatan)->format('H:i') : '-');
            $sheet->setCellValue('G' . $row, $laporan->jenis_kegiatan ?? '-');
            $sheet->setCellValue('H' . $row, $laporan->deskripsi_kegiatan ?? '-');
            $sheet->setCellValue('I' . $row, $laporan->waktu_selesai_kegiatan ? Carbon::parse($laporan->waktu_selesai_kegiatan)->format('H:i') : '-');
            $sheet->setCellValue('J' . $row, $laporan->durasi_waktu ? number_format($laporan->durasi_waktu, 2) . ' jam' : '0 jam');
            $sheet->setCellValue('K' . $row, $laporan->lokasi ?? '-');
            $sheet->setCellValue('L' . $row, $laporan->file_path ? 'Ada File' : '-');
            $row++;
            $no++;
        }
        
        // Auto size semua kolom
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set border untuk tabel laporan
        if ($laporanKaryawans->count() > 0) {
            $this->setTableBorders($sheet, 'A' . $headerRow . ':L' . ($headerRow + $laporanKaryawans->count()));
        }
    }
    
    private function setTableBorders($sheet, $range)
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }
    
    private function exportAllKelompokDataToExcel($spreadsheet, $kelompoks, $karyawans, $laporanKaryawans, $jobPekerjaans)
    {
        // Sheet 1: Semua Data Kelompok - DINAMIS
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Semua Data Kelompok');
        
        // Header utama
        $sheet->setCellValue('A1', 'SEMUA DATA KELOMPOK - DINAMIS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        
        $sheet->setCellValue('A2', 'Total Kelompok: ' . $kelompoks->count() . ' (Otomatis menyesuaikan)');
        $sheet->setCellValue('A3', 'Total Karyawan: ' . $karyawans->count());
        $sheet->setCellValue('A4', 'Total Laporan: ' . $laporanKaryawans->count());
        $sheet->setCellValue('A5', 'Tanggal Export: ' . now()->format('Y-m-d H:i:s'));
        $sheet->setCellValue('A6', 'Catatan: Sistem otomatis menyesuaikan dengan perubahan kelompok (tambah/hapus)');
        
        $currentRow = 8;
        
        // DINAMIS: Loop untuk setiap kelompok yang ada
        // Jika ada kelompok baru, akan otomatis ditambahkan
        // Jika ada kelompok yang dihapus, akan otomatis tidak muncul
        foreach ($kelompoks as $index => $kelompok) {
            // Header Kelompok - Gunakan nama kelompok langsung, tidak pakai nomor urut
            $sheet->setCellValue('A' . $currentRow, strtoupper($kelompok->nama_kelompok));
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $currentRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('10B981'); // Green
            
            $currentRow++;
            
            // Info Kelompok - DINAMIS: Otomatis mengambil data kelompok yang ada
            $sheet->setCellValue('A' . $currentRow, 'ID Kelompok: ' . $kelompok->id);
            $currentRow++;
            $sheet->setCellValue('A' . $currentRow, 'Nama Kelompok: ' . $kelompok->nama_kelompok);
            $currentRow++;
            $sheet->setCellValue('A' . $currentRow, 'Shift: ' . $kelompok->shift);
            $currentRow++;
            
            // Get data for this kelompok - Pastikan menggunakan ID yang benar
            $kelompokId = $kelompok->id;
            // Filter menggunakan filter() untuk memastikan perbandingan yang benar
            $kelompokKaryawans = $karyawans->filter(function($karyawan) use ($kelompokId) {
                return $karyawan->kelompok_id == $kelompokId;
            });
            $kelompokLaporans = $laporanKaryawans->filter(function($laporan) use ($kelompokId) {
                return $laporan->kelompok_id == $kelompokId;
            });
            
            $sheet->setCellValue('A' . $currentRow, 'Jumlah Karyawan: ' . $kelompokKaryawans->count());
            $currentRow++;
            $sheet->setCellValue('A' . $currentRow, 'Jumlah Laporan: ' . $kelompokLaporans->count());
            $currentRow += 2;
            
            // Tabel 1: Input Laporan
            $startRowLaporan = $currentRow;
            $sheet->setCellValue('A' . $startRowLaporan, 'Tabel Input Laporan');
            $sheet->getStyle('A' . $startRowLaporan)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $startRowLaporan)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('3B82F6');
            
            $currentRow++;
            
            // Header tabel laporan - sesuai dengan field yang ada di database
            $laporanHeaders = ['No', 'Hari/Tanggal', 'Nama', 'Instansi', 'Alamat Tujuan', 'Waktu Mulai Kegiatan', 'Jenis Kegiatan', 'Deskripsi Kegiatan', 'Waktu Selesai Kegiatan', 'Durasi Waktu', 'Lokasi', 'Dokumentasi'];
            $col = 'A';
            $headerRow = $currentRow;
            foreach ($laporanHeaders as $header) {
                $sheet->setCellValue($col . $headerRow, $header);
                $col++;
            }
            
            // Style header tabel laporan
            $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E5E7EB');
            
            $currentRow++;
            $no = 1;
            
            // Data laporan
            foreach ($kelompokLaporans as $laporan) {
                $sheet->setCellValue('A' . $currentRow, $no);
                $sheet->setCellValue('B' . $currentRow, $laporan->hari . ' / ' . $laporan->tanggal->format('Y-m-d'));
                $sheet->setCellValue('C' . $currentRow, $laporan->nama);
                $sheet->setCellValue('D' . $currentRow, $laporan->instansi);
                $sheet->setCellValue('E' . $currentRow, $laporan->alamat_tujuan);
                $sheet->setCellValue('F' . $currentRow, $laporan->waktu_mulai_kegiatan ? Carbon::parse($laporan->waktu_mulai_kegiatan)->format('H:i') : '-');
                $sheet->setCellValue('G' . $currentRow, $laporan->jenis_kegiatan ?? '-');
                $sheet->setCellValue('H' . $currentRow, $laporan->deskripsi_kegiatan ?? '-');
                $sheet->setCellValue('I' . $currentRow, $laporan->waktu_selesai_kegiatan ? Carbon::parse($laporan->waktu_selesai_kegiatan)->format('H:i') : '-');
                $sheet->setCellValue('J' . $currentRow, $laporan->durasi_waktu ? number_format($laporan->durasi_waktu, 2) . ' jam' : '0 jam');
                $sheet->setCellValue('K' . $currentRow, $laporan->lokasi ?? '-');
                $sheet->setCellValue('L' . $currentRow, $laporan->file_path ? 'Ada File' : '-');
                $currentRow++;
                $no++;
            }
            
            // DINAMIS: Jika tidak ada laporan (kelompok baru atau data kosong)
            if ($kelompokLaporans->count() == 0) {
                $sheet->setCellValue('A' . $currentRow, 'Tidak ada data laporan untuk kelompok ini');
                $currentRow++;
            }
            
            // Set border untuk tabel laporan
            if ($kelompokLaporans->count() > 0) {
                $this->setTableBorders($sheet, 'A' . $headerRow . ':L' . ($headerRow + $kelompokLaporans->count()));
            }
            
            // Spasi antar kelompok (5 baris)
            $currentRow += 5;
        }
        
        // DINAMIS: Auto size semua kolom - otomatis menyesuaikan lebar kolom
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Catatan: Sistem ini sepenuhnya dinamis
        // - Jika ada kelompok baru, akan otomatis ditambahkan
        // - Jika ada kelompok yang dihapus, akan otomatis tidak muncul
        // - Data akan selalu up-to-date sesuai dengan database
    }
    
    private function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        
        $response = response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename);
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $response;
    }
}
