@extends('layouts.dashboard')

@section('title', 'Pengaturan Kelompok')

@section('content')
<div class="p-6" x-data="kelompokSettings()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan Kelompok</h1>
        <p class="text-gray-600 mt-2">Kelola informasi dan pengaturan kelompok Anda</p>
    </div>

    <!-- Group Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Anggota</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $karyawans->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Shift Kerja</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $kelompok?->shift ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="monthlyReports"></p>
                </div>
            </div>
        </div>
    </div>

        <!-- Account & Security -->
        <div class="space-y-6">
            <!-- Profile Kelompok -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Profil Kelompok</h2>
                
                <form @submit.prevent="updateGroupSettings()" class="space-y-6">
                    <!-- Profile Photo Section -->
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="relative">
                            <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                                <img x-show="groupSettings.avatar_url" 
                                     :src="groupSettings.avatar_url" 
                                     :alt="groupSettings.nama_kelompok"
                                     class="w-full h-full object-cover">
                                <i x-show="!groupSettings.avatar_url" 
                                   data-lucide="users" 
                                   class="w-12 h-12 text-gray-600"></i>
                            </div>
                            <button type="button" 
                                    @click="deleteKelompokAvatar()"
                                    x-show="groupSettings.avatar_url"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900" x-text="groupSettings.nama_kelompok || 'Nama Kelompok'"></h3>
                            <p class="text-sm text-gray-600" x-text="groupSettings.shift || 'Shift'"></p>
                            <div class="mt-2">
                                <label for="kelompok-avatar-upload" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i>
                                    Upload Foto
                                </label>
                                <input id="kelompok-avatar-upload" 
                                       type="file" 
                                       accept="image/*" 
                                       @change="handleKelompokAvatarUpload($event)"
                                       class="hidden">
                            </div>
                        </div>
                    </div>

                    <!-- Group Information Display -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelompok</label>
                            <p class="text-lg font-medium text-gray-900" x-text="groupSettings.nama_kelompok || 'Nama Kelompok'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                            <p class="text-lg font-medium text-gray-900" x-text="groupSettings.shift || 'Shift'"></p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="loadKelompokProfile()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 inline"></i>
                            Refresh
                        </button>
                        <button type="button" 
                                @click="updateGroupSettings()"
                                :disabled="loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                            <i data-lucide="save" class="w-4 h-4 mr-2" x-show="!loading"></i>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Foto Profil'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Ubah Password</h2>
                
                <form @submit.prevent="updateAccount()" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               x-model="accountData.current_password"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="••••••••••••••••">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               x-model="accountData.new_password"
                               required
                               :class="accountData.new_password.length > 0 && accountData.new_password.length < 6 ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-transparent'"
                               class="w-full px-4 py-2 border rounded-lg"
                               placeholder="Masukkan password baru">
                        <div x-show="accountData.new_password.length > 0 && accountData.new_password.length < 6" 
                             class="text-red-500 text-sm mt-1">
                            Password minimal 6 karakter
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               x-model="accountData.new_password_confirmation"
                               required
                               :class="accountData.new_password_confirmation.length > 0 && accountData.new_password !== accountData.new_password_confirmation ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-transparent'"
                               class="w-full px-4 py-2 border rounded-lg"
                               placeholder="Konfirmasi password baru">
                        <div x-show="accountData.new_password_confirmation.length > 0 && accountData.new_password !== accountData.new_password_confirmation" 
                             class="text-red-500 text-sm mt-1">
                            Konfirmasi password tidak sesuai
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="resetPasswordForm()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 inline"></i>
                            Refresh
                        </button>
                        <button type="submit" 
                                :disabled="loading || !isPasswordFormValid()"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                            <i data-lucide="key" class="w-4 h-4 mr-2" x-show="!loading"></i>
                            <span x-text="loading ? 'Mengupdate...' : 'Update Password'"></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Group Members -->
    <div class="mt-8 bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Anggota Kelompok</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posisi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bergabung
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($karyawans as $karyawan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="w-5 h-5 text-gray-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $karyawan->nama }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Anggota Kelompok
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $karyawan->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada anggota kelompok
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notification -->
    <div x-show="message" 
         x-transition
         :class="messageType === 'success' ? 'bg-green-100 border-green-300 text-green-800' : 'bg-red-100 border-red-300 text-red-800'"
         class="fixed top-4 right-4 border-2 rounded-lg px-6 py-4 shadow-xl z-50 font-medium">
        <div class="flex items-center">
            <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" 
               :class="messageType === 'success' ? 'text-green-600' : 'text-red-600'"
               class="w-6 h-6 mr-3"></i>
            <span x-text="message" class="text-lg"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kelompokSettings', () => ({
        loading: false,
        message: '',
        messageType: '',
        monthlyReports: 0,
        
        groupSettings: {
            id: '{{ $kelompok?->id ?? "" }}',
            nama_kelompok: '{{ $kelompok?->nama_kelompok ?? "" }}',
            shift: '{{ $kelompok?->shift ?? "Shift 1" }}',
            deskripsi: '{{ $kelompok?->deskripsi ?? "" }}',
            lokasi: '{{ $kelompok?->lokasi ?? "" }}',
            telepon: '{{ $kelompok?->telepon ?? "" }}',
            email: '{{ $kelompok?->email ?? "" }}',
            avatar: '{{ $kelompok?->avatar ?? "" }}',
            avatar_url: '{{ ($kelompok?->avatar) ? asset("storage/avatars/" . $kelompok?->avatar) : "" }}',
            avatar_file: null
        },
        
        accountData: {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },
        
        notificationSettings: {
            email_notifications: true,
            laporan_reminder: true,
            job_assignment: true,
            performance_update: true,
            reminder_time: '08:00'
        },
        
        async init() {
            await this.loadMonthlyReports();
            await this.loadKelompokProfile();
        },
        
        async loadMonthlyReports() {
            try {
                const response = await fetch('/api/kelompok/monthly-reports');
                const result = await response.json();
                this.monthlyReports = result.count || 0;
            } catch (error) {
                console.error('Error loading monthly reports:', error);
            }
        },
        
        async updateGroupSettings() {
            this.loading = true;
            
            try {
                // Check if there's an avatar file to upload
                if (!this.groupSettings.avatar_file) {
                    this.showMessage('Pilih foto terlebih dahulu!', 'error');
                    this.loading = false;
                    return;
                }

                const formData = new FormData();
                
                // Add avatar file
                formData.append('avatar', this.groupSettings.avatar_file);
                
                // Add required kelompok data
                formData.append('nama_kelompok', this.groupSettings.nama_kelompok || '');
                formData.append('shift', this.groupSettings.shift || 'Shift 1');
                
                const response = await fetch('/api/kelompok/settings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    // Update group data
                    this.groupSettings = {
                        ...this.groupSettings,
                        ...result.kelompok,
                        avatar_url: result.kelompok.avatar ? `/storage/avatars/${result.kelompok.avatar}` : '',
                        avatar_file: null
                    };
                    
                    // Refresh page to update avatar in header and sidebar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menyimpan foto profil', 'error');
            }
            
            this.loading = false;
        },

        async updateAccount() {
            // Validasi password baru
            if (this.accountData.new_password !== this.accountData.new_password_confirmation) {
                this.showMessage('Konfirmasi password tidak sesuai!', 'error');
                return;
            }
            
            if (this.accountData.new_password.length < 6) {
                this.showMessage('Password baru minimal 6 karakter!', 'error');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/api/kelompok/account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.accountData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage('Password berhasil diperbarui!', 'success');
                    // Reset password fields
                    this.resetPasswordForm();
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('Terjadi kesalahan saat memperbarui password', 'error');
            }
            
            this.loading = false;
        },

        resetPasswordForm() {
            this.accountData = {
                current_password: '',
                new_password: '',
                new_password_confirmation: ''
            };
        },

        isPasswordFormValid() {
            return this.accountData.current_password.length > 0 &&
                   this.accountData.new_password.length >= 6 &&
                   this.accountData.new_password_confirmation.length >= 6 &&
                   this.accountData.new_password === this.accountData.new_password_confirmation;
        },
        

        async loadKelompokProfile() {
            try {
                const response = await fetch('/api/kelompok/profile');
                const result = await response.json();
                
                if (result.success) {
                    this.groupSettings = {
                        ...this.groupSettings,
                        ...result.kelompok,
                        avatar_url: result.kelompok.avatar ? `/storage/avatars/${result.kelompok.avatar}` : '',
                        avatar_file: null
                    };
                }
            } catch (error) {
                console.error('Error loading kelompok profile:', error);
            }
        },

        handleKelompokAvatarUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    this.showMessage('File harus berupa gambar!', 'error');
                    return;
                }
                
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    this.showMessage('Ukuran file maksimal 2MB!', 'error');
                    return;
                }
                
                this.groupSettings.avatar_file = file;
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.groupSettings.avatar_url = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async deleteKelompokAvatar() {
            if (!confirm('Apakah Anda yakin ingin menghapus foto profil kelompok?')) {
                return;
            }
            
            try {
                const response = await fetch('/api/kelompok/profile/avatar', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    this.groupSettings.avatar = '';
                    this.groupSettings.avatar_url = '';
                    this.groupSettings.avatar_file = null;
                    
                    // Refresh page to update avatar in header and sidebar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menghapus foto profil kelompok', 'error');
            }
        },
        
        async updateNotifications() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/kelompok/notifications', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.notificationSettings)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menyimpan pengaturan notifikasi', 'error');
            }
            
            this.loading = false;
        },
        
        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            
            // Pesan sukses ditampilkan lebih lama (7 detik)
            const duration = type === 'success' ? 7000 : 5000;
            
            setTimeout(() => {
                this.message = '';
                this.messageType = '';
            }, duration);
        }
    }));
});
</script>
@endsection


