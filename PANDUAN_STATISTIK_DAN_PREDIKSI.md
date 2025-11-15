# 📊 Panduan Penggunaan Fitur Statistik & Prediksi

## 1. 📊 Statistik Kegiatan

### Cara Menggunakan:

#### A. Filter Data
1. **Filter Kelompok**: 
   - Pilih "Semua Kelompok" untuk melihat statistik semua kelompok
   - Atau pilih kelompok spesifik untuk melihat statistik kelompok tertentu

2. **Filter Bulan**:
   - Pilih bulan tertentu untuk melihat data bulan tersebut
   - Kosongkan untuk melihat data 12 bulan terakhir

3. **Reset Filter**:
   - Klik tombol "Reset Filter" untuk mengembalikan filter ke default

#### B. Grafik yang Ditampilkan:
1. **Jumlah Kegiatan per Bulan** (Bar Chart):
   - Menampilkan jumlah kegiatan yang dilakukan setiap bulan
   - Sumbu X: Bulan (12 bulan terakhir)
   - Sumbu Y: Jumlah kegiatan

2. **Rata-rata Durasi per Bulan** (Line Chart):
   - Menampilkan rata-rata durasi penyelesaian kegiatan per bulan
   - Sumbu X: Bulan (12 bulan terakhir)
   - Sumbu Y: Durasi (dalam hari)

#### C. Tabel Rekap Kegiatan:
- Menampilkan detail kegiatan (maksimal 100 data terbaru)
- Kolom: Kelompok, Tanggal Mulai, Tanggal Selesai, Durasi

### Cara Memasukkan Data Kegiatan:

Data kegiatan bisa dimasukkan melalui beberapa cara:

#### 1. Via Tinker (Untuk Testing):
```bash
php artisan tinker
```

```php
use App\Models\Kegiatan;
use Carbon\Carbon;

// Contoh data kegiatan
Kegiatan::create([
    'kelompok' => 'Kelompok 1',
    'tanggal_mulai' => Carbon::now()->subMonths(2)->startOfMonth(),
    'tanggal_selesai' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(5),
]);

Kegiatan::create([
    'kelompok' => 'Kelompok 1',
    'tanggal_mulai' => Carbon::now()->subMonths(1)->startOfMonth(),
    'tanggal_selesai' => Carbon::now()->subMonths(1)->startOfMonth()->addDays(7),
]);

Kegiatan::create([
    'kelompok' => 'Kelompok 2',
    'tanggal_mulai' => Carbon::now()->subMonths(2)->startOfMonth(),
    'tanggal_selesai' => Carbon::now()->subMonths(2)->startOfMonth()->addDays(3),
]);
```

#### 2. Via Seeder (Recommended):
Buat seeder untuk memasukkan data sample:
```bash
php artisan make:seeder KegiatanSeeder
```

#### 3. Via Form Input (Future Feature):
Bisa ditambahkan form untuk input data kegiatan manual

### Catatan Penting:
- **Nama Kelompok**: Pastikan nama kelompok di data kegiatan sesuai dengan `nama_kelompok` di tabel `kelompok`
- **Minimal Data**: Untuk prediksi, dibutuhkan minimal 12 bulan data
- **Durasi Otomatis**: Durasi akan dihitung otomatis dari tanggal_mulai dan tanggal_selesai

---

## 2. 🔮 Prediksi Waktu Penyelesaian

### Cara Menggunakan:

#### A. Form Prediksi:
1. **Pilih Kelompok**:
   - Pilih kelompok spesifik untuk prediksi kelompok tersebut
   - Atau pilih "Semua Kelompok" untuk prediksi agregat

2. **Pilih Bulan Target**:
   - Pilih bulan yang ingin diprediksi (format: YYYY-MM)
   - Contoh: 2025-12 untuk prediksi bulan Desember 2025

3. **Set Parameter** (α, β, γ):
   - **Alpha (α)**: Level smoothing parameter (0-1), default: 0.2
   - **Beta (β)**: Trend smoothing parameter (0-1), default: 0.1
   - **Gamma (γ)**: Seasonal smoothing parameter (0-1), default: 0.1
   - Nilai default biasanya sudah optimal untuk kebanyakan kasus

4. **Generate Prediksi**:
   - Klik tombol "🎯 Generate Prediksi"
   - Sistem akan menghitung prediksi menggunakan algoritma Holt-Winters

