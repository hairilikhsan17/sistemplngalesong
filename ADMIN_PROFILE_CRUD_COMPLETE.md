# 🎯 CRUD Profil Admin Lengkap - PLN Galesong

## ✅ **Fitur yang Telah Dibuat**

### **1. 📸 Upload & Kelola Foto Profil**

-   **Upload Foto**: Drag & drop atau click untuk upload foto
-   **Preview**: Tampilan preview foto sebelum upload
-   **Validasi**: File harus berupa gambar (JPEG, PNG, JPG, GIF) max 2MB
-   **Auto Replace**: Foto lama otomatis terhapus saat upload foto baru

### **2. 🗑️ Hapus Foto Profil**

-   **Delete Button**: Tombol X di pojok kanan atas foto
-   **Konfirmasi**: Dialog konfirmasi sebelum menghapus
-   **Auto Update**: Foto otomatis hilang dari seluruh aplikasi

### **3. 📝 Update Data Profil**

-   **Nama Lengkap**: Update nama admin
-   **Email**: Update email dengan validasi unique
-   **Password**: Ubah password dengan konfirmasi password lama

### **4. 🔄 Tampilan Real-time**

-   **Header Avatar**: Foto profil tampil di header dashboard
-   **Sidebar Avatar**: Foto profil tampil di sidebar
-   **Auto Refresh**: Halaman otomatis refresh setelah update/hapus foto

---

## 🏗️ **Struktur Database**

### **Tabel Users (Updated)**

```sql
- id (UUID, Primary Key)
- username (String, Unique)
- name (String, Nullable) ← BARU
- email (String, Nullable, Unique) ← BARU
- avatar (String, Nullable) ← BARU
- password (String, Hashed)
- role (Enum: atasan, karyawan)
- kelompok_id (UUID, Foreign Key)
- created_at, updated_at (Timestamps)
```

---

## 📁 **File yang Terlibat**

### **1. Database Migration**

-   `database/migrations/2025_10_14_152357_add_avatar_to_users_table.php`
    -   Menambahkan kolom `name`, `email`, `avatar` ke tabel users

### **2. Model**

-   `app/Models/User.php`
    -   Menambahkan `name`, `email`, `avatar` ke fillable
    -   Model siap untuk CRUD profil

### **3. Controller**

-   `app/Http/Controllers/SettingsController.php`
    -   `updateProfile()` - Update profil dengan foto
    -   `deleteAvatar()` - Hapus foto profil
    -   `getProfile()` - Ambil data profil

### **4. Routes**

-   `routes/web.php`
    -   `POST /api/admin/profile` - Update profil
    -   `GET /api/admin/profile` - Ambil profil
    -   `DELETE /api/admin/profile/avatar` - Hapus foto

### **5. Views**

-   `resources/views/dashboard/atasan/settings.blade.php`
    -   Form CRUD profil lengkap dengan upload foto
-   `resources/views/layouts/dashboard.blade.php`
    -   Header dengan avatar dinamis
-   `resources/views/layouts/sidebar.blade.php`
    -   Sidebar dengan avatar dinamis

---

## 🎨 **UI/UX Features**

### **Halaman Pengaturan Profil**

```
┌─────────────────────────────────────┐
│ 📸 Foto Profil (24x24)              │
│ [Foto] [Upload Foto] [X Delete]     │
│                                     │
│ Nama Lengkap: [Input Field]         │
│ Email: [Input Field]                │
│                                     │
│ ──── Ubah Password ────             │
│ Password Lama: [Input]              │
│ Password Baru: [Input]              │
│ Konfirmasi: [Input]                 │
│                                     │
│ [Refresh] [Update Profil]           │
└─────────────────────────────────────┘
```

### **Header Dashboard**

```
Dashboard                    🔔 [Foto] admin ▼
```

### **Sidebar**

```
┌─────────────────┐
│ [Foto] Admin    │
│ Role: atasan    │
│                 │
│ 📊 Dashboard    │
│ 👥 Manajemen    │
│ ⚙️ Pengaturan   │
└─────────────────┘
```

---

## 🔧 **Cara Penggunaan**

### **1. Upload Foto Profil**

1. Login sebagai Admin
2. Buka menu "Pengaturan"
3. Klik "Upload Foto" di bagian foto profil
4. Pilih file gambar (JPEG, PNG, JPG, GIF)
5. Klik "Update Profil"
6. Foto otomatis tampil di header dan sidebar

### **2. Update Data Profil**

1. Ubah nama lengkap dan email
2. Klik "Update Profil"
3. Data otomatis tersimpan

### **3. Ubah Password**

1. Masukkan password lama
2. Masukkan password baru
3. Konfirmasi password baru
4. Klik "Update Profil"

### **4. Hapus Foto Profil**

1. Klik tombol "X" di pojok kanan atas foto
2. Konfirmasi penghapusan
3. Foto otomatis hilang dari seluruh aplikasi

---

## 🚀 **Fitur Tambahan**

### **1. Auto-Refresh**

-   Halaman otomatis refresh 1.5 detik setelah update/hapus foto
-   Avatar langsung tampil di header dan sidebar

### **2. Validasi File**

-   Hanya menerima file gambar
-   Maksimal ukuran 2MB
-   Preview foto sebelum upload

### **3. Error Handling**

-   Pesan error yang user-friendly
-   Validasi server-side
-   Fallback ke icon default jika tidak ada foto

### **4. Security**

-   CSRF protection
-   File validation
-   Password hashing
-   Unique email validation

---

## 🎯 **Hasil Akhir**

✅ **CRUD Profil Admin Lengkap** dengan:

-   Upload foto profil
-   Update data profil (nama, email)
-   Ubah password
-   Hapus foto profil
-   Tampilan real-time di header dan sidebar
-   Validasi dan error handling
-   UI/UX yang user-friendly

✅ **Fitur Siap Digunakan** untuk:

-   Admin dapat mengelola profil mereka
-   Foto profil tampil konsisten di seluruh aplikasi
-   Pengalaman pengguna yang smooth dan intuitif

---

**Status**: ✅ **COMPLETED & READY TO USE**
**Tanggal**: {{ date('Y-m-d H:i:s') }}
**Versi**: 1.0
