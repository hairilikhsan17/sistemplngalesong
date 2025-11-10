# Prosedur Penelitian

## Sistem Prediksi Waktu Penyelesaian Kegiatan Lapangan PLN Galesong

---

## PENJELASAN PROSEDUR PENELITIAN

Prosedur penelitian dalam pengembangan sistem prediksi waktu penyelesaian kegiatan lapangan PLN Galesong dilakukan melalui tujuh tahapan utama yang saling terhubung dan berurutan, dimulai dari tahap persiapan dimana dilakukan identifikasi masalah, studi literatur tentang metode prediksi time series, dan perancangan sistem serta metodologi penelitian, kemudian dilanjutkan dengan tahap pengumpulan data yang mencakup observasi langsung di lapangan untuk mencatat waktu aktual penyelesaian kegiatan, wawancara terstruktur dengan supervisor distribusi dan petugas lapangan untuk memahami variabel-variabel yang mempengaruhi durasi kerja, serta dokumentasi data historis kegiatan lapangan minimal 12 bulan terakhir dari sistem monitoring internal dan laporan harian. Setelah data terkumpul, dilakukan tahap pengolahan data yang meliputi validasi dan cleaning data untuk memastikan kualitas data, agregasi data per bulan per kelompok kerja, dan identifikasi pola serta tren dalam data time series, kemudian data yang telah terolah digunakan dalam tahap analisis dan pemodelan dimana dilakukan analisis karakteristik data untuk mengidentifikasi komponen level, trend, dan seasonal, penentuan parameter optimal untuk algoritma Triple Exponential Smoothing (TES) yaitu alpha (α) untuk smoothing level, beta (β) untuk smoothing trend, dan gamma (γ) untuk smoothing seasonal, serta implementasi algoritma TES untuk membangun model prediksi yang dapat meramalkan waktu penyelesaian kegiatan di masa depan. Model yang telah dibangun kemudian diuji dalam tahap pengujian model melalui metode time series cross-validation dimana data historis dibagi menjadi data training untuk melatih model dan data testing untuk menguji akurasi prediksi, evaluasi akurasi menggunakan berbagai metrik seperti Mean Absolute Error (MAE), Mean Absolute Percentage Error (MAPE), dan Root Mean Squared Error (RMSE), serta validasi hasil prediksi dengan membandingkan nilai prediksi dengan data aktual yang baru tersedia untuk memastikan model dapat memberikan prediksi yang akurat dan dapat diandalkan. Setelah model terbukti akurat dan tervalidasi, dilakukan tahap implementasi sistem dimana dikembangkan aplikasi berbasis web yang mengintegrasikan model prediksi, dilakukan pengujian sistem secara keseluruhan untuk memastikan semua fitur berfungsi dengan baik, serta dilakukan training untuk pengguna sistem agar dapat menggunakan aplikasi dengan efektif. Tahap terakhir adalah tahap evaluasi dan monitoring yang dilakukan secara berkelanjutan, dimana dilakukan monitoring performa sistem secara berkala, evaluasi akurasi prediksi dengan data aktual baru yang terus masuk ke dalam sistem, update model jika diperlukan untuk meningkatkan akurasi atau menyesuaikan dengan perubahan pola data, serta perbaikan sistem berdasarkan feedback dari pengguna. Prosedur penelitian ini dirancang secara sistematis dan terstruktur untuk memastikan bahwa sistem prediksi yang dikembangkan dapat memberikan hasil yang akurat, dapat diandalkan, dan bermanfaat untuk perencanaan operasional distribusi listrik di PLN Galesong, dengan setiap tahapan memiliki kegiatan spesifik, output yang jelas, dan kriteria keberhasilan yang dapat diukur sehingga memungkinkan evaluasi progress penelitian dan identifikasi area yang perlu diperbaiki atau ditingkatkan.

---

## ALAT DAN BAHAN PENELITIAN

