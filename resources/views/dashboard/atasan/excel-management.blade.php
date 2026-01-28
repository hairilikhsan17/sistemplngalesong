@extends('layouts.dashboard')

@section('title', 'Manajemen Excel')

@section('content')
<div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" x-data="excelManagement()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i data-lucide="file-spreadsheet" class="w-8 h-8 text-white"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                    Manajemen Excel
                </h1>
                <p class="text-gray-600 mt-2 text-lg">Kelola upload data dan pembuatan file Excel template</p>
            </div>
        </div>
    </div>

    <!-- Action Cards & Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Upload Data -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md">
                        <i data-lucide="upload" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-900">Upload Data</h3>
                        <p class="text-sm text-gray-600">Upload data dari file Excel</p>
                    </div>
                </div>
            </div>
            <button @click="showUploadModal = true" 
                    class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg font-semibold transform hover:scale-105">
                <i data-lucide="upload-cloud" class="w-4 h-4 inline mr-2"></i>
                Upload Excel
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-6 border border-purple-200">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-white"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-gray-900">Statistik</h3>
                    <p class="text-sm text-gray-600">Ringkasan data Excel</p>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center bg-white/60 rounded-lg px-4 py-3 backdrop-blur-sm">
                    <span class="text-sm font-medium text-gray-700">Total Ukuran:</span>
                    <span class="text-xl font-bold text-purple-600" x-text="formatFileSize(totalFileSize)"></span>
                </div>
                <div class="flex justify-between items-center bg-white/60 rounded-lg px-4 py-3 backdrop-blur-sm">
                    <span class="text-sm font-medium text-gray-700">Laporan Karyawan:</span>
                    <span class="text-xl font-bold text-green-600" x-text="laporanCount"></span>
                </div>
            </div>
        </div>

        <!-- Quick Stats Card -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl shadow-lg p-6 border border-green-200">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-md">
                    <i data-lucide="file-check" class="w-6 h-6 text-white"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-gray-900">Laporan</h3>
                    <p class="text-sm text-gray-600">File Laporan Karyawan</p>
                </div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-green-600" x-text="filteredLaporanFiles.length"></div>
                <p class="text-sm text-gray-600 mt-2 font-medium">File tersedia</p>
            </div>
        </div>

    </div>

    <!-- Laporan Karyawan Files -->
    <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">File Excel Terbaru Laporan Karyawan</h2>
                        <p class="text-sm text-green-100">Kelola file Excel laporan karyawan</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Filter Bulan -->
                    <select x-model="filterLaporan.bulan" 
                            @change="filterLaporanFiles()"
                            class="px-4 py-2 bg-white/90 backdrop-blur-sm border border-white/30 rounded-lg text-sm font-medium text-gray-700 focus:ring-2 focus:ring-white focus:border-transparent shadow-sm hover:bg-white transition-colors">
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
                    <div class="relative">
                        <input type="number" 
                               x-model="filterLaporan.tahun" 
                               @input="filterLaporanFiles()"
                               placeholder="Tahun (contoh: 2024)"
                               min="2000"
                               max="2100"
                               class="px-4 py-2 pr-10 bg-white/90 backdrop-blur-sm border border-white/30 rounded-lg text-sm font-medium text-gray-700 focus:ring-2 focus:ring-white focus:border-transparent shadow-sm hover:bg-white transition-colors w-32">
                        <button x-show="filterLaporan.tahun" 
                                @click="filterLaporan.tahun = ''; filterLaporanFiles()"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    
                    <button @click="refreshLaporanFiles()" 
                            :disabled="loading"
                            class="flex items-center px-4 py-2 bg-white/90 backdrop-blur-sm border border-white/30 rounded-lg text-sm font-medium text-gray-700 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-all">
                        <i data-lucide="refresh-cw" :class="loading ? 'animate-spin' : ''" class="w-4 h-4 mr-2"></i>
                        <span x-text="loading ? 'Loading...' : 'Refresh'"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Nama File
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Ukuran
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Dibuat
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="file in filteredLaporanFiles" :key="file.name">
                        <tr class="hover:bg-green-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                                        <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900" x-text="file.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="file.type === 'template' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 'bg-gradient-to-r from-green-500 to-emerald-600 text-white'"
                                      class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm" 
                                      x-text="file.type === 'template' ? 'Template' : 'Upload'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm font-medium text-gray-700">
                                    <i data-lucide="hard-drive" class="w-4 h-4 mr-2 text-gray-400"></i>
                                    <span x-text="formatFileSize(file.size)"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-700">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                                    <span x-text="formatDate(file.created)"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="downloadFile(file.name)" 
                                            class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                                            title="Download file">
                                        <i data-lucide="download" class="w-4 h-4 mr-1.5"></i>
                                        <span class="text-xs font-semibold">Download</span>
                                    </button>
                                    <button @click="deleteFile(file.name)" 
                                            class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                                            title="Hapus file">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1.5"></i>
                                        <span class="text-xs font-semibold">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="filteredLaporanFiles.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-100 rounded-full mb-3">
                                    <i data-lucide="file-x" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Belum ada file Excel Laporan Karyawan</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Excel Modal -->
    <div x-show="showUploadModal" 
         x-cloak
         x-transition
         @click.away="showUploadModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-lg flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="upload-cloud" class="w-6 h-6 text-white mr-3"></i>
                    <h2 class="text-xl font-semibold text-white">Upload Data Excel</h2>
                </div>
                <button @click="closeUploadModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <form @submit.prevent="uploadExcel()" class="space-y-6">
                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            File Excel <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors"
                             @dragover.prevent
                             @drop.prevent="handleFileDrop($event)">
                            <div x-show="!selectedFileUpload">
                                <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                                <p class="text-gray-600 mb-2">Drag & drop file Excel di sini atau</p>
                                <input type="file" 
                                       @change="handleFileSelect($event)"
                                       accept=".xlsx,.xls"
                                       class="hidden" 
                                       id="upload-file-input">
                                <label for="upload-file-input" 
                                       class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                    Pilih File
                                </label>
                                <p class="text-xs text-gray-500 mt-2">Format: .xlsx, .xls (Max 10MB)</p>
                            </div>
                            <div x-show="selectedFileUpload" class="text-left">
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <i data-lucide="file-spreadsheet" class="w-8 h-8 text-green-600 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" x-text="selectedFileUpload?.name"></p>
                                            <p class="text-xs text-gray-500" x-text="formatFileSize(selectedFileUpload?.size)"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeFileUpload()" class="text-red-600 hover:text-red-800">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Type - Hidden, default ke laporan_karyawan -->
                    <input type="hidden" x-model="uploadFormData.jenis_data" value="laporan_karyawan">

                    <!-- Period Selection -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Bulan <span class="text-red-500">*</span>
                            </label>
                            <select x-model="uploadFormData.bulan" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   x-model="uploadFormData.tahun"
                                   min="2020" max="2030"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Upload Options -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Opsi Upload</h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="uploadFormData.skip_errors" class="mr-2">
                                <span class="text-sm text-gray-700">Lewati baris dengan error</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="uploadFormData.update_existing" class="mr-2">
                                <span class="text-sm text-gray-700">Update data yang sudah ada</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="uploadFormData.validate_data" class="mr-2">
                                <span class="text-sm text-gray-700">Validasi data sebelum upload</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-3">
                        <button type="button" 
                                @click="closeUploadModal()"
                                class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="!selectedFileUpload || !uploadFormData.bulan || !uploadFormData.tahun || uploadLoading"
                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                            <i data-lucide="upload" class="w-4 h-4 mr-2" x-show="!uploadLoading"></i>
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="uploadLoading"></i>
                            <span x-text="uploadLoading ? 'Mengupload...' : 'Upload Excel'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Progress Modal -->
    <div x-show="showUploadProgress" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Progress</h3>
            
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span x-text="progressText"></span>
                    <span x-text="progressPercent + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         :style="'width: ' + progressPercent + '%'"></div>
                </div>
            </div>
            
            <div x-show="uploadResult" class="mt-4">
                <div :class="uploadResult.success ? 'text-green-600' : 'text-red-600'">
                    <i :data-lucide="uploadResult.success ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mr-2 inline"></i>
                    <span x-text="uploadResult.message"></span>
                </div>
                
                <div x-show="uploadResult.success && uploadResult.data" class="mt-3">
                    <div class="flex items-center bg-green-50 p-3 rounded-lg border border-green-200">
                        <i data-lucide="file-spreadsheet" class="w-8 h-8 text-green-600 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="selectedFileUpload?.name || 'File Excel'"></p>
                            <p class="text-xs text-gray-600 mt-1">Data diproses: <span x-text="uploadResult.data.processed || 0"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button @click="closeUploadProgress()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div x-show="message" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full"
         :class="messageType === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-red-500 to-red-600 text-white'"
         class="fixed top-4 right-4 rounded-xl px-6 py-4 shadow-2xl z-50 min-w-[300px] max-w-md border border-white/20 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-6 h-6"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-semibold" x-text="message"></p>
            </div>
            <button @click="message = ''" class="ml-4 text-white/80 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('excelManagement', () => ({
        excelFiles: @json($excelFiles),
        loading: false,
        message: '',
        messageType: '',
        
        // Upload Modal
        showUploadModal: false,
        selectedFileUpload: null,
        uploadLoading: false,
        showUploadProgress: false,
        progressPercent: 0,
        progressText: '',
        uploadResult: null,
        
        uploadFormData: {
            jenis_data: 'laporan_karyawan', // Default ke laporan karyawan
            bulan: '',
            tahun: new Date().getFullYear(),
            skip_errors: true,
            update_existing: false,
            validate_data: true
        },
        
        filterLaporan: {
            bulan: '',
            tahun: ''
        },
        
        get totalFiles() {
            return this.excelFiles.length;
        },
        
        get totalFileSize() {
            return this.excelFiles.reduce((total, file) => total + (file.size || 0), 0);
        },
        
        get laporanCount() {
            return this.excelFiles.filter(file => 
                file.name.toLowerCase().includes('laporan_karyawan') || 
                file.name.toLowerCase().includes('laporan')
            ).length;
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
        
        async init() {
            console.log('Initializing Excel Management...');
            await this.refreshFiles();
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
            const result = await SwalHelper.confirmDelete(
                '⚠️ Hapus File Excel?',
                `Apakah Anda yakin ingin menghapus file "${fileName}"?`
            );
            
            if (!result.isConfirmed) {
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
                    await this.refreshFiles();
                    // Reinitialize icons after refresh
                    setTimeout(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }, 200);
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
                    
                    // Reinitialize Lucide icons after files are loaded
                    setTimeout(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }, 100);
                    
                    // Initialize again after Alpine.js updates the DOM
                    setTimeout(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }, 300);
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
                
                // Final initialization after loading completes
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 500);
            }
        },
        
        filterLaporanFiles() {
            // Filter sudah dihitung otomatis melalui computed property
            console.log('Filtering laporan files:', this.filterLaporan);
        },
        
        async refreshLaporanFiles() {
            console.log('Refreshing Laporan Karyawan files...');
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
        },
        
        // Upload Modal Functions
        closeUploadModal() {
            this.showUploadModal = false;
            this.selectedFileUpload = null;
            this.uploadFormData = {
                jenis_data: '',
                bulan: '',
                tahun: new Date().getFullYear(),
                skip_errors: true,
                update_existing: false,
                validate_data: true
            };
            document.getElementById('upload-file-input').value = '';
        },
        
        handleFileSelect(event) {
            this.selectedFileUpload = event.target.files[0];
        },
        
        handleFileDrop(event) {
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.selectedFileUpload = files[0];
            }
        },
        
        removeFileUpload() {
            this.selectedFileUpload = null;
            document.getElementById('upload-file-input').value = '';
        },
        
        closeUploadProgress() {
            const wasSuccess = this.uploadResult && this.uploadResult.success;
            this.showUploadProgress = false;
            this.progressPercent = 0;
            this.progressText = '';
            this.uploadResult = null;
            if (wasSuccess) {
                this.closeUploadModal();
                this.refreshFiles();
            }
            // Reinitialize Lucide icons after closing
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        },
        
        async uploadExcel() {
            if (!this.selectedFileUpload) {
                this.showMessage('Pilih file Excel terlebih dahulu', 'error');
                return;
            }
            
            this.uploadLoading = true;
            this.showUploadProgress = true;
            this.progressPercent = 0;
            this.progressText = 'Mempersiapkan upload...';
            
            // Initialize Lucide icons when modal opens
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
            
            const formData = new FormData();
            formData.append('excel_file', this.selectedFileUpload);
            formData.append('jenis_data', this.uploadFormData.jenis_data);
            formData.append('bulan', this.uploadFormData.bulan);
            formData.append('tahun', this.uploadFormData.tahun);
            formData.append('skip_errors', this.uploadFormData.skip_errors);
            formData.append('update_existing', this.uploadFormData.update_existing);
            formData.append('validate_data', this.uploadFormData.validate_data);
            
            try {
                this.progressText = 'Mengupload file...';
                this.progressPercent = 30;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                
                const response = await fetch('/api/excel/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                this.progressText = 'Memproses data...';
                this.progressPercent = 70;
                
                const contentType = response.headers.get('content-type');
                
                if (!contentType || !contentType.includes('application/json')) {
                    const textResponse = await response.text();
                    throw new Error('Server mengembalikan response HTML, bukan JSON. Status: ' + response.status);
                }
                
                const result = await response.json();
                
                this.progressPercent = 100;
                this.progressText = 'Selesai!';
                
                this.uploadResult = result;
                
                // Reinitialize Lucide icons after showing result
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 200);
                
                // Initialize again after a bit more delay to ensure DOM is ready
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 500);
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    setTimeout(async () => {
                        this.closeUploadModal();
                        await this.refreshFiles();
                        // Reinitialize icons after refresh
                        setTimeout(() => {
                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        }, 200);
                    }, 2000);
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                console.error('Upload error:', error);
                this.progressPercent = 0;
                this.progressText = 'Error!';
                
                let errorMessage = 'Terjadi kesalahan saat upload';
                if (error.message.includes('Unexpected token')) {
                    errorMessage = 'Server mengembalikan response yang tidak valid. Pastikan Anda sudah login dan memiliki akses.';
                } else {
                    errorMessage = 'Terjadi kesalahan saat upload: ' + error.message;
                }
                
                this.uploadResult = {
                    success: false,
                    message: errorMessage
                };
                this.showMessage(errorMessage, 'error');
            }
            
            this.uploadLoading = false;
        }
    }));
});
</script>
@endsection

