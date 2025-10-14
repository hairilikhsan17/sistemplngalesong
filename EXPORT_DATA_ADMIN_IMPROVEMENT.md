# Export Data Admin - Perbaikan Styling Excel

## Perubahan yang Dilakukan

Berdasarkan permintaan user, halaman Export Data admin telah diperbaiki untuk menghasilkan file Excel yang lebih rapi dan tersusun dengan baik.

## Masalah Sebelumnya

1. **Format CSV**: Export menggunakan format CSV yang tidak rapi di Excel
2. **Tidak Ada Styling**: File tidak memiliki styling, border, atau formatting
3. **Field Error**: Masih menggunakan field `bulan_data` yang sudah tidak ada
4. **Tampilan Tidak Profesional**: Data terlihat berantakan tanpa struktur yang jelas

## Perbaikan yang Dilakukan

### 1. Migrasi dari CSV ke Excel

**Sebelum:**

```php
// Format CSV dengan BOM
$content = "\xEF\xBB\xBF"; // UTF-8 BOM
$content .= "=== DATA KELOMPOK ===\n";
$content .= "ID,Nama Kelompok,Shift,Jumlah Karyawan,Jumlah Laporan,Created At\n";
```

**Sesudah:**

```php
// Format Excel dengan PhpSpreadsheet
$spreadsheet = new Spreadsheet();
$this->exportKelompokSheet($spreadsheet, $kelompoks);
return $this->downloadExcel($spreadsheet, $filename);
```

### 2. Styling Profesional

#### Export Semua Data (5 Sheet):

1. **Data Kelompok** - Header orange, border, auto-sizing
2. **Data Karyawan** - Header hijau, border, auto-sizing
3. **Data Laporan Karyawan** - Header biru, border, auto-sizing
4. **Data Job Pekerjaan** - Header ungu, border, auto-sizing
5. **Data Prediksi** - Header orange, border, auto-sizing

#### Export per Kelompok (1 Sheet):

-   **Header utama** dengan background orange dan font bold size 14
-   **Info kelompok** dengan detail lengkap
-   **Tabel 1: Input Laporan** dengan header biru dan border
-   **Tabel 2: Input Job Pekerjaan** dengan header ungu dan border

### 3. Fitur Styling yang Ditambahkan

#### Header Styling:

```php
// Header utama
$sheet->setCellValue('A1', 'DATA KELOMPOK');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setRGB('F59E0B'); // Orange
```

#### Tabel Header Styling:

```php
// Header tabel
$sheet->getStyle('A3:G3')->getFont()->setBold(true);
$sheet->getStyle('A3:G3')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E5E7EB'); // Gray
```

#### Border dan Auto-sizing:

```php
// Border untuk semua tabel
$this->setTableBorders($sheet, 'A3:G' . ($row - 1));

// Auto size semua kolom
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
```

### 4. Perbaikan Field Database

**Sebelum:**

```php
$content .= "ID,Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan,Lokasi,Bulan Data,Tanggal,Waktu Penyelesaian,Created At\n";
// Menggunakan $job->bulan_data (field yang sudah tidak ada)
```

**Sesudah:**

```php
$headers = ['No', 'ID Job', 'Tanggal', 'Hari', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Waktu (jam)', 'Kelompok', 'Created At'];
// Menggunakan $job->hari (field yang benar)
```

## Struktur File Excel Baru

### Export Semua Data:

```
📊 PLN_Galesong_All_Data_2025-01-14_10-30-00.xlsx
├── 📋 Data Kelompok (Sheet 1)
├── 👥 Data Karyawan (Sheet 2)
├── 📝 Data Laporan Karyawan (Sheet 3)
├── 🔧 Data Job Pekerjaan (Sheet 4)
└── 📈 Data Prediksi (Sheet 5)
```

### Export per Kelompok:

```
📊 PLN_Galesong_Kelompok_1_2025-01-14_10-30-00.xlsx
└── 📋 Data Kelompok Kelompok 1 (1 Sheet)
    ├── 📊 Header Info Kelompok
    ├── 📝 Tabel 1: Input Laporan
    └── 🔧 Tabel 2: Input Job Pekerjaan
```

## Warna dan Styling