### A. Perangkat Keras

1. 1 Unit Laptop atau Komputer dengan spesifikasi minimal Processor Intel Core i5 atau AMD Ryzen 5, RAM 8 GB, Storage 256 GB

2. Ponsel (Sebagai alat pendukung untuk observasi langsung di lapangan dan dokumentasi kegiatan)

### B. Perangkat Lunak

1. Microsoft Windows 11 64-bit

2. Laragon atau XAMPP (Environment development lokal untuk Apache, MySQL, dan PHP)

3. Laravel 11 (Framework backend untuk pengembangan aplikasi web)

4. MySQL 5.7 atau lebih tinggi (Sistem manajemen database)

5. PHP 8.1 atau lebih tinggi (Bahasa pemrograman server-side)

6. Composer (Dependency manager untuk PHP)

7. Microsoft Office (Word, Excel, PowerPoint)

8. Google Chrome atau Mozilla Firefox (Browser web untuk testing aplikasi)

---

## TAHAPAN PENELITIAN

### 1. Tahap Persiapan

**Kegiatan:**

-   Identifikasi masalah dan kebutuhan penelitian
-   Studi literatur tentang metode prediksi time series
-   Perancangan sistem dan metodologi penelitian
-   Persiapan instrumen penelitian (panduan wawancara, format observasi)

**Output:**

-   Proposal penelitian
-   Instrumen pengumpulan data
-   Rancangan sistem

---

### 2. Tahap Pengumpulan Data

**Kegiatan:**

-   Observasi langsung di lapangan untuk mencatat waktu aktual penyelesaian
-   Wawancara dengan supervisor dan petugas lapangan
-   Pengumpulan data historis dari sistem monitoring (minimal 12 bulan)
-   Dokumentasi kegiatan operasional

**Output:**

-   Data primer (waktu aktual, jenis kegiatan, lokasi)
-   Data sekunder (data historis, laporan harian)
-   Dokumentasi kegiatan

---

### 3. Tahap Pengolahan Data

**Kegiatan:**

-   Validasi dan cleaning data
-   Agregasi data per bulan per kelompok
-   Identifikasi pola dan tren dalam data
-   Persiapan data untuk analisis

**Output:**

-   Data tervalidasi dan terstruktur
-   Data time series siap untuk analisis

---

### 4. Tahap Analisis dan Pemodelan

**Kegiatan:**

-   Analisis karakteristik data (level, trend, seasonal)
-   Penentuan parameter optimal (α, β, γ)
-   Implementasi algoritma Triple Exponential Smoothing (TES)
-   Pembuatan model prediksi per kelompok kerja

**Output:**

-   Model prediksi yang telah dilatih
-   Parameter optimal untuk setiap kelompok
-   Algoritma prediksi yang siap digunakan

---

### 5. Tahap Pengujian Model

**Kegiatan:**

-   Pengujian model dengan data historis (time series cross-validation)
-   Evaluasi akurasi prediksi (MAE, MAPE, RMSE)
-   Validasi hasil prediksi dengan data aktual
-   Optimasi parameter jika diperlukan

**Output:**

-   Hasil evaluasi akurasi model
-   Laporan pengujian model
-   Model yang telah divalidasi

---

### 6. Tahap Implementasi Sistem

**Kegiatan:**

-   Pengembangan sistem aplikasi berbasis web
-   Integrasi model prediksi ke dalam sistem
-   Pengujian sistem secara keseluruhan
-   Training pengguna sistem

**Output:**

-   Sistem aplikasi yang berfungsi
-   Dokumentasi sistem
-   User manual

---

### 7. Tahap Evaluasi dan Monitoring

**Kegiatan:**

-   Monitoring performa sistem secara berkala
-   Evaluasi akurasi prediksi dengan data aktual baru
-   Update model jika diperlukan
-   Perbaikan sistem berdasarkan feedback pengguna

**Output:**

