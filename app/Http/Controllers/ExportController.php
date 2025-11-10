<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
    public function exportAllData()
    {
        $user = Auth::user();
        
        if (!$user->isAtasan()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $spreadsheet = new Spreadsheet();
        
        // Export Kelompok
        $this->exportKelompok($spreadsheet);
        
        // Export Laporan Karyawan
        $this->exportLaporanKaryawan($spreadsheet);
        
        // Export Job Pekerjaan
        $this->exportJobPekerjaan($spreadsheet);
        

        $filename = 'PLN_Galesong_All_Data_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return $this->downloadExcel($spreadsheet, $filename);
    }

    public function exportByKelompok(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAtasan()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kelompokId = $request->input('kelompok_id');
        
        if (!$kelompokId) {
            return response()->json(['error' => 'Kelompok ID required'], 400);
        }

        $kelompok = Kelompok::findOrFail($kelompokId);
        $spreadsheet = new Spreadsheet();
        
        // Export data kelompok tertentu
        $this->exportKelompokDataToExcel($spreadsheet, $kelompok);

        $filename = 'PLN_Galesong_' . str_replace(' ', '_', $kelompok->nama_kelompok) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return $this->downloadExcel($spreadsheet, $filename);
    }

    public function exportKelompokData()
    {
        $user = Auth::user();
        
        if (!$user->isKaryawan() || !$user->kelompok_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kelompok = $user->kelompok;
        $spreadsheet = new Spreadsheet();
        
        // Export data kelompok karyawan
        $this->exportKelompokDataToExcel($spreadsheet, $kelompok);

        $filename = 'PLN_Galesong_' . str_replace(' ', '_', $kelompok->nama_kelompok) . '_Data_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return $this->downloadExcel($spreadsheet, $filename);
    }

    private function exportKelompok($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kelompok');
        
        // Headers
        $headers = ['ID', 'Nama Kelompok', 'Shift', 'Jumlah Karyawan', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        
        // Data
        $kelompoks = Kelompok::with('karyawan')->get();
        $row = 2;
        foreach ($kelompoks as $kelompok) {
            $sheet->setCellValue('A' . $row, $kelompok->id);
            $sheet->setCellValue('B' . $row, $kelompok->nama_kelompok);
            $sheet->setCellValue('C' . $row, $kelompok->shift);
            $sheet->setCellValue('D' . $row, $kelompok->karyawan->count());
            $sheet->setCellValue('E' . $row, $kelompok->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function exportLaporanKaryawan($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Laporan Karyawan');
        
        // Headers
        $headers = ['ID', 'Hari', 'Tanggal', 'Nama', 'Instansi', 'Jabatan', 'Alamat Tujuan', 'Dokumentasi', 'Kelompok', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('10B981');
        
        // Data
        $laporans = LaporanKaryawan::with('kelompok')->get();
        $row = 2;
        foreach ($laporans as $laporan) {
            $sheet->setCellValue('A' . $row, $laporan->id);
            $sheet->setCellValue('B' . $row, $laporan->hari);
            $sheet->setCellValue('C' . $row, $laporan->tanggal->format('Y-m-d'));
            $sheet->setCellValue('D' . $row, $laporan->nama);
            $sheet->setCellValue('E' . $row, $laporan->instansi);
            $sheet->setCellValue('F' . $row, $laporan->jabatan);
            $sheet->setCellValue('G' . $row, $laporan->alamat_tujuan);
            $sheet->setCellValue('H' . $row, $laporan->dokumentasi ?? '-');
            $sheet->setCellValue('I' . $row, $laporan->kelompok->nama_kelompok);
            $sheet->setCellValue('J' . $row, $laporan->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function exportJobPekerjaan($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Job Pekerjaan');
        
        // Headers
        $headers = ['ID', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Hari', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Kelompok', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('8B5CF6');
        
        // Data
        $jobs = JobPekerjaan::with('kelompok')->get();
        $row = 2;
        foreach ($jobs as $job) {
            $sheet->setCellValue('A' . $row, $job->id);
            $sheet->setCellValue('B' . $row, $job->perbaikan_kwh);
            $sheet->setCellValue('C' . $row, $job->pemeliharaan_pengkabelan);
            $sheet->setCellValue('D' . $row, $job->pengecekan_gardu);
            $sheet->setCellValue('E' . $row, $job->penanganan_gangguan);
            $sheet->setCellValue('F' . $row, $job->lokasi);
            $sheet->setCellValue('G' . $row, $job->hari);
            $sheet->setCellValue('H' . $row, $job->tanggal->format('Y-m-d'));
            $sheet->setCellValue('I' . $row, $job->waktu_penyelesaian);
            $sheet->setCellValue('J' . $row, $job->kelompok->nama_kelompok);
            $sheet->setCellValue('K' . $row, $job->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }


    private function exportKelompokDataToExcel($spreadsheet, $kelompok)
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
        $sheet->setCellValue('A3', 'Jumlah Karyawan: ' . $kelompok->karyawan->count());
        $sheet->setCellValue('A4', 'Tanggal Export: ' . now()->format('Y-m-d H:i:s'));
        
        // Tabel 1: Input Laporan (dimulai dari baris 6)
        $startRowLaporan = 6;
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
        $laporans = LaporanKaryawan::where('kelompok_id', $kelompok->id)->get();
        $row = $headerRow + 1;
        $no = 1;
        foreach ($laporans as $laporan) {
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
        $jobs = JobPekerjaan::where('kelompok_id', $kelompok->id)->get();
        $row = $headerRowJob + 1;
        $no = 1;
        foreach ($jobs as $job) {
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
        $this->setTableBorders($sheet, 'A' . $headerRow . ':F' . ($headerRow + $laporans->count()));
        $this->setTableBorders($sheet, 'A' . $headerRowJob . ':J' . ($headerRowJob + $jobs->count()));
    }
    
    private function setTableBorders($sheet, $range)
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
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
