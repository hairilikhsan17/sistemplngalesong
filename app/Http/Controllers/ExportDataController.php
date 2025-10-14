<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
use App\Models\Prediksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                'laporan' => DB::table('laporan_karyawan')->count(),
                'job' => DB::table('job_pekerjaan')->count()
            ];
            
            return view('dashboard.atasan.export-data', compact('kelompoks', 'totalData'));
            
        } catch (\Exception $e) {
            return view('dashboard.atasan.export-data', [
                'kelompoks' => collect(),
                'totalData' => [
                    'kelompok' => 0,
                    'karyawan' => 0,
                    'laporan' => 0,
                    'job' => 0
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
            $kelompoks = Kelompok::with(['karyawan'])->orderBy('created_at')->get();
            $karyawans = DB::table('karyawan')->get();
            $laporanKaryawans = LaporanKaryawan::with(['kelompok'])->get();
            $jobPekerjaans = JobPekerjaan::with(['kelompok'])->get();
            $prediksis = Prediksi::with(['kelompok'])->get();

            $spreadsheet = new Spreadsheet();
            
            // Export semua data kelompok dengan struktur DINAMIS
            // Otomatis menyesuaikan dengan jumlah kelompok yang ada
            $this->exportAllKelompokDataToExcel($spreadsheet, $kelompoks, $karyawans, $laporanKaryawans, $jobPekerjaans, $prediksis);

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

            $kelompok = Kelompok::with(['karyawan'])->findOrFail($kelompokId);
            
            // Get data for this kelompok
            $karyawans = DB::table('karyawan')->where('kelompok_id', $kelompokId)->get();
            $laporanKaryawans = LaporanKaryawan::where('kelompok_id', $kelompokId)->get();
            $jobPekerjaans = JobPekerjaan::where('kelompok_id', $kelompokId)->get();
            $prediksis = Prediksi::where('kelompok_id', $kelompokId)->get();

            $spreadsheet = new Spreadsheet();
            
            // Export data kelompok dengan struktur yang sama seperti export karyawan
            $this->exportKelompokDataToExcel($spreadsheet, $kelompok, $karyawans, $laporanKaryawans, $jobPekerjaans, $prediksis);

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
    
    private function exportPrediksiSheet($spreadsheet, $prediksis)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Data Prediksi');
        
        // Header
        $sheet->setCellValue('A1', 'DATA PREDIKSI');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        
        // Headers
        $headers = ['No', 'ID Prediksi', 'Jenis Prediksi', 'Bulan Prediksi', 'Hasil Prediksi', 'Kelompok', 'Created At'];
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
        foreach ($prediksis as $prediksi) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $prediksi->id);
            $sheet->setCellValue('C' . $row, $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'Laporan Karyawan' : 'Job Pekerjaan');
            $sheet->setCellValue('D' . $row, $prediksi->bulan_prediksi);
            $sheet->setCellValue('E' . $row, $prediksi->hasil_prediksi);
            $sheet->setCellValue('F' . $row, $prediksi->kelompok->nama_kelompok ?? 'Semua Kelompok');
            $sheet->setCellValue('G' . $row, $prediksi->created_at->format('Y-m-d H:i:s'));
            $row++;
            $no++;
        }
        
        // Auto size dan border
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $this->setTableBorders($sheet, 'A3:G' . ($row - 1));
    }
    
    private function exportKelompokDataToExcel($spreadsheet, $kelompok, $karyawans, $laporanKaryawans, $jobPekerjaans, $prediksis)
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
        $sheet->setCellValue('A5', 'Jumlah Job: ' . $jobPekerjaans->count());
        $sheet->setCellValue('A6', 'Tanggal Export: ' . now()->format('Y-m-d H:i:s'));
        
        // Tabel 1: Input Laporan (dimulai dari baris 8)
        $startRowLaporan = 8;
        $sheet->setCellValue('A' . $startRowLaporan, 'TABEL 1: INPUT LAPORAN');
        $sheet->getStyle('A' . $startRowLaporan)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $startRowLaporan)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('3B82F6');
        
        // Header tabel laporan
        $laporanHeaders = ['No', 'Hari/Tanggal', 'Nama', 'Instansi', 'Alamat Tujuan', 'Dokumentasi'];
        $col = 'A';
        $headerRow = $startRowLaporan + 1;
        foreach ($laporanHeaders as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $col++;
        }
        
        // Style header tabel laporan
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFill()
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
            $sheet->setCellValue('F' . $row, $laporan->dokumentasi ?? '-');
            $row++;
            $no++;
        }
        
        // Tabel 2: Input Job Pekerjaan (dimulai 3 baris setelah tabel laporan)
        $startRowJob = $row + 3;
        $sheet->setCellValue('A' . $startRowJob, 'TABEL 2: INPUT JOB PEKERJAAN');
        $sheet->getStyle('A' . $startRowJob)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $startRowJob)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('8B5CF6');
        
        // Header tabel job pekerjaan
        $jobHeaders = ['No', 'Tanggal', 'Hari', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Waktu (jam)', 'Created At'];
        $col = 'A';
        $headerRowJob = $startRowJob + 1;
        foreach ($jobHeaders as $header) {
            $sheet->setCellValue($col . $headerRowJob, $header);
            $col++;
        }
        
        // Style header tabel job pekerjaan
        $sheet->getStyle('A' . $headerRowJob . ':J' . $headerRowJob)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRowJob . ':J' . $headerRowJob)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        
        // Data job pekerjaan
        $row = $headerRowJob + 1;
        $no = 1;
        foreach ($jobPekerjaans as $job) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $job->tanggal->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $job->hari);
            $sheet->setCellValue('D' . $row, $job->perbaikan_kwh);
            $sheet->setCellValue('E' . $row, $job->pemeliharaan_pengkabelan);
            $sheet->setCellValue('F' . $row, $job->pengecekan_gardu);
            $sheet->setCellValue('G' . $row, $job->penanganan_gangguan);
            $sheet->setCellValue('H' . $row, $job->lokasi);
            $sheet->setCellValue('I' . $row, $job->waktu_penyelesaian);
            $sheet->setCellValue('J' . $row, $job->created_at->format('Y-m-d H:i:s'));
            $row++;
            $no++;
        }
        
        // Auto size semua kolom
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set border untuk semua tabel
        $this->setTableBorders($sheet, 'A' . $headerRow . ':F' . ($headerRow + $laporanKaryawans->count()));
        $this->setTableBorders($sheet, 'A' . $headerRowJob . ':J' . ($headerRowJob + $jobPekerjaans->count()));
    }
    
    private function setTableBorders($sheet, $range)
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }
    
    private function exportAllKelompokDataToExcel($spreadsheet, $kelompoks, $karyawans, $laporanKaryawans, $jobPekerjaans, $prediksis)
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
        $sheet->setCellValue('A5', 'Total Job Pekerjaan: ' . $jobPekerjaans->count());
        $sheet->setCellValue('A6', 'Tanggal Export: ' . now()->format('Y-m-d H:i:s'));
        $sheet->setCellValue('A7', 'Catatan: Sistem otomatis menyesuaikan dengan perubahan kelompok (tambah/hapus)');
        
        $currentRow = 9;
        
        // DINAMIS: Loop untuk setiap kelompok yang ada
        // Jika ada kelompok baru, akan otomatis ditambahkan
        // Jika ada kelompok yang dihapus, akan otomatis tidak muncul
        foreach ($kelompoks as $index => $kelompok) {
            // Header Kelompok - DINAMIS: Otomatis menyesuaikan nama kelompok
            $sheet->setCellValue('A' . $currentRow, 'KELOMPOK ' . ($index + 1) . ': ' . strtoupper($kelompok->nama_kelompok));
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
            
            // Get data for this kelompok
            $kelompokKaryawans = $karyawans->where('kelompok_id', $kelompok->id);
            $kelompokLaporans = $laporanKaryawans->where('kelompok_id', $kelompok->id);
            $kelompokJobs = $jobPekerjaans->where('kelompok_id', $kelompok->id);
            
            $sheet->setCellValue('A' . $currentRow, 'Jumlah Karyawan: ' . $kelompokKaryawans->count());
            $currentRow++;
            $sheet->setCellValue('A' . $currentRow, 'Jumlah Laporan: ' . $kelompokLaporans->count());
            $currentRow++;
            $sheet->setCellValue('A' . $currentRow, 'Jumlah Job: ' . $kelompokJobs->count());
            $currentRow += 2;
            
            // Tabel 1: Input Laporan
            $startRowLaporan = $currentRow;
            $sheet->setCellValue('A' . $startRowLaporan, 'Tabel Input Laporan');
            $sheet->getStyle('A' . $startRowLaporan)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $startRowLaporan)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('3B82F6');
            
            $currentRow++;
            
            // Header tabel laporan
            $laporanHeaders = ['No', 'Hari/Tanggal', 'Nama', 'Instansi', 'Alamat Tujuan', 'Dokumentasi'];
            $col = 'A';
            $headerRow = $currentRow;
            foreach ($laporanHeaders as $header) {
                $sheet->setCellValue($col . $headerRow, $header);
                $col++;
            }
            
            // Style header tabel laporan
            $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->getFill()
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
                $sheet->setCellValue('F' . $currentRow, $laporan->dokumentasi ?? '-');
                $currentRow++;
                $no++;
            }
            
            // DINAMIS: Jika tidak ada laporan (kelompok baru atau data kosong)
            if ($kelompokLaporans->count() == 0) {
                $sheet->setCellValue('A' . $currentRow, 'Tidak ada data laporan untuk kelompok ini');
                $currentRow++;
            }
            
            $currentRow += 2;
            
            // Tabel 2: Input Job Pekerjaan
            $startRowJob = $currentRow;
            $sheet->setCellValue('A' . $startRowJob, 'Tabel Input Job Pekerjaan');
            $sheet->getStyle('A' . $startRowJob)->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A' . $startRowJob)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('8B5CF6');
            
            $currentRow++;
            
            // Header tabel job pekerjaan
            $jobHeaders = ['No', 'Tanggal', 'Hari', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Waktu (jam)', 'Created At'];
            $col = 'A';
            $headerRowJob = $currentRow;
            foreach ($jobHeaders as $header) {
                $sheet->setCellValue($col . $headerRowJob, $header);
                $col++;
            }
            
            // Style header tabel job pekerjaan
            $sheet->getStyle('A' . $headerRowJob . ':J' . $headerRowJob)->getFont()->setBold(true);
            $sheet->getStyle('A' . $headerRowJob . ':J' . $headerRowJob)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E5E7EB');
            
            $currentRow++;
            $no = 1;
            
            // Data job pekerjaan
            foreach ($kelompokJobs as $job) {
                $sheet->setCellValue('A' . $currentRow, $no);
                $sheet->setCellValue('B' . $currentRow, $job->tanggal->format('Y-m-d'));
                $sheet->setCellValue('C' . $currentRow, $job->hari);
                $sheet->setCellValue('D' . $currentRow, $job->perbaikan_kwh);
                $sheet->setCellValue('E' . $currentRow, $job->pemeliharaan_pengkabelan);
                $sheet->setCellValue('F' . $currentRow, $job->pengecekan_gardu);
                $sheet->setCellValue('G' . $currentRow, $job->penanganan_gangguan);
                $sheet->setCellValue('H' . $currentRow, $job->lokasi);
                $sheet->setCellValue('I' . $currentRow, $job->waktu_penyelesaian);
                $sheet->setCellValue('J' . $currentRow, $job->created_at->format('Y-m-d H:i:s'));
                $currentRow++;
                $no++;
            }
            
            // DINAMIS: Jika tidak ada job (kelompok baru atau data kosong)
            if ($kelompokJobs->count() == 0) {
                $sheet->setCellValue('A' . $currentRow, 'Tidak ada data job pekerjaan untuk kelompok ini');
                $currentRow++;
            }
            
            // Set border untuk tabel laporan
            if ($kelompokLaporans->count() > 0) {
                $this->setTableBorders($sheet, 'A' . $headerRow . ':F' . ($headerRow + $kelompokLaporans->count()));
            }
            
            // Set border untuk tabel job pekerjaan
            if ($kelompokJobs->count() > 0) {
                $this->setTableBorders($sheet, 'A' . $headerRowJob . ':J' . ($headerRowJob + $kelompokJobs->count()));
            }
            
            // Spasi antar kelompok (5 baris)
            $currentRow += 5;
        }
        
        // DINAMIS: Auto size semua kolom - otomatis menyesuaikan lebar kolom
        foreach (range('A', 'J') as $col) {
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
