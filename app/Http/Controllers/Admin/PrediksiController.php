<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Prediksi;
use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class PrediksiController extends Controller
{
    /**
     * Display prediction page
     */
    public function index(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            abort(403, 'Unauthorized access');
        }
        
        $tipe = $request->get('tipe', 'laporan'); // 'laporan' or 'job'
        
        // Validate tipe
        if (!in_array($tipe, ['laporan', 'job'])) {
            $tipe = 'laporan';
        }
        
        // Get all kelompok that are registered in the system
        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
        
        // Get list of kelompok names for filter dropdown
        $kelompokList = $kelompoks->pluck('nama_kelompok')->toArray();
        
        // Get latest predictions (filter by tipe if needed)
        $latestPredictions = Prediksi::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->filter(function ($prediksi) use ($tipe) {
                $params = $prediksi->params ?? [];
                return ($params['tipe'] ?? 'laporan') === $tipe;
            })
            ->values();
        
        // Format predictions for frontend
        $formattedPredictions = $latestPredictions->map(function ($prediksi) {
            $params = $prediksi->params ?? [];
            $tipe = $params['tipe'] ?? 'laporan';
            $kelompokName = $params['kelompok'] ?? 'N/A';
            $bulanTarget = $params['bulan_target'] ?? (strpos($prediksi->bulan, '_') !== false ? explode('_', $prediksi->bulan)[0] : $prediksi->bulan);
            $kelompokDisplay = $kelompokName === 'all' ? 'Semua Kelompok' : $kelompokName;
            $tipeLabel = $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
            $hasilLabel = $tipe === 'laporan' ? 'jumlah laporan' : 'hari';
            
            return [
                'id' => $prediksi->id,
                'bulan' => $bulanTarget,
                'tipe' => $tipe,
                'tipe_label' => $tipeLabel,
                'kelompok' => $kelompokDisplay,
                'hasil_prediksi' => round($prediksi->hasil_prediksi, 2),
                'hasil_label' => $hasilLabel,
                'akurasi' => round($prediksi->akurasi, 2),
                'metode' => $prediksi->metode,
                'created_at' => $prediksi->created_at->format('d/m/Y H:i'),
            ];
        })->values();
        
        return view('admin.prediksi.index', compact('kelompoks', 'kelompokList', 'latestPredictions', 'formattedPredictions', 'tipe'));
    }

    /**
     * Get latest predictions (API)
     */
    public function getLatest(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            $tipe = $request->get('tipe', 'laporan');
            
            // Validate tipe
            if (!in_array($tipe, ['laporan', 'job'])) {
                $tipe = 'laporan';
            }
            
            // Get latest predictions (filter by tipe if needed)
            $latestPredictions = Prediksi::orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->filter(function ($prediksi) use ($tipe) {
                    $params = $prediksi->params ?? [];
                    return ($params['tipe'] ?? 'laporan') === $tipe;
                })
                ->values();
            
            // Format data for frontend
            $formattedPredictions = $latestPredictions->map(function ($prediksi) {
                $params = $prediksi->params ?? [];
                $tipe = $params['tipe'] ?? 'laporan';
                $kelompokName = $params['kelompok'] ?? 'N/A';
                $bulanTarget = $params['bulan_target'] ?? (strpos($prediksi->bulan, '_') !== false ? explode('_', $prediksi->bulan)[0] : $prediksi->bulan);
                $kelompokDisplay = $kelompokName === 'all' ? 'Semua Kelompok' : $kelompokName;
                $tipeLabel = $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
                $hasilLabel = $tipe === 'laporan' ? 'jumlah laporan' : 'hari';
                
                return [
                    'id' => $prediksi->id,
                    'bulan' => $bulanTarget,
                    'tipe' => $tipe,
                    'tipe_label' => $tipeLabel,
                    'kelompok' => $kelompokDisplay,
                    'hasil_prediksi' => round($prediksi->hasil_prediksi, 2),
                    'hasil_label' => $hasilLabel,
                    'akurasi' => round($prediksi->akurasi, 2),
                    'metode' => $prediksi->metode,
                    'created_at' => $prediksi->created_at->format('d/m/Y H:i'),
                    'created_at_raw' => $prediksi->created_at->toIso8601String(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPredictions->values(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate prediction using Holt-Winters
     */
    public function generate(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $kelompokNames = Kelompok::pluck('nama_kelompok')->toArray();
        $validKelompok = array_merge(['all'], $kelompokNames);
        
        $request->validate([
            'tipe' => 'required|string|in:laporan,job',
            'kelompok' => ['required', 'string', function ($attribute, $value, $fail) use ($validKelompok) {
                if (!in_array($value, $validKelompok)) {
                    $fail('Kelompok yang dipilih tidak valid.');
                }
            }],
            'bulan_target' => 'required|date_format:Y-m',
            'alpha' => 'required|numeric|min:0|max:1',
            'beta' => 'required|numeric|min:0|max:1',
            'gamma' => 'required|numeric|min:0|max:1',
        ]);

        try {
            $tipe = $request->tipe;
            $kelompok = $request->kelompok;
            $bulanTarget = $request->bulan_target;
            $alpha = (float) $request->alpha;
            $beta = (float) $request->beta;
            $gamma = (float) $request->gamma;
            
            // Get historical data (last 12 months or more if available)
            $startDate = Carbon::parse($bulanTarget)->subMonths(24)->startOfMonth();
            $endDate = Carbon::parse($bulanTarget)->subMonth()->endOfMonth();
            
            if ($tipe === 'laporan') {
                // Query untuk Laporan Karyawan
                $query = LaporanKaryawan::with('kelompok')
                    ->whereBetween('tanggal', [$startDate, $endDate]);
                
                if ($kelompok !== 'all') {
                    $kelompokModel = Kelompok::where('nama_kelompok', $kelompok)->first();
                    if ($kelompokModel) {
                        $query->where('kelompok_id', $kelompokModel->id);
                    }
                }
                
                // Get data grouped by month - untuk laporan, kita hitung jumlah laporan per bulan
                // Karena laporan tidak ada durasi, kita gunakan jumlah laporan sebagai metrik
                $historicalData = (clone $query)->select(
                        DB::raw('YEAR(tanggal) as year'),
                        DB::raw('MONTH(tanggal) as month'),
                        DB::raw('COUNT(*) as jumlah_laporan')
                    )
                    ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                    ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                    ->get();
                
                // Convert jumlah laporan menjadi "durasi" untuk prediksi (asumsi 1 laporan = 1 hari)
                $series = $historicalData->pluck('jumlah_laporan')->toArray();
                
            } else {
                // Query untuk Job Pekerjaan
                $query = JobPekerjaan::with('kelompok')
                    ->whereBetween('tanggal', [$startDate, $endDate]);
                
                if ($kelompok !== 'all') {
                    $kelompokModel = Kelompok::where('nama_kelompok', $kelompok)->first();
                    if ($kelompokModel) {
                        $query->where('kelompok_id', $kelompokModel->id);
                    }
                }
                
                // Get data grouped by month - rata-rata waktu penyelesaian
                $historicalData = (clone $query)->select(
                        DB::raw('YEAR(tanggal) as year'),
                        DB::raw('MONTH(tanggal) as month'),
                        DB::raw('AVG(waktu_penyelesaian) as rata_durasi')
                    )
                    ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                    ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
                    ->get();
                
                $series = $historicalData->pluck('rata_durasi')->toArray();
            }
            
            if (count($series) < 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data historis kurang. Dibutuhkan minimal 12 bulan data untuk melakukan prediksi.',
                ], 400);
            }
            
            // Prepare labels
            $labels = $historicalData->map(function ($item) {
                return Carbon::create($item->year, $item->month, 1)->format('Y-m');
            })->toArray();
            
            // Apply Holt-Winters algorithm
            $s = 12; // season length (monthly with yearly seasonality)
            $h = 1; // forecast horizon (1 month ahead)
            
            $result = $this->holtWintersAdditive($series, $alpha, $beta, $gamma, $s, $h);
            
            // Calculate accuracy (MAPE)
            $akurasi = $this->calculateMAPE($series, $result['in_sample']);
            
            // Get forecast value
            $hasilPrediksi = $result['forecast'][0];
            
            // Save prediction to database
            // Store bulan_target, kelompok, and tipe info in params for better tracking
            $prediksiKey = $bulanTarget . '_' . $tipe . ($kelompok !== 'all' ? '_' . $kelompok : '_all');
            
            $prediksi = Prediksi::updateOrCreate(
                [
                    'bulan' => $prediksiKey,
                ],
                [
                    'hasil_prediksi' => $hasilPrediksi,
                    'akurasi' => $akurasi,
                    'metode' => 'Holt-Winters',
                    'params' => [
                        'tipe' => $tipe,
                        'alpha' => $alpha,
                        'beta' => $beta,
                        'gamma' => $gamma,
                        'kelompok' => $kelompok,
                        'bulan_target' => $bulanTarget,
                    ],
                ]
            );
            
            // Prepare response data for chart
            $chartLabels = array_merge($labels, [$bulanTarget]);
            $historisData = $series;
            $prediksiData = array_fill(0, count($series), null);
            $prediksiData[] = $hasilPrediksi;
            
            $kelompokLabel = $kelompok === 'all' ? 'Semua Kelompok' : $kelompok;
            $tipeLabel = $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
            $hasilLabel = $tipe === 'laporan' ? 'jumlah laporan' : 'hari';
            
            return response()->json([
                'success' => true,
                'message' => 'Prediksi ' . $tipeLabel . ' berhasil dihasilkan untuk ' . $kelompokLabel,
                'data' => [
                    'id' => $prediksi->id,
                    'tipe' => $tipe,
                    'tipe_label' => $tipeLabel,
                    'bulan' => $bulanTarget,
                    'hasil_prediksi' => round($hasilPrediksi, 2),
                    'hasil_label' => $hasilLabel,
                    'akurasi' => round($akurasi, 2),
                    'labels' => $chartLabels,
                    'historis' => $historisData,
                    'prediksi' => $prediksiData,
                    'final_params' => $result['final'],
                    'kelompok' => $kelompokLabel,
                    'metode' => 'Holt-Winters',
                    'created_at' => $prediksi->created_at->format('d/m/Y H:i'),
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Holt-Winters Additive (Triple Exponential Smoothing) algorithm
     * 
     * @param array $series Array of values indexed from 0..N-1 (Y_1 ... Y_N)
     * @param float $alpha Level smoothing parameter
     * @param float $beta Trend smoothing parameter
     * @param float $gamma Seasonal smoothing parameter
     * @param int $s Season length (12 for monthly with yearly seasonality)
     * @param int $h Forecast horizon (number of steps ahead)
     * @return array ['in_sample' => [], 'forecast' => [], 'final' => ['L' => float, 'T' => float, 'seasonals' => []]]
     */
    private function holtWintersAdditive(array $series, $alpha, $beta, $gamma, $s = 12, $h = 1)
    {
        $N = count($series);
        
        if ($N < $s * 2) {
            throw new \Exception("Butuh minimal " . ($s * 2) . " data poin untuk season length $s");
        }
        
        // Initialize seasonals (multiplicative)
        $seasonals = [];
        
        // Initial level: average of first season
        $avgSeason = array_sum(array_slice($series, 0, $s)) / $s;
        
        for ($i = 0; $i < $s; $i++) {
            $seasonals[$i] = $series[$i] / $avgSeason;
        }
        
        // Initial L and T
        $L = $avgSeason;
        
        // Initial trend: average difference between seasons
        $firstSeasonAvg = array_sum(array_slice($series, 0, $s)) / $s;
        $secondSeasonAvg = array_sum(array_slice($series, $s, $s)) / $s;
        $T = ($secondSeasonAvg - $firstSeasonAvg) / $s;
        
        $results = [];
        
        // Apply Holt-Winters formula
        for ($t = 0; $t < $N; $t++) {
            $seasonIndex = $t % $s;
            $season = $seasonals[$seasonIndex] ?? 1;
            $Yt = $series[$t];
            
            $lastL = $L;
            $lastT = $T;
            
            // Level: L_t = α(Y_t / S_{t-s}) + (1-α)(L_{t-1} + T_{t-1})
            $L = $alpha * ($Yt / $season) + (1 - $alpha) * ($lastL + $lastT);
            
            // Trend: T_t = β(L_t - L_{t-1}) + (1-β)T_{t-1}
            $T = $beta * ($L - $lastL) + (1 - $beta) * $lastT;
            
            // Seasonal: S_t = γ(Y_t / L_t) + (1-γ)S_{t-s}
            $seasonals[$seasonIndex] = $gamma * ($Yt / $L) + (1 - $gamma) * $season;
            
            // One-step forecast (in-sample)
            $forecast = ($L + $T) * $seasonals[$seasonIndex];
            $results[] = $forecast;
        }
        
        // Forecast h steps ahead
        $forecasts = [];
        for ($i = 1; $i <= $h; $i++) {
            $m = $i;
            $seasonIndex = ($N + $i - 1) % $s;
            $season = $seasonals[$seasonIndex];
            $forecast = ($L + $m * $T) * $season;
            $forecasts[] = $forecast;
        }
        
        return [
            'in_sample' => $results,
            'forecast' => $forecasts,
            'final' => [
                'L' => $L,
                'T' => $T,
                'seasonals' => $seasonals,
            ],
        ];
    }

    /**
     * Calculate MAPE (Mean Absolute Percentage Error)
     */
    private function calculateMAPE(array $actual, array $forecast)
    {
        $n = count($actual);
        $sum = 0;
        $count = 0;
        
        for ($i = 0; $i < $n; $i++) {
            if ($actual[$i] != 0) {
                $sum += abs(($actual[$i] - $forecast[$i]) / $actual[$i]) * 100;
                $count++;
            }
        }
        
        if ($count == 0) {
            return 0;
        }
        
        $mape = $sum / $count;
        
        // Convert to accuracy percentage (100 - MAPE)
        $accuracy = 100 - $mape;
        
        return max(0, $accuracy); // Ensure non-negative
    }

    /**
     * Show prediction detail
     */
    public function show($id)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            $prediksi = Prediksi::findOrFail($id);
            $params = $prediksi->params ?? [];
            
            $tipe = $params['tipe'] ?? 'laporan';
            $kelompok = $params['kelompok'] ?? 'N/A';
            $bulanTarget = $params['bulan_target'] ?? (strpos($prediksi->bulan, '_') !== false ? explode('_', $prediksi->bulan)[0] : $prediksi->bulan);
            
            $kelompokLabel = $kelompok === 'all' ? 'Semua Kelompok' : $kelompok;
            $tipeLabel = $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
            $hasilLabel = $tipe === 'laporan' ? 'jumlah laporan' : 'hari';
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $prediksi->id,
                    'bulan' => $bulanTarget,
                    'tipe' => $tipe,
                    'tipe_label' => $tipeLabel,
                    'kelompok' => $kelompokLabel,
                    'hasil_prediksi' => round($prediksi->hasil_prediksi, 2),
                    'hasil_label' => $hasilLabel,
                    'akurasi' => round($prediksi->akurasi, 2),
                    'metode' => $prediksi->metode,
                    'params' => $params,
                    'created_at' => $prediksi->created_at->format('d/m/Y H:i'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete prediction by id
     */
    public function destroy($id)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            $prediksi = Prediksi::findOrFail($id);
            $prediksi->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Prediksi berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset prediction data
     */
    public function reset(Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            $bulan = $request->get('bulan');
            
            if ($bulan) {
                Prediksi::where('bulan', $bulan)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Prediksi untuk bulan ' . $bulan . ' berhasil dihapus',
                ]);
            } else {
                Prediksi::truncate();
                return response()->json([
                    'success' => true,
                    'message' => 'Semua data prediksi berhasil dihapus',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export prediction data
     */
    public function export($format, Request $request)
    {
        // Ensure only atasan can access
        if (!auth()->user()->isAtasan()) {
            abort(403, 'Unauthorized access');
        }
        
        try {
            // Get tipe parameter (laporan or job)
            $tipe = $request->get('tipe', 'laporan');
            
            // Validate tipe
            if (!in_array($tipe, ['laporan', 'job'])) {
                $tipe = 'laporan';
            }
            
            // Check if we should export latest prediction only
            $latestOnly = $request->get('latest', false);
            
            if ($latestOnly) {
                // Get only the latest prediction for this tipe
                $prediction = Prediksi::orderBy('created_at', 'desc')
                    ->get()
                    ->filter(function ($prediksi) use ($tipe) {
                        $params = $prediksi->params ?? [];
                        return ($params['tipe'] ?? 'laporan') === $tipe;
                    })
                    ->first();
                
                if (!$prediction) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada data prediksi ' . ($tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan') . ' untuk diexport',
                    ], 404);
                }
                
                $predictions = collect([$prediction]);
            } else {
                // Get ALL latest predictions (last 20) - tidak filter tipe
                // Akan dipisahkan di export berdasarkan tipe masing-masing
                $allPredictions = Prediksi::orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
                
                // Separate by tipe
                $laporanPredictions = $allPredictions->filter(function ($prediksi) {
                    $params = $prediksi->params ?? [];
                    return ($params['tipe'] ?? 'laporan') === 'laporan';
                })->take(10);
                
                $jobPredictions = $allPredictions->filter(function ($prediksi) {
                    $params = $prediksi->params ?? [];
                    return ($params['tipe'] ?? 'laporan') === 'job';
                })->take(10);
                
                // Use the tipe that user requested
                if ($tipe === 'laporan') {
                    $predictions = $laporanPredictions;
                } else {
                    $predictions = $jobPredictions;
                }
                
                if (count($predictions) === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada data prediksi ' . ($tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan') . ' untuk diexport',
                    ], 404);
                }
            }
            
            if ($format === 'pdf') {
                // TODO: Implement PDF export using dompdf
                return response()->json([
                    'success' => false,
                    'message' => 'PDF export belum diimplementasikan',
                ], 501);
            } elseif ($format === 'excel') {
                return $this->exportToExcel($predictions, $latestOnly, $tipe);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Format tidak didukung',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export predictions to Excel
     */
    private function exportToExcel($predictions, $latestOnly = false, $tipe = 'laporan')
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Get tipe label
        $tipeLabel = $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
        
        // Set sheet title
        $sheetTitle = $latestOnly 
            ? 'Hasil Prediksi ' . $tipeLabel 
            : 'Rekap Prediksi ' . $tipeLabel;
        $sheet->setTitle($sheetTitle);
        
        // Title
        $title = $latestOnly 
            ? 'HASIL PREDIKSI TERAKHIR - ' . strtoupper($tipeLabel)
            : 'REKAP HASIL PREDIKSI - ' . strtoupper($tipeLabel);
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:G1');
        // Set header color based on tipe
        $headerColor = $tipe === 'laporan' ? '2563EB' : '059669'; // Blue for laporan, Green for job
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $headerColor]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Subtitle
        $sheet->setCellValue('A2', 'Sistem Prediksi PLN Galesong - ' . $tipeLabel);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 12,
                'italic' => true,
                'color' => ['rgb' => '6B7280']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        // Export date
        $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d F Y H:i:s'));
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '6B7280']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);
        
        // Empty row
        $sheet->getRowDimension(4)->setRowHeight(10);
        
        // Headers
        $headers = [
            'No',
            'Bulan yang Diprediksi',
            'Jenis Data',
            'Kelompok',
            'Hasil Prediksi',
            'Akurasi Model (MAPE)',
            'Tanggal Dibuat'
        ];
        
        $col = 'A';
        $row = 5;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        
        // Style headers - use different color based on tipe
        $tableHeaderColor = $tipe === 'laporan' ? 'F59E0B' : '10B981'; // Orange for laporan, Green for job
        $headerRange = 'A5:' . chr(64 + count($headers)) . '5';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $tableHeaderColor]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(25);
        
        // Data
        $row = 6;
        $no = 1;
        foreach ($predictions as $prediksi) {
            $params = $prediksi->params ?? [];
            $tipe = $params['tipe'] ?? 'laporan';
            $kelompokName = $params['kelompok'] ?? 'N/A';
            $bulanTarget = $params['bulan_target'] ?? (strpos($prediksi->bulan, '_') !== false ? explode('_', $prediksi->bulan)[0] : $prediksi->bulan);
            $kelompokDisplay = $kelompokName === 'all' ? 'Semua Kelompok' : $kelompokName;
            $tipeLabel = $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
            $hasilLabel = $tipe === 'laporan' ? 'jumlah laporan' : 'hari';
            
            // Format bulan
            try {
                $bulanFormatted = Carbon::createFromFormat('Y-m', $bulanTarget)->format('F Y');
            } catch (\Exception $e) {
                $bulanFormatted = $bulanTarget;
            }
            
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $bulanFormatted);
            $sheet->setCellValue('C' . $row, $tipeLabel);
            $sheet->setCellValue('D' . $row, $kelompokDisplay);
            $sheet->setCellValue('E' . $row, number_format($prediksi->hasil_prediksi, 2) . ' ' . $hasilLabel);
            $sheet->setCellValue('F' . $row, number_format($prediksi->akurasi, 2) . '%');
            $sheet->setCellValue('G' . $row, $prediksi->created_at->format('d/m/Y H:i'));
            
            // Style data rows
            $dataRange = 'A' . $row . ':' . chr(64 + count($headers)) . $row;
            $isEven = ($no % 2) === 0;
            $sheet->getStyle($dataRange)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $isEven ? 'F9FAFB' : 'FFFFFF']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);
            
            // Center align for No, Hasil Prediksi, Akurasi
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Color code accuracy
            $accuracy = $prediksi->akurasi;
            $accuracyColor = '10B981'; // Green
            if ($accuracy < 70) {
                $accuracyColor = 'EF4444'; // Red
            } elseif ($accuracy < 85) {
                $accuracyColor = 'F59E0B'; // Orange
            }
            $sheet->getStyle('F' . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $accuracyColor]
                ]
            ]);
            
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
            $no++;
        }
        
        // Summary section
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN');
        $sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
        $sheet->getStyle('A' . $summaryRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension($summaryRow)->setRowHeight(25);
        
        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Prediksi:');
        $sheet->setCellValue('B' . $summaryRow, count($predictions));
        $sheet->getStyle('A' . $summaryRow)->applyFromArray([
            'font' => ['bold' => true]
        ]);
        
        $summaryRow++;
        $avgAccuracy = $predictions->avg('akurasi');
        $sheet->setCellValue('A' . $summaryRow, 'Rata-rata Akurasi:');
        $sheet->setCellValue('B' . $summaryRow, number_format($avgAccuracy, 2) . '%');
        $sheet->getStyle('A' . $summaryRow)->applyFromArray([
            'font' => ['bold' => true]
        ]);
        $sheet->getStyle('B' . $summaryRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => $avgAccuracy >= 85 ? '10B981' : ($avgAccuracy >= 70 ? 'F59E0B' : 'EF4444')]
            ]
        ]);
        
        // Auto size columns
        foreach (range('A', chr(64 + count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            if ($col === 'A') {
                $sheet->getColumnDimension($col)->setWidth(8);
            } elseif ($col === 'B') {
                $sheet->getColumnDimension($col)->setWidth(25);
            } elseif ($col === 'C') {
                $sheet->getColumnDimension($col)->setWidth(18);
            } elseif ($col === 'D') {
                $sheet->getColumnDimension($col)->setWidth(20);
            } elseif ($col === 'E') {
                $sheet->getColumnDimension($col)->setWidth(20);
            } elseif ($col === 'F') {
                $sheet->getColumnDimension($col)->setWidth(20);
            } elseif ($col === 'G') {
                $sheet->getColumnDimension($col)->setWidth(20);
            }
        }
        
        // Set print area
        $sheet->getPageSetup()->setPrintArea('A1:' . chr(64 + count($headers)) . ($row - 1));
        
        // Generate filename based on tipe
        $tipeLabelForFile = $tipe === 'laporan' ? 'Laporan_Karyawan' : 'Job_Pekerjaan';
        $filenamePrefix = $latestOnly 
            ? 'Hasil_Prediksi_' . $tipeLabelForFile 
            : 'Rekap_Prediksi_' . $tipeLabelForFile;
        $filename = $filenamePrefix . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Download file
        $writer = new Xlsx($spreadsheet);
        
        $response = response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename);
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $response;
    }
}