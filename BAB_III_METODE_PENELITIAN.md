# BAB III

## METODE PENELITIAN

---

## 3.1 Jenis Penelitian

Penelitian ini menggunakan jenis **Penelitian Terapan (Applied Research)** dengan pendekatan **Kuantitatif**. Penelitian terapan dipilih karena penelitian ini bertujuan untuk mengembangkan sistem prediksi yang dapat langsung diterapkan di lingkungan kerja PLN Galesong untuk membantu perencanaan operasional dan pengambilan keputusan. Pendekatan kuantitatif digunakan karena penelitian ini melibatkan pengolahan data numerik (waktu penyelesaian kegiatan) dan penerapan algoritma matematis (Triple Exponential Smoothing) untuk menghasilkan prediksi yang dapat diukur secara objektif.

Penelitian ini termasuk dalam kategori penelitian pengembangan sistem (system development research) karena menghasilkan produk berupa sistem aplikasi berbasis web yang mengintegrasikan metode prediksi time series untuk memprediksi waktu penyelesaian kegiatan lapangan.

---

## 3.2 Lokasi dan Waktu Penelitian

### 3.2.1 Lokasi Penelitian

Penelitian ini dilaksanakan di **PLN Galesong**, yang berlokasi di **Kabupaten Takalar, Sulawesi Selatan**. Pemilihan lokasi ini didasarkan pada kebutuhan untuk mengatasi permasalahan prediksi waktu penyelesaian kegiatan lapangan di unit distribusi PLN Galesong, serta ketersediaan data historis kegiatan lapangan yang diperlukan untuk pengembangan sistem prediksi.

### 3.2.2 Waktu Penelitian

Penelitian ini dilaksanakan pada periode **Desember 2025 sampai dengan bulan Februari 2026**, dengan rincian sebagai berikut:

-   **Desember 2025**: Tahap persiapan, pengumpulan data, dan analisis kebutuhan sistem
-   **Januari 2026**: Tahap pengolahan data, implementasi algoritma, dan pengembangan sistem
-   **Februari 2026**: Tahap pengujian sistem, evaluasi, dan penyusunan dokumentasi

---

## 3.3 Subjek dan Objek Penelitian

### 3.3.1 Subjek Penelitian

Subjek penelitian dalam penelitian ini adalah:

1. **Karyawan/Kelompok Kerja Lapangan** di PLN Galesong yang terlibat langsung dalam kegiatan operasional lapangan, seperti:

    - Kelompok kerja perbaikan KWH
    - Kelompok kerja pemeliharaan pengkabelan
    - Kelompok kerja pengecekan gardu
    - Kelompok kerja penanganan gangguan

2. **Atasan/Admin** di PLN Galesong yang bertanggung jawab dalam pengelolaan dan pemantauan kegiatan lapangan serta perencanaan operasional.

### 3.3.2 Objek Penelitian

Objek penelitian dalam penelitian ini adalah:

1. **Data Kegiatan Lapangan**, yang meliputi:

    - Data laporan kerja harian karyawan
    - Data job pekerjaan (perbaikan KWH, pemeliharaan pengkabelan, pengecekan gardu, penanganan gangguan)
    - Data durasi/waktu penyelesaian kegiatan lapangan
    - Data historis kegiatan lapangan minimal 12 bulan terakhir

2. **Sistem Prediksi Waktu Penyelesaian** yang dikembangkan menggunakan metode Triple Exponential Smoothing untuk memprediksi waktu penyelesaian kegiatan lapangan di masa depan.

---

## 3.4 Sumber dan Teknik Pengumpulan Data

### 3.4.1 Sumber Data

#### a. Data Primer

Data primer diperoleh melalui:

1. **Wawancara Terstruktur** dengan:

    - Supervisor distribusi PLN Galesong untuk memahami proses operasional dan kebutuhan sistem
    - Petugas lapangan untuk memahami variabel-variabel yang mempengaruhi durasi penyelesaian kegiatan
    - Admin sistem untuk memahami alur kerja dan kebutuhan fitur aplikasi

2. **Observasi Langsung** di lapangan untuk:
    - Mencatat waktu aktual penyelesaian kegiatan lapangan
    - Mengamati proses kerja dan faktor-faktor yang mempengaruhi durasi
    - Mendokumentasikan jenis-jenis kegiatan yang dilakukan

#### b. Data Sekunder

Data sekunder diperoleh dari:

1. **Laporan Pekerjaan** yang tersimpan dalam sistem monitoring internal PLN Galesong
2. **Data Job Pekerjaan** yang berisi detail pekerjaan yang telah dilakukan oleh setiap kelompok kerja
3. **Data Durasi Penyelesaian Kegiatan** yang tercatat dalam sistem pelaporan harian
4. **Dokumentasi Kegiatan Operasional** berupa arsip laporan bulanan dan tahunan

### 3.4.2 Teknik Pengumpulan Data

Teknik pengumpulan data yang digunakan dalam penelitian ini adalah:

1. **Observasi**

    - Observasi langsung terhadap proses kegiatan lapangan
    - Pencatatan waktu aktual penyelesaian kegiatan
    - Dokumentasi visual kegiatan lapangan

2. **Dokumentasi**

    - Pengumpulan data historis dari sistem monitoring internal
    - Pengumpulan laporan pekerjaan dan job pekerjaan
    - Pengumpulan data durasi penyelesaian kegiatan minimal 12 bulan terakhir

3. **Wawancara**
    - Wawancara terstruktur dengan supervisor dan petugas lapangan
    - Wawancara untuk validasi kebutuhan sistem dan fitur aplikasi
    - Wawancara untuk validasi hasil prediksi dengan data aktual

---

## 3.5 Variabel Penelitian dan Indikator

### 3.5.1 Variabel Input

Variabel input yang digunakan dalam sistem prediksi adalah:

1. **Durasi Pekerjaan Sebelumnya**

    - **Indikator**: Waktu penyelesaian kegiatan dalam satuan jam untuk setiap periode (bulanan)
    - **Sumber Data**: Data historis dari laporan kerja dan job pekerjaan

2. **Waktu Mulai dan Selesai**

    - **Indikator**: Timestamp mulai dan selesai kegiatan
    - **Sumber Data**: Data dari input laporan kerja dan job pekerjaan

3. **Jenis Pekerjaan**

    - **Indikator**: Kategori pekerjaan (Perbaikan KWH, Pemeliharaan Pengkabelan, Pengecekan Gardu, Penanganan Gangguan)
    - **Sumber Data**: Data job pekerjaan

4. **Frekuensi Job**

    - **Indikator**: Jumlah job pekerjaan per periode (bulanan)
    - **Sumber Data**: Agregasi data job pekerjaan per bulan

