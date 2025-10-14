# Panduan Testing Fungsi Ubah Password Kelompok

## Fitur yang Sudah Diimplementasikan

### 1. Frontend Validasi

-   ✅ Validasi real-time untuk password baru (minimal 6 karakter)
-   ✅ Validasi konfirmasi password yang harus sama dengan password baru
-   ✅ Tombol submit dinonaktifkan jika validasi belum terpenuhi
-   ✅ Visual feedback dengan border merah jika validasi gagal

### 2. Backend Processing

-   ✅ Validasi password lama sebelum mengubah
-   ✅ Hash password baru menggunakan bcrypt
-   ✅ Response JSON dengan status success/error

### 3. UI/UX Improvements

-   ✅ Pesan sukses berwarna hijau yang lebih menonjol
-   ✅ Durasi pesan sukses lebih lama (7 detik)
-   ✅ Icon check-circle untuk pesan sukses
-   ✅ Form reset otomatis setelah password berhasil diubah

## Cara Testing

### Langkah 1: Akses Halaman

1. Login sebagai kelompok
2. Navigasi ke menu "Pengaturan"
3. Scroll ke bagian "Ubah Password"

### Langkah 2: Test Validasi Frontend

1. **Test password kosong**: Klik tombol "Update Password" tanpa mengisi apa-apa

    - Expected: Tombol disabled, tidak ada request ke server

2. **Test password kurang dari 6 karakter**:

    - Isi password lama
    - Isi password baru dengan kurang dari 6 karakter
    - Expected: Border merah, pesan error, tombol disabled

3. **Test konfirmasi password tidak sama**:
    - Isi password baru dengan 6+ karakter
    - Isi konfirmasi password yang berbeda
    - Expected: Border merah pada konfirmasi, pesan error, tombol disabled

### Langkah 3: Test Backend

1. **Test password lama salah**:

    - Isi password lama yang salah
    - Isi password baru dan konfirmasi yang benar
    - Expected: Pesan error "Password lama tidak sesuai!"

2. **Test password berhasil diubah**:
    - Isi password lama yang benar
    - Isi password baru dan konfirmasi yang benar
    - Expected: Pesan sukses hijau "Password berhasil diperbarui!"

### Langkah 4: Test UI/UX

1. **Test pesan sukses**:

    - Setelah password berhasil diubah
    - Expected:
        - Pesan hijau dengan background `bg-green-100`
        - Border hijau `border-green-300`
        - Text hijau `text-green-800`
        - Icon check-circle hijau
        - Durasi 7 detik

2. **Test form reset**:

    - Setelah password berhasil diubah
    - Expected: Semua field password kosong

3. **Test tombol refresh**:
    - Klik tombol "Refresh"
    - Expected: Form password direset tanpa pesan

## Endpoint yang Digunakan

```
POST /api/kelompok/account
Content-Type: application/json
X-CSRF-TOKEN: [token]

Body:
{
    "current_password": "password_lama",
    "new_password": "password_baru",
    "new_password_confirmation": "password_baru"
}
```

## Response Format

### Success Response

```json
{
    "success": true,
    "message": "Password berhasil diperbarui!"
}
```

### Error Response

```json
{
    "success": false,
    "message": "Password lama tidak sesuai!"
}
```

## Troubleshooting

### Jika password tidak bisa diubah:

1. Periksa console browser untuk error JavaScript
2. Periksa network tab untuk response dari server
3. Pastikan user sudah login dengan role kelompok
4. Pastikan password lama benar

### Jika pesan sukses tidak muncul:

1. Periksa apakah response dari server memiliki `success: true`
2. Periksa console untuk error JavaScript
3. Pastikan Alpine.js berjalan dengan baik

## File yang Dimodifikasi

1. `resources/views/dashboard/kelompok/settings.blade.php`

    - Tambah validasi frontend real-time
    - Perbaiki UI notification
    - Tambah fungsi `isPasswordFormValid()`

2. `app/Http/Controllers/SettingsController.php`

    - Method `updateAccount()` sudah benar (tidak perlu diubah)

3. `routes/web.php`
    - Route `/api/kelompok/account` sudah tersedia
