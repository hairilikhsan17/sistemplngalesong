# Setup PLN Galesong Laravel Application

## Prerequisites

-   PHP 8.1 or higher
-   MySQL 5.7 or higher
-   Composer
-   Node.js (for assets if needed)

## Installation Steps

### 1. Database Setup

1. Create a MySQL database named `pln_galesong`
2. Update your `.env` file with the following database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pln_galesong
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 2. Application Setup

1. Copy `.env.example` to `.env` and configure it
2. Generate application key:

```bash
php artisan key:generate
```

3. Run migrations:

```bash
php artisan migrate
```

4. Seed the database with initial data:

```bash
php artisan db:seed
```

### 3. Default Login Credentials

After seeding, you can login with these credentials:

**Atasan (Admin):**

-   Username: `admin`
-   Password: `admin123`

**Karyawan Kelompok 1:**

-   Username: `kelompok1`
-   Password: `kelompok1123`

**Karyawan Kelompok 2:**

-   Username: `kelompok2`
-   Password: `kelompok2123`

## Features

### For Atasan (Admin):

-   Dashboard dengan statistik lengkap
-   Manajemen Kelompok dan Karyawan
-   Pemantauan Laporan dari semua kelompok
-   Generate dan lihat Prediksi
-   Export data ke Excel

### For Karyawan:

-   Dashboard personal
-   Input Laporan Karyawan
-   Input Job Pekerjaan
-   Lihat prediksi untuk kelompok mereka

## Database Schema

The application includes the following tables:

-   `kelompok` - Data kelompok kerja
-   `users` - Data pengguna (atasan/karyawan)
-   `karyawan` - Data karyawan
-   `laporan_karyawan` - Laporan pekerjaan karyawan
-   `job_pekerjaan` - Data job pekerjaan
-   `prediksi` - Hasil prediksi sistem

## API Endpoints

All API endpoints are prefixed with `/api/` and require authentication:

-   `GET /api/kelompok` - Get all kelompok
-   `POST /api/kelompok` - Create new kelompok
-   `PUT /api/kelompok/{id}` - Update kelompok
-   `DELETE /api/kelompok/{id}` - Delete kelompok

-   `GET /api/karyawan` - Get all karyawan
-   `POST /api/karyawan` - Create new karyawan
-   `PUT /api/karyawan/{id}` - Update karyawan
-   `DELETE /api/karyawan/{id}` - Delete karyawan

-   `GET /api/laporan-karyawan` - Get laporan karyawan
-   `POST /api/laporan-karyawan` - Create new laporan
-   `PUT /api/laporan-karyawan/{id}` - Update laporan
-   `DELETE /api/laporan-karyawan/{id}` - Delete laporan

-   `GET /api/job-pekerjaan` - Get job pekerjaan
-   `POST /api/job-pekerjaan` - Create new job
-   `PUT /api/job-pekerjaan/{id}` - Update job
-   `DELETE /api/job-pekerjaan/{id}` - Delete job

-   `GET /api/prediksi` - Get prediksi
-   `POST /api/prediksi/generate` - Generate new prediksi
-   `DELETE /api/prediksi/{id}` - Delete prediksi

## Technologies Used

-   Laravel 11
-   MySQL
-   Tailwind CSS
-   Alpine.js
-   Chart.js
-   Lucide Icons

## Notes

-   The application uses UUIDs for primary keys
-   Password hashing is handled automatically by Laravel
-   File uploads for dokumentasi are stored as file names (not actual files)
-   The prediction algorithm is simplified and can be enhanced based on requirements



