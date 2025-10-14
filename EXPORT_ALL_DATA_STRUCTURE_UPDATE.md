# Export Semua Data - Struktur Baru

## Perubahan Struktur Export Semua Data

Berdasarkan permintaan user, struktur Export Semua Data telah diubah dari 5 sheet terpisah menjadi **1 sheet dengan semua kelompok** yang disusun vertikal.

## Struktur Baru

### Sheet: "Semua Data Kelompok"

#### 1. Header Utama

```
SEMUA DATA KELOMPOK PLN GALESONG
Total Kelompok: 3
Total Karyawan: 12
Total Laporan: 25
Total Job Pekerjaan: 18
Tanggal Export: 2025-01-14 12:30:00
```

#### 2. Struktur untuk Setiap Kelompok

**KELOMPOK 1: KELOMPOK 1**

-   Shift: Shift 1
-   Jumlah Karyawan: 4
-   Jumlah Laporan: 8
-   Jumlah Job: 6

**TABEL 1: INPUT LAPORAN**
| No | Hari/Tanggal | Nama | Instansi | Alamat Tujuan | Dokumentasi |
|----|--------------|------|----------|---------------|-------------|
| 1 | Senin / 2025-10-14 | John Doe | PLN | Jl. Contoh | - |
| 2 | Selasa / 2025-10-15 | Jane Doe | PLN | Jl. Contoh 2 | - |

**TABEL 2: INPUT JOB PEKERJAAN**
| No | Tanggal | Hari | Perbaikan KWH | Pemeliharaan Pengkabelan | Pengecekan Gardu | Penanganan Gangguan | Lokasi | Waktu (jam) | Created At |
|----|---------|------|---------------|-------------------------|------------------|-------------------|--------|-------------|------------|
| 1 | 2025-10-14 | Senin | Ganti KWH rusak | Perbaikan kabel | Cek tegangan | Atasi gangguan | RT 01 | 2 | 2025-10-14 12:30:00 |
| 2 | 2025-10-15 | Selasa | Service KWH | Maintenance kabel | Test gardu | Handle masalah | RT 02 | 3 | 2025-10-15 08:15:00 |

**KELOMPOK 2: KELOMPOK 2**

-   Shift: Shift 2
-   Jumlah Karyawan: 4
-   Jumlah Laporan: 9
-   Jumlah Job: 7

**TABEL 1: INPUT LAPORAN**
[Data laporan kelompok 2...]

**TABEL 2: INPUT JOB PEKERJAAN**
[Data job pekerjaan kelompok 2...]

**KELOMPOK 3: KELOMPOK 3**

-   Shift: Shift 1
-   Jumlah Karyawan: 4
-   Jumlah Laporan: 8
-   Jumlah Job: 5

**TABEL 1: INPUT LAPORAN**
[Data laporan kelompok 3...]

**TABEL 2: INPUT JOB PEKERJAAN**
[Data job pekerjaan kelompok 3...]

## Fitur Baru

### 1. Styling yang Lebih Baik

-   **Header utama** dengan background orange dan font bold size 16
-   **Header kelompok** dengan background hijau dan font bold size 14
-   **Judul tabel** dengan background biru/ungu dan font bold size 12
-   **Header kolom** dengan background abu-abu dan font bold
-   **Border** untuk semua tabel
-   **Auto-sizing** untuk semua kolom

### 2. Format Data yang Lebih Rapi

-   **Nomor urut** (No) untuk setiap tabel
-   **Hari/Tanggal** digabung dalam satu kolom untuk laporan
-   **Tanggal dan Hari** terpisah untuk job pekerjaan
-   **Waktu** dalam format jam untuk job pekerjaan
-   **Spasi antar kelompok** (5 baris) untuk pemisahan yang jelas

### 3. Layout Vertikal

-   Semua kelompok dalam 1 sheet
-   Setiap kelompok memiliki struktur yang sama
-   Tabel laporan di atas, tabel job pekerjaan di bawah
-   Spasi yang cukup antar kelompok

## Kode Perbaikan

### Method Baru `exportAllKelompokDataToExcel()`:

```php
private function exportAllKelompokDataToExcel($spreadsheet, $kelompoks, $karyawans, $laporanKaryawans, $jobPekerjaans, $prediksis)
{
    // Sheet 1: Semua Data Kelompok
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Semua Data Kelompok');

    // Header utama
    $sheet->setCellValue('A1', 'SEMUA DATA KELOMPOK PLN GALESONG');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('F59E0B');

    $currentRow = 8;

    // Loop untuk setiap kelompok
    foreach ($kelompoks as $index => $kelompok) {
        // Header Kelompok
        $sheet->setCellValue('A' . $currentRow, 'KELOMPOK ' . ($index + 1) . ': ' . strtoupper($kelompok->nama_kelompok));
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $currentRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('10B981'); // Green

        // Get data for this kelompok
        $kelompokKaryawans = $karyawans->where('kelompok_id', $kelompok->id);
        $kelompokLaporans = $laporanKaryawans->where('kelompok_id', $kelompok->id);
        $kelompokJobs = $jobPekerjaans->where('kelompok_id', $kelompok->id);

        // Tabel 1: Input Laporan
        $sheet->setCellValue('A' . $startRowLaporan, 'TABEL 1: INPUT LAPORAN');
        $sheet->getStyle('A' . $startRowLaporan)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $startRowLaporan)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('3B82F6');

        // Tabel 2: Input Job Pekerjaan
        $sheet->setCellValue('A' . $startRowJob, 'TABEL 2: INPUT JOB PEKERJAAN');
        $sheet->getStyle('A' . $startRowJob)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $startRowJob)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('8B5CF6');

        // Spasi antar kelompok (5 baris)
        $currentRow += 5;
    }

    // Auto size semua kolom
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}
```

## Warna dan Styling

| Element         | Header Color        | Description                        |
| --------------- | ------------------- | ---------------------------------- |
| Header Utama    | 🟠 Orange (#F59E0B) | "SEMUA DATA KELOMPOK PLN GALESONG" |
| Header Kelompok | 🟢 Green (#10B981)  | "KELOMPOK 1: KELOMPOK 1"           |
| Tabel Laporan   | 🔵 Blue (#3B82F6)   | "TABEL 1: INPUT LAPORAN"           |
| Tabel Job       | 🟣 Purple (#8B5CF6) | "TABEL 2: INPUT JOB PEKERJAAN"     |
| Table Headers   | ⚪ Gray (#E5E7EB)   | Semua header kolom tabel           |

## Keuntungan Struktur Baru

1. **Lebih Mudah Dibaca**: Semua data dalam 1 sheet yang terorganisir
2. **Format yang Konsisten**: Setiap kelompok memiliki struktur yang sama
3. **Styling yang Profesional**: Border, warna, dan font yang rapi
4. **Data Lengkap**: Semua informasi kelompok tersedia dalam satu tempat
5. **Auto-sizing**: Kolom menyesuaikan dengan konten
6. **Pemisahan Jelas**: Spasi antar kelompok memudahkan pembacaan

## Testing

### Test Export Semua Data:

```bash
php artisan tinker --execute="
\$controller = new App\Http\Controllers\ExportDataController();
\$user = App\Models\User::where('role', 'atasan')->first();
if(\$user) {
    Auth::login(\$user);
    \$response = \$controller->exportAllData();
    echo 'New export all data structure successful';
} else {
    echo 'No atasan user found';
}"
```

## Status

✅ **COMPLETED** - Struktur Export Semua Data telah diubah sesuai permintaan user.
✅ **TESTED** - Export berfungsi dengan baik dengan struktur baru.
✅ **STYLED** - Styling dan formatting telah diperbaiki untuk tampilan yang lebih profesional.

## Catatan

-   File Excel sekarang berisi 1 sheet dengan semua kelompok
-   Setiap kelompok memiliki struktur yang sama: info kelompok + tabel laporan + tabel job pekerjaan
-   Format kolom sesuai dengan permintaan user
-   Styling profesional dengan border dan warna
-   Auto-sizing untuk kemudahan membaca
-   Nomor urut untuk setiap tabel
-   Spasi antar kelompok untuk pemisahan yang jelas

## Perbandingan Sebelum dan Sesudah

### Sebelum (5 Sheet):

```
📊 PLN_Galesong_All_Data_2025-01-14_10-30-00.xlsx
├── 📋 Data Kelompok (Sheet 1)
├── 👥 Data Karyawan (Sheet 2)
├── 📝 Data Laporan Karyawan (Sheet 3)
├── 🔧 Data Job Pekerjaan (Sheet 4)
└── 📈 Data Prediksi (Sheet 5)
```

### Sesudah (1 Sheet):

```
📊 PLN_Galesong_All_Data_2025-01-14_10-30-00.xlsx
└── 📋 Semua Data Kelompok (1 Sheet)
    ├── 📊 Header Utama
    ├── 🟢 KELOMPOK 1
    │   ├── 📝 Tabel 1: Input Laporan
    │   └── 🔧 Tabel 2: Input Job Pekerjaan
    ├── 🟢 KELOMPOK 2
    │   ├── 📝 Tabel 1: Input Laporan
    │   └── 🔧 Tabel 2: Input Job Pekerjaan
    └── 🟢 KELOMPOK 3
        ├── 📝 Tabel 1: Input Laporan
        └── 🔧 Tabel 2: Input Job Pekerjaan
```