-   Laporan evaluasi sistem
-   Update model prediksi
-   Rekomendasi perbaikan

---

## DIAGRAM ALIR PROSEDUR PENELITIAN

```
┌─────────────────────────────────────────────────────────────────┐
│                    TAHAP 1: PERSIAPAN                           │
│  • Identifikasi Masalah                                         │
│  • Studi Literatur                                              │
│  • Perancangan Sistem                                           │
│  • Persiapan Instrumen                                          │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│              TAHAP 2: PENGUMPULAN DATA                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │ Observasi    │  │ Wawancara    │  │ Dokumentasi  │         │
│  │ Langsung     │  │ Terstruktur  │  │ Data Historis│         │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘         │
│         │                 │                  │                  │
│         └─────────────────┴──────────────────┘                  │
│                            │                                    │
│                            ▼                                    │
│                  Data Primer & Sekunder                         │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│              TAHAP 3: PENGOLAHAN DATA                           │
│  • Validasi Data                                                │
│  • Cleaning Data                                                │
│  • Agregasi Data                                                │
│  • Identifikasi Pola                                            │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│           TAHAP 4: ANALISIS DAN PEMODELAN                       │
│  ┌──────────────────────────────────────────┐                  │
│  │  Analisis Karakteristik Data             │                  │
│  │  • Level                                  │                  │
│  │  • Trend                                  │                  │
│  │  • Seasonal                               │                  │
│  └──────────────┬───────────────────────────┘                  │
│                 │                                               │
│                 ▼                                               │
│  ┌──────────────────────────────────────────┐                  │
│  │  Penentuan Parameter Optimal             │                  │
│  │  • α (alpha) = 0.4                       │                  │
│  │  • β (beta) = 0.3                        │                  │
│  │  • γ (gamma) = 0.3                       │                  │
│  └──────────────┬───────────────────────────┘                  │
│                 │                                               │
│                 ▼                                               │
│  ┌──────────────────────────────────────────┐                  │
│  │  Implementasi Algoritma TES              │                  │
│  │  • Triple Exponential Smoothing          │                  │
│  │  • Perhitungan Level, Trend, Forecast    │                  │
│  └──────────────┬───────────────────────────┘                  │
│                 │                                               │
│                 ▼                                               │
│              Model Prediksi                                     │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│              TAHAP 5: PENGUJIAN MODEL                           │
│  ┌──────────────────────────────────────────┐                  │
│  │  Time Series Cross-Validation            │                  │
│  │  • Data Training (8-10 bulan)            │                  │
│  │  • Data Testing (2-4 bulan)              │                  │
│  └──────────────┬───────────────────────────┘                  │
│                 │                                               │
│                 ▼                                               │
│  ┌──────────────────────────────────────────┐                  │
│  │  Evaluasi Akurasi                        │                  │
│  │  • MAE (Mean Absolute Error)             │                  │
│  │  • MAPE (Mean Absolute Percentage Error) │                  │
│  │  • RMSE (Root Mean Squared Error)        │                  │
│  └──────────────┬───────────────────────────┘                  │
│                 │                                               │
│                 ▼                                               │
│  ┌──────────────────────────────────────────┐                  │
│  │  Validasi dengan Data Aktual             │                  │
│  │  • Bandingkan Prediksi vs Aktual         │                  │
│  │  • Hitung Error dan Akurasi              │                  │
│  └──────────────┬───────────────────────────┘                  │
│                 │                                               │
│                 ▼                                               │
│          Model Teruji & Tervalidasi                            │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│            TAHAP 6: IMPLEMENTASI SISTEM                         │
│  • Pengembangan Aplikasi Web                                   │
│  • Integrasi Model Prediksi                                    │
│  • Pengujian Sistem                                            │
│  • Training Pengguna                                           │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│         TAHAP 7: EVALUASI DAN MONITORING                        │
│  • Monitoring Performa Sistem                                  │
│  • Evaluasi Akurasi Berkelanjutan                              │
│  • Update Model jika Diperlukan                                │
│  • Perbaikan Sistem                                            │
└─────────────────────────────────────────────────────────────────┘
```

