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
                    <p class="text-2xl font-bold text-gray-900">{{ $kelompok->shift ?? '-' }}</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Group Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Kelompok</h2>
            
            <form @submit.prevent="updateGroupInfo()" class="space-y-6">
                <!-- Group Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kelompok <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="groupSettings.nama_kelompok"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Shift -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Shift Kerja <span class="text-red-500">*</span>
                    </label>
                    <select x-model="groupSettings.shift"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Shift 1">Shift 1</option>
                        <option value="Shift 2">Shift 2</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Kelompok
                    </label>
                    <textarea x-model="groupSettings.deskripsi"
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Deskripsi tentang kelompok kerja..."></textarea>
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi Kerja
                    </label>
                    <input type="text" 
                           x-model="groupSettings.lokasi"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Lokasi area kerja kelompok">
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <input type="tel" 
                               x-model="groupSettings.telepon"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Kelompok
                        </label>
                        <input type="email" 
                               x-model="groupSettings.email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="kelompok@pln.co.id">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="loading"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Informasi'"></span>
                </button>
            </form>
        </div>

        <!-- Account & Security -->
        <div class="space-y-6">
            <!-- Account Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Akun & Keamanan</h2>
                
                <form @submit.prevent="updateAccount()" class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               x-model="accountData.current_password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan password lama">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               x-model="accountData.new_password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan password baru">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               x-model="accountData.new_password_confirmation"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Konfirmasi password baru">
                    </div>

                    <button type="submit" 
                            :disabled="loading"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                        Update Password
                    </button>
                </form>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Pengaturan Notifikasi</h2>
                
                <form @submit.prevent="updateNotifications()" class="space-y-4">
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="notificationSettings.email_notifications" class="mr-3">
                            <span class="text-sm text-gray-700">Email Notifikasi</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" x-model="notificationSettings.laporan_reminder" class="mr-3">
                            <span class="text-sm text-gray-700">Pengingat Laporan Harian</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" x-model="notificationSettings.job_assignment" class="mr-3">
                            <span class="text-sm text-gray-700">Notifikasi Penugasan Job</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" x-model="notificationSettings.performance_update" class="mr-3">
                            <span class="text-sm text-gray-700">Update Performa Kelompok</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Waktu Pengingat Laporan
                        </label>
                        <select x-model="notificationSettings.reminder_time"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>

                    <button type="submit" 
                            :disabled="loading"
                            class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                        Simpan Notifikasi
                    </button>
                </form>
            </div>

            <!-- Work Schedule -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Jadwal Kerja</h2>
                
                <form @submit.prevent="updateWorkSchedule()" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Masuk
                            </label>
                            <input type="time" 
                                   x-model="workSchedule.start_time"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Keluar
                            </label>
                            <input type="time" 
                                   x-model="workSchedule.end_time"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hari Kerja
                        </label>
                        <div class="grid grid-cols-7 gap-2">
                            <template x-for="(day, index) in workDays" :key="index">
                                <label class="flex flex-col items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50"
                                       :class="workSchedule.work_days.includes(day.value) ? 'bg-blue-50 border-blue-300' : 'border-gray-300'">
                                    <input type="checkbox" 
                                           x-model="workSchedule.work_days"
                                           :value="day.value"
                                           class="hidden">
                                    <span class="text-xs font-medium" x-text="day.label"></span>
                                    <span class="text-xs text-gray-500" x-text="day.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi Istirahat (menit)
                        </label>
                        <input type="number" 
                               x-model="workSchedule.break_duration"
                               min="15" max="120"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <button type="submit" 
                            :disabled="loading"
                            class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                        Simpan Jadwal
                    </button>
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
    Alpine.data('kelompokSettings', () => ({
        loading: false,
        message: '',
        messageType: '',
        monthlyReports: 0,
        
        groupSettings: {
            nama_kelompok: '{{ $kelompok->nama_kelompok ?? "" }}',
            shift: '{{ $kelompok->shift ?? "Shift 1" }}',
            deskripsi: '{{ $kelompok->deskripsi ?? "" }}',
            lokasi: '{{ $kelompok->lokasi ?? "" }}',
            telepon: '{{ $kelompok->telepon ?? "" }}',
            email: '{{ $kelompok->email ?? "" }}'
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
        
        workSchedule: {
            start_time: '08:00',
            end_time: '17:00',
            work_days: ['senin', 'selasa', 'rabu', 'kamis', 'jumat'],
            break_duration: 60
        },
        
        workDays: [
            { value: 'senin', label: 'S', name: 'Senin' },
            { value: 'selasa', label: 'S', name: 'Selasa' },
            { value: 'rabu', label: 'R', name: 'Rabu' },
            { value: 'kamis', label: 'K', name: 'Kamis' },
            { value: 'jumat', label: 'J', name: 'Jumat' },
            { value: 'sabtu', label: 'S', name: 'Sabtu' },
            { value: 'minggu', label: 'M', name: 'Minggu' }
        ],
        
        async init() {
            await this.loadMonthlyReports();
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
        
        async updateGroupInfo() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/kelompok/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.groupSettings)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menyimpan informasi kelompok', 'error');
            }
            
            this.loading = false;
        },
        
        async updateAccount() {
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
                    this.showMessage(result.message, 'success');
                    // Reset password fields
                    this.accountData = {
                        current_password: '',
                        new_password: '',
                        new_password_confirmation: ''
                    };
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat memperbarui akun', 'error');
            }
            
            this.loading = false;
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
        
        async updateWorkSchedule() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/kelompok/work-schedule', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.workSchedule)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menyimpan jadwal kerja', 'error');
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


