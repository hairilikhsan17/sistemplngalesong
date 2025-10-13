<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
use App\Models\Prediksi;
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
        
        // Export Prediksi
        $this->exportPrediksi($spreadsheet);

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
        $headers = ['ID', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Bulan Data', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Kelompok', 'Created At'];
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
            $sheet->setCellValue('G' . $row, $job->bulan_data);
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

    private function exportPrediksi($spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Prediksi');
        
        // Headers
        $headers = ['ID', 'Jenis Prediksi', 'Bulan Prediksi', 'Hasil Prediksi', 'Kelompok', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        
        // Data
        $prediksis = Prediksi::with('kelompok')->get();
        $row = 2;
        foreach ($prediksis as $prediksi) {
            $sheet->setCellValue('A' . $row, $prediksi->id);
            $sheet->setCellValue('B' . $row, $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'Laporan Karyawan' : 'Job Pekerjaan');
            $sheet->setCellValue('C' . $row, $prediksi->bulan_prediksi);
            $sheet->setCellValue('D' . $row, $prediksi->hasil_prediksi);
            $sheet->setCellValue('E' . $row, $prediksi->kelompok ? $prediksi->kelompok->nama_kelompok : 'Semua Kelompok');
            $sheet->setCellValue('F' . $row, $prediksi->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function exportKelompokDataToExcel($spreadsheet, $kelompok)
    {
        // Sheet 1: Info Kelompok
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Info Kelompok');
        
        $sheet->setCellValue('A1', 'Nama Kelompok');
        $sheet->setCellValue('B1', $kelompok->nama_kelompok);
        $sheet->setCellValue('A2', 'Shift');
        $sheet->setCellValue('B2', $kelompok->shift);
        $sheet->setCellValue('A3', 'Jumlah Karyawan');
        $sheet->setCellValue('B3', $kelompok->karyawan->count());
        $sheet->setCellValue('A4', 'Created At');
        $sheet->setCellValue('B4', $kelompok->created_at->format('Y-m-d H:i:s'));
        
        // Sheet 2: Karyawan
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Karyawan');
        
        $headers = ['Nama', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $col++;
        }
        
        $row = 2;
        foreach ($kelompok->karyawan as $karyawan) {
            $sheet2->setCellValue('A' . $row, $karyawan->nama);
            $sheet2->setCellValue('B' . $row, $karyawan->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Sheet 3: Laporan Kelompok
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Laporan Kelompok');
        
        $headers = ['Hari', 'Tanggal', 'Nama', 'Instansi', 'Jabatan', 'Alamat Tujuan', 'Dokumentasi', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet3->setCellValue($col . '1', $header);
            $col++;
        }
        
        $laporans = LaporanKaryawan::where('kelompok_id', $kelompok->id)->get();
        $row = 2;
        foreach ($laporans as $laporan) {
            $sheet3->setCellValue('A' . $row, $laporan->hari);
            $sheet3->setCellValue('B' . $row, $laporan->tanggal->format('Y-m-d'));
            $sheet3->setCellValue('C' . $row, $laporan->nama);
            $sheet3->setCellValue('D' . $row, $laporan->instansi);
            $sheet3->setCellValue('E' . $row, $laporan->jabatan);
            $sheet3->setCellValue('F' . $row, $laporan->alamat_tujuan);
            $sheet3->setCellValue('G' . $row, $laporan->dokumentasi ?? '-');
            $sheet3->setCellValue('H' . $row, $laporan->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Sheet 4: Job Pekerjaan Kelompok
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Job Pekerjaan');
        
        $headers = ['Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Bulan Data', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Created At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet4->setCellValue($col . '1', $header);
            $col++;
        }
        
        $jobs = JobPekerjaan::where('kelompok_id', $kelompok->id)->get();
        $row = 2;
        foreach ($jobs as $job) {
            $sheet4->setCellValue('A' . $row, $job->perbaikan_kwh);
            $sheet4->setCellValue('B' . $row, $job->pemeliharaan_pengkabelan);
            $sheet4->setCellValue('C' . $row, $job->pengecekan_gardu);
            $sheet4->setCellValue('D' . $row, $job->penanganan_gangguan);
            $sheet4->setCellValue('E' . $row, $job->lokasi);
            $sheet4->setCellValue('F' . $row, $job->bulan_data);
            $sheet4->setCellValue('G' . $row, $job->tanggal->format('Y-m-d'));
            $sheet4->setCellValue('H' . $row, $job->waktu_penyelesaian);
            $sheet4->setCellValue('I' . $row, $job->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
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
