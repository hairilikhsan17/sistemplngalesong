# Seeder Data Guide - PLN Galesong

Dokumentasi lengkap untuk menjalankan dan memverifikasi seeder data sistem PLN Galesong.

## 📋 Daftar Isi

1. [Menjalankan Seeder](#menjalankan-seeder)
2. [Langkah-Langkah Menjalankan Seeder](#langkah-langkah-menjalankan-seeder)
3. [Verifikasi Data](#verifikasi-data)
4. [Informasi Login](#informasi-login)
5. [Troubleshooting](#troubleshooting)

---

## 🚀 Menjalankan Seeder

### Seeder Utama

Untuk menjalankan seeder lengkap (kelompok, karyawan, user, dan data laporan):

```bash
php artisan db:seed --class=SetupKelompokDanDataSeeder
```

Seeder ini akan membuat:

-   ✅ 1 user admin
-   ✅ 3 kelompok (Kelompok 1, 2, 3)
-   ✅ 6 karyawan (2 per kelompok)
-   ✅ 3 user login untuk kelompok
-   ✅ Data laporan dari Januari 2025 sampai sekarang

### Output Seeder

Setelah seeder berhasil dijalankan, Anda akan melihat output seperti:

```
=== Memulai Setup Kelompok dan Data ===
Menghapus data lama...
Membuat user admin...
✓ User admin berhasil dibuat
Membuat Kelompok 1...
✓ Kelompok 1 berhasil dibuat dengan 2 anggota
Membuat Kelompok 2...
✓ Kelompok 2 berhasil dibuat dengan 2 anggota
Membuat Kelompok 3...
✓ Kelompok 3 berhasil dibuat dengan 2 anggota
Membuat data laporan dari Januari 2025 sampai sekarang...
✓ Data laporan untuk Kelompok 1 selesai
✓ Data laporan untuk Kelompok 2 selesai
✓ Data laporan untuk Kelompok 3 selesai
=== Setup Selesai ===
Total Kelompok: 3
Total Karyawan: 6
Total Laporan: 1647

=== Informasi Login ===

=== Admin ===
Username: admin
Password: admin123

=== Kelompok ===
Username: kelompok1
Password: kelompok1123

Username: kelompok2
Password: kelompok2123

Username: kelompok3
Password: kelompok3123
```

---

## 📝 Langkah-Langkah Menjalankan Seeder

### Persiapan Sebelum Menjalankan Seeder

Sebelum menjalankan seeder `SetupKelompokDanDataSeeder.php`, pastikan Anda telah menyiapkan environment dengan benar:

#### 1. Pastikan Database Sudah Dikonfigurasi

Verifikasi koneksi database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_database
DB_PASSWORD=password_database
```

#### 2. Pastikan Migration Sudah Dijalankan

Jalankan semua migration terlebih dahulu:

```bash
php artisan migrate
```

Atau jika ingin fresh migration (hati-hati: akan menghapus semua tabel):

```bash
php artisan migrate:fresh
```

#### 3. Pastikan File Seeder Ada

Pastikan file seeder berada di lokasi yang benar:

```
database/seeders/SetupKelompokDanDataSeeder.php
```

---

### Langkah-Langkah Menjalankan Seeder

#### Langkah 1: Backup Database (Opsional tapi Disarankan)

Jika Anda memiliki data penting, lakukan backup database terlebih dahulu:

```bash
# Backup MySQL/MariaDB
mysqldump -u username -p nama_database > backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Langkah 2: Pilih Metode (Rollback atau Update)

Anda memiliki 2 pilihan:

**Opsi A: Rollback Semua Data dan Buat Ulang (Disarankan untuk Development)**

-   Menghapus semua data lama
-   Membuat data baru dengan format terbaru
-   Cocok untuk environment development/testing

```bash
php artisan db:seed --class=SetupKelompokDanDataSeeder
```

**Opsi B: Update Data yang Sudah Ada (Untuk Production)**

-   Mengupdate data lama tanpa menghapus
-   Hanya mengubah "Jenis Kegiatan" menjadi "Jenis Kegiatan lainnya"
-   Cocok jika Anda memiliki data penting yang tidak ingin dihapus

```bash
# Jalankan migration untuk update data yang sudah ada
php artisan migrate
```

**Catatan Penting untuk Opsi A:**

-   ⚠️ **Seeder ini akan menghapus semua data lama** (laporan, karyawan, kelompok, user karyawan) sebelum membuat data baru
-   ✅ **Data admin akan diupdate password** jika sudah ada, atau dibuat baru jika belum ada
-   ⏱️ Proses seeder membutuhkan waktu beberapa menit tergantung jumlah data yang dibuat

#### Langkah 3: Tunggu Proses Selesai

Selama proses seeder berjalan, Anda akan melihat output seperti:

```
=== Memulai Setup Kelompok dan Data ===
Menghapus data lama...
Membuat user admin...
✓ User admin sudah ada, password diupdate
Membuat Kelompok 1...
✓ Kelompok 1 berhasil dibuat dengan 2 anggota
...
✓ Data laporan untuk Kelompok 1 selesai
✓ Data laporan untuk Kelompok 2 selesai
✓ Data laporan untuk Kelompok 3 selesai
=== Setup Selesai ===
Total Kelompok: 3
Total Karyawan: 6
Total Laporan: 1671
```

**Jangan tutup terminal** sampai proses selesai!

#### Langkah 4: Verifikasi Data

Setelah seeder selesai, verifikasi data yang telah dibuat:

```bash
# Verifikasi total data
php artisan tinker --execute="echo 'Kelompok: ' . \App\Models\Kelompok::count() . PHP_EOL; echo 'Karyawan: ' . \App\Models\Karyawan::count() . PHP_EOL; echo 'Laporan: ' . \App\Models\LaporanKaryawan::count() . PHP_EOL;"
```

Verifikasi jenis kegiatan:

```bash
php artisan tinker --execute="echo 'Total Laporan: ' . \App\Models\LaporanKaryawan::count() . PHP_EOL; echo 'Laporan dengan Jenis Kegiatan lainnya: ' . \App\Models\LaporanKaryawan::where('jenis_kegiatan', 'Jenis Kegiatan lainnya')->count() . PHP_EOL; echo 'Laporan dengan Jenis Kegiatan (lama): ' . \App\Models\LaporanKaryawan::where('jenis_kegiatan', 'Jenis Kegiatan')->count() . PHP_EOL;"
```

#### Langkah 5: Test Login

Uji login dengan kredensial yang ditampilkan di output seeder:

**Admin:**

-   Username: `admin`
-   Password: `admin123`

**Kelompok:**

-   Username: `kelompok1`, `kelompok2`, atau `kelompok3`
-   Password: `kelompok1123`, `kelompok2123`, atau `kelompok3123`

---

### Data yang Akan Dibuat oleh Seeder

Seeder `SetupKelompokDanDataSeeder.php` akan membuat:

#### 1. User Admin (1 user)

-   Username: `admin`
-   Password: `admin123`
-   Role: `atasan`
-   Akses: Full access ke semua fitur admin

#### 2. Kelompok (3 kelompok)

| Kelompok   | Shift   | Username  | Password     | Jumlah Anggota |
| ---------- | ------- | --------- | ------------ | -------------- |
| Kelompok 1 | Shift 1 | kelompok1 | kelompok1123 | 2              |
| Kelompok 2 | Shift 1 | kelompok2 | kelompok2123 | 2              |
| Kelompok 3 | Shift 2 | kelompok3 | kelompok3123 | 2              |

#### 3. Karyawan (6 karyawan)

-   **Kelompok 1:** Andi Pratama, Rizky Ramadhan
-   **Kelompok 2:** Ahmad Fauzan, Muhammad Ilham
-   **Kelompok 3:** Rahmat Hidayat, Aditya Saputra

#### 4. Data Laporan (sekitar 1600+ laporan)

-   **Periode:** Januari 2025 sampai hari ini
-   **Jenis Kegiatan:**
    -   Perbaikan Meteran
    -   Perbaikan Sambungan Rumah
    -   Pemeriksaan Gardu
    -   **Jenis Kegiatan lainnya** (format baru)
-   **Distribusi:**
    -   Hari kerja (Senin-Jumat): 1-3 laporan per hari
    -   Weekend: 0-1 laporan per hari
-   **Data Lengkap:** Setiap laporan memiliki instansi, alamat, deskripsi, waktu, durasi, dan lokasi

---

### Troubleshooting Seeder

#### Error: Seeder File Not Found

**Solusi:** Pastikan file seeder ada di `database/seeders/SetupKelompokDanDataSeeder.php`

#### Error: Foreign Key Constraint

**Solusi:** Seeder sudah menangani dengan menonaktifkan sementara foreign key checks. Jika masih error, jalankan:

```bash
php artisan migrate:fresh
php artisan db:seed --class=SetupKelompokDanDataSeeder
```

#### Error: Memory Limit Exceeded

**Solusi:** Tingkatkan memory limit PHP:

```bash
php -d memory_limit=512M artisan db:seed --class=SetupKelompokDanDataSeeder
```

#### Error: Database Connection Failed

**Solusi:** Periksa konfigurasi database di file `.env` dan pastikan database server berjalan.

#### Data Tidak Muncul Setelah Seeder

**Solusi:**

1. Periksa apakah ada error selama proses seeder
2. Verifikasi dengan command verifikasi data
3. Periksa log error Laravel di `storage/logs/laravel.log`

---

## ✅ Verifikasi Data

### 1. Verifikasi Data Kelompok dan Karyawan

Jalankan perintah berikut untuk memverifikasi data yang telah dibuat:

```bash
php artisan tinker --execute="echo 'Kelompok: ' . \App\Models\Kelompok::count() . PHP_EOL; echo 'Karyawan: ' . \App\Models\Karyawan::count() . PHP_EOL; echo 'User: ' . \App\Models\User::where('role', 'karyawan')->count() . PHP_EOL; echo 'Laporan: ' . \App\Models\LaporanKaryawan::count() . PHP_EOL; \App\Models\Kelompok::with('karyawan')->get()->each(function(\$k) { echo PHP_EOL . \$k->nama_kelompok . ':' . PHP_EOL; \$k->karyawan->each(function(\$kar) { echo '  - ' . \$kar->nama . PHP_EOL; }); });"
```

**Output yang Diharapkan:**

```
Kelompok: 3
Karyawan: 6
User: 3
Laporan: 1647

Kelompok 1:
  - Andi Pratama
  - Rizky Ramadhan

Kelompok 2:
  - Ahmad Fauzan
  - Muhammad Ilham

Kelompok 3:
  - Rahmat Hidayat
  - Aditya Saputra
```

### 2. Verifikasi Data Laporan

Untuk melihat sample data laporan dan jumlah laporan per kelompok:

```bash
php artisan tinker --execute="echo 'Sample Data Laporan:' . PHP_EOL; \App\Models\LaporanKaryawan::with('kelompok')->orderBy('tanggal', 'desc')->limit(5)->get()->each(function(\$l) { echo PHP_EOL . 'Tanggal: ' . \$l->tanggal . ' | Kelompok: ' . \$l->kelompok->nama_kelompok . ' | Nama: ' . \$l->nama . ' | Jenis: ' . \$l->jenis_kegiatan . PHP_EOL; }); echo PHP_EOL . 'Data per Kelompok:' . PHP_EOL; \App\Models\Kelompok::all()->each(function(\$k) { \$count = \App\Models\LaporanKaryawan::where('kelompok_id', \$k->id)->count(); echo \$k->nama_kelompok . ': ' . \$count . ' laporan' . PHP_EOL; });"
```

**Output yang Diharapkan:**

```
Sample Data Laporan:

Tanggal: 2025-12-20 | Kelompok: Kelompok 3 | Nama: Aditya Saputra | Jenis: Pemeriksaan Gardu
Tanggal: 2025-12-19 | Kelompok: Kelompok 3 | Nama: Rahmat Hidayat | Jenis: Perbaikan Meteran
Tanggal: 2025-12-19 | Kelompok: Kelompok 2 | Nama: Muhammad Ilham | Jenis: Jenis Kegiatan lainnya
Tanggal: 2025-12-18 | Kelompok: Kelompok 1 | Nama: Andi Pratama | Jenis: Perbaikan Sambungan Rumah

Data per Kelompok:
Kelompok 1: 557 laporan
Kelompok 2: 567 laporan
Kelompok 3: 567 laporan
```

**Catatan:** Output dapat menampilkan berbagai jenis kegiatan termasuk "Jenis Kegiatan lainnya" yang merupakan jenis kegiatan khusus yang memerlukan deskripsi wajib.

### 2.1. Verifikasi Jenis Kegiatan

Untuk memverifikasi bahwa semua data menggunakan format baru "Jenis Kegiatan lainnya" dan tidak ada data lama:

```bash
php artisan tinker --execute="echo 'Total Laporan: ' . \App\Models\LaporanKaryawan::count() . PHP_EOL; echo 'Laporan dengan Jenis Kegiatan lainnya: ' . \App\Models\LaporanKaryawan::where('jenis_kegiatan', 'Jenis Kegiatan lainnya')->count() . PHP_EOL; echo 'Laporan dengan Jenis Kegiatan (lama): ' . \App\Models\LaporanKaryawan::where('jenis_kegiatan', 'Jenis Kegiatan')->count() . PHP_EOL;"
```

**Output yang Diharapkan:**

```
Total Laporan: 1671
Laporan dengan Jenis Kegiatan lainnya: 419
Laporan dengan Jenis Kegiatan (lama): 0
```

**Catatan:** Jika ditemukan laporan dengan "Jenis Kegiatan" (lama), jalankan migration untuk update data:

```bash
php artisan migrate
```

### 3. Verifikasi User Admin

Untuk memverifikasi user admin telah dibuat dengan benar:

```bash
php artisan tinker --execute="echo 'Admin User: ' . PHP_EOL; \$admin = \App\Models\User::where('username', 'admin')->where('role', 'atasan')->first(); if (\$admin) { echo 'Username: ' . \$admin->username . PHP_EOL; echo 'Role: ' . \$admin->role . PHP_EOL; echo 'Password verified: ' . (\Illuminate\Support\Facades\Hash::check('admin123', \$admin->password) ? 'Yes' : 'No') . PHP_EOL; } else { echo 'Admin tidak ditemukan' . PHP_EOL; }"
```

**Output yang Diharapkan:**

```
Admin User:
Username: admin
Role: atasan
Password verified: Yes
```

---

## 🔐 Informasi Login

### Admin

-   **Username:** `admin`
-   **Password:** `admin123`
-   **Role:** `atasan`
-   **Akses:** Full access ke semua fitur admin

### Kelompok

#### Kelompok 1

-   **Username:** `kelompok1`
-   **Password:** `kelompok1123`
-   **Anggota:**
    -   Andi Pratama
    -   Rizky Ramadhan
-   **Shift:** Shift 1

#### Kelompok 2

-   **Username:** `kelompok2`
-   **Password:** `kelompok2123`
-   **Anggota:**
    -   Ahmad Fauzan
    -   Muhammad Ilham
-   **Shift:** Shift 1

#### Kelompok 3

-   **Username:** `kelompok3`
-   **Password:** `kelompok3123`
-   **Anggota:**
    -   Rahmat Hidayat
    -   Aditya Saputra
-   **Shift:** Shift 2

---

## 📊 Data yang Dibuat

### Kelompok

Seeder membuat 3 kelompok dengan konfigurasi berikut:

| Kelompok   | Shift   | Jumlah Anggota | Username  |
| ---------- | ------- | -------------- | --------- |
| Kelompok 1 | Shift 1 | 2              | kelompok1 |
| Kelompok 2 | Shift 1 | 2              | kelompok2 |
| Kelompok 3 | Shift 2 | 2              | kelompok3 |

### Karyawan

Setiap kelompok memiliki 2 karyawan dengan nama lengkap:

-   **Kelompok 1:** Andi Pratama, Rizky Ramadhan
-   **Kelompok 2:** Ahmad Fauzan, Muhammad Ilham
-   **Kelompok 3:** Rahmat Hidayat, Aditya Saputra

### Laporan

Seeder membuat data laporan untuk setiap kelompok dengan karakteristik:

-   **Periode:** Januari 2025 - Desember 2025 (sampai hari ini)
-   **Jenis Kegiatan:**
    -   Perbaikan Meteran
    -   Perbaikan Sambungan Rumah
    -   Pemeriksaan Gardu
    -   Jenis Kegiatan lainnya
-   **Distribusi:**
    -   Hari kerja (Senin-Jumat): 1-3 laporan per hari
    -   Weekend: 0-1 laporan per hari
-   **Data Lengkap:** Setiap laporan memiliki instansi, alamat, deskripsi, waktu, durasi, dan lokasi

---

## 🔧 Troubleshooting

### Seeder Gagal karena Foreign Key Constraint

Jika terjadi error foreign key constraint saat menghapus data, seeder sudah menangani dengan menonaktifkan sementara foreign key checks.

### Admin Tidak Bisa Login

Jika admin tidak bisa login, verifikasi dengan perintah:

```bash
php artisan tinker --execute="\$admin = \App\Models\User::where('username', 'admin')->first(); \$admin->password = \Illuminate\Support\Facades\Hash::make('admin123'); \$admin->save(); echo 'Password admin berhasil direset';"
```

### Data Laporan Tidak Muncul

Pastikan seeder berhasil dijalankan dan tidak ada error. Verifikasi dengan:

```bash
php artisan tinker --execute="echo 'Total Laporan: ' . \App\Models\LaporanKaryawan::count();"
```

Atau untuk verifikasi lebih lengkap termasuk jenis kegiatan:

```bash
php artisan tinker --execute="echo 'Total Laporan: ' . \App\Models\LaporanKaryawan::count() . PHP_EOL; echo 'Laporan dengan Jenis Kegiatan lainnya: ' . \App\Models\LaporanKaryawan::where('jenis_kegiatan', 'Jenis Kegiatan lainnya')->count() . PHP_EOL; echo 'Laporan dengan Jenis Kegiatan (lama): ' . \App\Models\LaporanKaryawan::where('jenis_kegiatan', 'Jenis Kegiatan')->count() . PHP_EOL;"
```

### User Kelompok Tidak Bisa Login

Pastikan user telah dibuat dengan benar:

```bash
php artisan tinker --execute="\App\Models\User::where('role', 'karyawan')->get()->each(function(\$u) { echo \$u->username . ' - ' . \$u->kelompok->nama_kelompok . PHP_EOL; });"
```

---

## 📝 Catatan Penting

1. **Seeder akan menghapus semua data lama** sebelum membuat data baru
2. **Admin akan diupdate password** jika sudah ada, atau dibuat baru jika belum ada
3. **Data laporan** dibuat secara random untuk setiap hari dalam periode
4. **Jumlah laporan** per kelompok akan bervariasi karena dibuat secara random
5. **Jenis Kegiatan** sekarang menggunakan format baru: "Jenis Kegiatan lainnya" (menggantikan "Jenis Kegiatan")
6. **Data "Jenis Kegiatan lainnya"** akan selalu memiliki deskripsi kegiatan karena merupakan field wajib

---

## 🔄 Menjalankan Ulang Seeder

Untuk menjalankan ulang seeder dan membuat ulang semua data:

```bash
php artisan db:seed --class=SetupKelompokDanDataSeeder
```

Seeder akan:

1. Menghapus data laporan lama
2. Menghapus data karyawan lama
3. Menghapus user karyawan lama
4. Menghapus kelompok lama
5. Membuat ulang semua data dengan struktur baru

---

## 📞 Support

Jika mengalami masalah saat menjalankan seeder, periksa:

1. ✅ Pastikan database connection berfungsi
2. ✅ Pastikan semua migration sudah dijalankan
3. ✅ Pastikan tidak ada foreign key constraint error
4. ✅ Pastikan ada cukup space di database untuk menyimpan data

---

---

## 📌 Update Terbaru

### Perubahan Jenis Kegiatan (Desember 2025)

Sebagai catatan penting, jenis kegiatan "Jenis Kegiatan" telah diubah menjadi **"Jenis Kegiatan lainnya"** untuk konsistensi di seluruh sistem. Seeder akan otomatis membuat data dengan nama baru ini.

**Catatan:**

-   Data lama dengan "Jenis Kegiatan" akan tetap bisa dibaca karena adanya backward compatibility
-   Seeder baru akan selalu menggunakan "Jenis Kegiatan lainnya"
-   Semua validasi dan form sudah menggunakan nama baru

---

**Last Updated:** Desember 2025
