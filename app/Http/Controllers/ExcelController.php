<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use App\Models\JobPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelController extends Controller
{
    /**
     * Display Excel management page
     */
    public function index()
    {
        $kelompoks = Kelompok::with(['karyawan', 'laporanKaryawan'])->get();
        $excelFiles = $this->getExcelFiles();
        
        return view('dashboard.atasan.excel-management', compact('kelompoks', 'excelFiles'));
    }

    /**
     * Process Excel upload
     */
    public function store(Request $request)
    {
        try {
            // Log request for debugging
            \Log::info('Excel upload request received', [
                'has_file' => $request->hasFile('excel_file'),
                'jenis_data' => $request->input('jenis_data'),
                'bulan' => $request->input('bulan'),
                'tahun' => $request->input('tahun'),
                'all_input' => $request->all(),
                'files' => $request->files->all()
            ]);

            // Simple test response first
            if ($request->input('test') === 'true') {
                \Log::info('Test endpoint called');
                return response()->json([
                    'success' => true,
                    'message' => 'Test endpoint working',
                    'data' => [
                        'has_file' => $request->hasFile('excel_file'),
                        'jenis_data' => $request->input('jenis_data'),
                        'bulan' => $request->input('bulan'),
                        'tahun' => $request->input('tahun')
                    ]
                ]);
            }
            
            \Log::info('Processing real upload');

            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
                'bulan' => 'required|string',
                'tahun' => 'required|integer|min:2020|max:2030',
                'jenis_data' => 'required|in:laporan_karyawan,job_pekerjaan'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Excel upload validation failed', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Excel upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }

        try {
            $file = $request->file('excel_file');
            $fileName = $request->jenis_data . '_' . $request->bulan . '_' . $request->tahun . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            \Log::info('Storing file', [
                'original_name' => $file->getClientOriginalName(),
                'new_name' => $fileName,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);
            
            // Store file
            $filePath = $file->storeAs('excel-uploads', $fileName, 'public');
            
            \Log::info('File stored', [
                'file_path' => $filePath,
                'full_path' => storage_path('app/public/' . $filePath),
                'file_exists' => file_exists(storage_path('app/public/' . $filePath))
            ]);
            
            // Process Excel data
            try {
                $result = $this->processExcelFile($filePath, $request->jenis_data, $request->bulan, $request->tahun);
                
                \Log::info('Excel processing result', ['result' => $result]);
                
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data Excel berhasil diupload dan diproses!',
                        'data' => $result['data']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memproses file Excel: ' . $result['error']
                    ], 400);
                }
            } catch (\Exception $e) {
                \Log::error('Excel processing error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses Excel: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate new Excel file
     */
    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer|min:2020|max:2030',
            'jenis_data' => 'required|in:laporan_karyawan,job_pekerjaan',
            'kelompok_id' => 'nullable|exists:kelompoks,id'
        ]);

        try {
            $fileName = $request->jenis_data . '_template_' . $request->bulan . '_' . $request->tahun . '_' . time() . '.xlsx';
            $filePath = storage_path('app/public/excel-templates/' . $fileName);
            
            // Ensure directory exists
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            
            $result = $this->createExcelTemplate($filePath, $request->jenis_data, $request->bulan, $request->tahun, $request->kelompok_id);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'File Excel template berhasil dibuat!',
                    'file_url' => $result['file_url'],
                    'file_name' => $fileName
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat file Excel: ' . $result['error']
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Excel file
     */
    public function download($fileName)
    {
        // Check in uploads directory first
        $uploadPath = storage_path('app/public/excel-uploads/' . $fileName);
        $templatePath = storage_path('app/public/excel-templates/' . $fileName);
        
        if (file_exists($uploadPath)) {
            return response()->download($uploadPath);
        } elseif (file_exists($templatePath)) {
            return response()->download($templatePath);
        }
        
        return response()->json(['error' => 'File tidak ditemukan'], 404);
    }

    /**
     * Delete Excel file
     */
    public function destroy($fileName)
    {
        try {
            // Check in uploads directory first
            $uploadPath = storage_path('app/public/excel-uploads/' . $fileName);
            $templatePath = storage_path('app/public/excel-templates/' . $fileName);
            
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File Excel berhasil dihapus!'
                ]);
            } elseif (file_exists($templatePath)) {
                unlink($templatePath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File Excel berhasil dihapus!'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan!'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of Excel files
     */
    private function getExcelFiles()
    {
        $files = [];
        $templatePath = storage_path('app/public/excel-templates');
        $uploadPath = storage_path('app/public/excel-uploads');
        
        // Get template files
        if (is_dir($templatePath)) {
            foreach (glob($templatePath . '/*.xlsx') as $file) {
                $files[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => filesize($file),
                    'created' => date('Y-m-d H:i:s', filemtime($file)),
                    'type' => 'template'
                ];
            }
        }
        
        // Get uploaded files
        if (is_dir($uploadPath)) {
            foreach (glob($uploadPath . '/*.xlsx') as $file) {
                $files[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => filesize($file),
                    'created' => date('Y-m-d H:i:s', filemtime($file)),
                    'type' => 'upload'
                ];
            }
        }
        
        // Sort by creation date
        usort($files, function($a, $b) {
            return strtotime($b['created']) - strtotime($a['created']);
        });
        
        return $files;
    }

    /**
     * Process uploaded Excel file
     */
    private function processExcelFile($filePath, $jenisData, $bulan, $tahun)
    {
        try {
            $fullPath = storage_path('app/public/' . $filePath);
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $processedData = [];
            $errors = [];
            
            // Find the actual data header row
            $headerRow = $this->findHeaderRow($worksheet, $highestRow);
            
            if (!$headerRow) {
                return [
                    'success' => false,
                    'error' => 'Tidak dapat menemukan baris header data. Pastikan file Excel memiliki format yang benar.'
                ];
            }
            
            // Keywords to skip (metadata/header rows)
            $skipKeywords = [
                'shift:', 'jumlah', 'tanggal export:', 'tabel', 'no', 
                'nama karyawan', 'kelompok', 'tanggal', 'waktu', 'keterangan',
                'perbaikan', 'pemeliharaan', 'pengecekan', 'penanganan', 'lokasi'
            ];
            
            // Start from row after header
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                $cellA = trim((string)$worksheet->getCell('A' . $row)->getValue());
                $cellB = trim((string)$worksheet->getCell('B' . $row)->getValue());
                
                // Skip empty rows
                if (empty($cellA) && empty($cellB)) {
                    continue;
                }
                
                // Get cell C early for validation
                $cellC = trim((string)$worksheet->getCell('C' . $row)->getValue());
                
                // Skip if cell A is numeric only (likely a row number/No column)
                // This handles cases where column A contains just row numbers like "1", "2", "3"
                if (preg_match('/^\d+$/', $cellA)) {
                    $numValue = (int)$cellA;
                    // Skip if it's a small number (1-10000) which is definitely a row number
                    if ($numValue > 0 && $numValue <= 10000) {
                        // Get other cells to check if this is really a data row
                        $cellD = trim((string)$worksheet->getCell('D' . $row)->getValue());
                        $cellE = trim((string)$worksheet->getCell('E' . $row)->getValue());
                        
                        // Very aggressive skip: If cell C (tanggal) is empty, definitely skip
                        // This means the row number column doesn't have corresponding data
                        if (empty($cellC)) {
                            continue; // Skip - no date means this is just row number, no data
                        }
                        
                        // If cell B is empty, numeric, or very short (< 4 chars) - it's a row number
                        if (empty($cellB) || is_numeric($cellB) || strlen(trim($cellB)) < 4) {
                            continue; // Skip - this is definitely a row number, not data
                        }
                        
                        // If cell B exists but is too short and cell D is empty, skip
                        if (strlen(trim($cellB)) < 4 && empty($cellD)) {
                            continue; // Skip - too short to be a valid name and missing required data
                        }
                    }
                }
                
                // Skip if looks like header/metadata row
                $cellALower = strtolower($cellA);
                foreach ($skipKeywords as $keyword) {
                    if (strpos($cellALower, $keyword) !== false) {
                        continue 2; // Skip this row, continue to next row
                    }
                }
                
                // Skip if cell A contains colon (likely metadata like "Shift: Shift 2")
                if (strpos($cellA, ':') !== false && strlen($cellA) < 50) {
                    continue;
                }
                
                // Skip if cell A looks like "TABEL" or "INPUT"
                if (stripos($cellA, 'TABEL') !== false || stripos($cellA, 'INPUT') !== false) {
                    continue;
                }
                
                // Skip if cell A is only digits and cell B is empty or looks like header
                if (preg_match('/^\d+$/', $cellA) && (empty($cellB) || strlen($cellB) < 3)) {
                    continue;
                }
                
                // Final check: If cell A is numeric and cell C is empty, definitely skip
                // This catches any row numbers that might have slipped through
                if (preg_match('/^\d+$/', $cellA)) {
                    $numValue = (int)$cellA;
                    if ($numValue > 0 && $numValue <= 10000 && empty($cellC)) {
                        continue; // Skip - this is definitely a row number column
                    }
                }
                
                $data = [
                    'nama_karyawan' => $cellA,
                    'kelompok' => $cellB,
                    'tanggal' => $cellC,
                    'waktu_penyelesaian' => $worksheet->getCell('D' . $row)->getValue(),
                    'keterangan' => $worksheet->getCell('E' . $row)->getValue(),
                ];
                
                // Validate and process data
                $result = $this->validateAndSaveData($data, $jenisData, $bulan, $tahun);
                
                if ($result['success']) {
                    $processedData[] = $result['data'];
                } else {
                    $errors[] = "Baris $row: " . $result['error'];
                }
            }
            
            return [
                'success' => true,
                'data' => [
                    'processed' => count($processedData),
                    'errors' => count($errors),
                    'error_details' => $errors
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Find the header row that contains actual data columns
     */
    private function findHeaderRow($worksheet, $highestRow)
    {
        $headerKeywords = ['nama', 'kelompok', 'tanggal', 'waktu', 'keterangan', 'no'];
        
        // Search from row 1 to row 20 for header
        for ($row = 1; $row <= min(20, $highestRow); $row++) {
            $cellA = trim(strtolower((string)$worksheet->getCell('A' . $row)->getValue()));
            $cellB = trim(strtolower((string)$worksheet->getCell('B' . $row)->getValue()));
            
            // Check if this row looks like a header row
            $matches = 0;
            foreach ($headerKeywords as $keyword) {
                if (strpos($cellA, $keyword) !== false || strpos($cellB, $keyword) !== false) {
                    $matches++;
                }
            }
            
            // If we find 2+ header keywords, this is likely the header row
            if ($matches >= 2 && ($cellA === 'no' || $cellA === 'nama' || strpos($cellA, 'nama') !== false)) {
                return $row;
            }
        }
        
        // Fallback: return row 1 if no header found
        return 1;
    }

    /**
     * Validate and save data from Excel
     */
    private function validateAndSaveData($data, $jenisData, $bulan, $tahun)
    {
        try {
            // Validate nama karyawan is not just a number
            $namaKaryawan = trim($data['nama_karyawan']);
            if (empty($namaKaryawan) || preg_match('/^\d+$/', $namaKaryawan)) {
                return ['success' => false, 'error' => 'Baris ini sepertinya bukan data karyawan (hanya berisi angka)'];
            }
            
            // Validate nama karyawan has at least 3 characters
            if (strlen($namaKaryawan) < 3) {
                return ['success' => false, 'error' => 'Nama karyawan terlalu pendek: ' . $namaKaryawan];
            }
            
            // Find karyawan by name
            $karyawan = Karyawan::where('nama', $namaKaryawan)->first();
            if (!$karyawan) {
                return ['success' => false, 'error' => 'Karyawan tidak ditemukan: ' . $namaKaryawan];
            }
            
            // Find kelompok
            $kelompok = Kelompok::where('nama_kelompok', $data['kelompok'])->first();
            if (!$kelompok) {
                return ['success' => false, 'error' => 'Kelompok tidak ditemukan: ' . $data['kelompok']];
            }
            
            // Parse date
            $tanggal = Carbon::parse($data['tanggal']);
            $tanggal->month = $this->getMonthNumber($bulan);
            $tanggal->year = $tahun;
            
            if ($jenisData === 'laporan_karyawan') {
                LaporanKaryawan::create([
                    'nama' => $data['nama_karyawan'],
                    'tanggal' => $tanggal,
                    'alamat_tujuan' => $data['keterangan'],
                    'kelompok_id' => $kelompok->id,
                    'instansi' => 'PLN Galesong',
                    'jabatan' => 'Karyawan',
                    'hari' => $tanggal->format('l'),
                    'dokumentasi' => 'Upload Excel'
                ]);
            } else {
                JobPekerjaan::create([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $tanggal,
                    'waktu_penyelesaian' => $data['waktu_penyelesaian'],
                    'keterangan' => $data['keterangan'],
                    'status' => 'selesai'
                ]);
            }
            
            return [
                'success' => true,
                'data' => [
                    'karyawan' => $karyawan->nama,
                    'kelompok' => $kelompok->nama_kelompok,
                    'tanggal' => $tanggal->format('Y-m-d')
                ]
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create Excel template
     */
    private function createExcelTemplate($filePath, $jenisData, $bulan, $tahun, $kelompokId = null)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title
            $title = $jenisData === 'laporan_karyawan' ? 'Laporan Karyawan' : 'Job Pekerjaan';
            $sheet->setTitle($title . ' ' . $bulan . ' ' . $tahun);
            
            // Set headers
            $headers = [
                'A1' => 'Nama Karyawan',
                'B1' => 'Kelompok',
                'C1' => 'Tanggal',
                'D1' => 'Waktu Penyelesaian (Hari)',
                'E1' => 'Keterangan/Deskripsi'
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }
            
            // Style headers
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            
            $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
            
            // Get karyawan data for dropdown
            $karyawans = $kelompokId ? 
                Karyawan::where('kelompok_id', $kelompokId)->get() : 
                Karyawan::with('kelompok')->get();
            
            $kelompoks = Kelompok::all();
            
            // Add sample data and dropdowns
            $row = 2;
            foreach ($karyawans->take(10) as $karyawan) {
                $sheet->setCellValue('A' . $row, $karyawan->nama);
                $sheet->setCellValue('B' . $row, $karyawan->kelompok->nama_kelompok ?? '');
                $sheet->setCellValue('C' . $row, date('Y-m-d'));
                $sheet->setCellValue('D' . $row, 1);
                $sheet->setCellValue('E' . $row, 'Deskripsi pekerjaan...');
                $row++;
            }
            
            // Add data validation for kelompok column
            $kelompokNames = $kelompoks->pluck('nama_kelompok')->toArray();
            $validation = $sheet->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setFormula1('"' . implode(',', $kelompokNames) . '"');
            $validation->setShowDropDown(true);
            $sheet->setDataValidation('B2:B1000', $validation);
            
            // Auto-fit columns
            foreach (range('A', 'E') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Save file
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            
            $fileUrl = asset('storage/excel-templates/' . basename($filePath));
            
            return [
                'success' => true,
                'file_url' => $fileUrl
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get list of Excel files (API endpoint)
     */
    public function getFiles()
    {
        try {
            $files = $this->getExcelFiles();
            \Log::info('Excel files retrieved', [
                'files_count' => count($files),
                'files' => $files
            ]);
            return response()->json(['files' => $files]);
        } catch (\Exception $e) {
            \Log::error('Error getting Excel files', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get month number from month name
     */
    private function getMonthNumber($monthName)
    {
        $months = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];
        
        return $months[$monthName] ?? 1;
    }
}
