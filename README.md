# PLN Galesong - Sistem Prediksi Waktu Penyelesaian Kegiatan Lapangan

Sistem manajemen dan prediksi untuk kegiatan lapangan PLN Unit Induk Distribusi Sulselrabar yang telah di-porting dari aplikasi React/Vite ke Laravel.

## 🚀 Fitur Utama

### Untuk Atasan (Admin)

-   **Dashboard Lengkap** dengan statistik real-time
-   **Manajemen Kelompok** - Tambah, edit, hapus kelompok kerja
-   **Manajemen Karyawan** - Kelola data karyawan per kelompok
-   **Pemantauan Laporan** - Monitor laporan dari semua kelompok
-   **Sistem Prediksi** - Generate prediksi berdasarkan data historis
-   **Export Data** - Download data dalam format Excel

### Untuk Karyawan

-   **Dashboard Personal** dengan ringkasan aktivitas
-   **Input Laporan** - Catat kegiatan lapangan harian
-   **Input Job Pekerjaan** - Rekam detail pekerjaan teknis
-   **Lihat Prediksi** - Akses prediksi untuk kelompok mereka

## 🛠 Teknologi yang Digunakan

-   **Backend**: Laravel 11
-   **Database**: MySQL
-   **Frontend**: Blade Templates + Alpine.js
-   **Styling**: Tailwind CSS
-   **Charts**: Chart.js
-   **Icons**: Lucide React Icons

## 📊 Struktur Database

### Tabel Utama

-   `kelompok` - Data kelompok kerja (Shift 1 & 2)
-   `users` - Data pengguna (atasan/karyawan)
-   `karyawan` - Detail karyawan per kelompok
-   `laporan_karyawan` - Laporan kegiatan harian
-   `job_pekerjaan` - Data pekerjaan teknis
-   `prediksi` - Hasil prediksi sistem

### Relasi Database

```
kelompok (1) -> (many) users
kelompok (1) -> (many) karyawan
kelompok (1) -> (many) laporan_karyawan
kelompok (1) -> (many) job_pekerjaan
kelompok (1) -> (many) prediksi
```

## 🔧 Instalasi dan Setup

### Prerequisites

-   PHP 8.1+
-   MySQL 5.7+
-   Composer
-   Web server (Apache/Nginx)

### Langkah Instalasi

1. **Clone Repository**

```bash
git clone <repository-url>
cd plngalesong
```

2. **Install Dependencies**

```bash
composer install
```

3. **Setup Environment**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi Database**
   Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pln_galesong
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Jalankan Migrasi**

```bash
php artisan migrate
```

6. **Seed Database**

```bash
php artisan db:seed
```

7. **Start Server**

```bash
php artisan serve
```

## 🔑 Kredensial Default

### Atasan (Admin)

-   **Username**: `admin`
-   **Password**: `admin123`

### Karyawan Kelompok 1

-   **Username**: `kelompok1`
-   **Password**: `kelompok1123`

### Karyawan Kelompok 2

-   **Username**: `kelompok2`
-   **Password**: `kelompok2123`

## 📱 Cara Penggunaan

### Login

1. Akses aplikasi melalui browser
2. Masukkan username dan password
3. Sistem akan redirect ke dashboard sesuai role

### Dashboard Atasan

1. **Kelompok & Karyawan** - Kelola data kelompok dan anggota
2. **Pemantauan Laporan** - Lihat dan monitor laporan
3. **Statistik & Prediksi** - Generate dan analisis prediksi

### Dashboard Karyawan

1. **Dashboard** - Lihat ringkasan aktivitas
2. **Input Laporan** - Catat kegiatan harian
3. **Input Job Pekerjaan** - Rekam detail pekerjaan

## 🔌 API Endpoints

Semua endpoint memerlukan autentikasi dan menggunakan prefix `/api/`:

### Kelompok

-   `GET /api/kelompok` - Ambil semua kelompok
-   `POST /api/kelompok` - Buat kelompok baru
-   `PUT /api/kelompok/{id}` - Update kelompok
-   `DELETE /api/kelompok/{id}` - Hapus kelompok

### Karyawan

-   `GET /api/karyawan` - Ambil semua karyawan
-   `POST /api/karyawan` - Buat karyawan baru
-   `PUT /api/karyawan/{id}` - Update karyawan
-   `DELETE /api/karyawan/{id}` - Hapus karyawan

### Laporan Karyawan

-   `GET /api/laporan-karyawan` - Ambil laporan
-   `POST /api/laporan-karyawan` - Buat laporan baru
-   `PUT /api/laporan-karyawan/{id}` - Update laporan
-   `DELETE /api/laporan-karyawan/{id}` - Hapus laporan

### Job Pekerjaan

-   `GET /api/job-pekerjaan` - Ambil job pekerjaan
-   `POST /api/job-pekerjaan` - Buat job baru
-   `PUT /api/job-pekerjaan/{id}` - Update job
-   `DELETE /api/job-pekerjaan/{id}` - Hapus job

### Prediksi

-   `GET /api/prediksi` - Ambil prediksi
-   `POST /api/prediksi/generate` - Generate prediksi baru
-   `DELETE /api/prediksi/{id}` - Hapus prediksi

## 🎨 Customization

### Styling

-   File CSS custom: `public/css/app.css`
-   Menggunakan Tailwind CSS dengan konfigurasi custom
-   Support untuk dark mode dan responsive design

### Theme Colors

-   **Primary**: Amber/Orange gradient
-   **Secondary**: Blue/Cyan gradient
-   **Success**: Green
-   **Warning**: Yellow
-   **Danger**: Red

## 📈 Fitur Prediksi

Sistem prediksi menggunakan algoritma sederhana berdasarkan data historis:

-   **Prediksi Laporan**: Berdasarkan rata-rata laporan per bulan + 10% peningkatan
-   **Prediksi Job**: Berdasarkan rata-rata waktu penyelesaian - 5% perbaikan

## 🔒 Keamanan

-   Password di-hash menggunakan bcrypt
-   CSRF protection untuk semua form
-   Authentication middleware untuk protected routes
-   Input validation dan sanitization

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**

    - Pastikan MySQL service running
    - Check konfigurasi database di `.env`
    - Pastikan database `pln_galesong` sudah dibuat

2. **Migration Error**

    - Pastikan database kosong atau backup data lama
    - Jalankan `php artisan migrate:fresh` untuk reset

3. **Permission Error**
    - Pastikan folder `storage` dan `bootstrap/cache` writable
    - Jalankan `chmod -R 755 storage bootstrap/cache`

## 📝 Changelog

### Version 1.0.0

-   Porting dari React/Vite ke Laravel
-   Implementasi sistem autentikasi
-   Dashboard untuk atasan dan karyawan
-   CRUD untuk semua entitas
-   Sistem prediksi dasar
-   Export data functionality

## 👥 Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

Untuk bantuan teknis, hubungi tim development atau buat issue di repository.

---

**PLN Unit Induk Distribusi Sulselrabar**  
Sistem Prediksi Waktu Penyelesaian Kegiatan Lapangan
