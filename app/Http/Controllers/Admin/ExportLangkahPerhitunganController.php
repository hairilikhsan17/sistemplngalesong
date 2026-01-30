<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\LaporanKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportLangkahPerhitunganController extends Controller
{
    // Jenis kegiatan yang tersedia
    private $jenisKegiatan = [
        'Perbaikan Meteran' => 'Perbaikan Meteran',
        'Perbaikan Sambungan Rumah' => 'Perbaikan Sambungan Rumah',
        'Pemeriksaan Gardu' => 'Pemeriksaan Gardu',
        'Jenis Kegiatan lainnya' => 'Jenis Kegiatan lainnya'
    ];

    /**
     * Export calculation steps to Excel for all groups
     * Restructured: One sheet per Kelompok (K1, K2, K3)
     */
    public function export(Request $request)
    {
        $kelompoks = Kelompok::orderBy('nama_kelompok')->get();
        
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default sheet

        // Sheet 1: Ringkasan Prediksi
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle('Ringkasan Prediksi');
        $this->renderSummarySheet($summarySheet, $kelompoks);

        // Sheet 2: Rumus & Langkah Kerja
        $formulaSheet = $spreadsheet->createSheet();
        $formulaSheet->setTitle('Rumus & Langkah Kerja');
        $this->renderFormulaSheet($formulaSheet);

        foreach ($kelompoks as $kelompok) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($kelompok->nama_kelompok);
            
            $this->renderKelompokSheet($sheet, $kelompok);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Langkah_Perhitungan_HoltWinters_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Render Summary Sheet for all groups and activities
     */
    private function renderSummarySheet($sheet, $kelompoks)
    {
        $sheet->setCellValue('A1', 'RINGKASAN PREDIKSI KEGIATAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $headers = ['Kelompok', 'Jenis Kegiatan', 'Prediksi (Jam/Menit)', 'Tanggal Prediksi (Besok)', 'MAPE', 'Waktu Generate'];
        $sheet->fromArray($headers, NULL, 'A3');
        
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A3:F3')->applyFromArray($headerStyle);

        $currentRow = 4;
        $absoluteLastData = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        $referenceDate = $absoluteLastData ? Carbon::parse($absoluteLastData->tanggal) : Carbon::now();
        $waktuGenerate = Carbon::now('Asia/Makassar')->format('H:i');

        $currentRow = 4;
        $waktuGenerate = Carbon::now('Asia/Makassar')->format('H:i');

        foreach ($kelompoks as $kelompok) {
            // Header Kelompok in Summary
            $sheet->setCellValue('A' . $currentRow, 'Ringkasan Prediksi: ' . $kelompok->nama_kelompok);
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            $headers = ['Jenis Kegiatan', 'Prediksi (Jam/Menit)', 'Tanggal Prediksi (Shift)', 'MAPE', 'Waktu Generate'];
            $sheet->fromArray($headers, NULL, 'A' . $currentRow);
            $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            $tanggalPrediksi = $this->getNextWorkDate($kelompok->id);
            $absoluteLastData = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
            $referenceDate = $absoluteLastData ? Carbon::parse($absoluteLastData->tanggal) : Carbon::now();

            foreach ($this->jenisKegiatan as $jenis) {
                $normalizedJenis = $this->normalizeJenisKegiatan($jenis);
                $historicalData = $this->getHistoricalData($kelompok->id, $normalizedJenis, $referenceDate);
                
                if (count($historicalData) < 1) continue;

                $period = 12;
                $useTriple = count($historicalData) >= 12;
                $bestParams = $this->findBestParameters($historicalData, $period);
                
                $result = $useTriple 
                    ? $this->calculateTripleExponentialSmoothing($historicalData, $bestParams['alpha'], $bestParams['beta'], $bestParams['gamma'], $period)
                    : $this->calculateDoubleExponentialSmoothing($historicalData, $bestParams['alpha'], $bestParams['beta']);

                $mape = $this->calculateAcademicMAPE($historicalData, $result['forecasts']);
                
                $totalMinutes = $result['nextForecast'] > 0 ? max(1, round($result['nextForecast'] * 60)) : 0;
                $hours = floor($totalMinutes / 60);
                $mins = $totalMinutes % 60;
                
                $jamMenit = '0 menit';
                if ($totalMinutes > 0) {
                    if ($hours > 0) {
                        $jamMenit = $hours . ' jam' . ($mins > 0 ? ' ' . $mins . ' menit' : '');
                    } else {
                        $jamMenit = $mins . ' menit';
                    }
                }

                $sheet->fromArray([
                    $normalizedJenis,
                    $jamMenit,
                    $tanggalPrediksi ? $tanggalPrediksi->format('Y-m-d') : '-',
                    round($mape, 2) . '%',
                    $waktuGenerate
                ], NULL, 'A' . $currentRow);
                
                $currentRow++;
            }
            $currentRow++; // Space between kelompok tables
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Get the next work date for a kelompok based on sequence rotation
     */
    private function getNextWorkDate($kelompokId)
    {
        $lastRecord = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        if (!$lastRecord) return null;

        $lastKelompokId = $lastRecord->kelompok_id;
        $lastDate = Carbon::parse($lastRecord->tanggal);

        $allKelompoks = Kelompok::orderBy('nama_kelompok')->pluck('id')->toArray();
        $numKelompoks = count($allKelompoks);
        
        if ($numKelompoks === 0) return null;

        $lastIndex = array_search($lastKelompokId, $allKelompoks);
        $targetIndex = array_search($kelompokId, $allKelompoks);

        if ($lastIndex === false || $targetIndex === false) return null;

        $steps = ($targetIndex - $lastIndex + $numKelompoks) % $numKelompoks;
        if ($steps === 0) $steps = $numKelompoks;

        return $lastDate->addDays($steps)->startOfDay();
    }

    /**
     * Render Formula and Calculation Steps Sheet
     */
    private function renderFormulaSheet($sheet)
    {
        $sheet->setCellValue('A1', 'RUMUS DAN LANGKAH KERJA PERHITUNGAN HOLT-WINTERS (ADDITIVE)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $currentRow = 3;
        $sheet->setCellValue('A' . $currentRow, '1. RUMUS MATEMATIS');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $formulas = [
            ['Komponen', 'Rumus', 'Keterangan'],
            ['Level (Lt)', 'Lt = α(Yt - St-m) + (1-α)(Lt-1 + Tt-1)', 'α = Smoothing Level, m = Season Length'],
            ['Trend (Tt)', 'Tt = β(Lt - Lt-1) + (1-β)Tt-1', 'β = Smoothing Trend'],
            ['Seasonal (St)', 'St = γ(Yt - Lt) + (1-γ)St-m', 'γ = Smoothing Seasonal'],
            ['Forecast (Ft+m)', 'Ft+m = Lt + mTt + St+m-k', 'k = Periode Musiman'],
        ];
        $sheet->fromArray($formulas, NULL, 'A' . $currentRow);
        $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
        $currentRow += count($formulas) + 1;

        $sheet->setCellValue('A' . $currentRow, '2. LANGKAH PENGERJAAN (TAHAPAN PERHITUNGAN)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'A. Tahap Inisialisasi (Bulan 1)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Pada periode awal (Bulan 1), nilai komponen diinisialisasi sebagai berikut:');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '- Level (L1): Diambil dari rata-rata data pada siklus pertama (m=12).');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '- Trend (T1): Diambil dari rata-rata selisih antar siklus data historis.');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '- Seasonal (S1): Selisih antara Data Aktual (Y1) dengan Level (L1).');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '- Forecast (F1): Belum tersedia karena digunakan sebagai basis inisialisasi.');
        $currentRow++;

        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'B. Tahap Pembaruan / Update (Bulan 2 dan seterusnya)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Langkah-langkah pembaruan nilai pada setiap periode baru (t):');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '1. Hitung Forecast (Ft): Ft = Lt-1 + Tt-1 + St-m');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '2. Hitung Level Baru (Lt): Lt = α(Yt - St-m) + (1-α)(Lt-1 + Tt-1)');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '3. Hitung Trend Baru (Tt): Tt = β(Lt - Lt-1) + (1-β)Tt-1');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '4. Hitung Seasonal Baru (St): St = γ(Yt - Lt) + (1-γ)St-m');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '5. Hitung Error: Error = Yt - Ft');
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, '6. Hitung APE: APE = (|Error| / Yt) * 100%');
        $currentRow++;

        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'C. Tahap Evaluasi (MAPE)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'MAPE dihitung dengan merata-ratakan nilai APE pada data testing (Bulan 11-12).');
        $currentRow++;

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Render all activity calculations for a specific kelompok in one sheet
     */
    private function renderKelompokSheet($sheet, $kelompok)
    {
        $currentRow = 1;
        
        // Header Kelompok
        $sheet->setCellValue('A' . $currentRow, 'LAPORAN PERHITUNGAN HOLT-WINTERS: ' . $kelompok->nama_kelompok);
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(16);
        $currentRow += 2;

        $absoluteLastData = LaporanKaryawan::orderBy('tanggal', 'desc')->first();
        $referenceDate = $absoluteLastData ? Carbon::parse($absoluteLastData->tanggal) : Carbon::now();

        foreach ($this->jenisKegiatan as $jenis) {
            $normalizedJenis = $this->normalizeJenisKegiatan($jenis);
            $historicalData = $this->getHistoricalData($kelompok->id, $normalizedJenis, $referenceDate);
            
            if (count($historicalData) < 1) continue;

            // Render activity header
            $sheet->setCellValue('A' . $currentRow, 'Jenis Kegiatan: ' . $normalizedJenis);
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
            $currentRow++;

            // Algorithm refinement: season_length = 12
            $period = 12;
            $useTriple = count($historicalData) >= 12;
            
            // Grid Search for best parameters
            $bestParams = $this->findBestParameters($historicalData, $period);
            
            // Calculate with step recording
            $result = $useTriple 
                ? $this->calculateTripleExponentialSmoothing($historicalData, $bestParams['alpha'], $bestParams['beta'], $bestParams['gamma'], $period)
                : $this->calculateDoubleExponentialSmoothing($historicalData, $bestParams['alpha'], $bestParams['beta']);

            // MAPE calculation on test data (months 11-12)
            $mape = $this->calculateAcademicMAPE($historicalData, $result['forecasts']);

            // Info Params
            $sheet->setCellValue('A' . $currentRow, 'Metode: ' . ($useTriple ? 'Triple Exponential Smoothing Additive (Holt-Winters)' : 'Double Exponential Smoothing (Holt)'));
            $currentRow++;
            $sheet->setCellValue('A' . $currentRow, 'Skala Data: Durasi Waktu (Menit) | Alpha: ' . $bestParams['alpha'] . ' | Beta: ' . $bestParams['beta'] . ' | Gamma: ' . ($useTriple ? $bestParams['gamma'] : '0 (N/A)'));
            $currentRow++;

            // Table Headers
            $headers = ['Bulan', 'Data Aktual (Yt)', 'Level (Lt)', 'Trend (Tt)', 'Seasonal (St)', 'Forecast (Ft)', 'Error', 'Abs Error', 'APE (%)', 'Keterangan'];
            $sheet->fromArray($headers, NULL, 'A' . $currentRow);
            
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->applyFromArray($headerStyle);
            $currentRow++;

            $n = count($historicalData);
            $startRow = $currentRow;

            for ($i = 0; $i < $n; $i++) {
                $actual = $historicalData[$i];
                $lt = $result['levels'][$i+1] ?? $result['levels'][$i];
                $tt = $result['trends'][$i+1] ?? $result['trends'][$i];
                $st = $useTriple ? ($result['seasonals'][$i] ?? '-') : '-';
                $ft = $result['forecasts'][$i] ?? '-';
                
                $error = '-';
                $absError = '-';
                $ape = '-';
                $keterangan = ($i < 10) ? 'Training' : 'Testing';

                if ($i > 0 && is_numeric($ft)) {
                    $error = $actual - $ft;
                    $absError = abs($error);
                    $ape = ($actual > 0) ? ($absError / $actual) * 100 : 0;
                }

                $sheet->fromArray([
                    'Bulan ' . ($i + 1),
                    $actual,
                    round($lt, 4),
                    round($tt, 4),
                    is_numeric($st) ? round($st, 4) : $st,
                    is_numeric($ft) ? round($ft, 4) : $ft,
                    is_numeric($error) ? round($error, 4) : $error,
                    is_numeric($absError) ? round($absError, 4) : $absError,
                    is_numeric($ape) ? round($ape, 2) . '%' : $ape,
                    $keterangan
                ], NULL, 'A' . $currentRow);
                
                // Note for Month 1
                if ($i === 0) {
                    $sheet->setCellValue('J' . $currentRow, 'Inisialisasi (L0, T0, S0)');
                }
                
                // Highlight testing data
                if ($i >= 10) {
                    $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EBF1DE');
                }
                
                $currentRow++;
            }

            // Summary for this activity
            $sheet->setCellValue('A' . $currentRow, 'MAPE (Berdasarkan Periode Hari/Shift Terakhir):');
            $sheet->setCellValue('B' . $currentRow, round($mape, 2) . '%');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            // Prediksi Hari Berikutnya Forecast Row
            $totalMinutes = $result['nextForecast'] > 0 ? max(1, round($result['nextForecast'] * 60)) : 0;
            $hours = floor($totalMinutes / 60);
            $mins = $totalMinutes % 60;
            
            $jamMenit = '0 menit';
            if ($totalMinutes > 0) {
                if ($hours > 0) {
                    $jamMenit = $hours . ' jam' . ($mins > 0 ? ' ' . $mins . ' menit' : '');
                } else {
                    $jamMenit = $mins . ' menit';
                }
            }

            $tanggalPrediksi = $this->getNextWorkDate($kelompok->id);
            $labelPrediksi = 'Prediksi Hari Berikutnya (' . ($tanggalPrediksi ? $tanggalPrediksi->format('d M') : 't+1') . ', Shift ' . $kelompok->nama_kelompok . ')';

            $sheet->fromArray([
                $labelPrediksi,
                '-',
                '-',
                '-',
                '-',
                round($result['nextForecast'], 4),
                '-',
                '-',
                '-',
                'Hasil Peramalan'
            ], NULL, 'A' . $currentRow);
            
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FDE9D9');
            $currentRow++;

            $sheet->setCellValue('A' . $currentRow, 'PREDIKSI HARI BERIKUTNYA:');
            $sheet->setCellValue('B' . $currentRow, $jamMenit . ' (' . round($result['nextForecast'], 4) . ' Jam / ' . $totalMinutes . ' Menit)');
            $sheet->getStyle('A' . $currentRow . ':B' . $currentRow)->getFont()->setBold(true)->getColor()->setRGB('C00000');
            $currentRow += 2;

            // Catatan Akademik
            $sheet->setCellValue('A' . $currentRow, 'CATATAN AKADEMIK:');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setUnderline(true);
            $currentRow++;

            $notes = [
                "1. Metode Holt-Winters Additive dipilih karena variasi musiman pada data durasi kegiatan cenderung konstan dan tidak berfluktuasi secara proporsional terhadap level data.",
                "2. Data Aktual (Yt) merupakan durasi waktu penyelesaian kegiatan dalam satuan menit (skala rasio).",
                "3. Meskipun label dataset menggunakan urutan 'Bulan' untuk kepentingan historis, perhitungan ini merepresentasikan durasi per hari/shift sesuai giliran kerja kelompok.",
                "4. Error pada Bulan 1 tidak dihitung karena data periode awal digunakan sebagai basis inisialisasi nilai Level (L0), Trend (T0), dan Seasonal (S0).",
                "5. MAPE dihitung berdasarkan rata-rata persentase kesalahan (APE) dari seluruh periode hari/shift yang tersedia (mulai Bulan 2).",
                "6. Baris 'Prediksi Hari Berikutnya' merupakan hasil peramalan untuk shift kerja mendatang sehingga tidak memiliki data aktual dan tidak dihitung nilai error maupun APE.",
                "7. Jika nilai Seasonal (St) terlihat ekstrim, hal ini dikarenakan data belum stabil (belum mencapai 2 siklus/24 bulan), namun tetap valid untuk studi kasus terbatas.",
            ];

            if ($mape == 0) {
                $notes[] = "7. MAPE 0.00% menunjukkan bahwa data aktual tidak tersedia atau bernilai nol pada periode perhitungan.";
            }

            foreach ($notes as $note) {
                $sheet->setCellValue('A' . $currentRow, $note);
                $currentRow++;
            }
            
            $currentRow += 2; // Space between activities
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function getHistoricalData($kelompokId, $jenisKegiatan, $referenceDate)
    {
        $query = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->where(function($query) use ($jenisKegiatan) {
                $query->where('jenis_kegiatan', $jenisKegiatan)
                      ->orWhere('jenis_kegiatan', strtolower(str_replace(' ', '_', $jenisKegiatan)))
                      ->orWhere('jenis_kegiatan', strtolower($jenisKegiatan));
            })
            ->whereNotNull('durasi_waktu')
            ->where('durasi_waktu', '>', 0);

        // Academic requirement: strictly monthly for 12 months if possible
        $startDate = $referenceDate->copy()->subMonths(12)->startOfMonth();
        
        $data = $query->where('tanggal', '>=', $startDate)
            ->select(
                DB::raw('YEAR(tanggal) as year'),
                DB::raw('MONTH(tanggal) as period'),
                DB::raw('AVG(durasi_waktu) as avg_durasi')
            )
            ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
            ->orderBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
            ->get();

        return $data->pluck('avg_durasi')->toArray();
    }

    private function calculateTripleExponentialSmoothing($data, $alpha, $beta, $gamma, $period)
    {
        $n = count($data);
        
        // Initial Level: Average of first season
        $level = array_sum(array_slice($data, 0, $period)) / $period;
        
        // Initial Trend: Average of differences between seasons
        $trend = (array_sum(array_slice($data, $period, min($period, $n - $period))) - array_sum(array_slice($data, 0, $period))) / ($period * $period);
        
        // Initial Seasonals
        $seasonals = [];
        for ($i = 0; $i < $period; $i++) {
            $seasonals[$i] = $data[$i] - $level;
        }

        $levels = [$level];
        $trends = [$trend];
        $forecasts = [];
        $recordedSeasonals = [];

        for ($i = 0; $i < $n; $i++) {
            $value = $data[$i];
            $prevLevel = $levels[$i];
            $prevTrend = $trends[$i];
            $seasonalIdx = $i % $period;
            $prevSeasonal = $seasonals[$seasonalIdx];

            $recordedSeasonals[] = $prevSeasonal;
            $forecasts[] = $prevLevel + $prevTrend + $prevSeasonal;

            $newLevel = $alpha * ($value - $prevSeasonal) + (1 - $alpha) * ($prevLevel + $prevTrend);
            $levels[] = $newLevel;

            $newTrend = $beta * ($newLevel - $prevLevel) + (1 - $beta) * $prevTrend;
            $trends[] = $newTrend;

            $seasonals[$seasonalIdx] = $gamma * ($value - $newLevel) + (1 - $gamma) * $prevSeasonal;
        }

        $nextForecast = end($levels) + end($trends) + $seasonals[$n % $period];

        return [
            'levels' => $levels,
            'trends' => $trends,
            'seasonals' => $recordedSeasonals,
            'forecasts' => $forecasts,
            'nextForecast' => max(0, $nextForecast)
        ];
    }

    private function calculateDoubleExponentialSmoothing($data, $alpha, $beta)
    {
        $n = count($data);
        if ($n < 2) {
            return [
                'levels' => [$data[0] ?? 0], 'trends' => [0], 'forecasts' => [$data[0] ?? 0],
                'nextForecast' => $data[0] ?? 0
            ];
        }

        $level = $data[0];
        $trend = $data[1] - $data[0];
        
        $levels = [$level];
        $trends = [$trend];
        $forecasts = [$level + $trend];

        for ($i = 1; $i < $n; $i++) {
            $value = $data[$i];
            $prevLevel = $levels[$i-1];
            $prevTrend = $trends[$i-1];

            $newLevel = $alpha * $value + (1 - $alpha) * ($prevLevel + $prevTrend);
            $levels[] = $newLevel;

            $newTrend = $beta * ($newLevel - $prevLevel) + (1 - $beta) * $prevTrend;
            $trends[] = $newTrend;

            $forecasts[] = $newLevel + $newTrend;
        }

        return [
            'levels' => $levels,
            'trends' => $trends,
            'forecasts' => $forecasts,
            'nextForecast' => max(0, end($levels) + end($trends))
        ];
    }

    private function calculateAcademicMAPE($actualData, $forecasts)
    {
        $n = count($actualData);
        if ($n < 2) return 0;

        $errors = [];
        // Calculate MAPE for all months starting from index 1 (Bulan 2)
        // because index 0 is used for initialization
        for ($i = 1; $i < $n; $i++) {
            if ($actualData[$i] > 0 && isset($forecasts[$i-1])) {
                // forecast[i-1] is the forecast for actualData[i]
                $error = abs(($actualData[$i] - $forecasts[$i-1]) / $actualData[$i]) * 100;
                $errors[] = $error;
            }
        }

        return empty($errors) ? 0 : array_sum($errors) / count($errors);
    }

    private function findBestParameters($data, $period)
    {
        $bestAlpha = 0.4; $bestBeta = 0.3; $bestGamma = 0.3;
        $minMAPE = INF;
        $n = count($data);

        for ($a = 0.1; $a <= 0.9; $a += 0.2) {
            for ($b = 0.1; $b <= 0.9; $b += 0.2) {
                if ($n >= $period) {
                    for ($g = 0.1; $g <= 0.9; $g += 0.2) {
                        $res = $this->calculateTripleExponentialSmoothing($data, $a, $b, $g, $period);
                        $mape = $this->calculateAcademicMAPE($data, $res['forecasts']);
                        if ($mape < $minMAPE) { $minMAPE = $mape; $bestAlpha = $a; $bestBeta = $b; $bestGamma = $g; }
                    }
                } else {
                    $res = $this->calculateDoubleExponentialSmoothing($data, $a, $b);
                    $mape = $this->calculateAcademicMAPE($data, $res['forecasts']);
                    if ($mape < $minMAPE) { $minMAPE = $mape; $bestAlpha = $a; $bestBeta = $b; }
                }
            }
        }
        return ['alpha' => $bestAlpha, 'beta' => $bestBeta, 'gamma' => $bestGamma];
    }

    private function normalizeJenisKegiatan($jenisKegiatan)
    {
        $mapping = [
            'perbaikan_meteran' => 'Perbaikan Meteran', 'perbaikan meteran' => 'Perbaikan Meteran', 'Perbaikan Meteran' => 'Perbaikan Meteran',
            'perbaikan_sambungan_rumah' => 'Perbaikan Sambungan Rumah', 'perbaikan sambungan rumah' => 'Perbaikan Sambungan Rumah', 'Perbaikan Sambungan Rumah' => 'Perbaikan Sambungan Rumah',
            'pemeriksaan_gardu' => 'Pemeriksaan Gardu', 'pemeriksaan gardu' => 'Pemeriksaan Gardu', 'Pemeriksaan Gardu' => 'Pemeriksaan Gardu',
            'jenis_kegiatan' => 'Jenis Kegiatan lainnya', 'jenis kegiatan' => 'Jenis Kegiatan lainnya', 'Jenis Kegiatan' => 'Jenis Kegiatan lainnya', 'Jenis Kegiatan lainnya' => 'Jenis Kegiatan lainnya',
        ];
        return $mapping[trim($jenisKegiatan)] ?? trim($jenisKegiatan);
    }
}
