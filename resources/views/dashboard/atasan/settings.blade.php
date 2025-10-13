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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- System Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Pengaturan Sistem</h2>
            
            <form @submit.prevent="updateSystemSettings()" class="space-y-6">
                <!-- System Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Sistem <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="systemSettings.system_name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- System Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Sistem
                    </label>
                    <textarea x-model="systemSettings.system_description"
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Timezone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Timezone <span class="text-red-500">*</span>
                    </label>
                    <select x-model="systemSettings.timezone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                        <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                        <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                    </select>
                </div>

                <!-- Date Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Format Tanggal <span class="text-red-500">*</span>
                    </label>
                    <select x-model="systemSettings.date_format"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="d/m/Y">DD/MM/YYYY</option>
                        <option value="Y-m-d">YYYY-MM-DD</option>
                        <option value="d-m-Y">DD-MM-YYYY</option>
                    </select>
                </div>

                <!-- Default Shift -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Shift Default <span class="text-red-500">*</span>
                    </label>
                    <select x-model="systemSettings.default_shift"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Shift 1">Shift 1</option>
                        <option value="Shift 2">Shift 2</option>
                    </select>
                </div>

                <!-- Max Upload Size -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Maksimal Ukuran Upload (MB) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           x-model="systemSettings.max_upload_size"
                           min="1" max="50"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- System Options -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Opsi Sistem</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="systemSettings.auto_backup" class="mr-3">
                            <span class="text-sm text-gray-700">Backup Otomatis</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="systemSettings.maintenance_mode" class="mr-3">
                            <span class="text-sm text-gray-700">Mode Maintenance</span>
                        </label>
                    </div>
                </div>

                <!-- Notification Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email Notifikasi
                    </label>
                    <input type="email" 
                           x-model="systemSettings.notification_email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="loading"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
                </button>
            </form>
        </div>

        <!-- System Management -->
        <div class="space-y-6">
            <!-- Profile Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Profil Admin</h2>
                
                <form @submit.prevent="updateProfile()" class="space-y-4">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-8 h-8 text-gray-600"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" 
                               x-model="profileData.name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               x-model="profileData.email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                        <input type="password" 
                               x-model="profileData.current_password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <input type="password" 
                               x-model="profileData.new_password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" 
                               x-model="profileData.new_password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <button type="submit" 
                            :disabled="loading"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                        Update Profil
                    </button>
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
            name: '{{ auth()->user()->name }}',
            email: '{{ auth()->user()->email }}',
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        },
        
        async init() {
            await this.loadSystemStats();
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
        
        async updateProfile() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/admin/profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.profileData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    // Reset password fields
                    this.profileData.current_password = '';
                    this.profileData.new_password = '';
                    this.profileData.new_password_confirmation = '';
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat memperbarui profil', 'error');
            }
            
            this.loading = false;
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
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
                this.messageType = '';
            }, 5000);
        }
    }));
});
</script>
@endsection

