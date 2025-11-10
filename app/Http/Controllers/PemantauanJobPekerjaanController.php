<?php

namespace App\Http\Controllers;

use App\Models\JobPekerjaan;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PemantauanJobPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $kelompoks = Kelompok::with(['jobPekerjaan'])->orderBy('created_at', 'desc')->get();
        
        // Build query with filters
        $query = JobPekerjaan::with(['kelompok']);
        
        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok_id', $request->kelompok);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        
        $jobPekerjaans = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        // Calculate statistics with filters
        $totalJob = (clone $query)->count();
        $jobHariIni = (clone $query)->whereDate('tanggal', today())->count();
        $totalWaktu = (clone $query)->sum('waktu_penyelesaian');
        
        // Get groups that have submitted jobs (with filters)
        $kelompokQuery = Kelompok::query();
        if ($request->filled('kelompok')) {
            $kelompokQuery->where('id', $request->kelompok);
        }
        
        $kelompokDenganJob = (clone $kelompokQuery)->whereHas('jobPekerjaan', function($q) use ($request) {
            if ($request->filled('tanggal')) {
                $q->whereDate('tanggal', $request->tanggal);
            }
            if ($request->filled('hari')) {
                $q->where('hari', $request->hari);
            }
        })->get();
        
        $kelompokTanpaJob = (clone $kelompokQuery)->whereDoesntHave('jobPekerjaan', function($q) use ($request) {
            if ($request->filled('tanggal')) {
                $q->whereDate('tanggal', $request->tanggal);
            }
            if ($request->filled('hari')) {
                $q->where('hari', $request->hari);
            }
        })->get();
        
        $statistics = [
            'totalJob' => $totalJob,
            'jobHariIni' => $jobHariIni,
            'totalWaktu' => $totalWaktu,
            'kelompokDenganJob' => $kelompokDenganJob->count(),
            'kelompokTanpaJob' => $kelompokTanpaJob->count(),
            'totalKelompok' => Kelompok::count()
        ];
        
        return view('dashboard.atasan.pemantauan-job-pekerjaan', compact('kelompoks', 'jobPekerjaans', 'statistics', 'kelompokDenganJob', 'kelompokTanpaJob'));
    }

    public function getStatistics(Request $request)
    {
        try {
            $query = JobPekerjaan::query();
            
            // Apply same filters as index method
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->filled('kelompok')) {
                $query->where('kelompok_id', $request->kelompok);
            }

            if ($request->filled('hari')) {
                $query->where('hari', $request->hari);
            }
            
            $totalJob = $query->count();
            $jobHariIni = (clone $query)->whereDate('tanggal', today())->count();
            $totalWaktu = (clone $query)->sum('waktu_penyelesaian');
            
            // Get groups that have submitted jobs
            $kelompokQuery = Kelompok::query();
            if ($request->filled('kelompok')) {
                $kelompokQuery->where('id', $request->kelompok);
            }
            
            $kelompokDenganJob = (clone $kelompokQuery)->whereHas('jobPekerjaan', function($q) use ($request) {
                if ($request->filled('tanggal')) {
                    $q->whereDate('tanggal', $request->tanggal);
                }
                if ($request->filled('hari')) {
                    $q->where('hari', $request->hari);
                }
            })->count();
            
            $kelompokTanpaJob = (clone $kelompokQuery)->whereDoesntHave('jobPekerjaan', function($q) use ($request) {
                if ($request->filled('tanggal')) {
                    $q->whereDate('tanggal', $request->tanggal);
                }
                if ($request->filled('hari')) {
                    $q->where('hari', $request->hari);
                }
            })->count();
            
            return response()->json([
                'totalJob' => $totalJob,
                'jobHariIni' => $jobHariIni,
                'totalWaktu' => $totalWaktu,
                'kelompokDenganJob' => $kelompokDenganJob,
                'kelompokTanpaJob' => $kelompokTanpaJob,
                'totalKelompok' => $kelompokDenganJob + $kelompokTanpaJob
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat statistik'], 500);
        }
    }

    public function getJobData(Request $request)
    {
        try {
            $query = JobPekerjaan::with(['kelompok']);

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->filled('kelompok')) {
                $query->where('kelompok_id', $request->kelompok);
            }

            if ($request->filled('hari')) {
                $query->where('hari', $request->hari);
            }

            $jobPekerjaans = $query->orderBy('tanggal', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'data' => $jobPekerjaans->items(),
                'current_page' => $jobPekerjaans->currentPage(),
                'last_page' => $jobPekerjaans->lastPage(),
                'per_page' => $jobPekerjaans->perPage(),
                'total' => $jobPekerjaans->total(),
                'from' => $jobPekerjaans->firstItem(),
                'to' => $jobPekerjaans->lastItem()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data job pekerjaan'], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $query = JobPekerjaan::with(['kelompok']);

            // Apply filters
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            if ($request->filled('kelompok')) {
                $query->where('kelompok_id', $request->kelompok);
            }

            if ($request->filled('hari')) {
                $query->where('hari', $request->hari);
            }

            $jobPekerjaans = $query->orderBy('tanggal', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Job Pekerjaan');

            // Header
            $headers = [
                'No', 'Tanggal', 'Hari', 'Kelompok', 'Lokasi', 
                'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 
                'Pengecekan Gardu', 'Penanganan Gangguan', 
                'Waktu Penyelesaian (jam)', 'Dibuat Pada'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Style header
            $headerRange = 'A1:' . chr(ord('A') + count($headers) - 1) . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F59E0B']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

            // Data
            $row = 2;
            $no = 1;
            foreach ($jobPekerjaans as $job) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $job->tanggal->format('Y-m-d'));
                $sheet->setCellValue('C' . $row, $job->hari ?? '-');
                $sheet->setCellValue('D' . $row, $job->kelompok->nama_kelompok ?? '-');
                $sheet->setCellValue('E' . $row, $job->lokasi);
                $sheet->setCellValue('F' . $row, $job->perbaikan_kwh);
                $sheet->setCellValue('G' . $row, $job->pemeliharaan_pengkabelan);
                $sheet->setCellValue('H' . $row, $job->pengecekan_gardu);
                $sheet->setCellValue('I' . $row, $job->penanganan_gangguan);
                $sheet->setCellValue('J' . $row, $job->waktu_penyelesaian);
                $sheet->setCellValue('K' . $row, $job->created_at->format('Y-m-d H:i:s'));
                
                // Wrap text for long descriptions
                $sheet->getStyle('F' . $row . ':I' . $row)->getAlignment()->setWrapText(true);
                
                $row++;
                $no++;
            }

            // Auto size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set borders for all data
            $dataRange = 'A1:K' . ($row - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            // Set row height for wrapped cells
            for ($i = 2; $i < $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(-1);
            }

            $filename = 'Job_Pekerjaan_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            
            $response = response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $filename);
            
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal export data: ' . $e->getMessage()], 500);
        }
    }
}