---

## DIAGRAM ALIR PROSES PREDIKSI

```
┌─────────────────────────────────────────────────────────────────┐
│                    INPUT: Data Historis                         │
│              (Minimal 12 bulan per kelompok)                    │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
                    ┌───────────────┐
                    │  Inisialisasi │
                    │  • S₀ (Level) │
                    │  • b₀ (Trend) │
                    └───────┬───────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │      Untuk Setiap Periode (t = 1 to n)   │
        └───────────────────┬───────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │       1. Ambil Data Aktual (Yₜ)          │
        └───────────────────┬───────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │   2. Hitung Level Baru (Sₜ)              │
        │   Sₜ = αYₜ + (1-α)(Sₜ₋₁ + bₜ₋₁)         │
        └───────────────────┬───────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │   3. Hitung Trend Baru (bₜ)              │
        │   bₜ = β(Sₜ - Sₜ₋₁) + (1-β)bₜ₋₁         │
        └───────────────────┬───────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │   4. Hitung Forecast (Fₜ₊₁)              │
        │   Fₜ₊₁ = Sₜ + m × bₜ                     │
        └───────────────────┬───────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │   5. Evaluasi Akurasi                    │
        │   Error = |Yₜ - Fₜ|                      │
        └───────────────────┬───────────────────────┘
                            │
                            ▼
                    ┌───────────────┐
                    │  Periode      │
                    │  Berikutnya?  │
                    └───┬───────┬───┘
                        │ Ya    │ Tidak
                        ▼       ▼
                ┌──────────┐  ┌──────────────┐
                │  t = t+1 │  │  Prediksi    │
                │          │  │  Periode     │
                │          │  │  Mendatang   │
                └────┬─────┘  └──────┬───────┘
                     │               │
                     └───────┬───────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │   OUTPUT:       │
                    │   • Forecast    │
                    │   • Akurasi     │
                    │   • Level       │
                    │   • Trend       │
                    └─────────────────┘
```

---

## DIAGRAM ALIR PENGUJIAN MODEL

```
┌─────────────────────────────────────────────────────────────────┐
│                    Data Historis (12 bulan)                     │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
        ┌───────────────────────────────────────────┐
        │         Pembagian Data                    │
        │  • Training Data (8-10 bulan)             │
        │  • Testing Data (2-4 bulan)               │
        └───────────┬───────────────┬───────────────┘
                    │               │
                    ▼               ▼
        ┌──────────────────┐  ┌──────────────────┐
        │  Training Data   │  │  Testing Data    │
        │  • Fit Model     │  │  • Prediksi      │
        │  • Tentukan      │  │  • Evaluasi      │
        │    Parameter     │  │    Akurasi       │
        └──────────┬───────┘  └────────┬─────────┘
                   │                   │
                   └─────────┬─────────┘
                             │
                             ▼
        ┌───────────────────────────────────────────┐
        │         Model Terlatih                    │
        │  • Parameter Optimal (α, β, γ)            │
        │  • Level dan Trend Terakhir               │
        └───────────┬───────────────────────────────┘
                    │
                    ▼
        ┌───────────────────────────────────────────┐
        │      Prediksi pada Testing Data           │
        │  • Forecast untuk setiap periode          │
        └───────────┬───────────────────────────────┘
                    │
                    ▼
        ┌───────────────────────────────────────────┐
        │      Evaluasi Akurasi                     │
        │  • MAE = Σ|Yₜ - Fₜ| / n                   │
        │  • MAPE = Σ|Yₜ - Fₜ|/Yₜ × 100% / n       │
        │  • RMSE = √(Σ(Yₜ - Fₜ)² / n)             │
        └───────────┬───────────────────────────────┘
                    │
                    ▼
        ┌───────────────────────────────────────────┐
        │      Validasi dengan Data Aktual          │
        │  • Bandingkan Prediksi vs Aktual          │
        │  • Hitung Error                           │
        │  • Hitung Akurasi                         │
        └───────────┬───────────────────────────────┘
                    │
                    ▼
        ┌───────────────────────────────────────────┐
        │      Akurasi Memenuhi Kriteria?           │
        │      (Target: > 85%)                      │
        └───────────┬───────────────┬───────────────┘
                    │ Ya            │ Tidak
                    ▼               ▼
        ┌──────────────────┐  ┌──────────────────┐
        │  Model Siap      │  │  Optimasi        │
        │  Digunakan       │  │  Parameter       │
        └──────────────────┘  └────────┬─────────┘
                                       │
                                       └─────┐
                                             │
                                             ▼
                                    Kembali ke Training
```

