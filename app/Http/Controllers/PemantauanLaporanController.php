<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemantauanLaporanController extends Controller
{
    public function index(Request $request)
    {
        $kelompoks = Kelompok::with(['karyawan'])->orderBy('created_at', 'desc')->get();
        $karyawans = Karyawan::with('kelompok')->orderBy('created_at', 'desc')->get();
        
        // Build query with filters
        $query = LaporanKaryawan::with(['kelompok']);
        
        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok_id', $request->kelompok);
        }
        
        $laporanKaryawans = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        // Calculate statistics for initial display
        $totalLaporan = $query->count();
        $laporanHariIni = (clone $query)->whereDate('tanggal', today())->count();
        $pendingReview = (clone $query)->whereDate('tanggal', '<', now()->subDays(1))->count();
        
        // Calculate average per day
        $monthQuery = clone $query;
        if (!$request->filled('tanggal')) {
            $monthQuery->whereMonth('tanggal', now()->month)
                      ->whereYear('tanggal', now()->year);
            $avgPerHari = $monthQuery->count() / now()->day;
        } else {
            // If filtering by date, show average based on filtered results
            $avgPerHari = $totalLaporan;
        }
        
        $statistics = [
            'totalLaporan' => $totalLaporan,
            'laporanHariIni' => $laporanHariIni,
            'pendingReview' => $pendingReview,
            'avgPerHari' => round($avgPerHari, 1)
        ];
        
        return view('dashboard.atasan.pemantauan-laporan', compact('kelompoks', 'karyawans', 'laporanKaryawans', 'statistics'));
    }

    public function getStatistics(Request $request)
    {
        try {
            // Build base query
            $query = LaporanKaryawan::query();
            
            // Apply same filters as index method
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->filled('kelompok')) {
                $query->where('kelompok_id', $request->kelompok);
            }
            
            $totalLaporan = $query->count();
            
            // For daily stats, we need to consider the same filters
            $laporanHariIni = (clone $query)->whereDate('tanggal', today())->count();
            $pendingReview = (clone $query)->whereDate('tanggal', '<', now()->subDays(1))->count();
            
            // Calculate average per day for current month or filtered period
            $monthQuery = clone $query;
            if (!$request->filled('tanggal')) {
                $monthQuery->whereMonth('tanggal', now()->month)
                          ->whereYear('tanggal', now()->year);
                $avgPerHari = $monthQuery->count() / now()->day;
            } else {
                // For filtered periods, calculate based on the period
                $avgPerHari = $totalLaporan; // Show total for specific date
            }
            
            return response()->json([
                'totalLaporan' => $totalLaporan,
                'laporanHariIni' => $laporanHariIni,
                'pendingReview' => $pendingReview,
                'avgPerHari' => round($avgPerHari, 1)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat statistik'], 500);
        }
    }

    public function getLaporanData(Request $request)
    {
        try {
            $query = LaporanKaryawan::with(['kelompok']);

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->filled('kelompok')) {
                $query->where('kelompok_id', $request->kelompok);
            }

            $laporanKaryawans = $query->orderBy('tanggal', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'data' => $laporanKaryawans->items(),
                'current_page' => $laporanKaryawans->currentPage(),
                'last_page' => $laporanKaryawans->lastPage(),
                'per_page' => $laporanKaryawans->perPage(),
                'total' => $laporanKaryawans->total(),
                'from' => $laporanKaryawans->firstItem(),
                'to' => $laporanKaryawans->lastItem()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data laporan'], 500);
        }
    }

    public function show($id)
    {
        try {
            $laporan = LaporanKaryawan::with('kelompok')->findOrFail($id);
            return response()->json($laporan);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memuat detail laporan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDokumentasi($id)
    {
        try {
            $laporan = LaporanKaryawan::findOrFail($id);
            
            $dokumentasi = [];
            
            // Add file if exists
            if ($laporan->file_path) {
                $dokumentasi[] = '/storage/' . $laporan->file_path;
            }
            
            // Add text dokumentasi if exists
            if ($laporan->dokumentasi) {
                $dokumentasi[] = $laporan->dokumentasi;
            }

            return response()->json([
                'dokumentasi' => $dokumentasi,
                'file_path' => $laporan->file_path,
                'text_dokumentasi' => $laporan->dokumentasi
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat dokumentasi'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'instansi' => 'required|string|max:255',
                'alamat_tujuan' => 'required|string|max:255',
                'dokumentasi' => 'nullable|string',
                'tanggal' => 'required|date',
                'hari' => 'nullable|string|max:255',
                'jabatan' => 'nullable|string|max:255'
            ]);

            $laporan = LaporanKaryawan::findOrFail($id);
            
            // Update only allowed fields
            $laporan->update([
                'nama' => $request->nama,
                'instansi' => $request->instansi,
                'alamat_tujuan' => $request->alamat_tujuan,
                'dokumentasi' => $request->dokumentasi,
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'jabatan' => $request->jabatan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diperbarui',
                'data' => $laporan->load('kelompok')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memperbarui laporan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $laporan = LaporanKaryawan::findOrFail($id);
            $laporan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menghapus laporan',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $query = LaporanKaryawan::with(['kelompok']);

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->filled('kelompok')) {
                $query->where('kelompok_id', $request->kelompok);
            }

            $laporanKaryawans = $query->orderBy('tanggal', 'desc')->get();

            // Create CSV file (Excel-compatible)
            $filename = 'laporan-karyawan-' . now()->format('Y-m-d') . '.csv';
            
            // CSV headers for Excel compatibility
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ];

            // Create CSV content with BOM for Excel UTF-8 support
            $content = "\xEF\xBB\xBF"; // UTF-8 BOM
            $content .= "ID,Tanggal,Nama,Kelompok,Instansi,Alamat Tujuan,Jabatan,Dokumentasi,Status\n";
            
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
                    'Selesai'
                ];
                $content .= implode(',', $row) . "\n";
            }

            return response($content, 200, $headers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal export data: ' . $e->getMessage()], 500);
        }
    }
}
