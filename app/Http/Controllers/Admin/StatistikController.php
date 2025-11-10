<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistikController extends Controller
{
    /**
     * Display statistics page
     */
    public function index(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            abort(403, 'Unauthorized access');
        }
        
        $tipe = $request->get('tipe', 'laporan'); // 'laporan' or 'job'
        $kelompokFilter = $request->get('kelompok', 'all');
        $bulanFilter = $request->get('bulan', now()->format('Y-m'));
        
        // Validate tipe
        if (!in_array($tipe, ['laporan', 'job'])) {
            $tipe = 'laporan';
        }
        
        // Get all kelompok that are registered in the system
        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
        
        // Get list of kelompok names for filter dropdown
        $kelompokList = $kelompoks->pluck('nama_kelompok')->toArray();
        
        return view('admin.statistik.index', compact('kelompoks', 'kelompokList', 'kelompokFilter', 'bulanFilter', 'tipe'));
    }

    /**
     * Get statistics data for Chart.js (JSON API)
     */
    public function data(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            $tipe = $request->get('tipe', 'laporan');
            $kelompokFilter = $request->get('kelompok', 'all');
            $bulanFilter = $request->get('bulan', null);
            
            // Validate tipe
            if (!in_array($tipe, ['laporan', 'job'])) {
                $tipe = 'laporan';
            }
            
            // Get data grouped by month (last 12 months)
            $startDate = Carbon::now()->subMonths(12)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            
            if ($tipe === 'laporan') {
                // Query untuk Laporan Karyawan
                $query = LaporanKaryawan::with('kelompok')
                    ->whereBetween('tanggal', [$startDate, $endDate]);
                
                if ($kelompokFilter !== 'all') {
                    $kelompok = Kelompok::where('nama_kelompok', $kelompokFilter)->first();
                    if ($kelompok) {
                        $query->where('kelompok_id', $kelompok->id);
                    }
                }
                
                // Get jumlah laporan per bulan
                $jumlahKegiatanPerBulan = (clone $query)->select(
                    DB::raw('YEAR(tanggal) as year'),
                    DB::raw('MONTH(tanggal) as month'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                ->get();
                
                // Untuk laporan, kita hitung jumlah laporan per bulan (tidak ada durasi)
                $rataDurasiPerBulan = collect([]);
                
                // Rekap tabel
                $rekapTabel = (clone $query)->select(
                    'kelompok_id',
                    'tanggal',
                    'nama'
                )
                ->orderBy('tanggal', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($item) {
                    return [
                        'kelompok' => $item->kelompok->nama_kelompok ?? 'N/A',
                        'tanggal_mulai' => $item->tanggal->format('Y-m-d'),
                        'tanggal_selesai' => $item->tanggal->format('Y-m-d'),
                        'durasi' => 1, // Laporan harian, durasi 1 hari
                        'nama' => $item->nama,
                    ];
                });
                
            } else {
                // Query untuk Job Pekerjaan
                $query = JobPekerjaan::with('kelompok')
                    ->whereBetween('tanggal', [$startDate, $endDate]);
                
                if ($kelompokFilter !== 'all') {
                    $kelompok = Kelompok::where('nama_kelompok', $kelompokFilter)->first();
                    if ($kelompok) {
                        $query->where('kelompok_id', $kelompok->id);
                    }
                }
                
                // Get jumlah job per bulan
                $jumlahKegiatanPerBulan = (clone $query)->select(
                    DB::raw('YEAR(tanggal) as year'),
                    DB::raw('MONTH(tanggal) as month'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                ->get();
                
                // Get rata-rata waktu penyelesaian per bulan
                $rataDurasiPerBulan = (clone $query)->select(
                    DB::raw('YEAR(tanggal) as year'),
                    DB::raw('MONTH(tanggal) as month'),
                    DB::raw('AVG(waktu_penyelesaian) as rata_durasi')
                )
                ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                ->get();
                
                // Rekap tabel
                $rekapTabel = (clone $query)->select(
                    'kelompok_id',
                    'tanggal',
                    'waktu_penyelesaian',
                    'lokasi'
                )
                ->orderBy('tanggal', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($item) {
                    return [
                        'kelompok' => $item->kelompok->nama_kelompok ?? 'N/A',
                        'tanggal_mulai' => $item->tanggal->format('Y-m-d'),
                        'tanggal_selesai' => $item->tanggal->format('Y-m-d'),
                        'durasi' => $item->waktu_penyelesaian ?? 0,
                        'lokasi' => $item->lokasi ?? 'N/A',
                    ];
                });
            }
            
            // Prepare labels and data for charts
            $labels = [];
            $jumlahData = [];
            $durasiData = [];
            
            // Generate labels for last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M Y');
                
                // Find matching data
                $jumlahItem = $jumlahKegiatanPerBulan->first(function ($item) use ($date) {
                    return $item->year == $date->year && $item->month == $date->month;
                });
                $jumlahData[] = $jumlahItem ? $jumlahItem->jumlah : 0;
                
                if ($tipe === 'job' && $rataDurasiPerBulan->isNotEmpty()) {
                    $durasiItem = $rataDurasiPerBulan->first(function ($item) use ($date) {
                        return $item->year == $date->year && $item->month == $date->month;
                    });
                    $durasiData[] = $durasiItem ? round($durasiItem->rata_durasi, 2) : 0;
                } else {
                    // Untuk laporan, tidak ada durasi, isi dengan 0 atau jumlah laporan
                    $durasiData[] = 0;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tipe' => $tipe,
                    'labels' => $labels,
                    'jumlah_kegiatan' => [
                        'labels' => $labels,
                        'data' => $jumlahData,
                    ],
                    'rata_durasi' => [
                        'labels' => $labels,
                        'data' => $durasiData,
                    ],
                    'rekap_tabel' => $rekapTabel,
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'labels' => [],
                    'jumlah_kegiatan' => ['labels' => [], 'data' => []],
                    'rata_durasi' => ['labels' => [], 'data' => []],
                    'rekap_tabel' => [],
                ],
            ], 500);
        }
    }
}