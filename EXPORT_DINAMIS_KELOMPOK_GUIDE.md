# Export Dinamis Semua Data Kelompok - PLN Galesong

## Deskripsi Fitur

Sistem export dinamis yang otomatis menyesuaikan dengan perubahan data kelompok. Ketika ada penambahan atau penghapusan kelompok, file Excel akan otomatis menyesuaikan strukturnya.

## Cara Kerja Sistem Dinamis

### 1. **Otomatis Menyesuaikan Jumlah Kelompok**

-   Sistem membaca semua kelompok yang ada di database secara real-time
-   Menggunakan query `Kelompok::with(['karyawan'])->orderBy('created_at')->get()`
-   Jumlah kelompok yang diexport akan selalu sesuai dengan data terbaru

### 2. **Penambahan Kelompok Baru**

Ketika Anda menambahkan kelompok baru (misalnya "Kelompok B"):

-   Sistem otomatis mendeteksi kelompok baru
-   Menambahkan section baru di Excel dengan format:
    ```
    KELOMPOK 2: KELOMPOK B
    ID Kelompok: [ID_KELOMPOK_B]
    Nama Kelompok: Kelompok B
    Shift: [SHIFT_KELOMPOK_B]
    ```
-   Menambahkan tabel Input Laporan untuk Kelompok B
-   Menambahkan tabel Input Job Pekerjaan untuk Kelompok B

### 3. **Penghapusan Kelompok**

Ketika Anda menghapus kelompok (misalnya "Kelompok B"):

-   Sistem otomatis tidak menampilkan data kelompok yang dihapus
-   Data kelompok yang dihapus tidak akan muncul di file Excel
-   Struktur Excel menyesuaikan dengan kelompok yang masih ada

### 4. **Struktur File Excel Dinamis**

#### Header Utama

```
SEMUA DATA KELOMPOK - DINAMIS
Total Kelompok: X (Otomatis menyesuaikan)
Total Karyawan: Y
Total Laporan: Z
Total Job Pekerjaan: W
Tanggal Export: [TIMESTAMP]
Catatan: Sistem otomatis menyesuaikan dengan perubahan kelompok (tambah/hapus)
```

#### Untuk Setiap Kelompok

```
KELOMPOK 1: NAMA_KELOMPOK
ID Kelompok: [ID]
Nama Kelompok: [NAMA]
Shift: [SHIFT]
Jumlah Karyawan: X
Jumlah Laporan: Y
Jumlah Job: Z

Tabel Input Laporan
No | Hari/Tanggal | Nama | Instansi | Alamat Tujuan | Dokumentasi
[Data laporan...]

Tabel Input Job Pekerjaan
No | Tanggal | Hari | Perbaikan KWH | Pemeliharaan Pengkabelan | Pengecekan Gardu | Penanganan Gangguan | Lokasi | Waktu (jam) | Created At
[Data job pekerjaan...]
```

## Skenario Penggunaan

### Skenario 1: Kelompok Baru Ditambahkan

1. Admin menambahkan "Kelompok C" di sistem
2. Admin melakukan export "Semua Data Kelompok"
3. File Excel otomatis menampilkan:
    - Kelompok 1: [Nama Kelompok 1]
    - Kelompok 2: [Nama Kelompok 2]
    - **Kelompok 3: Kelompok C** ← Otomatis ditambahkan

### Skenario 2: Kelompok Dihapus

1. Admin menghapus "Kelompok B" dari sistem
2. Admin melakukan export "Semua Data Kelompok"
3. File Excel otomatis menampilkan:
    - Kelompok 1: [Nama Kelompok 1]
    - Kelompok 2: Kelompok C ← Renumbered otomatis
    - Kelompok B tidak muncul

### Skenario 3: Kelompok Kosong (Baru Dibuat)

1. Admin membuat "Kelompok D" baru (belum ada data)
2. Admin melakukan export
3. File Excel menampilkan:

    ```
    KELOMPOK 4: KELOMPOK D
    ID Kelompok: [ID_D]
    Nama Kelompok: Kelompok D
    Shift: [SHIFT_D]
    Jumlah Karyawan: 0
    Jumlah Laporan: 0
    Jumlah Job: 0

    Tabel Input Laporan
    Tidak ada data laporan untuk kelompok ini

    Tabel Input Job Pekerjaan
    Tidak ada data job pekerjaan untuk kelompok ini
    ```

## Keunggulan Sistem Dinamis

1. **Real-time**: Selalu menggunakan data terbaru dari database
2. **Otomatis**: Tidak perlu konfigurasi manual untuk perubahan kelompok
3. **Fleksibel**: Menyesuaikan dengan jumlah kelompok berapa pun
4. **Konsisten**: Format dan struktur tetap sama untuk semua kelompok
5. **User-friendly**: Admin tidak perlu khawatir tentang update manual

## File yang Terlibat

### Controller

-   `app/Http/Controllers/ExportDataController.php`
    -   Method: `exportAllData()`
    -   Method: `exportAllKelompokDataToExcel()`

### View

-   `resources/views/dashboard/atasan/export-data.blade.php`
    -   Button: "Export Semua Data Kelompok"
    -   Deskripsi fitur dinamis

### Routes

-   `routes/web.php`
    -   Route: `/api/export-data/all`

## Cara Menggunakan

1. Login sebagai Atasan/Admin
2. Buka menu "Export Data"
3. Klik button "Export Semua Data Kelompok"
4. File Excel akan didownload dengan nama:
   `PLN_Galesong_Semua_Data_Kelompok_[TIMESTAMP].xlsx`

## Catatan Penting

-   Sistem ini sepenuhnya dinamis dan otomatis
-   Tidak ada konfigurasi manual yang diperlukan
-   Data selalu up-to-date sesuai dengan database
-   Format Excel konsisten untuk semua skenario
-   Mendukung unlimited jumlah kelompok

---

**Dibuat**: {{ date('Y-m-d H:i:s') }}
**Versi**: 1.0
**Status**: Aktif dan Siap Digunakan