5. **Volume Pekerjaan**

    - **Indikator**: Total jumlah kegiatan yang dilakukan per periode
    - **Sumber Data**: Agregasi data laporan kerja per bulan

6. **Data Kelompok Kerja**
    - **Indikator**: Identitas kelompok kerja yang melakukan kegiatan
    - **Sumber Data**: Data kelompok dari sistem

### 3.5.2 Variabel Output

Variabel output dari sistem prediksi adalah:

1. **Prediksi Waktu Penyelesaian**

    - **Indikator**: Waktu penyelesaian yang diprediksi untuk periode berikutnya (dalam satuan jam)
    - **Metode Perhitungan**: Triple Exponential Smoothing (Holt-Winters)
    - **Parameter**: Level (Sₜ), Trend (bₜ), dan Seasonal (γ)

2. **Tingkat Akurasi Prediksi**

    - **Indikator**:
        - Mean Absolute Error (MAE)
        - Mean Absolute Percentage Error (MAPE)
        - Root Mean Squared Error (RMSE)
    - **Metode Evaluasi**: Perbandingan nilai prediksi dengan data aktual

3. **Analisis Statistik**
    - **Indikator**:
        - Rata-rata waktu penyelesaian per kelompok
        - Tren performa kelompok
        - Distribusi jenis pekerjaan
        - Ranking performa kelompok

### 3.5.3 Indikator Time Series

Data yang digunakan dalam prediksi berbentuk time series dengan indikator:

-   **Periode Waktu**: Data bulanan (minimal 12 bulan)
-   **Unit Waktu**: Bulan (Januari, Februari, Maret, dst.)
-   **Nilai**: Waktu penyelesaian rata-rata per bulan (dalam jam)
-   **Pola Data**: Level, Trend, dan Seasonal (jika ada)

---

## 3.6 Prosedur Penelitian / Alur Penelitian

Prosedur penelitian dalam pengembangan sistem prediksi waktu penyelesaian kegiatan lapangan PLN Galesong dilakukan melalui tujuh tahapan utama yang saling terhubung dan berurutan:

### 3.6.1 Tahap 1: Identifikasi Masalah

**Kegiatan:**

-   Identifikasi permasalahan prediksi waktu penyelesaian kegiatan lapangan di PLN Galesong
-   Analisis kebutuhan sistem prediksi
-   Studi literatur tentang metode prediksi time series
-   Perancangan sistem dan metodologi penelitian

**Output:**

-   Proposal penelitian
-   Rancangan sistem
-   Instrumen pengumpulan data

### 3.6.2 Tahap 2: Pengumpulan Data Kegiatan Lapangan

**Kegiatan:**

-   Observasi langsung di lapangan untuk mencatat waktu aktual penyelesaian
-   Wawancara dengan supervisor distribusi dan petugas lapangan
-   Pengumpulan data historis dari sistem monitoring internal (minimal 12 bulan)
-   Dokumentasi kegiatan operasional

**Output:**

-   Data primer (waktu aktual, jenis kegiatan, lokasi)
-   Data sekunder (data historis, laporan harian)
-   Dokumentasi kegiatan

### 3.6.3 Tahap 3: Membersihkan dan Mempersiapkan Dataset

**Kegiatan:**

-   Validasi dan cleaning data untuk memastikan kualitas data
-   Agregasi data per bulan per kelompok kerja
-   Identifikasi pola dan tren dalam data time series
-   Persiapan data untuk analisis

**Output:**

-   Data tervalidasi dan terstruktur
-   Data time series siap untuk analisis
-   Identifikasi pola data (level, trend, seasonal)

### 3.6.4 Tahap 4: Analisis Pola Time Series

**Kegiatan:**

-   Analisis karakteristik data untuk mengidentifikasi komponen level, trend, dan seasonal
-   Visualisasi data time series untuk memahami pola
-   Identifikasi outlier dan data yang tidak valid
-   Analisis distribusi data

**Output:**

-   Analisis karakteristik data
-   Visualisasi pola time series
-   Data yang siap untuk pemodelan

### 3.6.5 Tahap 5: Menerapkan Metode Triple Exponential Smoothing

**Kegiatan:**

-   Penentuan parameter optimal untuk algoritma TES:
    -   α (alpha) = 0.4 untuk smoothing level
    -   β (beta) = 0.3 untuk smoothing trend
    -   γ (gamma) = 0.3 untuk smoothing seasonal
-   Implementasi algoritma Triple Exponential Smoothing (Holt-Winters)
-   Pembuatan model prediksi per kelompok kerja
-   Perhitungan level, trend, dan forecast

**Output:**

-   Model prediksi yang telah dilatih
-   Parameter optimal untuk setiap kelompok
-   Algoritma prediksi yang siap digunakan

### 3.6.6 Tahap 6: Membangun Sistem Prediksi Berbasis Web

**Kegiatan:**

-   Pengembangan aplikasi berbasis web menggunakan framework Laravel
-   Integrasi model prediksi ke dalam sistem
-   Pengembangan fitur-fitur sistem:
    -   Dashboard untuk admin dan karyawan
    -   Manajemen data kelompok dan karyawan
    -   Input laporan kerja dan job pekerjaan
    -   Pemantauan laporan dan job pekerjaan
    -   Generate prediksi
    -   Statistik dan analisis
    -   Export data ke Excel
-   Pengujian fungsional sistem

**Output:**

-   Sistem aplikasi yang berfungsi
-   Dokumentasi sistem
-   User manual

### 3.6.7 Tahap 7: Implementasi Use Case (Admin & Karyawan)

**Kegiatan:**

-   Implementasi use case untuk Admin (Atasan):
    -   Login, Dashboard, Kelola Data Kelompok, Kelola Data Karyawan
    -   Pemantauan Laporan, Pemantauan Job Pekerjaan
    -   Generate Prediksi, Lihat Statistik
    -   Export Data, Kelola Excel, Pengaturan Admin
-   Implementasi use case untuk Karyawan (Kelompok):
    -   Login, Lihat Dashboard Kelompok
    -   Input Laporan Kerja, Input Job Pekerjaan
    -   Export Data Kelompok, Pengaturan Kelompok

**Output:**

-   Sistem dengan fitur lengkap sesuai use case
-   Interface pengguna yang user-friendly

### 3.6.8 Tahap 8: Menghasilkan Grafik, Laporan, dan Prediksi

**Kegiatan:**

-   Pengembangan fitur visualisasi data:
    -   Grafik line chart untuk tren performa bulanan
    -   Grafik doughnut chart untuk distribusi pekerjaan
    -   Grafik bar chart untuk perbandingan kelompok
-   Pengembangan fitur laporan:
    -   Laporan statistik performa
    -   Laporan prediksi waktu penyelesaian
    -   Laporan perbandingan kelompok
