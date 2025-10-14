# Export Data Kelompok - Struktur Baru

## Perubahan Struktur Export

Berdasarkan permintaan user, struktur export Excel telah diubah dari 4 sheet terpisah menjadi **1 sheet dengan 2 tabel** yang disusun vertikal.

## Struktur Baru

### Sheet: "Data Kelompok {Nama Kelompok}"

#### 1. Header Info Kelompok

```
DATA KELOMPOK: KELOMPOK 1
Shift: Shift 1
Jumlah Karyawan: 4
Tanggal Export: 2025-10-14 12:30:00
```

#### 2. Tabel 1: Input Laporan

| No  | Hari/Tanggal        | Nama     | Instansi | Alamat Tujuan | Dokumentasi |
| --- | ------------------- | -------- | -------- | ------------- | ----------- |
| 1   | Senin / 2025-10-14  | John Doe | PLN      | Jl. Contoh    | -           |
| 2   | Selasa / 2025-10-15 | Jane Doe | PLN      | Jl. Contoh 2  | -           |

#### 3. Tabel 2: Input Job Pekerjaan (3 baris setelah tabel laporan)

| No  | Tanggal    | Hari   | Perbaikan KWH   | Pemeliharaan Pengkabelan | Pengecekan Gardu | Penanganan Gangguan | Lokasi | Waktu (jam) | Created At          |
| --- | ---------- | ------ | --------------- | ------------------------ | ---------------- | ------------------- | ------ | ----------- | ------------------- |
| 1   | 2025-10-14 | Senin  | Ganti KWH rusak | Perbaikan kabel          | Cek tegangan     | Atasi gangguan      | RT 01  | 2           | 2025-10-14 12:30:00 |
| 2   | 2025-10-15 | Selasa | Service KWH     | Maintenance kabel        | Test gardu       | Handle masalah      | RT 02  | 3           | 2025-10-15 08:15:00 |

## Fitur Baru

### 1. Styling yang Lebih Baik

-   **Header utama** dengan background orange dan font bold size 14
-   **Judul tabel** dengan background biru/ungu dan font bold size 12
-   **Header kolom** dengan background abu-abu dan font bold
-   **Border** untuk semua tabel
-   **Auto-sizing** untuk semua kolom

### 2. Format Data yang Lebih Rapi

-   **Nomor urut** (No) untuk setiap tabel
-   **Hari/Tanggal** digabung dalam satu kolom untuk laporan
-   **Tanggal dan Hari** terpisah untuk job pekerjaan
-   **Waktu** dalam format jam untuk job pekerjaan

### 3. Layout Vertikal

-   Tabel laporan di atas
-   Tabel job pekerjaan di bawah dengan jarak 3 baris
-   Semua dalam 1 sheet untuk kemudahan viewing

## Kode Perbaikan

### Method `exportKelompokDataToExcel()` - Diperbaiki:

```php
private function exportKelompokDataToExcel($spreadsheet, $kelompok)
{
    // Sheet 1: Data Kelompok Lengkap
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Kelompok ' . $kelompok->nama_kelompok);

    // Header Info Kelompok
    $sheet->setCellValue('A1', 'DATA KELOMPOK: ' . strtoupper($kelompok->nama_kelompok));
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('F59E0B');

    // Tabel 1: Input Laporan
    $startRowLaporan = 6;
    $sheet->setCellValue('A' . $startRowLaporan, 'TABEL 1: INPUT LAPORAN');

    $laporanHeaders = ['No', 'Hari/Tanggal', 'Nama', 'Instansi', 'Alamat Tujuan', 'Dokumentasi'];
    // ... data laporan ...

    // Tabel 2: Input Job Pekerjaan
    $startRowJob = $row + 3;
    $sheet->setCellValue('A' . $startRowJob, 'TABEL 2: INPUT JOB PEKERJAAN');

    $jobHeaders = ['No', 'Tanggal', 'Hari', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Waktu (jam)', 'Created At'];
    // ... data job pekerjaan ...

    // Auto size dan border
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $this->setTableBorders($sheet, $range);
}
```

### Method Baru `setTableBorders()`:

```php
private function setTableBorders($sheet, $range)
{
    $sheet->getStyle($range)->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
}
```

## Keuntungan Struktur Baru

1. **Lebih Mudah Dibaca**: Semua data dalam 1 sheet
2. **Format yang Konsisten**: Sesuai dengan permintaan user
3. **Styling yang Profesional**: Border, warna, dan font yang rapi
4. **Data Lengkap**: Semua informasi penting tersedia
5. **Auto-sizing**: Kolom menyesuaikan dengan konten

## Testing

### Test Export:

```bash
php artisan tinker --execute="
\$controller = new App\Http\Controllers\ExportController();
\$user = App\Models\User::where('role', 'karyawan')->where('kelompok_id', '!=', null)->first();
if(\$user) {
    Auth::login(\$user);
    \$response = \$controller->exportKelompokData();
    echo 'New export structure successful';
} else {
    echo 'No karyawan with kelompok found';
}"
```

## Status

✅ **COMPLETED** - Struktur export Excel telah diubah sesuai permintaan user.
✅ **TESTED** - Export berfungsi dengan baik dengan struktur baru.
✅ **STYLED** - Styling dan formatting telah diperbaiki untuk tampilan yang lebih profesional.

## Catatan

-   File Excel sekarang berisi 1 sheet dengan 2 tabel vertikal
-   Format kolom sesuai dengan permintaan user
-   Styling profesional dengan border dan warna
-   Auto-sizing untuk kemudahan membaca
-   Nomor urut untuk setiap tabel
