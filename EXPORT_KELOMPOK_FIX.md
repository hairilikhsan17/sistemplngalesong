# Export Data Kelompok Fix

## Masalah yang Ditemukan

Menu "Export Data Kelompok" di sidebar untuk karyawan tidak berfungsi dengan baik. Setelah investigasi, ditemukan masalah dengan field `bulan_data` yang sudah tidak ada di tabel `job_pekerjaan`.

## Masalah Utama

1. **Field Tidak Ada**: Controller `ExportController` masih menggunakan field `bulan_data` yang sudah di-drop dari tabel `job_pekerjaan`
2. **Migration Update**: Field `bulan_data` sudah diganti dengan field `hari` berdasarkan migration `2025_10_13_153955_update_job_pekerjaan_table_add_hari_column.php`

## Perbaikan yang Dilakukan

### 1. Update ExportController

**File**: `app/Http/Controllers/ExportController.php`

#### Perbaikan di method `exportJobPekerjaan()`:

```php
// SEBELUM (SALAH):
$headers = ['ID', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Bulan Data', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Kelompok', 'Created At'];
$sheet->setCellValue('G' . $row, $job->bulan_data);

// SESUDAH (BENAR):
$headers = ['ID', 'Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Hari', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Kelompok', 'Created At'];
$sheet->setCellValue('G' . $row, $job->hari);
```

#### Perbaikan di method `exportKelompokDataToExcel()`:

```php
// SEBELUM (SALAH):
$headers = ['Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Bulan Data', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Created At'];
$sheet4->setCellValue('F' . $row, $job->bulan_data);

// SESUDAH (BENAR):
$headers = ['Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Lokasi', 'Hari', 'Tanggal', 'Waktu Penyelesaian (Jam)', 'Created At'];
$sheet4->setCellValue('F' . $row, $job->hari);
```

## Struktur Export Data Kelompok

Fungsi export akan menghasilkan file Excel dengan 4 sheet:

### Sheet 1: Info Kelompok

-   Nama Kelompok
-   Shift
-   Jumlah Karyawan
-   Created At

### Sheet 2: Karyawan

-   Nama
-   Created At

### Sheet 3: Laporan Kelompok

-   Hari
-   Tanggal
-   Nama
-   Instansi
-   Jabatan
-   Alamat Tujuan
-   Dokumentasi
-   Created At

### Sheet 4: Job Pekerjaan

-   Perbaikan KWH
-   Pemeliharaan Pengkabelan
-   Pengecekan Gardu
-   Penanganan Gangguan
-   Lokasi
-   Hari
-   Tanggal
-   Waktu Penyelesaian (Jam)
-   Created At

## Cara Menggunakan

### Untuk Karyawan:

1. Login sebagai karyawan
2. Klik menu "Export Data Kelompok" di sidebar
3. File Excel akan otomatis ter-download dengan nama: `PLN_Galesong_{Nama_Kelompok}_Data_{Timestamp}.xlsx`

### Route yang Digunakan:

```
GET /api/export/my-kelompok
```

### Controller Method:

```php
public function exportKelompokData()
{
    $user = Auth::user();

    if (!$user->isKaryawan() || !$user->kelompok_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $kelompok = $user->kelompok;
    $spreadsheet = new Spreadsheet();

    // Export data kelompok karyawan
    $this->exportKelompokDataToExcel($spreadsheet, $kelompok);

    $filename = 'PLN_Galesong_' . str_replace(' ', '_', $kelompok->nama_kelompok) . '_Data_' . date('Y-m-d_H-i-s') . '.xlsx';

    return $this->downloadExcel($spreadsheet, $filename);
}
```

## Testing

### 1. Test Controller

```bash
php artisan tinker --execute="
\$controller = new App\Http\Controllers\ExportController();
\$user = App\Models\User::where('role', 'karyawan')->where('kelompok_id', '!=', null)->first();
if(\$user) {
    Auth::login(\$user);
    try {
        \$response = \$controller->exportKelompokData();
        echo 'Export successful, response type: ' . get_class(\$response);
    } catch(Exception \$e) {
        echo 'Error: ' . \$e->getMessage();
    }
} else {
    echo 'No karyawan with kelompok found';
}"
```

### 2. Test Route

Route sudah terdaftar dengan benar:

```
GET|HEAD  api/export/my-kelompok ....... ExportController@exportKelompokData
```

### 3. Test JavaScript Function

```javascript
function exportKelompokData() {
    // Export kelompok data for karyawan
    window.open("/api/export/my-kelompok", "_blank");
}
```

## Dependencies

-   **PhpSpreadsheet**: `phpoffice/phpspreadsheet` (v5.1.0) - untuk generate Excel file
-   **Laravel**: Framework untuk handling request/response
-   **Authentication**: Middleware untuk memastikan hanya karyawan yang bisa export

## Perbaikan Tambahan - Styling dan Visibility

### Masalah: Hanya Sheet "Info Kelompok" yang Terlihat

Setelah testing, ditemukan bahwa meskipun semua 4 sheet dibuat, hanya sheet pertama yang terlihat dengan jelas di Excel. Hal ini disebabkan oleh kurangnya styling dan auto-sizing pada sheet lainnya.

### Perbaikan Styling:

1. **Header Styling**: Menambahkan bold font dan background color untuk semua header
2. **Auto-sizing**: Menambahkan auto-sizing untuk semua kolom
3. **Color Coding**: Setiap sheet memiliki warna header yang berbeda:
    - Sheet 1 (Info Kelompok): Orange (#F59E0B)
    - Sheet 2 (Karyawan): Green (#10B981)
    - Sheet 3 (Laporan Kelompok): Blue (#3B82F6)
    - Sheet 4 (Job Pekerjaan): Purple (#8B5CF6)

### Kode Perbaikan:

```php
// Style headers dengan warna berbeda untuk setiap sheet
$sheet->getStyle('A1:B1')->getFont()->setBold(true);
$sheet->getStyle('A1:B1')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setRGB('F59E0B');

// Auto size columns
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
```

## Status

✅ **FIXED** - Menu "Export Data Kelompok" sudah berfungsi dengan baik dan siap digunakan.
✅ **IMPROVED** - Semua 4 sheet sekarang terlihat dengan jelas dengan styling yang baik.

## Catatan

-   Export hanya bisa dilakukan oleh karyawan yang sudah terdaftar dalam kelompok
-   File Excel akan berisi data lengkap kelompok karyawan yang login
-   Format file: `.xlsx` (Excel 2007+)
-   Nama file otomatis dengan timestamp untuk menghindari konflik