-   Pengembangan fitur prediksi:
    -   Generate prediksi berdasarkan data historis
    -   Tampilan hasil prediksi dengan tingkat akurasi
    -   Visualisasi prediksi dalam bentuk grafik

**Output:**

-   Grafik dan visualisasi data yang informatif
-   Laporan yang dapat diekspor ke Excel
-   Hasil prediksi yang akurat

### 3.6.9 Tahap 9: Melakukan Pengujian dan Evaluasi Akurasi

**Kegiatan:**

-   Pengujian model dengan data historis (time series cross-validation)
-   Evaluasi akurasi prediksi menggunakan metrik:
    -   Mean Absolute Error (MAE)
    -   Mean Absolute Percentage Error (MAPE)
    -   Root Mean Squared Error (RMSE)
-   Validasi hasil prediksi dengan data aktual yang baru tersedia
-   Pengujian fungsional sistem secara keseluruhan
-   Optimasi parameter jika diperlukan

**Output:**

-   Hasil evaluasi akurasi model
-   Laporan pengujian sistem
-   Model yang telah divalidasi dan siap digunakan

---

## 3.7 Desain Sistem

Desain sistem menjelaskan struktur dan arsitektur sistem prediksi waktu penyelesaian kegiatan lapangan yang dikembangkan. Desain sistem ini mencakup diagram-diagram yang menggambarkan alur kerja, interaksi pengguna, dan proses prediksi.

### 3.7.1 Use Case Diagram

Use Case Diagram menggambarkan interaksi antara aktor (Admin/Atasan dan Karyawan/Kelompok) dengan sistem. Sistem ini memiliki dua aktor utama:

Use Case Diagram merupakan representasi visual yang menunjukkan fungsi-fungsi yang dapat dilakukan oleh setiap aktor dalam sistem. Diagram ini membantu dalam memahami kebutuhan pengguna, merancang antarmuka sistem, dan memastikan bahwa semua kebutuhan fungsional telah terpenuhi. Setiap use case dalam diagram ini merepresentasikan satu fungsi atau fitur yang dapat diakses oleh aktor tertentu, dimana hubungan antara aktor dan use case ditunjukkan dengan garis yang menghubungkan keduanya. Use Case Diagram ini juga membantu dalam identifikasi batasan akses antara Admin dan Karyawan, dimana Admin memiliki akses yang lebih luas untuk mengelola dan memantau seluruh sistem, sementara Karyawan memiliki akses terbatas pada fitur-fitur yang relevan dengan pekerjaan mereka. Dengan demikian, Use Case Diagram ini menjadi dasar untuk pengembangan sistem yang memastikan setiap aktor dapat melakukan tugas mereka dengan efisien sesuai dengan peran dan tanggung jawab masing-masing.

#### a. Aktor: Admin/Atasan

Use case yang dapat dilakukan oleh Admin/Atasan:

1. **Login** - Autentikasi untuk mengakses sistem
2. **Lihat Dashboard** - Melihat dashboard dengan statistik lengkap tentang laporan, karyawan, dan kelompok
3. **Kelola Data Kelompok** - Membuat, membaca, memperbarui, dan menghapus data kelompok kerja
4. **Kelola Data Karyawan** - Membuat, membaca, memperbarui, dan menghapus data karyawan
5. **Pemantauan Laporan** - Melihat, memfilter, dan memantau semua laporan kerja dari semua kelompok
6. **Pemantauan Job Pekerjaan** - Melihat, memfilter, dan memantau semua job pekerjaan dari semua kelompok
7. **Generate Prediksi** - Menghasilkan prediksi waktu penyelesaian berdasarkan data laporan dan job pekerjaan
8. **Lihat Statistik** - Melihat statistik detail dan grafik analisis data
9. **Export Data** - Mengekspor data ke format Excel untuk keperluan laporan
10. **Kelola Excel** - Mengunggah, mengelola, dan mengunduh file Excel
11. **Pengaturan Admin** - Mengatur profil, pengaturan sistem, dan konfigurasi aplikasi

#### b. Aktor: Karyawan/Kelompok

Use case yang dapat dilakukan oleh Karyawan/Kelompok:

1. **Login** - Autentikasi untuk mengakses sistem
2. **Lihat Dashboard Kelompok** - Melihat dashboard personal dengan statistik kelompok mereka
3. **Input Laporan Kerja** - Membuat, melihat, mengedit, dan menghapus laporan kerja harian
4. **Input Job Pekerjaan** - Membuat, melihat, mengedit, dan menghapus job pekerjaan
5. **Export Data Kelompok** - Mengekspor data laporan dan job pekerjaan kelompok mereka ke format Excel
6. **Pengaturan Kelompok** - Mengatur profil kelompok, akun, dan notifikasi

