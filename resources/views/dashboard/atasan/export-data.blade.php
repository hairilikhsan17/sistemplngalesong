@extends('layouts.dashboard')

@section('title', 'Export Data')

@section('content')
<div class="p-6" x-data="exportData()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Export Data</h1>
        <p class="text-gray-600 mt-2">Export semua data sistem atau data per kelompok ke format CSV</p>
    </div>

    <!-- Export Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Export Semua Data -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="download" class="w-8 h-8 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">Export Semua Data Kelompok</h2>
                    <p class="text-gray-600">Export semua data kelompok dengan tabel laporan dan job pekerjaan</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-medium text-blue-900 mb-2">Data yang akan diexport (DINAMIS):</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• <strong>Otomatis menyesuaikan</strong> dengan jumlah kelompok</li>
                        <li>• Jika ada kelompok baru → otomatis ditambahkan</li>
                        <li>• Jika ada kelompok dihapus → otomatis tidak muncul</li>
                        <li>• Tabel Input Laporan untuk setiap kelompok</li>
                        <li>• Tabel Input Job Pekerjaan untuk setiap kelompok</li>
                        <li>• Data lengkap dengan informasi kelompok</li>
                    </ul>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Total Data (Dinamis)</p>
                        <p class="text-xs text-gray-600">Otomatis menyesuaikan perubahan</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900" x-text="totalData.kelompok + ' Kelompok'"></p>
                        <p class="text-xs text-gray-600" x-text="totalData.karyawan + ' Karyawan'"></p>
                    </div>
                </div>
                
                <button @click="exportAllData()" 
                        :disabled="loading"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <i data-lucide="download" class="w-5 h-5 mr-2" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-5 h-5 mr-2 animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Mengexport...' : 'Export Semua Data Kelompok'"></span>
                </button>
            </div>
        </div>

        <!-- Export per Kelompok -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="users" class="w-8 h-8 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold text-gray-900">Export per Kelompok</h2>
                    <p class="text-gray-600">Export data berdasarkan kelompok tertentu</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="font-medium text-green-900 mb-2">Data yang akan diexport:</h3>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• Info Kelompok</li>
                        <li>• Data Karyawan Kelompok</li>
                        <li>• Laporan Kelompok</li>
                        <li>• Job Pekerjaan Kelompok</li>
                    </ul>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelompok</label>
                    <select x-model="selectedKelompok" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            x-init="console.log('Dropdown initialized with kelompokList:', kelompokList)">
                        <option value="">Pilih Kelompok</option>
                        <template x-for="kelompok in kelompokList" :key="kelompok.id">
                            <option :value="kelompok.id" x-text="kelompok.nama_kelompok"></option>
                        </template>
                    </select>
                    <!-- Debug info -->
                    <div x-show="kelompokList.length === 0" class="text-sm text-red-600 mt-2">
                        Tidak ada kelompok yang tersedia
                    </div>
                    <div x-show="kelompokList.length > 0" class="text-sm text-green-600 mt-2">
                        <span x-text="kelompokList.length"></span> kelompok tersedia
                    </div>
                </div>
                
                <div x-show="selectedKelompok" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Data Kelompok</p>
                        <p class="text-xs text-gray-600" x-text="getSelectedKelompokName()"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900" x-text="getKelompokDataCount() + ' Data'"></p>
                        <p class="text-xs text-gray-600">Karyawan & Laporan</p>
                    </div>
                </div>
                
                <button @click="exportByKelompok()" 
                        :disabled="loading || !selectedKelompok"
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <i data-lucide="download" class="w-5 h-5 mr-2" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-5 h-5 mr-2 animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Mengexport...' : 'Export Kelompok'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Kelompok List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Kelompok</h2>
                <button @click="refreshData()" 
                        class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Kelompok
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Kelompok
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Shift
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah Karyawan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah Laporan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="kelompok in kelompokList" :key="kelompok.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <button @click="copyKelompokId(kelompok.id)" 
                                            class="text-blue-600 hover:text-blue-900 font-mono text-sm">
                                        <span x-text="kelompok.id"></span>
                                        <i data-lucide="copy" class="w-3 h-3 ml-1 inline"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="kelompok.nama_kelompok"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="kelompok.shift === 'Shift 1' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      x-text="kelompok.shift">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="kelompok.karyawan_count">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="kelompok.laporan_count">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="exportSpecificKelompok(kelompok.id)" 
                                        class="text-green-600 hover:text-green-900 mr-3">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </button>
                                <button @click="viewKelompokDetails(kelompok.id)" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="kelompokList.length === 0">
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Tidak ada data kelompok
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export History -->
    <div class="mt-8 bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Riwayat Export</h2>
        </div>
        
        <div class="p-6">
            <div class="space-y-3">
                <template x-for="export in exportHistory" :key="export.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-600 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="export.filename"></p>
                                <p class="text-xs text-gray-600" x-text="export.created_at"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span :class="export.type === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                  x-text="export.type === 'all' ? 'Semua Data' : 'Per Kelompok'">
                            </span>
                            <button @click="downloadExport(export.filename)" 
                                    class="text-blue-600 hover:text-blue-900">
                                <i data-lucide="download" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </template>
                
                <div x-show="exportHistory.length === 0" class="text-center py-8">
                    <i data-lucide="file-spreadsheet" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500">Belum ada riwayat export</p>
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
    Alpine.data('exportData', () => ({
        loading: false,
        message: '',
        messageType: '',
        selectedKelompok: '',
        kelompokList: @json($kelompoks ?? []),
        totalData: {
            kelompok: {{ $totalData['kelompok'] ?? 0 }},
            karyawan: {{ $totalData['karyawan'] ?? 0 }},
            laporan: {{ $totalData['laporan'] ?? 0 }},
            job: {{ $totalData['job'] ?? 0 }}
        },
        exportHistory: [],
        
        async init() {
            await this.loadExportHistory();
            await this.loadKelompokList();
        },
        
        async loadKelompokList() {
            try {
                console.log('Loading kelompok list from controller:', this.kelompokList);
                // Data sudah di-load dari controller, tidak perlu API call
                if (this.kelompokList.length === 0) {
                    console.log('No kelompok data found, trying API...');
                    const response = await fetch('/api/kelompok', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.kelompokList = data.kelompok || [];
                        console.log('Loaded kelompok from API:', this.kelompokList);
                    }
                }
            } catch (error) {
                console.error('Error loading kelompok list:', error);
            }
        },
        
        async exportAllData() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/export-data/all', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `PLN_Galesong_Semua_Data_Kelompok_${new Date().toISOString().split('T')[0]}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showMessage('Export semua data kelompok berhasil!', 'success');
                    await this.loadExportHistory();
                } else {
                    this.showMessage('Gagal export semua data kelompok', 'error');
                }
            } catch (error) {
                console.error('Export error:', error);
                this.showMessage('Terjadi kesalahan saat export', 'error');
            }
            
            this.loading = false;
        },
        
        async exportByKelompok() {
            if (!this.selectedKelompok) {
                this.showMessage('Pilih kelompok terlebih dahulu', 'error');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch(`/api/export-data/kelompok?kelompok_id=${this.selectedKelompok}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    const kelompokName = this.getSelectedKelompokName().replace(/\s+/g, '_');
                    a.download = `PLN_Galesong_${kelompokName}_${new Date().toISOString().split('T')[0]}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showMessage('Export data kelompok berhasil!', 'success');
                    await this.loadExportHistory();
                } else {
                    this.showMessage('Gagal export data kelompok', 'error');
                }
            } catch (error) {
                console.error('Export error:', error);
                this.showMessage('Terjadi kesalahan saat export', 'error');
            }
            
            this.loading = false;
        },
        
        async exportSpecificKelompok(kelompokId) {
            this.loading = true;
            
            try {
                const response = await fetch(`/api/export-data/kelompok?kelompok_id=${kelompokId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    const kelompok = this.kelompokList.find(k => k.id === kelompokId);
                    const kelompokName = kelompok ? kelompok.nama_kelompok.replace(/\s+/g, '_') : 'Kelompok';
                    a.download = `PLN_Galesong_${kelompokName}_${new Date().toISOString().split('T')[0]}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showMessage('Export data kelompok berhasil!', 'success');
                    await this.loadExportHistory();
                } else {
                    this.showMessage('Gagal export data kelompok', 'error');
                }
            } catch (error) {
                console.error('Export error:', error);
                this.showMessage('Terjadi kesalahan saat export', 'error');
            }
            
            this.loading = false;
        },
        
        async loadExportHistory() {
            try {
                // Simulate loading export history
                // In real implementation, this would fetch from API
                this.exportHistory = [
                    {
                        id: 1,
                        filename: 'PLN_Galesong_All_Data_2025-01-12.csv',
                        type: 'all',
                        created_at: '2025-01-12 10:30:00'
                    },
                    {
                        id: 2,
                        filename: 'PLN_Galesong_Kelompok_1_2025-01-12.csv',
                        type: 'kelompok',
                        created_at: '2025-01-12 09:15:00'
                    }
                ];
            } catch (error) {
                console.error('Error loading export history:', error);
            }
        },
        
        async refreshData() {
            try {
                const response = await fetch('/api/kelompok');
                const result = await response.json();
                this.kelompokList = result.kelompok || [];
                await this.loadExportHistory();
                this.showMessage('Data berhasil diperbarui', 'success');
            } catch (error) {
                console.error('Error refreshing data:', error);
                this.showMessage('Gagal memperbarui data', 'error');
            }
        },
        
        copyKelompokId(kelompokId) {
            navigator.clipboard.writeText(kelompokId).then(() => {
                this.showMessage('ID Kelompok berhasil disalin', 'success');
            }).catch(() => {
                this.showMessage('Gagal menyalin ID Kelompok', 'error');
            });
        },
        
        getSelectedKelompokName() {
            const kelompok = this.kelompokList.find(k => k.id === this.selectedKelompok);
            return kelompok ? kelompok.nama_kelompok : '';
        },
        
        getKelompokDataCount() {
            const kelompok = this.kelompokList.find(k => k.id === this.selectedKelompok);
            if (!kelompok) return 0;
            return (kelompok.karyawan_count || 0) + (kelompok.laporan_count || 0);
        },
        
        viewKelompokDetails(kelompokId) {
            // Navigate to kelompok details page
            window.location.href = `/atasan/kelompok/${kelompokId}`;
        },
        
        downloadExport(filename) {
            // Download export file
            window.open(`/downloads/exports/${filename}`, '_blank');
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
