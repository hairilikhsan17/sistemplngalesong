# Panduan Job Pekerjaan - PLN Galesong

## Overview

Fitur Job Pekerjaan telah berhasil dibuat dan terintegrasi dengan sistem PLN Galesong. Fitur ini memungkinkan karyawan untuk mengelola data pekerjaan dan aktivitas kelompok mereka.

## Fitur yang Tersedia

### 1. Dashboard Job Pekerjaan

-   **Lokasi**: `/kelompok/job-pekerjaan`
-   **Akses**: Hanya untuk karyawan (role: karyawan)
-   **Fitur**:
    -   Statistik total pekerjaan (Perbaikan KWH, Pemeliharaan Kabel, Pengecekan Gardu, Penanganan Gangguan)
    -   Filter berdasarkan bulan
    -   Pencarian berdasarkan lokasi
    -   Tabel data pekerjaan dengan aksi CRUD

### 2. CRUD Operations

-   **Create**: Tambah job pekerjaan baru
-   **Read**: Lihat daftar job pekerjaan
-   **Update**: Edit job pekerjaan yang sudah ada
-   **Delete**: Hapus job pekerjaan

### 3. Form Input Job Pekerjaan

Form input mencakup field berikut:

-   **Tanggal** (required): Tanggal pekerjaan dilakukan
-   **Bulan Data** (required): Bulan data pekerjaan
-   **Lokasi** (required): Lokasi pekerjaan
-   **Perbaikan KWH** (required): Jumlah perbaikan KWH
-   **Pemeliharaan Pengkabelan** (required): Jumlah pemeliharaan pengkabelan
-   **Pengecekan Gardu** (required): Jumlah pengecekan gardu
-   **Penanganan Gangguan** (required): Jumlah penanganan gangguan
-   **Waktu Penyelesaian** (required): Waktu penyelesaian dalam jam

## File yang Dibuat/Dimodifikasi

### 1. View File

-   `resources/views/dashboard/job-pekerjaan.blade.php` - Halaman utama job pekerjaan

### 2. Route File

-   `routes/web.php` - Menambahkan route untuk halaman job pekerjaan

### 3. Sidebar

-   `resources/views/layouts/sidebar.blade.php` - Update menu sidebar untuk karyawan

### 4. Controller (Sudah Ada)

-   `app/Http/Controllers/JobPekerjaanController.php` - Controller untuk CRUD operations

### 5. Model (Sudah Ada)

-   `app/Models/JobPekerjaan.php` - Model untuk job pekerjaan

## Cara Menggunakan

### 1. Akses Halaman

1. Login sebagai karyawan
2. Klik menu "Input Job Pekerjaan" di sidebar
3. Halaman job pekerjaan akan terbuka

### 2. Menambah Job Pekerjaan

1. Klik tombol "Tambah Job" di halaman utama
2. Isi form dengan data yang diperlukan
3. Klik "Simpan"
4. Data akan tersimpan dan muncul di tabel

### 3. Mengedit Job Pekerjaan

1. Klik ikon edit (pensil) pada baris data yang ingin diedit
2. Ubah data yang diperlukan
3. Klik "Update"
4. Data akan terupdate

### 4. Menghapus Job Pekerjaan

1. Klik ikon delete (tong sampah) pada baris data yang ingin dihapus
2. Konfirmasi penghapusan
3. Data akan terhapus

### 5. Filter dan Pencarian

1. Gunakan dropdown "Bulan" untuk filter berdasarkan bulan
2. Gunakan field "Cari Job" untuk mencari berdasarkan lokasi
3. Klik tombol search untuk menerapkan filter

## API Endpoints

### 1. Get All Jobs

-   **URL**: `GET /api/job-pekerjaan`
-   **Response**: Array of job objects

### 2. Create Job

-   **URL**: `POST /api/job-pekerjaan`
-   **Body**: Job data (tanggal, lokasi, dll)
-   **Response**: Created job object

### 3. Get Single Job

-   **URL**: `GET /api/job-pekerjaan/{id}`
-   **Response**: Job object

### 4. Update Job

-   **URL**: `PUT /api/job-pekerjaan/{id}`
-   **Body**: Updated job data
-   **Response**: Updated job object

### 5. Delete Job

-   **URL**: `DELETE /api/job-pekerjaan/{id}`
-   **Response**: Success message

## Validasi Data

### 1. Required Fields

-   Semua field wajib diisi
-   Tanggal harus dalam format yang valid
-   Angka harus >= 0

### 2. Data Types

-   Perbaikan KWH: Integer
-   Pemeliharaan Pengkabelan: Integer
-   Pengecekan Gardu: Integer
-   Penanganan Gangguan: Integer
-   Waktu Penyelesaian: Integer (dalam jam)

## Keamanan

### 1. Authentication

-   Hanya user yang login yang bisa mengakses
-   Karyawan hanya bisa melihat data kelompok mereka

### 2. Authorization

-   Karyawan tidak bisa mengakses data kelompok lain
-   Validasi CSRF token untuk semua request

## Troubleshooting

### 1. Halaman Tidak Bisa Diakses

-   Pastikan sudah login sebagai karyawan
-   Periksa apakah route sudah terdaftar
-   Periksa apakah middleware auth sudah aktif

### 2. Data Tidak Tersimpan

-   Periksa validasi form
-   Pastikan semua field required sudah diisi
-   Periksa console browser untuk error JavaScript

### 3. API Error

-   Periksa network tab di browser
-   Pastikan CSRF token sudah benar
-   Periksa log Laravel untuk error detail

## Database Schema

### Table: job_pekerjaan

```sql
- id (string, primary key)
- perbaikan_kwh (integer)
- pemeliharaan_pengkabelan (integer)
- pengecekan_gardu (integer)
- penanganan_gangguan (integer)
- lokasi (string)
- kelompok_id (string, foreign key)
- bulan_data (string)
- tanggal (date)
- waktu_penyelesaian (integer)
- created_at (timestamp)
- updated_at (timestamp)
```

## Fitur Tambahan yang Bisa Dikembangkan

1. **Export Data**: Export data job pekerjaan ke Excel
2. **Import Data**: Import data dari Excel
3. **Laporan**: Generate laporan pekerjaan
4. **Notifikasi**: Notifikasi untuk deadline pekerjaan
5. **Dashboard Analytics**: Grafik dan analisis data pekerjaan
6. **Mobile Responsive**: Optimasi untuk mobile device
7. **Offline Mode**: Bekerja tanpa internet
8. **File Upload**: Upload dokumentasi pekerjaan

## Kesimpulan

Fitur Job Pekerjaan telah berhasil dibuat dengan lengkap dan terintegrasi dengan sistem PLN Galesong. Fitur ini menyediakan:

-   ✅ CRUD operations lengkap
-   ✅ Interface yang user-friendly
-   ✅ Validasi data yang proper
-   ✅ Keamanan yang baik
-   ✅ Responsive design
-   ✅ Filter dan pencarian
-   ✅ Statistik real-time

Fitur siap digunakan dan dapat dikembangkan lebih lanjut sesuai kebutuhan.
