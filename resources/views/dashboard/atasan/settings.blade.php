@extends('layouts.dashboard')

@section('title', 'Pengaturan Admin')

@section('content')
<div class="p-6" x-data="adminSettings()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan Admin</h1>
        <p class="text-gray-600 mt-2">Kelola pengaturan sistem dan konfigurasi aplikasi</p>
    </div>

    <!-- System Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Kelompok</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="systemStats.total_kelompok"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="systemStats.total_karyawan"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Laporan</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="systemStats.total_laporan"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i data-lucide="hard-drive" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disk Usage</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="systemStats.disk_usage?.percentage + '%'"></p>
                </div>
            </div>
        </div>
    </div>

   

        <!-- System Management -->
        <div class="space-y-6">
            <!-- Profile Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Profil Admin</h2>
                
                <form @submit.prevent="updateProfile()" class="space-y-6">
                    <!-- Profile Photo Section -->
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="relative">
                            <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                                <img x-show="profileData.avatar_url" 
                                     :src="profileData.avatar_url" 
                                     :alt="profileData.name"
                                     class="w-full h-full object-cover">
                                <i x-show="!profileData.avatar_url" 
                                   data-lucide="user" 
                                   class="w-12 h-12 text-gray-600"></i>
                            </div>
                            <button type="button" 
                                    @click="deleteAvatar()"
                                    x-show="profileData.avatar_url"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900" x-text="profileData.name || 'Nama tidak tersedia'"></h3>
                            <p class="text-sm text-gray-600" x-text="profileData.email || 'Email tidak tersedia'"></p>
                            <div class="mt-2">
                                <label for="avatar-upload" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i>
                                    Upload Foto
                                </label>
                                <input id="avatar-upload" 
                                       type="file" 
                                       accept="image/*" 
                                       @change="handleAvatarUpload($event)"
                                       class="hidden">
                            </div>
                        </div>
                    </div>

                    <!-- Profile Form -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" 
                                   x-model="profileData.name"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   x-model="profileData.email"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                                <input type="password" 
                                       x-model="profileData.current_password"
                                       placeholder="Masukkan password lama"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <input type="password" 
                                       x-model="profileData.new_password"
                                       placeholder="Masukkan password baru"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" 
                                       x-model="profileData.new_password_confirmation"
                                       placeholder="Konfirmasi password baru"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Kosongkan jika tidak ingin mengubah password</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="loadProfile()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2 inline"></i>
                            Refresh
                        </button>
                        <button type="submit" 
                                :disabled="loading"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                            <i data-lucide="save" class="w-4 h-4 mr-2" x-show="!loading"></i>
                            <span x-text="loading ? 'Menyimpan...' : 'Update Profil'"></span>
                        </button>
                    </div>
                </form>
            </div>

          

            <!-- System Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Sistem</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">PHP Version:</span>
                        <span class="font-medium">{{ phpversion() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Laravel Version:</span>
                        <span class="font-medium">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Server:</span>
                        <span class="font-medium">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Disk Usage:</span>
                        <span class="font-medium" x-text="systemStats.disk_usage?.used + ' / ' + systemStats.disk_usage?.total"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div x-show="message" 
         x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="fixed top-4 right-4 border rounded-lg px-4 py-3 shadow-lg z-50">
        <div class="flex items-center">
            <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mr-2"></i>
            <span x-text="message"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminSettings', () => ({
        loading: false,
        message: '',
        messageType: '',
        
        systemStats: {
            total_kelompok: {{ $systemStats['total_kelompok'] ?? 0 }},
            total_karyawan: {{ $systemStats['total_karyawan'] ?? 0 }},
            total_laporan: {{ $systemStats['total_laporan'] ?? 0 }},
            total_job: {{ $systemStats['total_job'] ?? 0 }},
            disk_usage: {
                percentage: {{ $systemStats['disk_usage']['percentage'] ?? 0 }},
                used: '{{ $systemStats['disk_usage']['used'] ?? '0 B' }}',
                total: '{{ $systemStats['disk_usage']['total'] ?? '0 B' }}'
            },
            last_backup: '{{ $systemStats['last_backup'] ?? 'Belum pernah' }}',
            system_uptime: '{{ $systemStats['system_uptime'] ?? '0 hari' }}'
        },
        
        systemSettings: {
            system_name: 'PLN Galesong Management System',
            system_description: 'Sistem manajemen laporan kerja PLN Galesong',
            timezone: 'Asia/Jakarta',
            date_format: 'd/m/Y',
            default_shift: 'Shift 1',
            max_upload_size: 10,
            auto_backup: true,
            notification_email: '',
            maintenance_mode: false
        },
        
        profileData: {
            id: '{{ auth()->user()->id }}',
            name: '{{ auth()->user()->name ?? "" }}',
            email: '{{ auth()->user()->email ?? "" }}',
            avatar: '{{ auth()->user()->avatar ?? "" }}',
            avatar_url: '{{ auth()->user()->avatar ? asset("storage/avatars/" . auth()->user()->avatar) : "" }}',
            current_password: '',
            new_password: '',
            new_password_confirmation: '',
            avatar_file: null
        },
        
        async init() {
            await this.loadSystemStats();
            await this.loadProfile();
        },
        
        async loadSystemStats() {
            try {
                console.log('System stats loaded from controller:', this.systemStats);
                // Data sudah di-load dari controller, tidak perlu API call
            } catch (error) {
                console.error('Error loading system stats:', error);
            }
        },
        
        async updateSystemSettings() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/admin/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.systemSettings)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menyimpan pengaturan', 'error');
            }
            
            this.loading = false;
        },
        
        async loadProfile() {
            try {
                const response = await fetch('/api/admin/profile', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.profileData = {
                        ...this.profileData,
                        ...result.user,
                        avatar_url: result.user.avatar ? `/storage/avatars/${result.user.avatar}` : '',
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
                        avatar_file: null
                    };
                }
                
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        },

        async updateProfile() {
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('name', this.profileData.name);
                formData.append('email', this.profileData.email);
                formData.append('current_password', this.profileData.current_password);
                formData.append('new_password', this.profileData.new_password);
                formData.append('new_password_confirmation', this.profileData.new_password_confirmation);
                
                // Add avatar file if selected
                if (this.profileData.avatar_file) {
                    formData.append('avatar', this.profileData.avatar_file);
                }
                
                const response = await fetch('/api/admin/profile', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    // Update profile data
                    this.profileData = {
                        ...this.profileData,
                        ...result.user,
                        avatar_url: result.user.avatar ? `/storage/avatars/${result.user.avatar}` : '',
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: '',
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
                this.showMessage('Terjadi kesalahan saat memperbarui profil', 'error');
            }
            
            this.loading = false;
        },

        handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    this.showMessage('File harus berupa gambar', 'error');
                    return;
                }
                
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    this.showMessage('Ukuran file maksimal 2MB', 'error');
                    return;
                }
                
                this.profileData.avatar_file = file;
                
                // Preview image
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.profileData.avatar_url = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async deleteAvatar() {
            const result = await SwalHelper.confirmDelete('⚠️ Konfirmasi Penghapusan', 'Apakah Anda yakin ingin menghapus foto profil?');
            if (!result.isConfirmed) return;
            
            try {
                const response = await fetch('/api/admin/profile/avatar', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    this.profileData.avatar = '';
                    this.profileData.avatar_url = '';
                    this.profileData.avatar_file = null;
                    
                    // Refresh page to update avatar in header and sidebar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menghapus foto profil', 'error');
            }
        },
        
        async createBackup() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/admin/backup', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    await this.loadSystemStats(); // Refresh stats
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat membuat backup', 'error');
            }
            
            this.loading = false;
        },
        
        async handleRestoreFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.loading = true;
            
            const formData = new FormData();
            formData.append('backup_file', file);
            
            try {
                const response = await fetch('/api/admin/restore', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    await this.loadSystemStats(); // Refresh stats
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat restore data', 'error');
            }
            
            this.loading = false;
        },
        
        showMessage(text, type) {
            if (type === 'success') {
                SwalHelper.success('Berhasil 🎉', text);
            } else if (type === 'error') {
                SwalHelper.error('Gagal ❌', text);
            } else {
                Swal.fire({
                    text: text,
                    icon: type,
                    confirmButtonColor: '#f59e0b'
                });
            }
        }
    }));
});
</script>
@endsection