Penjelasan detail setiap use case dalam sistem adalah sebagai berikut. **Use Case Login** digunakan oleh kedua aktor (Admin dan Karyawan) sebagai proses autentikasi awal dimana pengguna memasukkan username dan password untuk mengakses sistem, setelah berhasil login sistem akan mengarahkan pengguna ke dashboard sesuai dengan peran mereka. **Use Case Lihat Dashboard** untuk Admin menampilkan statistik lengkap tentang total laporan kerja, total job pekerjaan, jumlah kelompok aktif, jumlah karyawan aktif, rata-rata waktu penyelesaian, dan tren performa bulanan dari semua kelompok, sedangkan **Use Case Lihat Dashboard Kelompok** untuk Karyawan menampilkan statistik personal kelompok mereka seperti laporan kerja kelompok bulan ini, job pekerjaan kelompok bulan ini, rata-rata waktu penyelesaian kelompok, dan statistik performa kelompok. **Use Case Kelola Data Kelompok** memungkinkan Admin untuk melakukan operasi CRUD (Create, Read, Update, Delete) pada data kelompok kerja termasuk menambah kelompok baru, melihat daftar kelompok, mengedit informasi kelompok, dan menghapus kelompok yang tidak aktif. **Use Case Kelola Data Karyawan** memungkinkan Admin untuk mengelola data karyawan termasuk menambah karyawan baru, melihat daftar karyawan, mengedit informasi karyawan, menghapus karyawan, dan mengaitkan karyawan dengan kelompok kerja tertentu. **Use Case Pemantauan Laporan** memungkinkan Admin untuk melihat, memfilter berdasarkan kelompok, tanggal, atau instansi, mencari laporan berdasarkan kata kunci, melihat detail laporan lengkap termasuk dokumentasi, dan mengekspor data laporan ke Excel. **Use Case Pemantauan Job Pekerjaan** memungkinkan Admin untuk melihat semua job pekerjaan dari semua kelompok, memfilter berdasarkan kelompok, tanggal, atau jenis pekerjaan, melihat statistik job seperti jumlah job per jenis pekerjaan dan rata-rata waktu penyelesaian, serta mengekspor data job ke Excel. **Use Case Generate Prediksi** merupakan fitur inti sistem dimana Admin dapat menghasilkan prediksi waktu penyelesaian dengan memilih kelompok kerja, jenis data (berdasarkan laporan atau job pekerjaan), mengatur parameter α, β, γ, dan sistem akan menjalankan algoritma Triple Exponential Smoothing untuk menghasilkan prediksi beserta tingkat akurasinya. **Use Case Lihat Statistik** menampilkan analisis data yang lebih detail termasuk grafik performa bulanan kelompok (line chart), distribusi jenis pekerjaan per kelompok (doughnut chart), summary cards (kelompok terbaik, rata-rata waktu, tren performa, target pencapaian), ranking kelompok, dan tabel perbandingan metrik antar kelompok. **Use Case Export Data** memungkinkan Admin untuk mengekspor berbagai data ke format Excel termasuk data laporan, data job pekerjaan, data statistik, dan data prediksi untuk keperluan pelaporan dan dokumentasi. **Use Case Kelola Excel** memungkinkan Admin untuk mengunggah file Excel, mengelola file yang sudah diunggah, dan mengunduh file Excel yang diperlukan. **Use Case Pengaturan Admin** memungkinkan Admin untuk mengatur profil admin, mengubah password, mengatur konfigurasi sistem, dan mengatur preferensi aplikasi. **Use Case Input Laporan Kerja** memungkinkan Karyawan untuk membuat laporan kerja harian dengan mengisi informasi seperti hari, tanggal, nama, instansi, jabatan, alamat tujuan, dan upload dokumentasi, serta dapat melihat, mengedit, dan menghapus laporan yang telah dibuat. **Use Case Input Job Pekerjaan** memungkinkan Karyawan untuk mencatat job pekerjaan yang telah dilakukan dengan mengisi jenis pekerjaan (Perbaikan KWH, Pemeliharaan Pengkabelan, Pengecekan Gardu, Penanganan Gangguan), lokasi, tanggal, dan waktu penyelesaian dalam menit, serta dapat melihat, mengedit, dan menghapus job yang telah dibuat. **Use Case Export Data Kelompok** memungkinkan Karyawan untuk mengekspor data laporan dan job pekerjaan kelompok mereka ke format Excel untuk keperluan dokumentasi dan pelaporan internal kelompok. **Use Case Pengaturan Kelompok** memungkinkan Karyawan untuk mengatur profil kelompok, mengubah password akun kelompok, dan mengatur notifikasi sesuai kebutuhan.

### 3.7.2 Activity Diagram

Activity Diagram menggambarkan alur proses dalam sistem, antara lain:

Activity Diagram dalam sistem ini menjelaskan urutan aktivitas dan alur kerja yang terjadi dalam setiap proses penting. Setiap activity diagram menunjukkan langkah-langkah yang harus dilakukan oleh sistem dan pengguna, mulai dari input awal hingga output akhir. Diagram ini membantu memahami bagaimana sistem memproses data, melakukan validasi, menyimpan informasi, dan menghasilkan output yang diinginkan. Activity diagram juga menunjukkan decision point (titik keputusan) dimana sistem harus memilih alur tertentu berdasarkan kondisi tertentu, serta menunjukkan paralelisme aktivitas yang dapat dilakukan secara bersamaan. Dengan activity diagram, dapat dipahami bagaimana sistem menangani berbagai skenario, termasuk penanganan error dan validasi data, sehingga memastikan sistem berjalan dengan benar dan dapat diandalkan.

1. **Alur Login**: Proses autentikasi pengguna (Admin atau Karyawan)
2. **Alur Input Laporan Kerja**: Proses input, validasi, dan penyimpanan data laporan kerja
3. **Alur Input Job Pekerjaan**: Proses input, validasi, dan penyimpanan data job pekerjaan
4. **Alur Generate Prediksi**: Proses pengambilan data historis, perhitungan TES, dan menghasilkan prediksi
5. **Alur Export Data**: Proses pengambilan data, format ke Excel, dan download file

### 3.7.3 Flowchart Proses Prediksi

Flowchart proses prediksi menggambarkan alur perhitungan prediksi menggunakan metode Triple Exponential Smoothing:

```
┌─────────────────────────────────────┐
│   INPUT: Data Historis              │
│   (Minimal 12 bulan per kelompok)   │
└──────────────┬──────────────────────┘
               │
               ▼
       ┌───────────────┐
       │ Inisialisasi  │
       │ • S₀ (Level)  │
       │ • b₀ (Trend)  │
       └───────┬───────┘
               │
               ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│         Untuk Setiap Periode (t = 1 to n)                                    │
└───────────────────┬──────────────────────────────────────────────────────────┘
                    │
                    ▼
┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│1. Ambil  │──▶│2. Hitung │──▶│3. Hitung │──▶│4. Hitung │──▶│5. Eval.  │
│   Data   │   │  Level   │   │  Trend   │   │Forecast  │   │ Akurasi  │
│ Aktual   │   │  (Sₜ)    │   │  (bₜ)    │   │ (Fₜ₊₁)   │   │          │
│  (Yₜ)    │   │          │   │          │   │          │   │          │
└──────────┘   └──────────┘   └──────────┘   └──────────┘   └────┬─────┘
                                                                  │
                                                                  ▼
                                                          ┌───────────────┐
                                                          │ Periode       │
                                                          │ Berikutnya?   │
                                                          └───┬───────┬───┘
                                                              │ Ya    │ Tidak
                                                              ▼       ▼
                                                      ┌──────────┐  ┌──────────────┐
                                                      │ t = t+1  │  │ Prediksi     │
                                                      │          │  │ Periode      │
                                                      │          │  │ Mendatang    │
                                                      └────┬─────┘  └──────┬───────┘
                                                           │               │
                                                           └───────┬───────┘
                                                                   │
                                                                   ▼
                                                           ┌─────────────────┐
                                                           │ OUTPUT:         │
                                                           │ • Forecast      │
                                                           │ • Akurasi       │
                                                           │ • Level         │
                                                           │ • Trend         │
                                                           └─────────────────┘
```

