<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
use App\Models\Prediksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * Export all data
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

            // Get all data
            $kelompoks = Kelompok::with(['karyawan'])->get();
            $karyawans = DB::table('karyawan')->get();
            $laporanKaryawans = LaporanKaryawan::with(['kelompok'])->get();
            $jobPekerjaans = DB::table('job_pekerjaan')->get();
            $prediksis = collect(); // Prediksi model might not exist

            // Create CSV content
            $filename = 'PLN_Galesong_All_Data_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ];

            // Create CSV content with BOM for Excel UTF-8 support
            $content = "\xEF\xBB\xBF"; // UTF-8 BOM
            
            // Sheet 1: Kelompok
            $content .= "=== DATA KELOMPOK ===\n";
            $content .= "ID,Nama Kelompok,Shift,Jumlah Karyawan,Jumlah Laporan,Created At\n";
            foreach ($kelompoks as $kelompok) {
                $row = [
                    $kelompok->id,
                    '"' . str_replace('"', '""', $kelompok->nama_kelompok) . '"',
                    '"' . str_replace('"', '""', $kelompok->shift) . '"',
                    $kelompok->karyawan_count,
                    0, // Laporan count will be calculated separately
                    $kelompok->created_at->format('Y-m-d H:i:s')
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA KARYAWAN ===\n";
            $content .= "ID,Nama,ID Kelompok,Nama Kelompok,Status,Created At\n";
            foreach ($karyawans as $karyawan) {
                $kelompok = $kelompoks->find($karyawan->kelompok_id);
                $row = [
                    $karyawan->id,
                    '"' . str_replace('"', '""', $karyawan->nama) . '"',
                    $karyawan->kelompok_id,
                    '"' . str_replace('"', '""', $kelompok->nama_kelompok ?? '-') . '"',
                    '"' . str_replace('"', '""', $karyawan->status ?? 'Aktif') . '"',
                    $karyawan->created_at ? date('Y-m-d H:i:s', strtotime($karyawan->created_at)) : '-'
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA LAPORAN KARYAWAN ===\n";
            $content .= "ID,Tanggal,Nama,Kelompok,Instansi,Alamat Tujuan,Jabatan,Dokumentasi,Created At\n";
            foreach ($laporanKaryawans as $laporan) {
                $row = [
                    $laporan->id,
                    $laporan->created_at->format('Y-m-d H:i:s'),
                    '"' . str_replace('"', '""', $laporan->nama) . '"',
                    '"' . str_replace('"', '""', $laporan->kelompok->nama_kelompok ?? '-') . '"',
                    '"' . str_replace('"', '""', $laporan->instansi) . '"',
                    '"' . str_replace('"', '""', $laporan->alamat_tujuan) . '"',
                    '"' . str_replace('"', '""', $laporan->jabatan ?? '-') . '"',
                    '"' . str_replace('"', '""', $laporan->dokumentasi ?? '-') . '"',
                    $laporan->created_at->format('Y-m-d H:i:s')
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA JOB PEKERJAAN ===\n";
            $content .= "ID,Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan,Lokasi,Bulan Data,Tanggal,Waktu Penyelesaian,Created At\n";
            foreach ($jobPekerjaans as $job) {
                $row = [
                    $job->id,
                    $job->perbaikan_kwh ?? 0,
                    $job->pemeliharaan_pengkabelan ?? 0,
                    $job->pengecekan_gardu ?? 0,
                    $job->penanganan_gangguan ?? 0,
                    '"' . str_replace('"', '""', $job->lokasi ?? '-') . '"',
                    '"' . str_replace('"', '""', $job->bulan_data ?? '-') . '"',
                    $job->tanggal ? date('Y-m-d', strtotime($job->tanggal)) : '-',
                    $job->waktu_penyelesaian ?? 0,
                    $job->created_at ? date('Y-m-d H:i:s', strtotime($job->created_at)) : '-'
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA PREDIKSI ===\n";
            $content .= "ID,Kelompok,Periode,Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan,Alpha,Beta,Gamma,Created At\n";
            foreach ($prediksis as $prediksi) {
                $row = [
                    $prediksi->id,
                    '"' . str_replace('"', '""', $prediksi->kelompok->nama_kelompok ?? '-') . '"',
                    '"' . str_replace('"', '""', $prediksi->periode ?? '-') . '"',
                    $prediksi->perbaikan_kwh ?? 0,
                    $prediksi->pemeliharaan_pengkabelan ?? 0,
                    $prediksi->pengecekan_gardu ?? 0,
                    $prediksi->penanganan_gangguan ?? 0,
                    $prediksi->alpha ?? 0,
                    $prediksi->beta ?? 0,
                    $prediksi->gamma ?? 0,
                    $prediksi->created_at->format('Y-m-d H:i:s')
                ];
                $content .= implode(',', $row) . "\n";
            }

            return response($content, 200, $headers);
            
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
            $jobPekerjaans = DB::table('job_pekerjaan')->where('kelompok_id', $kelompokId)->get();
            $prediksis = collect(); // Prediksi model might not exist

            // Create CSV content
            $filename = 'PLN_Galesong_' . str_replace(' ', '_', $kelompok->nama_kelompok) . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ];

            // Create CSV content with BOM for Excel UTF-8 support
            $content = "\xEF\xBB\xBF"; // UTF-8 BOM
            
            // Sheet 1: Info Kelompok
            $content .= "=== INFO KELOMPOK ===\n";
            $content .= "Field,Value\n";
            $content .= "ID Kelompok," . $kelompok->id . "\n";
            $content .= "Nama Kelompok,\"" . str_replace('"', '""', $kelompok->nama_kelompok) . "\"\n";
            $content .= "Shift,\"" . str_replace('"', '""', $kelompok->shift) . "\"\n";
            $content .= "Jumlah Karyawan," . $karyawans->count() . "\n";
            $content .= "Jumlah Laporan," . $laporanKaryawans->count() . "\n";
            $content .= "Created At," . $kelompok->created_at->format('Y-m-d H:i:s') . "\n";
            
            $content .= "\n=== DATA KARYAWAN ===\n";
            $content .= "ID,Nama,Status,Created At\n";
            foreach ($karyawans as $karyawan) {
                $row = [
                    $karyawan->id,
                    '"' . str_replace('"', '""', $karyawan->nama) . '"',
                    '"' . str_replace('"', '""', $karyawan->status ?? 'Aktif') . '"',
                    $karyawan->created_at ? date('Y-m-d H:i:s', strtotime($karyawan->created_at)) : '-'
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA LAPORAN KELOMPOK ===\n";
            $content .= "ID,Tanggal,Nama,Instansi,Alamat Tujuan,Jabatan,Dokumentasi,Created At\n";
            foreach ($laporanKaryawans as $laporan) {
                $row = [
                    $laporan->id,
                    $laporan->created_at->format('Y-m-d H:i:s'),
                    '"' . str_replace('"', '""', $laporan->nama) . '"',
                    '"' . str_replace('"', '""', $laporan->instansi) . '"',
                    '"' . str_replace('"', '""', $laporan->alamat_tujuan) . '"',
                    '"' . str_replace('"', '""', $laporan->jabatan ?? '-') . '"',
                    '"' . str_replace('"', '""', $laporan->dokumentasi ?? '-') . '"',
                    $laporan->created_at->format('Y-m-d H:i:s')
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA JOB PEKERJAAN ===\n";
            $content .= "ID,Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan,Lokasi,Bulan Data,Tanggal,Waktu Penyelesaian,Created At\n";
            foreach ($jobPekerjaans as $job) {
                $row = [
                    $job->id,
                    $job->perbaikan_kwh ?? 0,
                    $job->pemeliharaan_pengkabelan ?? 0,
                    $job->pengecekan_gardu ?? 0,
                    $job->penanganan_gangguan ?? 0,
                    '"' . str_replace('"', '""', $job->lokasi ?? '-') . '"',
                    '"' . str_replace('"', '""', $job->bulan_data ?? '-') . '"',
                    $job->tanggal ? date('Y-m-d', strtotime($job->tanggal)) : '-',
                    $job->waktu_penyelesaian ?? 0,
                    $job->created_at ? date('Y-m-d H:i:s', strtotime($job->created_at)) : '-'
                ];
                $content .= implode(',', $row) . "\n";
            }
            
            $content .= "\n=== DATA PREDIKSI ===\n";
            $content .= "ID,Periode,Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan,Alpha,Beta,Gamma,Created At\n";
            foreach ($prediksis as $prediksi) {
                $row = [
                    $prediksi->id,
                    '"' . str_replace('"', '""', $prediksi->periode ?? '-') . '"',
                    $prediksi->perbaikan_kwh ?? 0,
                    $prediksi->pemeliharaan_pengkabelan ?? 0,
                    $prediksi->pengecekan_gardu ?? 0,
                    $prediksi->penanganan_gangguan ?? 0,
                    $prediksi->alpha ?? 0,
                    $prediksi->beta ?? 0,
                    $prediksi->gamma ?? 0,
                    $prediksi->created_at->format('Y-m-d H:i:s')
                ];
                $content .= implode(',', $row) . "\n";
            }

            return response($content, 200, $headers);
            
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
}
