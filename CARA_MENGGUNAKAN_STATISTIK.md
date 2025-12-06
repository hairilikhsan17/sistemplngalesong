# 📊 Cara Menggunakan Fitur Statistik Kegiatan

## 🚀 Langkah 1: Masukkan Data Kegiatan

Sebelum menggunakan fitur statistik, pastikan ada data kegiatan di database.

### Opsi A: Menggunakan Seeder (Recommended)

```bash
php artisan db:seed --class=KegiatanSeeder
```

Seeder ini akan membuat data kegiatan untuk semua kelompok yang terdaftar selama 12 bulan terakhir.

### Opsi B: Manual via Tinker

```bash
php artisan tinker
```

```php
use App\Models\Kegiatan;
use Carbon\Carbon;

Kegiatan::create([
    'kelompok' => 'Kelompok 1',  // Pastikan sesuai dengan nama_kelompok di tabel kelompok
    'tanggal_mulai' => Carbon::now()->subMonths(2)->startOfMonth(),
    'tanggal_selesai' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(5),
]);
```

## 📊 Langkah 2: Menggunakan Halaman Statistik

### 1. Akses Halaman Statistik

-   Login sebagai Admin (Atasan)
-   Klik menu **"Statistik & Prediksi"** di sidebar
-   Pilih **"📊 Statistik"**

### 2. Filter Data

#### Filter Kelompok:

-   **"Semua Kelompok"**: Menampilkan statistik semua kelompok
-   **Pilih Kelompok Spesifik**: Menampilkan statistik kelompok tertentu saja
    -   Contoh: "Kelompok 1", "Kelompok 2", dll

#### Filter Bulan:

-   **Kosongkan**: Menampilkan data 12 bulan terakhir
-   **Pilih Bulan**: Menampilkan data bulan tertentu (format: YYYY-MM)
    -   Contoh: "2025-11" untuk November 2025

#### Reset Filter:

-   Klik tombol **"Reset Filter"** untuk mengembalikan filter ke default

### 3. Melihat Grafik

Setelah memilih filter, grafik akan otomatis ter-update:

#### Grafik 1: Jumlah Kegiatan per Bulan

-   **Tipe**: Bar Chart (Grafik Batang)
-   **Menampilkan**: Jumlah kegiatan yang dilakukan setiap bulan
-   **Sumbu X**: Bulan (12 bulan terakhir)
-   **Sumbu Y**: Jumlah kegiatan

#### Grafik 2: Rata-rata Durasi per Bulan

-   **Tipe**: Line Chart (Grafik Garis)
-   **Menampilkan**: Rata-rata durasi penyelesaian kegiatan per bulan
-   **Sumbu X**: Bulan (12 bulan terakhir)
-   **Sumbu Y**: Durasi (dalam hari)

### 4. Melihat Tabel Rekap

Tabel di bawah grafik menampilkan:

-   **Kelompok**: Nama kelompok yang melakukan kegiatan
-   **Tanggal Mulai**: Tanggal mulai kegiatan
-   **Tanggal Selesai**: Tanggal selesai kegiatan
-   **Durasi**: Lama kegiatan (dalam hari)

Tabel menampilkan maksimal 100 data terbaru.

## 🔄 Refresh Data

Klik tombol **"Refresh Data"** di pojok kanan atas untuk:

-   Memuat ulang data terbaru dari database
-   Memperbarui grafik dan tabel

## ⚠️ Troubleshooting

### Problem: Tidak ada data yang ditampilkan

**Penyebab:**

1. Belum ada data kegiatan di database
2. Nama kelompok di data tidak sesuai dengan yang terdaftar
3. Filter yang dipilih tidak sesuai dengan data yang ada

**Solusi:**

1. Jalankan seeder: `php artisan db:seed --class=KegiatanSeeder`
2. Pastikan nama kelompok di data kegiatan sama persis dengan `nama_kelompok` di tabel `kelompok`
3. Coba ubah filter atau klik "Reset Filter"

### Problem: Grafik tidak muncul

**Penyebab:**

1. Chart.js belum ter-load
2. Tidak ada data untuk ditampilkan
3. Error JavaScript di console

**Solusi:**

1. Refresh halaman (F5)
2. Pastikan ada data kegiatan di database
3. Cek console browser (F12) untuk melihat error

### Problem: Filter tidak bekerja

**Penyebab:**

1. Nama kelompok tidak sesuai
2. Format bulan salah

**Solusi:**

1. Pastikan nama kelompok di dropdown sesuai dengan data
2. Format bulan harus YYYY-MM (contoh: 2025-11)
3. Coba reset filter

## 💡 Tips

1. **Untuk Overview**: Gunakan "Semua Kelompok" untuk melihat gambaran keseluruhan
2. **Untuk Analisis Detail**: Pilih kelompok spesifik untuk analisis lebih mendalam
3. **Untuk Perbandingan**: Bandingkan statistik beberapa kelompok dengan memilih satu per satu
4. **Refresh Rutin**: Refresh data secara berkala untuk melihat update terbaru

## 📝 Contoh Data

Contoh struktur data kegiatan:

```php
[
    'kelompok' => 'Kelompok 1',
    'tanggal_mulai' => '2025-10-01',
    'tanggal_selesai' => '2025-10-05',
    'durasi' => 5  // Otomatis dihitung
]
```

## 🔗 Link Terkait

-   **Panduan Lengkap**: `PANDUAN_STATISTIK_DAN_PREDIKSI.md`
-   **Prediksi**: Menu "🔮 Prediksi" di sidebar

---

**Catatan**: Pastikan data kegiatan sudah ada sebelum menggunakan fitur statistik. Jika belum ada, jalankan seeder terlebih dahulu.

