Flowchart proses prediksi di atas menggambarkan alur perhitungan yang sistematis dan berulang untuk menghasilkan prediksi waktu penyelesaian menggunakan metode Triple Exponential Smoothing. Proses dimulai dengan pengambilan data historis minimal 12 bulan per kelompok sebagai input utama sistem, kemudian dilakukan inisialisasi nilai awal untuk level (S₀) dan trend (b₀) yang dihitung dari beberapa data pertama. Setelah inisialisasi, sistem melakukan perhitungan iteratif untuk setiap periode data historis (t = 1 sampai n) dimana pada setiap iterasi, sistem mengambil data aktual (Yₜ), menghitung level baru (Sₜ) menggunakan rumus Sₜ = αYₜ + (1-α)(Sₜ₋₁ + bₜ₋₁) yang menggabungkan data aktual dengan estimasi sebelumnya, menghitung trend baru (bₜ) menggunakan rumus bₜ = β(Sₜ - Sₜ₋₁) + (1-β)bₜ₋₁ yang menyesuaikan arah pergerakan data, menghitung forecast (Fₜ₊₁) untuk periode berikutnya menggunakan rumus Fₜ₊₁ = Sₜ + m × bₜ, dan melakukan evaluasi akurasi dengan menghitung error absolut antara nilai aktual dan forecast. Proses iteratif ini berlanjut untuk setiap periode data historis, dan setelah semua data diproses, sistem menggunakan level dan trend terakhir untuk menghasilkan prediksi periode mendatang. Output yang dihasilkan meliputi nilai forecast (prediksi waktu penyelesaian), tingkat akurasi prediksi, nilai level terakhir, dan nilai trend terakhir yang dapat digunakan untuk analisis lebih lanjut dan perencanaan operasional.

### 3.7.4 Class Diagram (Opsional)

Class Diagram menggambarkan struktur kelas dalam sistem, termasuk:

-   **Model**: User, Kelompok, Karyawan, LaporanKaryawan, JobPekerjaan, Prediksi, Kegiatan
-   **Controller**: AuthController, AdminController, KelompokController, LaporanController, JobController, PrediksiController
-   **Service**: PrediksiService (untuk logika perhitungan TES)

---

## 3.8 Perancangan Sistem Prediksi

Perancangan sistem prediksi menjelaskan bagaimana metode Triple Exponential Smoothing diterapkan dalam sistem untuk menghasilkan prediksi waktu penyelesaian kegiatan lapangan.

### 3.8.1 Alur Perhitungan TES dalam Sistem

Alur perhitungan Triple Exponential Smoothing dalam sistem dilakukan melalui tahapan berikut:

1. **Pengambilan Data Historis**

    - Sistem mengambil data waktu penyelesaian kegiatan dari database
    - Data diagregasi per bulan per kelompok kerja
    - Data minimal 12 bulan diperlukan untuk perhitungan yang akurat

2. **Inisialisasi Parameter**

    - Level awal (S₀) dihitung dari rata-rata beberapa data pertama
    - Trend awal (b₀) dihitung dari selisih rata-rata data awal dan akhir
    - Parameter smoothing ditetapkan: α = 0.4, β = 0.3, γ = 0.3

3. **Perhitungan Iteratif untuk Setiap Periode**

    - Untuk setiap periode (t = 1, 2, 3, ..., n):
        - Ambil data aktual waktu penyelesaian (Yₜ)
        - Hitung level baru: Sₜ = αYₜ + (1-α)(Sₜ₋₁ + bₜ₋₁)
        - Hitung trend baru: bₜ = β(Sₜ - Sₜ₋₁) + (1-β)bₜ₋₁
        - Hitung forecast: Fₜ₊₁ = Sₜ + m × bₜ
        - Evaluasi error: Error = |Yₜ - Fₜ|

4. **Perhitungan Prediksi Periode Mendatang**

    - Setelah semua data historis diproses, gunakan level dan trend terakhir
    - Hitung prediksi: Fₜ₊ₘ = Sₜ + m × bₜ
    - m = jumlah periode ke depan yang akan diprediksi (biasanya 1 bulan)

5. **Evaluasi Akurasi**
    - Hitung metrik akurasi: MAE, MAPE, RMSE
    - Simpan hasil prediksi dan akurasi ke database
    - Tampilkan hasil prediksi kepada pengguna

### 3.8.2 Input yang Digunakan

Input yang digunakan dalam sistem prediksi adalah:

1. **Data Historis Waktu Penyelesaian**

    - Sumber: Tabel `job_pekerjaan` (kolom `waktu_penyelesaian`)
    - Format: Data time series bulanan per kelompok kerja
    - Minimal: 12 bulan data historis
    - Agregasi: Rata-rata waktu penyelesaian per bulan per kelompok

2. **Data Kelompok Kerja**

    - Sumber: Tabel `kelompok`
    - Informasi: ID kelompok, nama kelompok
    - Digunakan untuk: Mengelompokkan data dan menghasilkan prediksi per kelompok

3. **Data Job Pekerjaan**

    - Sumber: Tabel `job_pekerjaan`
    - Informasi: Jenis pekerjaan, lokasi, tanggal, waktu penyelesaian
    - Digunakan untuk: Menghitung waktu penyelesaian rata-rata per periode

4. **Parameter Konfigurasi**
    - α (alpha) = 0.4: Parameter smoothing untuk level
    - β (beta) = 0.3: Parameter smoothing untuk trend
    - γ (gamma) = 0.3: Parameter smoothing untuk seasonal (jika digunakan)
    - Periode prediksi (m) = 1: Jumlah periode ke depan yang diprediksi

### 3.8.3 Output Prediksi

Output yang dihasilkan oleh sistem prediksi adalah:

1. **Hasil Prediksi Waktu Penyelesaian**

    - Nilai prediksi untuk periode berikutnya (dalam satuan jam)
    - Prediksi per kelompok kerja
    - Prediksi per jenis pekerjaan (opsional)

2. **Tingkat Akurasi Prediksi**

    - Mean Absolute Error (MAE): Rata-rata selisih absolut antara prediksi dan aktual
    - Mean Absolute Percentage Error (MAPE): Rata-rata persentase error
    - Root Mean Squared Error (RMSE): Akar dari rata-rata kuadrat error

3. **Komponen Perhitungan**

    - Level (Sₜ): Nilai level terakhir setelah perhitungan
    - Trend (bₜ): Nilai trend terakhir yang menunjukkan arah pergerakan
    - Forecast (Fₜ₊ₘ): Nilai prediksi untuk periode mendatang

4. **Visualisasi Data**
    - Grafik time series menampilkan data historis dan prediksi
    - Grafik perbandingan prediksi vs aktual
    - Tabel hasil prediksi dengan detail akurasi

### 3.8.4 Penjelasan Alur Fitur

#### a. Dashboard

**Alur Dashboard Admin:**

1. Admin login ke sistem
2. Sistem menampilkan dashboard dengan statistik:
    - Total laporan kerja
    - Total job pekerjaan
    - Jumlah kelompok aktif
    - Jumlah karyawan aktif
    - Rata-rata waktu penyelesaian
    - Tren performa bulanan
