<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\PrediksiKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    // Jenis kegiatan yang tersedia
    private $jenisKegiatan = [
        'Perbaikan Meteran' => 'Perbaikan Meteran',
        'Perbaikan Sambungan Rumah' => 'Perbaikan Sambungan Rumah',
        'Pemeriksaan Gardu' => 'Pemeriksaan Gardu',
        'Jenis Kegiatan' => 'Jenis Kegiatan'
    ];

    // Parameter Triple Exponential Smoothing
    private $alpha = 0.4; // Smoothing constant for level
    private $beta = 0.3;  // Smoothing constant for trend

    /**
     * Normalize jenis kegiatan format
     * Convert various formats to standard format
     */
    private function normalizeJenisKegiatan($jenisKegiatan)
    {
        // Mapping untuk format yang berbeda (termasuk format lama untuk backward compatibility)
        $mapping = [
            // Format baru
            'perbaikan_meteran' => 'Perbaikan Meteran',
            'perbaikan meteran' => 'Perbaikan Meteran',
            'Perbaikan Meteran' => 'Perbaikan Meteran',
            'perbaikan_sambungan_rumah' => 'Perbaikan Sambungan Rumah',
            'perbaikan sambungan rumah' => 'Perbaikan Sambungan Rumah',
            'Perbaikan Sambungan Rumah' => 'Perbaikan Sambungan Rumah',
            'pemeriksaan_gardu' => 'Pemeriksaan Gardu',
            'pemeriksaan gardu' => 'Pemeriksaan Gardu',
            'Pemeriksaan Gardu' => 'Pemeriksaan Gardu',
            'jenis_kegiatan' => 'Jenis Kegiatan',
            'jenis kegiatan' => 'Jenis Kegiatan',
            'Jenis Kegiatan' => 'Jenis Kegiatan',
            // Format lama (untuk backward compatibility)
            'perbaikan_kwh' => 'Perbaikan Meteran',
            'perbaikan kwh' => 'Perbaikan Meteran',
            'Perbaikan KWH' => 'Perbaikan Meteran',
            'pemeliharaan_pengkabelan' => 'Perbaikan Sambungan Rumah',
            'pemeliharaan pengkabelan' => 'Perbaikan Sambungan Rumah',
            'Pemeliharaan Pengkabelan' => 'Perbaikan Sambungan Rumah',
            'pengecekan_gardu' => 'Pemeriksaan Gardu',
            'pengecekan gardu' => 'Pemeriksaan Gardu',
            'Pengecekan Gardu' => 'Pemeriksaan Gardu',
            'penanganan_gangguan' => 'Jenis Kegiatan',
            'penanganan gangguan' => 'Jenis Kegiatan',
            'Penanganan Gangguan' => 'Jenis Kegiatan',
        ];

        $normalized = trim($jenisKegiatan);
        return $mapping[$normalized] ?? $normalized;
    }

    /**
     * Display generate kegiatan page
     */
    public function generateKegiatan()
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            abort(403, 'Unauthorized access');
        }

        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
        
        // Format kelompok untuk dropdown
        $kelompoksFormatted = $kelompoks->map(function ($kelompok) {
            return [
                'id' => $kelompok->id,
                'label' => $kelompok->nama_kelompok . ' (' . $kelompok->shift . ')'
            ];
        });

        // Get latest predictions if any
        $latestPredictions = PrediksiKegiatan::with('kelompok')
            ->orderBy('waktu_generate', 'desc')
            ->get()
            ->groupBy('kelompok_id')
            ->map(function ($predictions) {
                return $predictions->first();
            });

        $formattedPredictions = collect();
        foreach ($latestPredictions as $prediction) {
            $formattedPredictions->push([
                'kelompok_id' => $prediction->kelompok_id,
                'kelompok' => $prediction->kelompok->nama_kelompok ?? 'N/A',
                'tanggal_prediksi' => $prediction->tanggal_prediksi->format('Y-m-d'),
                'waktu_generate' => $prediction->waktu_generate->format('H:i'),
            ]);
        }

        // Define jenisKegiatan variable for view
        $jenisKegiatan = $this->jenisKegiatan;

        return view('admin.prediksi.generate-kegiatan', compact(
            'kelompoksFormatted',
            'jenisKegiatan',
            'formattedPredictions'
        ));
    }

    /**
     * Generate prediksi kegiatan using Triple Exponential Smoothing
     */
    public function generatePrediksiKegiatan(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'kelompok_id' => 'required|exists:kelompok,id',
            'jenis_kegiatan' => 'nullable|in:all,Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan'
        ]);

        $kelompokId = $request->kelompok_id;
        $jenisKegiatanFilter = $request->jenis_kegiatan;

        // Get kelompok
        $kelompok = Kelompok::findOrFail($kelompokId);

        // Determine which jenis kegiatan to predict
        $jenisKegiatanList = $jenisKegiatanFilter === 'all' 
            ? array_keys($this->jenisKegiatan) 
            : [$jenisKegiatanFilter];

        $results = [];
        
        // Set timezone ke Makassar (WITA - UTC+8)
        $now = Carbon::now('Asia/Makassar');
        $tanggalPrediksi = $now->copy()->addDay()->startOfDay(); // Besok

        foreach ($jenisKegiatanList as $jenisKegiatan) {
            // Normalize jenis kegiatan to standard format
            $normalizedJenisKegiatan = $this->normalizeJenisKegiatan($jenisKegiatan);
            
            // Get historical data for this jenis kegiatan
            $historicalData = $this->getHistoricalData($kelompokId, $normalizedJenisKegiatan);

            if (count($historicalData) < 3) {
                continue; // Skip if not enough data
            }

            // Calculate prediction using Triple Exponential Smoothing
            $prediction = $this->calculateTripleExponentialSmoothing($historicalData);

            // Calculate MAPE
            $mape = $this->calculateMAPE($historicalData, $prediction['forecasts']);

            // Delete old predictions with different format for same jenis kegiatan
            PrediksiKegiatan::where('kelompok_id', $kelompokId)
                ->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))
                ->where(function($query) use ($normalizedJenisKegiatan) {
                    $query->where('jenis_kegiatan', $normalizedJenisKegiatan)
                          ->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $normalizedJenisKegiatan)))
                          ->orWhere('jenis_kegiatan', strtolower($normalizedJenisKegiatan));
                })
                ->delete();

            // Save prediction to database with normalized format
            // Waktu generate menggunakan timezone Makassar
            $waktuGenerate = Carbon::now('Asia/Makassar');
            
            $prediksiKegiatan = PrediksiKegiatan::create([
                'kelompok_id' => $kelompokId,
                'jenis_kegiatan' => $normalizedJenisKegiatan,
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'prediksi_jam' => $prediction['nextForecast'],
                'mape' => $mape,
                'waktu_generate' => $waktuGenerate,
                'params' => [
                    'alpha' => $this->alpha,
                    'beta' => $this->beta,
                    'level' => $prediction['lastLevel'],
                    'trend' => $prediction['lastTrend']
                ]
            ]);

            $results[] = [
                'jenis_kegiatan' => $normalizedJenisKegiatan,
                'prediksi_jam' => round($prediction['nextForecast'], 2),
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'mape' => round($mape, 2),
                'waktu_generate' => $waktuGenerate->format('H:i')
            ];
        }

        if (empty($results)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data historis yang cukup untuk melakukan prediksi. Minimal diperlukan 3 data historis.'
            ]);
        }

        // Prepare chart data
        $chartData = $this->prepareChartData($results);

        return response()->json([
            'success' => true,
            'message' => 'Prediksi berhasil dihasilkan untuk ' . count($results) . ' jenis kegiatan',
            'kelompok' => $kelompok->nama_kelompok,
            'chart' => $chartData,
            'table' => $results
        ]);
    }

    /**
     * Get historical data for prediction
     */
    private function getHistoricalData($kelompokId, $jenisKegiatan)
    {
        // Normalize jenis kegiatan
        $normalizedJenisKegiatan = $this->normalizeJenisKegiatan($jenisKegiatan);
        
        // Get data from last 12 months, grouped by month
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        
        // Get data with normalized jenis_kegiatan and also check for variations
        $data = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->where(function($query) use ($normalizedJenisKegiatan) {
                $query->where('jenis_kegiatan', $normalizedJenisKegiatan)
                      ->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $normalizedJenisKegiatan)))
                      ->orWhere('jenis_kegiatan', strtolower($normalizedJenisKegiatan));
            })
            ->where('tanggal', '>=', $startDate)
            ->whereNotNull('durasi_waktu')
            ->where('durasi_waktu', '>', 0)
            ->select(
                DB::raw('YEAR(tanggal) as year'),
                DB::raw('MONTH(tanggal) as month'),
                DB::raw('AVG(durasi_waktu) as avg_durasi')
            )
            ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
            ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
            ->get();

        // Convert to simple array of values
        return $data->pluck('avg_durasi')->toArray();
    }

    /**
     * Calculate Triple Exponential Smoothing (Holt's Method - Double Exponential Smoothing)
     * Since we don't have clear seasonality, we use Double Exponential Smoothing
     */
    private function calculateTripleExponentialSmoothing($data)
    {
        $n = count($data);
        
        if ($n < 3) {
            throw new \Exception('Minimal 3 data diperlukan untuk prediksi');
        }

        // Initialize level and trend
        // Level awal = rata-rata dari 3-4 data pertama
        $initialLevel = array_sum(array_slice($data, 0, min(4, $n))) / min(4, $n);
        
        // Trend awal = (rata-rata 2 data terakhir - rata-rata 2 data pertama) / jumlah periode tengah
        $firstHalf = array_slice($data, 0, min(2, floor($n/2)));
        $lastHalf = array_slice($data, -min(2, floor($n/2)));
        $avgFirst = array_sum($firstHalf) / count($firstHalf);
        $avgLast = array_sum($lastHalf) / count($lastHalf);
        $initialTrend = ($avgLast - $avgFirst) / max(1, $n - 2);

        // Initialize arrays
        $levels = [$initialLevel];
        $trends = [$initialTrend];
        $forecasts = [];

        // Calculate level and trend for each period
        for ($i = 0; $i < $n; $i++) {
            $currentData = $data[$i];
            $prevLevel = $levels[$i];
            $prevTrend = $trends[$i];

            // Calculate new level: S_t = α * Y_t + (1-α) * (S_{t-1} + b_{t-1})
            $newLevel = $this->alpha * $currentData + (1 - $this->alpha) * ($prevLevel + $prevTrend);
            $levels[] = $newLevel;

            // Calculate new trend: b_t = β * (S_t - S_{t-1}) + (1-β) * b_{t-1}
            $newTrend = $this->beta * ($newLevel - $prevLevel) + (1 - $this->beta) * $prevTrend;
            $trends[] = $newTrend;

            // Forecast for next period: F_{t+1} = S_t + b_t
            if ($i < $n - 1) {
                $forecasts[] = $prevLevel + $prevTrend;
            }
        }

        // Forecast for next period (besok)
        $lastLevel = $levels[$n];
        $lastTrend = $trends[$n];
        $nextForecast = $lastLevel + $lastTrend;

        return [
            'levels' => $levels,
            'trends' => $trends,
            'forecasts' => $forecasts,
            'lastLevel' => $lastLevel,
            'lastTrend' => $lastTrend,
            'nextForecast' => $nextForecast
        ];
    }

    /**
     * Calculate MAPE (Mean Absolute Percentage Error)
     */
    private function calculateMAPE($actualData, $forecasts)
    {
        if (count($forecasts) === 0 || count($actualData) <= 1) {
            return 0;
        }

        $errors = [];
        // Skip first data point (no forecast for it)
        for ($i = 1; $i < count($actualData); $i++) {
            if (isset($forecasts[$i - 1]) && $actualData[$i] > 0) {
                $error = abs(($actualData[$i] - $forecasts[$i - 1]) / $actualData[$i]) * 100;
                $errors[] = $error;
            }
        }

        if (empty($errors)) {
            return 0;
        }

        return array_sum($errors) / count($errors);
    }

    /**
     * Prepare chart data for Chart.js
     */
    private function prepareChartData($results)
    {
        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = $result['jenis_kegiatan'];
            $data[] = $result['prediksi_jam'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Prediksi (Jam)',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)',
                        'rgba(16, 185, 129, 0.5)',
                        'rgba(245, 158, 11, 0.5)',
                        'rgba(239, 68, 68, 0.5)'
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    'borderWidth' => 2
                ]
            ]
        ];
    }

    /**
     * Get prediksi kegiatan by kelompok
     */
    public function getPrediksiKegiatanByKelompok(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $kelompokId = $request->get('kelompok_id');

        if (!$kelompokId) {
            return response()->json([
                'success' => false,
                'message' => 'Kelompok ID diperlukan'
            ]);
        }

        $kelompok = Kelompok::findOrFail($kelompokId);
        // Set timezone ke Makassar untuk tanggal prediksi besok
        $tanggalPrediksi = Carbon::now('Asia/Makassar')->addDay()->startOfDay();

        // Get latest predictions for this kelompok, grouped by normalized jenis_kegiatan
        $allPredictions = PrediksiKegiatan::where('kelompok_id', $kelompokId)
            ->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))
            ->orderBy('waktu_generate', 'desc')
            ->get();

        if ($allPredictions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada prediksi untuk kelompok ini'
            ]);
        }

        // Group by normalized jenis_kegiatan and get only the latest one
        $groupedPredictions = $allPredictions->groupBy(function($prediction) {
            return $this->normalizeJenisKegiatan($prediction->jenis_kegiatan);
        });

        $results = [];
        foreach ($groupedPredictions as $normalizedJenis => $predictions) {
            // Get the latest prediction for this jenis kegiatan
            $latestPrediction = $predictions->first();
            
            // Format waktu_generate dengan timezone Makassar
            $waktuGenerate = Carbon::parse($latestPrediction->waktu_generate)->setTimezone('Asia/Makassar');
            
            $results[] = [
                'jenis_kegiatan' => $normalizedJenis,
                'prediksi_jam' => round($latestPrediction->prediksi_jam, 2),
                'tanggal_prediksi' => $latestPrediction->tanggal_prediksi->format('Y-m-d'),
                'mape' => round($latestPrediction->mape ?? 0, 2),
                'waktu_generate' => $waktuGenerate->format('H:i')
            ];
        }

        // Sort by jenis_kegiatan
        usort($results, function($a, $b) {
            return strcmp($a['jenis_kegiatan'], $b['jenis_kegiatan']);
        });

        // Prepare chart data
        $chartData = $this->prepareChartData($results);

        return response()->json([
            'success' => true,
            'message' => 'Data prediksi berhasil dimuat',
            'kelompok' => $kelompok->nama_kelompok,
            'chart' => $chartData,
            'table' => $results
        ]);
    }

    /**
     * Display generate kegiatan page for karyawan
     */
    public function generateKegiatanKaryawan()
    {
        // Ensure only karyawan can access
        $user = auth()->user();
        if (!$user->isKaryawan() || !$user->kelompok_id) {
            abort(403, 'Unauthorized access');
        }

        // Get kelompok from user
        $kelompok = Kelompok::findOrFail($user->kelompok_id);

        // Get latest predictions if any
        $latestPredictions = PrediksiKegiatan::with('kelompok')
            ->where('kelompok_id', $user->kelompok_id)
            ->orderBy('waktu_generate', 'desc')
            ->get()
            ->groupBy('kelompok_id')
            ->map(function ($predictions) {
                return $predictions->first();
            });

        $formattedPredictions = collect();
        foreach ($latestPredictions as $prediction) {
            $formattedPredictions->push([
                'kelompok_id' => $prediction->kelompok_id,
                'kelompok' => $prediction->kelompok->nama_kelompok ?? 'N/A',
                'tanggal_prediksi' => $prediction->tanggal_prediksi->format('Y-m-d'),
                'waktu_generate' => $prediction->waktu_generate->format('H:i'),
            ]);
        }

        // Define jenisKegiatan variable for view
        $jenisKegiatan = $this->jenisKegiatan;

        return view('kelompok.prediksi.generate-kegiatan', compact(
            'kelompok',
            'jenisKegiatan',
            'formattedPredictions'
        ));
    }

    /**
     * Generate prediksi kegiatan for karyawan (using their kelompok)
     */
    public function generatePrediksiKegiatanKaryawan(Request $request)
    {
        // Ensure only karyawan can access
        $user = auth()->user();
        if (!$user->isKaryawan() || !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'jenis_kegiatan' => 'nullable|in:all,Perbaikan Meteran,Perbaikan Sambungan Rumah,Pemeriksaan Gardu,Jenis Kegiatan'
        ]);

        // Use kelompok_id from logged in user
        $kelompokId = $user->kelompok_id;
        $jenisKegiatanFilter = $request->jenis_kegiatan ?? 'all';

        // Get kelompok
        $kelompok = Kelompok::findOrFail($kelompokId);

        // Determine which jenis kegiatan to predict
        $jenisKegiatanList = $jenisKegiatanFilter === 'all' 
            ? array_keys($this->jenisKegiatan) 
            : [$jenisKegiatanFilter];

        $results = [];
        
        // Set timezone ke Makassar (WITA - UTC+8)
        $now = Carbon::now('Asia/Makassar');
        $tanggalPrediksi = $now->copy()->addDay()->startOfDay(); // Besok

        foreach ($jenisKegiatanList as $jenisKegiatan) {
            // Normalize jenis kegiatan to standard format
            $normalizedJenisKegiatan = $this->normalizeJenisKegiatan($jenisKegiatan);
            
            // Get historical data for this jenis kegiatan
            $historicalData = $this->getHistoricalData($kelompokId, $normalizedJenisKegiatan);

            if (count($historicalData) < 3) {
                continue; // Skip if not enough data
            }

            // Calculate prediction using Triple Exponential Smoothing
            $prediction = $this->calculateTripleExponentialSmoothing($historicalData);

            // Calculate MAPE
            $mape = $this->calculateMAPE($historicalData, $prediction['forecasts']);

            // Delete old predictions with different format for same jenis kegiatan
            PrediksiKegiatan::where('kelompok_id', $kelompokId)
                ->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))
                ->where(function($query) use ($normalizedJenisKegiatan) {
                    $query->where('jenis_kegiatan', $normalizedJenisKegiatan)
                          ->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $normalizedJenisKegiatan)))
                          ->orWhere('jenis_kegiatan', strtolower($normalizedJenisKegiatan));
                })
                ->delete();

            // Save prediction to database with normalized format
            // Waktu generate menggunakan timezone Makassar
            $waktuGenerate = Carbon::now('Asia/Makassar');
            
            $prediksiKegiatan = PrediksiKegiatan::create([
                'kelompok_id' => $kelompokId,
                'jenis_kegiatan' => $normalizedJenisKegiatan,
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'prediksi_jam' => $prediction['nextForecast'],
                'mape' => $mape,
                'waktu_generate' => $waktuGenerate,
                'params' => [
                    'alpha' => $this->alpha,
                    'beta' => $this->beta,
                    'level' => $prediction['lastLevel'],
                    'trend' => $prediction['lastTrend']
                ]
            ]);

            $results[] = [
                'jenis_kegiatan' => $normalizedJenisKegiatan,
                'prediksi_jam' => round($prediction['nextForecast'], 2),
                'tanggal_prediksi' => $tanggalPrediksi->format('Y-m-d'),
                'mape' => round($mape, 2),
                'waktu_generate' => $waktuGenerate->format('H:i')
            ];
        }

        if (empty($results)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data historis yang cukup untuk melakukan prediksi. Minimal diperlukan 3 data historis.'
            ]);
        }

        // Prepare chart data
        $chartData = $this->prepareChartData($results);

        return response()->json([
            'success' => true,
            'message' => 'Prediksi berhasil dihasilkan untuk ' . count($results) . ' jenis kegiatan',
            'kelompok' => $kelompok->nama_kelompok,
            'chart' => $chartData,
            'table' => $results
        ]);
    }

    /**
     * Get prediksi kegiatan by kelompok for karyawan
     */
    public function getPrediksiKegiatanByKelompokKaryawan(Request $request)
    {
        // Ensure only karyawan can access
        $user = auth()->user();
        if (!$user->isKaryawan() || !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Use kelompok_id from logged in user
        $kelompokId = $user->kelompok_id;
        $kelompok = Kelompok::findOrFail($kelompokId);
        // Set timezone ke Makassar untuk tanggal prediksi besok
        $tanggalPrediksi = Carbon::now('Asia/Makassar')->addDay()->startOfDay();

        // Get latest predictions for this kelompok, grouped by normalized jenis_kegiatan
        $allPredictions = PrediksiKegiatan::where('kelompok_id', $kelompokId)
            ->where('tanggal_prediksi', $tanggalPrediksi->format('Y-m-d'))
            ->orderBy('waktu_generate', 'desc')
            ->get();

        if ($allPredictions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada prediksi untuk kelompok ini'
            ]);
        }

        // Group by normalized jenis_kegiatan and get only the latest one
        $groupedPredictions = $allPredictions->groupBy(function($prediction) {
            return $this->normalizeJenisKegiatan($prediction->jenis_kegiatan);
        });

        $results = [];
        foreach ($groupedPredictions as $normalizedJenis => $predictions) {
            // Get the latest prediction for this jenis kegiatan
            $latestPrediction = $predictions->first();
            
            // Format waktu_generate dengan timezone Makassar
            $waktuGenerate = Carbon::parse($latestPrediction->waktu_generate)->setTimezone('Asia/Makassar');
            
            $results[] = [
                'jenis_kegiatan' => $normalizedJenis,
                'prediksi_jam' => round($latestPrediction->prediksi_jam, 2),
                'tanggal_prediksi' => $latestPrediction->tanggal_prediksi->format('Y-m-d'),
                'mape' => round($latestPrediction->mape ?? 0, 2),
                'waktu_generate' => $waktuGenerate->format('H:i')
            ];
        }

        // Sort by jenis_kegiatan
        usort($results, function($a, $b) {
            return strcmp($a['jenis_kegiatan'], $b['jenis_kegiatan']);
        });

        // Prepare chart data
        $chartData = $this->prepareChartData($results);

        return response()->json([
            'success' => true,
            'message' => 'Data prediksi berhasil dimuat',
            'kelompok' => $kelompok->nama_kelompok,
            'chart' => $chartData,
            'table' => $results
        ]);
    }
}

