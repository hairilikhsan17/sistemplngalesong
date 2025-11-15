# Penjelasan Alur Use Case - Sistem Manajemen Laporan Kerja PLN Galesong

## Paragraf Penjelasan Alur Use Case

Aplikasi PLN Galesong merupakan sistem manajemen laporan kerja yang dirancang untuk mengelola aktivitas kerja harian karyawan dengan dua peran utama, yaitu **Atasan (Admin)** dan **Karyawan (Kelompok)**.
Alur kerja sistem dimulai dengan proses **Login** dimana kedua aktor (**Atasan** dan **Karyawan**) harus melakukan autentikasi terlebih dahulu dengan memasukkan username dan password untuk mengakses sistem. Proses login ini merupakan langkah awal yang wajib dilakukan sebelum pengguna dapat mengakses fitur-fitur lainnya dalam sistem. Setelah berhasil login, sistem akan melakukan validasi kredensial dan mengarahkan pengguna ke dashboard sesuai dengan peran mereka masing-masing (Atasan akan diarahkan ke dashboard admin, sedangkan Karyawan akan diarahkan ke dashboard kelompok).
Alur kerja sistem kemudian berlanjut ketika **Atasan** melakukan setup awal dengan mengelola data kelompok dan data karyawan melalui fitur **Kelola Data Kelompok** dan **Kelola Data Karyawan**,
kemudian **Atasan** dapat memantau seluruh aktivitas melalui **Lihat Dashboard** yang menampilkan statistik lengkap.
Di sisi lain, **Karyawan** mulai bekerja dengan mengakses **Lihat Dashboard Kelompok** untuk melihat ringkasan aktivitas kelompok mereka,
kemudian melakukan input data harian melalui **Input Laporan Kerja** dan **Input Job Pekerjaan** yang berisi detail pekerjaan yang telah dilakukan.
Setelah data laporan dan job pekerjaan masuk ke sistem, **Atasan** dapat melakukan **Pemantauan Laporan** dan **Pemantauan Job Pekerjaan**
untuk melihat, memfilter, dan menganalisis semua aktivitas dari berbagai kelompok.
Berdasarkan data yang terkumpul, **Atasan** dapat melakukan **Generate Prediksi** untuk menghasilkan analisis prediktif,
serta melihat **Lihat Statistik** yang menyajikan grafik dan analisis data yang lebih detail.
Untuk keperluan dokumentasi dan pelaporan kelompok, **Karyawan** dapat melakukan **Export Data Kelompok** untuk mengekspor data laporan dan job pekerjaan kelompok mereka ke format Excel.
Untuk keperluan pelaporan dan dokumentasi, **Atasan** dapat melakukan **Export Data** ke format Excel dan **Kelola Excel** untuk mengelola file-file Excel yang diperlukan.
Kedua aktor juga memiliki akses ke fitur pengaturan masing-masing, yaitu **Pengaturan Admin** untuk Atasan dan **Pengaturan Kelompok** untuk Karyawan,
yang memungkinkan mereka mengatur profil, akun, dan konfigurasi sesuai kebutuhan.
Secara keseluruhan, sistem ini memfasilitasi alur kerja yang terstruktur dari input data oleh karyawan, pemantauan dan analisis oleh atasan, hingga menghasilkan prediksi dan laporan yang dapat digunakan untuk pengambilan keputusan yang lebih baik.

## Ringkasan Alur

0. **Autentikasi (Semua Aktor)**: Login
1. **Setup Awal (Atasan)**: Kelola Data Kelompok → Kelola Data Karyawan
2. **Input Data (Karyawan)**: Lihat Dashboard Kelompok → Input Laporan Kerja → Input Job Pekerjaan
3. **Pemantauan (Atasan)**: Lihat Dashboard → Pemantauan Laporan → Pemantauan Job Pekerjaan
4. **Analisis (Atasan)**: Generate Prediksi → Lihat Statistik
5. **Export Data (Karyawan)**: Export Data Kelompok
6. **Pelaporan (Atasan)**: Export Data → Kelola Excel
7. **Pengaturan**: Pengaturan Admin (Atasan) / Pengaturan Kelompok (Karyawan)