3. Admin dapat melihat ringkasan aktivitas semua kelompok
4. Admin dapat mengakses fitur lain dari dashboard

**Alur Dashboard Kelompok:**

1. Karyawan login ke sistem
2. Sistem menampilkan dashboard kelompok dengan statistik:
    - Laporan kerja kelompok bulan ini
    - Job pekerjaan kelompok bulan ini
    - Rata-rata waktu penyelesaian kelompok
    - Statistik performa kelompok
3. Karyawan dapat melihat aktivitas kelompok mereka
4. Karyawan dapat mengakses fitur input laporan dan job

#### b. Statistik

**Alur Fitur Statistik:**

1. Admin mengakses menu "Statistik"
2. Sistem menampilkan:
    - Grafik performa bulanan kelompok (line chart)
    - Distribusi jenis pekerjaan per kelompok (doughnut chart)
    - Summary cards: Kelompok terbaik, rata-rata waktu, tren performa, target pencapaian
    - Ranking kelompok berdasarkan performa
    - Tabel perbandingan metrik antar kelompok
3. Admin dapat memfilter data berdasarkan:
    - Periode waktu (bulan/tahun)
    - Kelompok kerja
    - Jenis pekerjaan
4. Admin dapat mengekspor data statistik ke Excel

#### c. Prediksi

**Alur Generate Prediksi:**

1. Admin mengakses menu "Prediksi" atau "Generate Prediksi"
2. Sistem menampilkan form konfigurasi:
    - Pilihan kelompok kerja (atau semua kelompok)
    - Pilihan jenis data (berdasarkan laporan atau job pekerjaan)
    - Parameter α, β, γ (dapat disesuaikan)
    - Periode prediksi
3. Admin klik tombol "Generate Prediksi"
4. Sistem melakukan proses:
    - Mengambil data historis dari database
    - Mengagregasi data per bulan per kelompok
    - Menjalankan algoritma Triple Exponential Smoothing
    - Menghitung prediksi dan akurasi
    - Menyimpan hasil prediksi ke database
5. Sistem menampilkan hasil prediksi:
    - Tabel prediksi per kelompok dengan akurasi
    - Grafik prediksi vs data historis
    - Detail perhitungan (level, trend, forecast)
6. Admin dapat menyimpan atau mengekspor hasil prediksi

#### d. Laporan

**Alur Pemantauan Laporan:**

1. Admin mengakses menu "Pemantauan Laporan"
2. Sistem menampilkan daftar semua laporan kerja dari semua kelompok
3. Admin dapat:
    - Memfilter laporan berdasarkan: kelompok, tanggal, instansi
    - Mencari laporan berdasarkan kata kunci
    - Melihat detail laporan (nama, instansi, jabatan, alamat tujuan, dokumentasi)
    - Mengekspor laporan ke Excel
4. Admin dapat melihat statistik laporan:
    - Jumlah laporan per periode
    - Distribusi laporan per kelompok
    - Grafik tren laporan

**Alur Input Laporan (Karyawan):**

1. Karyawan login dan mengakses menu "Input Laporan Kerja"
2. Karyawan mengisi form laporan:
    - Hari, tanggal
    - Nama, instansi, jabatan
    - Alamat tujuan
    - Upload dokumentasi (opsional)
3. Sistem melakukan validasi data
4. Sistem menyimpan laporan ke database
5. Sistem menampilkan konfirmasi dan daftar laporan yang telah dibuat

#### e. Job Pekerjaan

**Alur Pemantauan Job Pekerjaan (Admin):**

1. Admin mengakses menu "Pemantauan Job Pekerjaan"
2. Sistem menampilkan daftar semua job pekerjaan dari semua kelompok
3. Admin dapat:
    - Memfilter job berdasarkan: kelompok, tanggal, jenis pekerjaan
    - Mencari job berdasarkan kata kunci
    - Melihat detail job (jenis pekerjaan, lokasi, waktu penyelesaian)
    - Mengekspor data job ke Excel
4. Admin dapat melihat statistik job:
    - Jumlah job per jenis pekerjaan
    - Rata-rata waktu penyelesaian per jenis pekerjaan
    - Distribusi job per kelompok

**Alur Input Job Pekerjaan (Karyawan):**

1. Karyawan login dan mengakses menu "Input Job Pekerjaan"
2. Karyawan mengisi form job:
    - Hari, tanggal
    - Jenis pekerjaan (Perbaikan KWH, Pemeliharaan Pengkabelan, Pengecekan Gardu, Penanganan Gangguan)
    - Lokasi
    - Waktu penyelesaian (dalam menit)
3. Sistem melakukan validasi data
4. Sistem menyimpan job ke database
5. Sistem menampilkan konfirmasi dan daftar job yang telah dibuat

---

## 3.9 Implementasi Metode Triple Exponential Smoothing

Implementasi metode Triple Exponential Smoothing dalam sistem dilakukan secara praktis melalui tahapan-tahapan berikut:

### 3.9.1 Persiapan Data Time-Series

**Langkah-langkah:**

1. **Pengambilan Data dari Database**

    - Sistem mengambil data dari tabel `job_pekerjaan` berdasarkan `kelompok_id`
    - Data difilter berdasarkan periode waktu (minimal 12 bulan terakhir)
    - Data diurutkan berdasarkan tanggal secara ascending

2. **Agregasi Data per Bulan**

    - Data waktu penyelesaian diagregasi per bulan per kelompok
    - Rata-rata waktu penyelesaian dihitung untuk setiap bulan
    - Format data: Array time series dengan format `[periode => waktu_penyelesaian]`

3. **Validasi Data**
    - Memastikan data minimal 12 bulan tersedia
    - Menghapus data outlier yang tidak valid
    - Memastikan tidak ada missing value yang kritis

**Contoh Format Data:**

```
Januari 2024: 2.5 jam
Februari 2024: 2.8 jam
Maret 2024: 2.3 jam
...
Desember 2024: 2.9 jam
```

### 3.9.2 Penentuan Parameter

**Parameter yang Digunakan:**

1. **α (Alpha) = 0.4**

    - Konstanta pemulusan untuk level
    - Memberikan bobot 40% pada data terbaru
    - Memberikan bobot 60% pada estimasi sebelumnya

2. **β (Beta) = 0.3**

    - Konstanta pemulusan untuk trend
    - Memberikan bobot 30% pada perubahan trend terbaru
    - Memberikan bobot 70% pada trend sebelumnya

3. **γ (Gamma) = 0.3**

    - Konstanta pemulusan untuk seasonal (jika digunakan)
    - Memberikan bobot 30% pada komponen seasonal terbaru

4. **m = 1**
    - Jumlah periode ke depan yang akan diprediksi
    - Biasanya 1 bulan ke depan

**Penentuan Parameter:**

