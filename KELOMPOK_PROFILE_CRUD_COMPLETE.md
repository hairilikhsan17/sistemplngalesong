# 🎯 CRUD Profil Kelompok Lengkap - PLN Galesong

## ✅ **Fitur yang Telah Dibuat**

### **1. 📸 Upload & Kelola Foto Profil Kelompok**

-   **Upload Foto**: Drag & drop atau click untuk upload foto kelompok
-   **Preview**: Tampilan preview foto sebelum upload
-   **Validasi**: File harus berupa gambar (JPEG, PNG, JPG, GIF) max 2MB
-   **Auto Replace**: Foto lama otomatis terhapus saat upload foto baru

### **2. 🗑️ Hapus Foto Profil Kelompok**

-   **Delete Button**: Tombol X di pojok kanan atas foto
-   **Konfirmasi**: Dialog konfirmasi sebelum menghapus
-   **Auto Update**: Foto otomatis hilang dari seluruh aplikasi

### **3. 📝 Update Data Kelompok**

-   **Nama Kelompok**: Update nama kelompok
-   **Shift**: Pilih shift kerja (Shift 1 atau Shift 2)
-   **Lokasi**: Update lokasi kelompok
-   **Telepon**: Update nomor telepon
-   **Deskripsi**: Update deskripsi kelompok

### **4. 🔄 Tampilan Real-time**

-   **Header Avatar**: Foto profil kelompok tampil di header dashboard
-   **Sidebar Avatar**: Foto profil kelompok tampil di sidebar
-   **Auto Refresh**: Halaman otomatis refresh setelah update/hapus foto
-   **Role-based Display**: Tampilan berbeda untuk admin vs kelompok

---

## 🏗️ **Struktur Database**

### **Tabel Kelompok (Updated)**

```sql
- id (UUID, Primary Key)
- nama_kelompok (String)
- shift (Enum: Shift 1, Shift 2)
- avatar (String, Nullable) ← BARU
- created_at, updated_at (Timestamps)
```

---

## 📁 **File yang Terlibat**

### **1. Database Migration**

-   `database/migrations/2025_10_14_155113_add_avatar_to_kelompok_table.php`
    -   Menambahkan kolom `avatar` ke tabel kelompok

### **2. Model**

-   `app/Models/Kelompok.php`
    -   Menambahkan `avatar` ke fillable
    -   Model siap untuk CRUD profil kelompok

### **3. Controller**

-   `app/Http/Controllers/SettingsController.php`
    -   `updateKelompokSettings()` - Update profil kelompok dengan foto
    -   `deleteKelompokAvatar()` - Hapus foto profil kelompok
    -   `getKelompokProfile()` - Ambil data profil kelompok

### **4. Routes**

-   `routes/web.php`
    -   `POST /api/kelompok/settings` - Update profil kelompok
    -   `GET /api/kelompok/profile` - Ambil profil kelompok
    -   `DELETE /api/kelompok/profile/avatar` - Hapus foto

### **5. Views**

-   `resources/views/dashboard/kelompok/settings.blade.php`
    -   Form CRUD profil kelompok lengkap dengan upload foto
-   `resources/views/layouts/dashboard.blade.php`
    -   Header dengan avatar dinamis berdasarkan role
-   `resources/views/layouts/sidebar.blade.php`
    -   Sidebar dengan avatar dinamis berdasarkan role

---

## 🎨 **UI/UX Features**

### **Halaman Pengaturan Kelompok**

```
┌─────────────────────────────────────┐
│ 📸 Foto Profil Kelompok (24x24)     │
│ [Foto] [Upload Foto] [X Delete]     │
│                                     │
│ Nama Kelompok: [Input Field]        │
│ Shift: [Select Shift 1/2]           │
│ Lokasi: [Input Field]               │
│ Telepon: [Input Field]              │
│ Deskripsi: [Textarea]               │
│                                     │
│ [Refresh] [Update Profil Kelompok]  │
└─────────────────────────────────────┘
```

### **Header Dashboard (Role-based)**

```
Dashboard                    🔔 [Foto Kelompok] Kelompok A ▼
```

### **Sidebar (Role-based)**

```
┌─────────────────┐
│ [Foto] Kelompok A│
│ Role: karyawan   │
│ Shift 1          │
│                 │
│ 📊 Dashboard    │
│ 📝 Laporan      │
│ ⚙️ Pengaturan   │
└─────────────────┘
```

---

## 🔧 **Cara Penggunaan**

### **1. Upload Foto Profil Kelompok**

1. Login sebagai Karyawan (anggota kelompok)
2. Buka menu "Pengaturan"
3. Klik "Upload Foto Kelompok" di bagian foto profil
4. Pilih file gambar (JPEG, PNG, JPG, GIF)
5. Klik "Update Profil Kelompok"
6. Foto otomatis tampil di header dan sidebar

### **2. Update Data Kelompok**

1. Ubah nama kelompok, shift, lokasi, telepon, deskripsi
2. Klik "Update Profil Kelompok"
3. Data otomatis tersimpan

### **3. Hapus Foto Profil Kelompok**

1. Klik tombol "X" di pojok kanan atas foto
2. Konfirmasi penghapusan
3. Foto otomatis hilang dari seluruh aplikasi

---

## 🚀 **Fitur Tambahan**

### **1. Role-based Avatar Display**

-   **Admin (Atasan)**: Menampilkan foto profil admin dengan gradient orange
-   **Karyawan (Kelompok)**: Menampilkan foto profil kelompok dengan gradient blue-purple
-   **Fallback Icons**: Icon default jika tidak ada foto

### **2. Auto-Refresh**

-   Halaman otomatis refresh 1.5 detik setelah update/hapus foto
-   Avatar langsung tampil di header dan sidebar

### **3. Validasi File**

-   Hanya menerima file gambar
-   Maksimal ukuran 2MB
-   Preview foto sebelum upload

### **4. Error Handling**

-   Pesan error yang user-friendly
-   Validasi server-side
-   Fallback ke icon default jika tidak ada foto

### **5. Security**

-   CSRF protection
-   File validation
-   Role-based access control

---

## 🎯 **Perbedaan Admin vs Kelompok**

### **Admin (Atasan)**

-   Foto profil pribadi
-   Nama admin di header/sidebar
-   Link ke pengaturan admin
-   Gradient orange untuk fallback

### **Kelompok (Karyawan)**

-   Foto profil kelompok
-   Nama kelompok di header/sidebar
-   Link ke pengaturan kelompok
-   Gradient blue-purple untuk fallback

---

## 🎯 **Hasil Akhir**

✅ **CRUD Profil Kelompok Lengkap** dengan:

-   Upload foto profil kelompok
-   Update data kelompok (nama, shift, lokasi, telepon, deskripsi)
-   Hapus foto profil kelompok
-   Tampilan real-time di header dan sidebar
-   Role-based display (admin vs kelompok)
-   Validasi dan error handling
-   UI/UX yang user-friendly

✅ **Fitur Siap Digunakan** untuk:

-   Kelompok dapat mengelola profil mereka
-   Foto profil kelompok tampil konsisten di seluruh aplikasi
-   Pengalaman pengguna yang smooth dan intuitif
-   Sistem yang terintegrasi dengan role-based access

---

**Status**: ✅ **COMPLETED & READY TO USE**
**Tanggal**: {{ date('Y-m-d H:i:s') }}
**Versi**: 1.0