---

## RINGKASAN PROSEDUR PENELITIAN

### Alur Singkat:

1. **Persiapan** → Identifikasi masalah, studi literatur, perancangan sistem
2. **Pengumpulan Data** → Observasi, wawancara, dokumentasi (data primer & sekunder)
3. **Pengolahan Data** → Validasi, cleaning, agregasi data
4. **Analisis & Pemodelan** → Implementasi algoritma TES, pembuatan model prediksi
5. **Pengujian Model** → Cross-validation, evaluasi akurasi, validasi
6. **Implementasi Sistem** → Pengembangan aplikasi, integrasi model, testing
7. **Evaluasi & Monitoring** → Monitoring performa, update model, perbaikan

### Kriteria Keberhasilan:

-   **Akurasi Prediksi:** ≥ 85% (MAPE ≤ 15%)
-   **Error Rata-rata:** ≤ 0.25 jam (15 menit)
-   **Sistem Berfungsi:** Aplikasi dapat menghasilkan prediksi dengan baik
-   **Pengguna Puas:** Sistem mudah digunakan dan bermanfaat

### Waktu Penelitian:

-   **Tahap 1-2 (Persiapan & Pengumpulan Data):** 2-3 bulan
-   **Tahap 3-4 (Pengolahan & Analisis):** 1-2 bulan
-   **Tahap 5 (Pengujian Model):** 1 bulan
-   **Tahap 6 (Implementasi Sistem):** 2-3 bulan
-   **Tahap 7 (Evaluasi & Monitoring):** 1-2 bulan (berkelanjutan)

**Total:** 7-11 bulan

---

## DIAGRAM KONTEKSTUAL SISTEM

```
┌─────────────────────────────────────────────────────────────────┐
│                    SISTEM PREDIKSI                              │
│              Waktu Penyelesaian Kegiatan                        │
└───────────────────────────┬─────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐   ┌──────────────┐   ┌──────────────┐
│   Input      │   │   Proses     │   │   Output     │
│              │   │              │   │              │
│ • Data       │──▶│ • Algoritma  │──▶│ • Forecast   │
│   Historis   │   │   TES        │   │ • Akurasi    │
│ • Parameter  │   │ • Perhitungan│   │ • Level      │
│   (α, β, γ)  │   │   Level/     │   │ • Trend      │
│              │   │   Trend      │   │              │
└──────────────┘   └──────────────┘   └──────────────┘
```

---

## KESIMPULAN

Prosedur penelitian ini dirancang secara sistematis untuk mengembangkan sistem prediksi waktu penyelesaian kegiatan lapangan menggunakan metode Triple Exponential Smoothing (TES). Prosedur ini mencakup tujuh tahapan utama mulai dari persiapan hingga evaluasi dan monitoring berkelanjutan. Setiap tahapan memiliki kegiatan spesifik dan output yang jelas, sehingga memastikan penelitian berjalan terstruktur dan mencapai tujuan yang diinginkan.