#### B. Hasil Prediksi:
1. **Bulan yang Diprediksi**: Bulan target prediksi
2. **Kelompok**: Kelompok yang diprediksi
3. **Hasil Prediksi**: Waktu penyelesaian yang diprediksi (dalam hari)
4. **Akurasi Model**: Tingkat akurasi model (dalam persen)

#### C. Grafik Prediksi:
- **Garis Biru**: Data historis (12 bulan terakhir)
- **Garis Merah (Putus-putus)**: Hasil prediksi untuk bulan berikutnya

#### D. Export & Reset:
- **Unduh PDF**: Export hasil prediksi ke PDF (belum diimplementasikan)
- **Unduh Excel**: Export hasil prediksi ke Excel (belum diimplementasikan)
- **Reset Data**: Hapus data prediksi yang telah dibuat

### Syarat Prediksi:
- **Minimal Data**: Dibutuhkan minimal 12 bulan data historis
- **Data Konsisten**: Data kegiatan harus konsisten per bulan
- **Nama Kelompok**: Nama kelompok harus sesuai dengan data di tabel `kelompok`

### Algoritma:
- **Metode**: Holt-Winters (Triple Exponential Smoothing)
- **Season Length**: 12 bulan (seasonality tahunan)
- **Forecast Horizon**: 1 bulan ke depan

---

## 3. 📝 Contoh Data Kegiatan

Untuk testing, berikut contoh struktur data kegiatan:

```php
// Data untuk 12 bulan terakhir
$kelompoks = ['Kelompok 1', 'Kelompok 2', 'Kelompok 3', 'Kelompok 4', 'Kelompok 5'];

for ($i = 11; $i >= 0; $i--) {
    $month = Carbon::now()->subMonths($i);
    
    foreach ($kelompoks as $kelompok) {
        // Setiap kelompok melakukan 2-5 kegiatan per bulan
        $jumlahKegiatan = rand(2, 5);
        
        for ($j = 0; $j < $jumlahKegiatan; $j++) {
            $tanggalMulai = $month->copy()->startOfMonth()->addDays(rand(0, 15));
            $durasi = rand(2, 10); // Durasi 2-10 hari
            $tanggalSelesai = $tanggalMulai->copy()->addDays($durasi - 1);
            
            Kegiatan::create([
                'kelompok' => $kelompok,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                // Durasi akan dihitung otomatis
            ]);
        }
    }
}
```

---

## 4. 🔧 Troubleshooting

### Problem: Tidak ada data yang ditampilkan
**Solusi**: 
- Pastikan ada data kegiatan di database
- Pastikan nama kelompok di data kegiatan sesuai dengan nama di tabel `kelompok`
- Cek filter yang digunakan

### Problem: Prediksi error "Data historis kurang"
**Solusi**:
- Pastikan ada minimal 12 bulan data untuk kelompok yang dipilih
- Pastikan data konsisten per bulan

### Problem: Grafik tidak muncul
**Solusi**:
- Pastikan Chart.js sudah ter-load (cek console browser)
- Pastikan ada data untuk ditampilkan
- Coba refresh halaman

### Problem: Filter tidak bekerja
**Solusi**:
- Pastikan nama kelompok di data kegiatan tepat sesuai dengan dropdown
- Cek format bulan (YYYY-MM)
- Coba reset filter

---

## 5. 💡 Tips Penggunaan

1. **Untuk Analisis**: Gunakan filter kelompok spesifik untuk analisis per kelompok
2. **Untuk Overview**: Gunakan "Semua Kelompok" untuk melihat gambaran keseluruhan
3. **Untuk Prediksi**: Pastikan data historis cukup (minimal 12 bulan) untuk hasil yang akurat
4. **Parameter Prediksi**: Gunakan nilai default (α=0.2, β=0.1, γ=0.1) untuk mulai, lalu sesuaikan jika perlu
5. **Akurasi**: Semakin tinggi akurasi model, semakin dapat diandalkan prediksinya

---

## 6. 📞 Bantuan

Jika mengalami masalah, pastikan:
1. Data kegiatan sudah ada di database
2. Nama kelompok sesuai dengan yang terdaftar
3. Format tanggal benar (YYYY-MM-DD)
4. Minimal 12 bulan data untuk prediksi



