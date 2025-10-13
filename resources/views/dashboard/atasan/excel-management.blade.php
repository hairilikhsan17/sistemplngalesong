@extends('layouts.dashboard')

@section('title', 'Manajemen Excel')

@section('content')
<div class="p-6" x-data="excelManagement()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Excel</h1>
        <p class="text-gray-600 mt-2">Kelola upload data dan pembuatan file Excel template</p>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Upload Data -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i data-lucide="upload" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Upload Data</h3>
                        <p class="text-sm text-gray-600">Upload data dari file Excel</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('atasan.excel.upload') }}" 
               class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                Upload Excel
            </a>
        </div>

        <!-- Create Template -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i data-lucide="file-plus" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Buat Template</h3>
                        <p class="text-sm text-gray-600">Buat file Excel template baru</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('atasan.excel.create') }}" 
               class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                Buat Template
            </a>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                        <p class="text-sm text-gray-600">Ringkasan data Excel</p>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total File:</span>
                    <span class="text-sm font-medium" x-text="totalFiles"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Template:</span>
                    <span class="text-sm font-medium" x-text="templateCount"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Upload:</span>
                    <span class="text-sm font-medium" x-text="uploadCount"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Karyawan Files -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">File Excel Terbaru Laporan Karyawan</h2>
                <div class="flex items-center space-x-4">
                    <!-- Filter Bulan -->
                    <select x-model="filterLaporan.bulan" 
                            @change="filterLaporanFiles()"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Bulan</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                    
                    <!-- Filter Tahun -->
                    <select x-model="filterLaporan.tahun" 
                            @change="filterLaporanFiles()"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Tahun</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                    
                    <button @click="refreshLaporanFiles()" 
                            :disabled="loading"
                            class="flex items-center text-sm text-gray-600 hover:text-gray-900 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <i data-lucide="refresh-cw" :class="loading ? 'animate-spin' : ''" class="w-4 h-4 mr-2"></i>
                        <span x-text="loading ? 'Loading...' : 'Refresh'"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama File
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ukuran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="file in filteredLaporanFiles" :key="file.name">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-600 mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900" x-text="file.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="file.type === 'template' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      x-text="file.type === 'template' ? 'Template' : 'Upload'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatFileSize(file.size)">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatDate(file.created)">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="downloadFile(file.name)" 
                                            class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 rounded-md transition-colors"
                                            title="Download file">
                                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                        Download
                                    </button>
                                    <button @click="deleteFile(file.name)" 
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 hover:border-red-400 rounded-md transition-colors"
                                            title="Hapus file">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="filteredLaporanFiles.length === 0">
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada file Excel Laporan Karyawan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Job Pekerjaan Files -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">File Excel Terbaru Job Pekerjaan</h2>
                <div class="flex items-center space-x-4">
                    <!-- Filter Bulan -->
                    <select x-model="filterJob.bulan" 
                            @change="filterJobFiles()"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Semua Bulan</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                    
                    <!-- Filter Tahun -->
                    <select x-model="filterJob.tahun" 
                            @change="filterJobFiles()"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Semua Tahun</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                    
                    <button @click="refreshJobFiles()" 
                            :disabled="loading"
                            class="flex items-center text-sm text-gray-600 hover:text-gray-900 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <i data-lucide="refresh-cw" :class="loading ? 'animate-spin' : ''" class="w-4 h-4 mr-2"></i>
                        <span x-text="loading ? 'Loading...' : 'Refresh'"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama File
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ukuran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="file in filteredJobFiles" :key="file.name">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i data-lucide="file-spreadsheet" class="w-5 h-5 text-orange-600 mr-3"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900" x-text="file.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="file.type === 'template' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'"
                                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                      x-text="file.type === 'template' ? 'Template' : 'Upload'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatFileSize(file.size)">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatDate(file.created)">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="downloadFile(file.name)" 
                                            class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 rounded-md transition-colors"
                                            title="Download file">
                                        <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                        Download
                                    </button>
                                    <button @click="deleteFile(file.name)" 
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 hover:border-red-400 rounded-md transition-colors"
                                            title="Hapus file">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="filteredJobFiles.length === 0">
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada file Excel Job Pekerjaan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Quick Upload -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Cepat</h3>
            <form @submit.prevent="quickUpload()" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel</label>
                    <input type="file" 
                           @change="handleFileSelect($event)"
                           accept=".xlsx,.xls"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select x-model="quickUploadData.bulan" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Bulan</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" 
                               x-model="quickUploadData.tahun"
                               min="2020" max="2030"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Data</label>
                    <select x-model="quickUploadData.jenis_data" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Jenis Data</option>
                        <option value="laporan_karyawan">Laporan Karyawan</option>
                        <option value="job_pekerjaan">Job Pekerjaan</option>
                    </select>
                </div>
                <button type="submit" 
                        :disabled="!selectedFile || !quickUploadData.bulan || !quickUploadData.tahun || !quickUploadData.jenis_data"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    Upload Sekarang
                </button>
            </form>
        </div>

        <!-- Quick Template -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Cepat</h3>
            <form @submit.prevent="quickTemplate()" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select x-model="quickTemplateData.bulan" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Pilih Bulan</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                        <input type="number" 
                               x-model="quickTemplateData.tahun"
                               min="2020" max="2030"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Data</label>
                    <select x-model="quickTemplateData.jenis_data" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Pilih Jenis Data</option>
                        <option value="laporan_karyawan">Laporan Karyawan</option>
                        <option value="job_pekerjaan">Job Pekerjaan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelompok (Opsional)</label>
                    <select x-model="quickTemplateData.kelompok_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id }}">{{ $kelompok->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" 
                        :disabled="!quickTemplateData.bulan || !quickTemplateData.tahun || !quickTemplateData.jenis_data"
                        class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    Buat Template
                </button>
            </form>
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
    Alpine.data('excelManagement', () => ({
        excelFiles: @json($excelFiles),
        selectedFile: null,
        loading: false,
        message: '',
        messageType: '',
        
        filterLaporan: {
            bulan: '',
            tahun: ''
        },
        
        filterJob: {
            bulan: '',
            tahun: ''
        },
        
        quickUploadData: {
            bulan: '',
            tahun: new Date().getFullYear(),
            jenis_data: ''
        },
        
        quickTemplateData: {
            bulan: '',
            tahun: new Date().getFullYear(),
            jenis_data: '',
            kelompok_id: ''
        },
        
        get totalFiles() {
            return this.excelFiles.length;
        },
        
        get templateCount() {
            return this.excelFiles.filter(file => file.type === 'template').length;
        },
        
        get uploadCount() {
            return this.excelFiles.filter(file => file.type === 'upload').length;
        },
        
        get filteredLaporanFiles() {
            let files = this.excelFiles.filter(file => 
                file.name.toLowerCase().includes('laporan_karyawan') || 
                file.name.toLowerCase().includes('laporan')
            );
            
            if (this.filterLaporan.bulan) {
                files = files.filter(file => 
                    file.name.toLowerCase().includes(this.filterLaporan.bulan.toLowerCase())
                );
            }
            
            if (this.filterLaporan.tahun) {
                files = files.filter(file => 
                    file.name.includes(this.filterLaporan.tahun)
                );
            }
            
            return files;
        },
        
        get filteredJobFiles() {
            let files = this.excelFiles.filter(file => 
                file.name.toLowerCase().includes('job_pekerjaan') || 
                file.name.toLowerCase().includes('job')
            );
            
            if (this.filterJob.bulan) {
                files = files.filter(file => 
                    file.name.toLowerCase().includes(this.filterJob.bulan.toLowerCase())
                );
            }
            
            if (this.filterJob.tahun) {
                files = files.filter(file => 
                    file.name.includes(this.filterJob.tahun)
                );
            }
            
            return files;
        },
        
        async init() {
            console.log('Initializing Excel Management...');
            await this.refreshFiles();
        },
        
        handleFileSelect(event) {
            this.selectedFile = event.target.files[0];
        },
        
        async quickUpload() {
            if (!this.selectedFile) {
                this.showMessage('Pilih file Excel terlebih dahulu', 'error');
                return;
            }
            
            this.loading = true;
            
            const formData = new FormData();
            formData.append('excel_file', this.selectedFile);
            formData.append('bulan', this.quickUploadData.bulan);
            formData.append('tahun', this.quickUploadData.tahun);
            formData.append('jenis_data', this.quickUploadData.jenis_data);
            
            try {
                const response = await fetch('/api/excel/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    await this.refreshFiles();
                    // Reset form
                    this.selectedFile = null;
                    this.quickUploadData = { bulan: '', tahun: new Date().getFullYear(), jenis_data: '' };
                    document.querySelector('input[type="file"]').value = '';
                } else {
                    this.showMessage(result.message, 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat upload', 'error');
            }
            
            this.loading = false;
        },
        
        async quickTemplate() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/excel/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.quickTemplateData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    await this.refreshFiles();
                    // Download file
                    window.open(result.file_url, '_blank');
                    // Reset form
                    this.quickTemplateData = { bulan: '', tahun: new Date().getFullYear(), jenis_data: '', kelompok_id: '' };
                } else {
                    this.showMessage(result.message, 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat membuat template', 'error');
            }
            
            this.loading = false;
        },
        
        async downloadFile(fileName) {
            try {
                const response = await fetch(`/api/excel/download/${fileName}`);
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    this.showMessage('Gagal mengunduh file', 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat mengunduh file', 'error');
            }
        },
        
        async deleteFile(fileName) {
            if (!confirm('Yakin ingin menghapus file ini?')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/excel/delete/${fileName}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    this.refreshFiles();
                } else {
                    this.showMessage(result.message, 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menghapus file', 'error');
            }
        },
        
        async refreshFiles() {
            try {
                this.loading = true;
                console.log('Refreshing Excel files...');
                
                const response = await fetch('/api/excel/files', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Files loaded:', result.files);
                    
                    this.excelFiles = result.files || [];
                    this.showMessage(`Berhasil memuat ${this.excelFiles.length} file Excel`, 'success');
                } else {
                    console.error('Failed to load files:', response.status);
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    this.showMessage('Gagal memuat file Excel', 'error');
                }
            } catch (error) {
                console.error('Error refreshing files:', error);
                this.showMessage('Terjadi kesalahan saat memuat file Excel', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        filterLaporanFiles() {
            // Filter sudah dihitung otomatis melalui computed property
            console.log('Filtering laporan files:', this.filterLaporan);
        },
        
        filterJobFiles() {
            // Filter sudah dihitung otomatis melalui computed property
            console.log('Filtering job files:', this.filterJob);
        },
        
        async refreshLaporanFiles() {
            console.log('Refreshing Laporan Karyawan files...');
            await this.refreshFiles();
        },
        
        async refreshJobFiles() {
            console.log('Refreshing Job Pekerjaan files...');
            await this.refreshFiles();
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
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

