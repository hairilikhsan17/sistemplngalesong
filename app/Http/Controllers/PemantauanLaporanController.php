<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PemantauanLaporanController extends Controller
{
    public function index(Request $request)
    {
        // Only allow atasan/admin
        if (!Auth::user()->isAtasan()) {
            abort(403, 'Unauthorized');
        }
        
        // Base query - get all reports from all groups
        $query = LaporanKaryawan::with('kelompok');
        
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
        
        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }
        
        $laporans = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);
        
        $statistics = [
            'totalLaporan' => $totalLaporan,
            'laporanHariIni' => $laporanHariIni,
            'laporanBulanIni' => $laporanBulanIni,
        ];
        
        // Get all kelompok for filter
        $kelompoks = Kelompok::orderBy('nama_kelompok', 'asc')->get();
        
        // Get all unique names for filter
        $namaKaryawans = LaporanKaryawan::select('nama')
            ->distinct()
            ->orderBy('nama', 'asc')
            ->pluck('nama');
        
        return view('dashboard.atasan.pemantauan-laporan', compact('laporans', 'statistics', 'kelompoks', 'namaKaryawans'));
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
            'alamat_tujuan' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan',
            'deskripsi_kegiatan' => 'nullable|string',
            'waktu_mulai_kegiatan' => 'nullable|date_format:H:i',
            'waktu_selesai_kegiatan' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        
        // Validasi khusus: deskripsi wajib jika jenis kegiatan adalah Jenis Kegiatan
        if ($request->jenis_kegiatan === 'Jenis Kegiatan' && empty($request->deskripsi_kegiatan)) {
            return response()->json([
                'success' => false,
                'message' => 'Deskripsi Jenis Kegiatan wajib diisi ketika jenis kegiatan adalah Jenis Kegiatan.',
                'errors' => [
                    'deskripsi_kegiatan' => ['Deskripsi Jenis Kegiatan wajib diisi.']
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
            'alamat_tujuan' => $request->alamat_tujuan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'lokasi' => $request->lokasi,
        ];
        
        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
                Storage::disk('public')->delete($laporan->file_path);
            }
            
            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
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
        
        return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus']);
    }
    
    public function downloadFile($id)
    {
        $laporan = LaporanKaryawan::with('kelompok')->findOrFail($id);
        
        if (!$laporan->file_path || !Storage::disk('public')->exists($laporan->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
        
        return Storage::disk('public')->download($laporan->file_path);
    }
    
    public function exportExcel(Request $request)
    {
        if (!Auth::user()->isAtasan()) {
            abort(403, 'Unauthorized');
        }
        
        // Get filtered data (same as index method)
        $query = LaporanKaryawan::with('kelompok');
        
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
        
        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }
        
        $laporans = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Karyawan');
        
        // Header Title
        $sheet->setCellValue('A1', 'DATA LAPORAN KARYAWAN');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F59E0B');
        $sheet->getRowDimension('1')->setRowHeight(25);
        
        // Info Row
        $sheet->setCellValue('A2', 'Tanggal Export: ' . now()->format('d-m-Y H:i:s'));
        $sheet->mergeCells('A2:N2');
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Headers
        $headers = [
            'No', 'Hari/Tanggal', 'KELOMPOK', 'Nama', 'Instansi', 'Alamat Tujuan',
            'Waktu Mulai Kegiatan', 'Jenis Kegiatan', 'Deskripsi Kegiatan', 'Waktu Selesai Kegiatan',
            'Durasi Waktu', 'Lokasi', 'Dokumentasi'
        ];
        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A3:N3')->getFont()->setBold(true);
        $sheet->getStyle('A3:N3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FCD34D');
        $sheet->getStyle('A3:N3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:N3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension('3')->setRowHeight(20);
        
        // Data
        $row = 4;
        $no = 1;
        foreach ($laporans as $laporan) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $laporan->hari . ' / ' . $laporan->tanggal->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $laporan->kelompok->nama_kelompok ?? '-');
            $sheet->setCellValue('D' . $row, $laporan->nama);
            $sheet->setCellValue('E' . $row, $laporan->instansi);
            $sheet->setCellValue('F' . $row, $laporan->alamat_tujuan);
            $sheet->setCellValue('G' . $row, $laporan->waktu_mulai_kegiatan ? Carbon::parse($laporan->waktu_mulai_kegiatan)->format('H:i') : '-');
            $sheet->setCellValue('H' . $row, $laporan->jenis_kegiatan ?? '-');
            $sheet->setCellValue('I' . $row, $laporan->deskripsi_kegiatan ?? '-');
            $sheet->setCellValue('J' . $row, $laporan->waktu_selesai_kegiatan ? Carbon::parse($laporan->waktu_selesai_kegiatan)->format('H:i') : '-');
            $sheet->setCellValue('K' . $row, $laporan->durasi_waktu ? number_format($laporan->durasi_waktu, 2) . ' jam' : '0 jam');
            $sheet->setCellValue('L' . $row, $laporan->lokasi ?? '-');
            $sheet->setCellValue('M' . $row, $laporan->file_path ? 'Ada File' : '-');
            $row++;
            $no++;
        }
        
        // Auto size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set borders
        $lastRow = $row - 1;
        $sheet->getStyle('A3:N' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        // Wrap text for long content (termasuk deskripsi)
        $sheet->getStyle('F4:M' . $lastRow)->getAlignment()->setWrapText(true);
        
        $filename = 'Laporan_Karyawan_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}