-   Parameter ini ditentukan berdasarkan eksperimen dan optimasi
-   Parameter dapat disesuaikan oleh admin melalui interface sistem
-   Sistem menyimpan parameter untuk setiap kelompok kerja

### 3.9.3 Penghitungan Level, Trend, dan Seasonality

**Implementasi dalam Sistem:**

1. **Inisialisasi Nilai Awal**

    ```php
    // Level awal (S₀) = rata-rata dari 3-4 data pertama
    $S0 = array_sum(array_slice($series, 0, 4)) / 4;

    // Trend awal (b₀) = (rata-rata 2 periode terakhir - rata-rata 2 periode pertama) / jumlah periode tengah
    $firstAvg = array_sum(array_slice($series, 0, 2)) / 2;
    $lastAvg = array_sum(array_slice($series, -2)) / 2;
    $b0 = ($lastAvg - $firstAvg) / (count($series) - 2);
    ```

2. **Perhitungan Iteratif untuk Setiap Periode**

    ```php
    for ($t = 0; $t < $N; $t++) {
        $Yt = $series[$t]; // Data aktual periode t

        // Hitung Level: Sₜ = αYₜ + (1-α)(Sₜ₋₁ + bₜ₋₁)
        $St = $alpha * $Yt + (1 - $alpha) * ($lastS + $lastB);

        // Hitung Trend: bₜ = β(Sₜ - Sₜ₋₁) + (1-β)bₜ₋₁
        $bt = $beta * ($St - $lastS) + (1 - $beta) * $lastB;

        // Hitung Forecast: Fₜ₊₁ = Sₜ + m × bₜ
        $forecast = $St + $m * $bt;

        // Simpan nilai untuk iterasi berikutnya
        $lastS = $St;
        $lastB = $bt;
    }
    ```

3. **Perhitungan Seasonal (jika diperlukan)**
    - Untuk data dengan pola musiman, sistem menggunakan Holt-Winters Additive
    - Seasonal component dihitung menggunakan parameter gamma (γ)

### 3.9.4 Perhitungan Hasil Prediksi

**Langkah-langkah:**

1. **Setelah semua data historis diproses:**

    - Sistem mendapatkan level terakhir (Sₜ) dan trend terakhir (bₜ)
    - Nilai ini digunakan untuk prediksi periode mendatang

2. **Perhitungan Prediksi:**

    ```php
    // Prediksi untuk periode berikutnya (m = 1)
    $forecast = $St + $m * $bt;

    // Untuk prediksi beberapa periode ke depan (m > 1)
    $forecast = $St + ($m * $bt);
    ```

3. **Format Hasil:**
    - Prediksi dalam satuan jam (dengan desimal)
    - Contoh: 2.715 jam (2 jam 43 menit)

### 3.9.5 Penyimpanan Data ke Sistem

**Proses Penyimpanan:**

1. **Menyimpan Hasil Prediksi ke Database**

    - Tabel: `prediksi`
    - Field yang disimpan:
        - `bulan`: Periode prediksi (bulan dan tahun)
        - `hasil_prediksi`: Nilai prediksi (dalam jam)
        - `akurasi`: Tingkat akurasi (MAPE dalam persen)
        - `metode`: "Triple Exponential Smoothing"
        - `params`: Parameter yang digunakan (JSON: {alpha: 0.4, beta: 0.3, gamma: 0.3})
        - `kelompok_id`: ID kelompok kerja (foreign key)

2. **Menyimpan Data Perhitungan**

    - Level terakhir (Sₜ)
    - Trend terakhir (bₜ)
    - Data historis yang digunakan
    - Metrik akurasi (MAE, MAPE, RMSE)

3. **Logging Proses**
    - Sistem mencatat waktu generate prediksi
    - Sistem mencatat parameter yang digunakan
    - Sistem mencatat jumlah data yang diproses

### 3.9.6 Visualisasi Grafik Prediksi

**Fitur Visualisasi:**

1. **Grafik Time Series**

    - Sumbu X: Periode waktu (bulan)
    - Sumbu Y: Waktu penyelesaian (jam)
    - Line chart menampilkan:
        - Data historis (garis biru)
        - Prediksi (garis merah putus-putus)
        - Data aktual untuk validasi (jika tersedia)

2. **Grafik Perbandingan Prediksi vs Aktual**

    - Bar chart menampilkan perbandingan nilai prediksi dan aktual
    - Menunjukkan error untuk setiap periode

3. **Grafik Akurasi**

    - Menampilkan tingkat akurasi (MAPE) untuk setiap prediksi
    - Menampilkan tren akurasi dari waktu ke waktu

4. **Teknologi Visualisasi**
    - Menggunakan library Chart.js atau similar
    - Grafik interaktif dengan tooltip
    - Dapat di-zoom dan di-export sebagai gambar

---

## 3.10 Pengujian Sistem

Pengujian sistem dilakukan untuk memastikan bahwa sistem prediksi berfungsi dengan baik dan menghasilkan prediksi yang akurat. Pengujian dilakukan dalam beberapa aspek:

### 3.10.1 Pengujian Prediksi Menggunakan Metrik Akurasi

**Metrik yang Digunakan:**

1. **Mean Absolute Error (MAE)**

    - **Rumus:** MAE = (1/n) × Σ|Yₜ - Fₜ|
    - **Keterangan:**
        - Yₜ = Nilai aktual
        - Fₜ = Nilai prediksi
        - n = Jumlah data
    - **Interpretasi:** Rata-rata selisih absolut antara prediksi dan aktual
    - **Target:** MAE ≤ 0.25 jam (15 menit)

2. **Mean Absolute Percentage Error (MAPE)**

    - **Rumus:** MAPE = (1/n) × Σ|Yₜ - Fₜ|/Yₜ × 100%
    - **Interpretasi:** Rata-rata persentase error
    - **Target:** MAPE ≤ 15% (akurasi ≥ 85%)

3. **Root Mean Squared Error (RMSE)**
    - **Rumus:** RMSE = √[(1/n) × Σ(Yₜ - Fₜ)²]
    - **Interpretasi:** Akar dari rata-rata kuadrat error (lebih sensitif terhadap outlier)
    - **Target:** RMSE ≤ 0.30 jam

**Proses Pengujian:**

1. **Time Series Cross-Validation**

    - Data dibagi menjadi data training (8-10 bulan) dan data testing (2-4 bulan)
    - Model dilatih menggunakan data training
    - Model diuji menggunakan data testing
    - Metrik akurasi dihitung berdasarkan hasil prediksi pada data testing

2. **Validasi dengan Data Aktual Baru**

    - Setelah prediksi dibuat, sistem menunggu data aktual periode berikutnya
    - Data aktual dibandingkan dengan prediksi
    - Error dan akurasi dihitung
    - Hasil validasi disimpan untuk evaluasi