| Sheet/Table   | Header Color        | Description        |
| ------------- | ------------------- | ------------------ |
| Data Kelompok | 🟠 Orange (#F59E0B) | Header utama       |
| Data Karyawan | 🟢 Green (#10B981)  | Header utama       |
| Data Laporan  | 🔵 Blue (#3B82F6)   | Header utama       |
| Data Job      | 🟣 Purple (#8B5CF6) | Header utama       |
| Data Prediksi | 🟠 Orange (#F59E0B) | Header utama       |
| Table Headers | ⚪ Gray (#E5E7EB)   | Semua header tabel |

## Method Helper yang Ditambahkan

### 1. `exportKelompokSheet()`

-   Membuat sheet Data Kelompok dengan styling orange
-   Header: No, ID Kelompok, Nama Kelompok, Shift, Jumlah Karyawan, Jumlah Laporan, Created At

### 2. `exportKaryawanSheet()`

-   Membuat sheet Data Karyawan dengan styling hijau
-   Header: No, ID Karyawan, Nama, ID Kelompok, Nama Kelompok, Status, Created At

### 3. `exportLaporanKaryawanSheet()`

-   Membuat sheet Data Laporan Karyawan dengan styling biru
-   Header: No, ID Laporan, Hari, Tanggal, Nama, Kelompok, Instansi, Jabatan, Alamat Tujuan, Dokumentasi, Created At

### 4. `exportJobPekerjaanSheet()`

-   Membuat sheet Data Job Pekerjaan dengan styling ungu
-   Header: No, ID Job, Tanggal, Hari, Perbaikan KWH, Pemeliharaan Pengkabelan, Pengecekan Gardu, Penanganan Gangguan, Lokasi, Waktu (jam), Kelompok, Created At

### 5. `exportPrediksiSheet()`

-   Membuat sheet Data Prediksi dengan styling orange
-   Header: No, ID Prediksi, Jenis Prediksi, Bulan Prediksi, Hasil Prediksi, Kelompok, Created At

### 6. `exportKelompokDataToExcel()`

-   Membuat sheet tunggal untuk export per kelompok
-   Struktur vertikal dengan 2 tabel dalam 1 sheet
-   Styling yang konsisten dengan export karyawan

### 7. `setTableBorders()`

-   Menambahkan border tipis untuk semua tabel
-   Meningkatkan readability data

### 8. `downloadExcel()`

-   Helper untuk download file Excel
-   Mengatur header response yang benar

## Testing

### Test Export Semua Data:

```bash
php artisan tinker --execute="
\$controller = new App\Http\Controllers\ExportDataController();
\$user = App\Models\User::where('role', 'atasan')->first();
if(\$user) {
    Auth::login(\$user);
    \$response = \$controller->exportAllData();
    echo 'Export all data successful';
} else {
    echo 'No atasan user found';
}"
```

### Test Export per Kelompok:

```bash
php artisan tinker --execute="
\$controller = new App\Http\Controllers\ExportDataController();
\$user = App\Models\User::where('role', 'atasan')->first();
\$kelompok = App\Models\Kelompok::first();
if(\$user && \$kelompok) {
    Auth::login(\$user);
    \$request = new Illuminate\Http\Request();
    \$request->merge(['kelompok_id' => \$kelompok->id]);
    \$response = \$controller->exportByKelompok(\$request);
    echo 'Export per kelompok successful';
} else {
    echo 'No atasan user or kelompok found';
}"
```

## Keuntungan Perbaikan

1. **Tampilan Profesional**: File Excel dengan styling yang rapi dan konsisten
2. **Mudah Dibaca**: Border, warna, dan auto-sizing membuat data mudah dibaca
3. **Struktur Jelas**: Setiap sheet memiliki header yang jelas dan data yang terorganisir
4. **Format Standar**: Menggunakan format Excel (.xlsx) yang universal
5. **Data Lengkap**: Semua field database yang benar digunakan
6. **Responsive**: Auto-sizing kolom menyesuaikan dengan konten
7. **Konsisten**: Styling yang sama untuk semua export

## Status

✅ **COMPLETED** - Export Data admin telah diperbaiki dengan styling Excel yang rapi
✅ **TESTED** - Kedua jenis export (semua data dan per kelompok) berfungsi dengan baik
✅ **STYLED** - File Excel memiliki styling profesional dengan border, warna, dan auto-sizing
✅ **FIXED** - Field database yang salah telah diperbaiki

## Catatan

-   File Excel sekarang memiliki tampilan yang profesional dan mudah dibaca
-   Semua data tersusun rapi dengan border dan styling yang konsisten
-   Auto-sizing kolom memastikan semua data terlihat dengan baik
-   Format Excel (.xlsx) lebih universal dan mudah dibuka di berbagai aplikasi