3. **Pengujian untuk Setiap Kelompok**
    - Pengujian dilakukan untuk setiap kelompok kerja secara terpisah
    - Setiap kelompok memiliki model prediksi sendiri
    - Metrik akurasi dihitung per kelompok

### 3.10.2 Pengujian Fungsional

**Pengujian Use Case:**

1. **Pengujian Use Case Admin:**

    - ✅ Login admin berhasil
    - ✅ Dashboard menampilkan statistik dengan benar
    - ✅ Kelola data kelompok (CRUD) berfungsi
    - ✅ Kelola data karyawan (CRUD) berfungsi
    - ✅ Pemantauan laporan menampilkan data dengan benar
    - ✅ Pemantauan job pekerjaan menampilkan data dengan benar
    - ✅ Generate prediksi menghasilkan hasil yang valid
    - ✅ Statistik menampilkan grafik dan data dengan benar
    - ✅ Export data ke Excel berfungsi
    - ✅ Kelola Excel (upload/download) berfungsi
    - ✅ Pengaturan admin dapat diubah dan disimpan

2. **Pengujian Use Case Karyawan:**

    - ✅ Login karyawan berhasil
    - ✅ Dashboard kelompok menampilkan statistik kelompok dengan benar
    - ✅ Input laporan kerja berhasil disimpan
    - ✅ Input job pekerjaan berhasil disimpan
    - ✅ Export data kelompok ke Excel berfungsi
    - ✅ Pengaturan kelompok dapat diubah dan disimpan

3. **Pengujian Integrasi:**
    - ✅ Data laporan dan job terintegrasi dengan sistem prediksi
    - ✅ Prediksi menggunakan data terbaru dari database
    - ✅ Grafik dan statistik menampilkan data real-time
    - ✅ Export data mencakup semua data yang diperlukan

### 3.10.3 Validasi Data oleh Pihak PLN

**Proses Validasi:**

1. **Validasi Data Input**

    - Data laporan dan job pekerjaan divalidasi oleh supervisor
    - Memastikan data akurat dan sesuai dengan kegiatan lapangan aktual
    - Memastikan waktu penyelesaian tercatat dengan benar

2. **Validasi Hasil Prediksi**

    - Hasil prediksi dibandingkan dengan perkiraan manual oleh supervisor
    - Supervisor memberikan feedback tentang akurasi prediksi
    - Prediksi yang tidak sesuai dengan ekspektasi dianalisis lebih lanjut

3. **Validasi Sistem Secara Keseluruhan**
    - Sistem diuji oleh pengguna (admin dan karyawan) di lingkungan PLN Galesong
    - Pengguna memberikan feedback tentang kemudahan penggunaan
    - Sistem diperbaiki berdasarkan feedback pengguna

### 3.10.4 Pengujian Performa Sistem

**Aspek yang Diuji:**

1. **Kecepatan Generate Prediksi**

    - Sistem dapat menghasilkan prediksi dalam waktu < 5 detik untuk 10 kelompok
    - Sistem dapat memproses data hingga 24 bulan dengan cepat

2. **Kecepatan Loading Halaman**

    - Dashboard loading dalam waktu < 3 detik
    - Halaman statistik loading dalam waktu < 5 detik
    - Export data ke Excel dalam waktu < 10 detik

3. **Stabilitas Sistem**
    - Sistem dapat menangani 50+ pengguna simultan
    - Sistem tidak crash saat memproses data besar
    - Sistem dapat recover dari error dengan baik

---

## 3.11 Kriteria Keberhasilan Penelitian

Kriteria keberhasilan penelitian digunakan untuk mengevaluasi apakah sistem prediksi yang dikembangkan telah mencapai tujuan penelitian. Kriteria keberhasilan meliputi:

### 3.11.1 Kriteria Akurasi Prediksi

1. **Tingkat Akurasi Prediksi**

    - **Target:** MAPE ≤ 15% (akurasi ≥ 85%)
    - **Pengukuran:** Mean Absolute Percentage Error (MAPE) dari hasil prediksi
    - **Evaluasi:** Prediksi dianggap berhasil jika MAPE ≤ 15% untuk minimal 80% kelompok kerja

2. **Error Rata-rata**

    - **Target:** MAE ≤ 0.25 jam (15 menit)
    - **Pengukuran:** Mean Absolute Error (MAE) dari hasil prediksi
    - **Evaluasi:** Error rata-rata tidak melebihi 15 menit untuk sebagian besar prediksi

3. **Konsistensi Akurasi**
    - **Target:** Akurasi konsisten untuk berbagai jenis pekerjaan
    - **Pengukuran:** Variasi MAPE antar jenis pekerjaan ≤ 5%
    - **Evaluasi:** Prediksi akurat untuk semua jenis pekerjaan (Perbaikan KWH, Pemeliharaan Pengkabelan, Pengecekan Gardu, Penanganan Gangguan)

### 3.11.2 Kriteria Fungsionalitas Sistem

1. **Data Laporan & Job Dapat Dipantau oleh Admin**

    - **Target:** Admin dapat melihat, memfilter, dan menganalisis semua data laporan dan job pekerjaan
    - **Pengukuran:** Semua fitur pemantauan berfungsi dengan baik
    - **Evaluasi:** Admin dapat mengakses dan menggunakan semua fitur pemantauan tanpa error

2. **Proses Prediksi Berjalan Otomatis**

    - **Target:** Sistem dapat menghasilkan prediksi secara otomatis dengan satu klik
    - **Pengukuran:** Waktu generate prediksi < 5 detik
    - **Evaluasi:** Admin dapat generate prediksi tanpa kesulitan dan hasil muncul dengan cepat

3. **Sistem Dapat Digunakan oleh Karyawan & Admin PLN Galesong**
    - **Target:** Baik admin maupun karyawan dapat menggunakan sistem dengan mudah
    - **Pengukuran:**
        - Waktu belajar penggunaan sistem < 30 menit
        - Tingkat kepuasan pengguna ≥ 80%
    - **Evaluasi:**
        - Pengguna dapat menyelesaikan tugas mereka menggunakan sistem
        - Pengguna memberikan feedback positif tentang kemudahan penggunaan

### 3.11.3 Kriteria Kualitas Data

1. **Kualitas Data Input**

    - **Target:** Data laporan dan job pekerjaan lengkap dan akurat
    - **Pengukuran:**
        - Tingkat kelengkapan data ≥ 95%
        - Tingkat akurasi data ≥ 90%
    - **Evaluasi:** Data yang diinput oleh karyawan sesuai dengan kegiatan lapangan aktual

2. **Ketersediaan Data Historis**
    - **Target:**
