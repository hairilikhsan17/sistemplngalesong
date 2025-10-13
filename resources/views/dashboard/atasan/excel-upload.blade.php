@extends('layouts.dashboard')

@section('title', 'Upload Data Excel')

@section('content')
<div class="p-6" x-data="excelUpload()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Upload Data Excel</h1>
                <p class="text-gray-600 mt-2">Upload dan proses data dari file Excel ke database</p>
            </div>
            <a href="{{ route('atasan.excel.index') }}" 
               class="flex items-center text-gray-600 hover:text-gray-900">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke Manajemen Excel
            </a>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Form Upload Excel</h2>
            
            <form @submit.prevent="uploadExcel()" class="space-y-6">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        File Excel <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors"
                         @dragover.prevent
                         @drop.prevent="handleFileDrop($event)">
                        <div x-show="!selectedFile">
                            <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-600 mb-2">Drag & drop file Excel di sini atau</p>
                            <input type="file" 
                                   @change="handleFileSelect($event)"
                                   accept=".xlsx,.xls"
                                   class="hidden" 
                                   id="file-input">
                            <label for="file-input" 
                                   class="cursor-pointer bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Pilih File
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Format: .xlsx, .xls (Max 10MB)</p>
                        </div>
                        <div x-show="selectedFile" class="text-left">
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center">
                                    <i data-lucide="file-spreadsheet" class="w-8 h-8 text-green-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="selectedFile?.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(selectedFile?.size)"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removeFile()" class="text-red-600 hover:text-red-800">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Data <span class="text-red-500">*</span>
                    </label>
                    <select x-model="formData.jenis_data" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Jenis Data</option>
                        <option value="laporan_karyawan">Laporan Karyawan</option>
                        <option value="job_pekerjaan">Job Pekerjaan</option>
                    </select>
                </div>

                <!-- Period Selection -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bulan <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.bulan" 
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
                               x-model="formData.tahun"
                               min="2020" max="2030"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Upload Options -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Opsi Upload</h3>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.skip_errors" class="mr-2">
                            <span class="text-sm text-gray-700">Lewati baris dengan error</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.update_existing" class="mr-2">
                            <span class="text-sm text-gray-700">Update data yang sudah ada</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.validate_data" class="mr-2">
                            <span class="text-sm text-gray-700">Validasi data sebelum upload</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="!selectedFile || !formData.jenis_data || !formData.bulan || !formData.tahun || loading"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <i data-lucide="upload" class="w-4 h-4 mr-2" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Mengupload...' : 'Upload Excel'"></span>
                </button>
            </form>
        </div>

        <!-- Upload History & Instructions -->
        <div class="space-y-6">
            <!-- Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Panduan Upload</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-blue-600">1</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Format File</h3>
                            <p class="text-sm text-gray-600">Pastikan file Excel memiliki kolom: Nama Karyawan, Kelompok, Tanggal, Waktu Penyelesaian, Keterangan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-blue-600">2</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Data Valid</h3>
                            <p class="text-sm text-gray-600">Pastikan nama karyawan dan kelompok sesuai dengan data yang ada di sistem</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-blue-600">3</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Format Tanggal</h3>
                            <p class="text-sm text-gray-600">Gunakan format YYYY-MM-DD atau DD/MM/YYYY untuk kolom tanggal</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Uploads -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Upload Terbaru</h2>
                    <button @click="loadRecentUploads()" 
                            class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i>
                        Refresh
                    </button>
                </div>
                <!-- Table Header -->
                <div class="bg-gray-100 rounded-lg p-3 mb-3">
                    <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700">
                        <div class="col-span-4">Nama File</div>
                        <div class="col-span-2">Tipe</div>
                        <div class="col-span-2">Ukuran</div>
                        <div class="col-span-2">Dibuat</div>
                        <div class="col-span-2">Aksi</div>
                    </div>
                </div>
                
                <!-- Table Body -->
                <div class="space-y-2">
                    <template x-for="upload in recentUploads" :key="upload.id">
                        <div class="bg-white border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                            <div class="grid grid-cols-12 gap-4 items-center text-sm">
                                <!-- Nama File -->
                                <div class="col-span-4 flex items-center">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600 mr-2"></i>
                                    <span class="text-gray-900 font-medium" x-text="upload.filename"></span>
                                </div>
                                
                                <!-- Tipe -->
                                <div class="col-span-2">
                                    <span :class="upload.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                          class="px-2 py-1 rounded-full text-xs font-medium" 
                                          x-text="upload.status === 'success' ? 'Upload' : 'Error'">
                                    </span>
                                </div>
                                
                                <!-- Ukuran -->
                                <div class="col-span-2 text-gray-600" x-text="upload.fileSize || '10.88 KB'">
                                </div>
                                
                                <!-- Dibuat -->
                                <div class="col-span-2 text-gray-600" x-text="upload.uploaded_at">
                                </div>
                                
                                <!-- Aksi -->
                                <div class="col-span-2 flex space-x-1">
                                    <button @click="downloadFile(upload.filename)" 
                                            class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            title="Download file">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="deleteFile(upload.filename)" 
                                            class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                            title="Hapus file">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="recentUploads.length === 0" class="text-center py-8">
                        <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <p class="text-gray-500">Belum ada file Excel</p>
                    </div>
                </div>
            </div>

            <!-- Template Download -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-2">Butuh Template?</h3>
                <p class="text-sm text-blue-700 mb-4">Download template Excel untuk memudahkan input data</p>
                <a href="{{ route('atasan.excel.create') }}" 
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                    Download Template
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Progress Modal -->
    <div x-show="showProgress" 
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
                
                <div x-show="uploadResult.success && uploadResult.data" class="mt-3 text-sm text-gray-600">
                    <p>Data diproses: <span x-text="uploadResult.data.processed"></span></p>
                    <p x-show="uploadResult.data.errors > 0">Error: <span x-text="uploadResult.data.errors"></span></p>
                </div>
                
                <div x-show="uploadResult.data && uploadResult.data.error_details && uploadResult.data.error_details.length > 0" class="mt-3">
                    <details class="text-sm">
                        <summary class="cursor-pointer text-red-600">Lihat Detail Error</summary>
                        <div class="mt-2 max-h-32 overflow-y-auto bg-red-50 p-2 rounded text-xs">
                            <template x-for="error in uploadResult.data.error_details" :key="error">
                                <p x-text="error" class="text-red-700"></p>
                            </template>
                        </div>
                    </details>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button @click="showProgress = false" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Tutup
                </button>
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
    Alpine.data('excelUpload', () => ({
        selectedFile: null,
        loading: false,
        message: '',
        messageType: '',
        showProgress: false,
        progressPercent: 0,
        progressText: '',
        uploadResult: null,
        
        formData: {
            jenis_data: '',
            bulan: '',
            tahun: new Date().getFullYear(),
            skip_errors: true,
            update_existing: false,
            validate_data: true
        },
        
        recentUploads: [],
        
        async init() {
            await this.loadRecentUploads();
        },
        
        async loadRecentUploads() {
            try {
                console.log('Loading recent uploads...');
                const response = await fetch('/api/excel/files', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Excel files loaded:', data.files);
                    console.log('Total files found:', data.files.length);
                    
                    // Filter only uploaded files (not templates)
                    const uploadedFiles = data.files.filter(file => file.type === 'upload');
                    console.log('Uploaded files:', uploadedFiles);
                    
                    // Convert to recentUploads format
                    this.recentUploads = uploadedFiles.map((file, index) => ({
                        id: index + 1,
                        filename: file.name,
                        uploaded_at: this.formatDate(file.created),
                        status: 'success',
                        fileSize: this.formatFileSize(file.size),
                        records_processed: Math.floor(Math.random() * 50) + 10 // Random for demo
                    }));
                    
                    console.log('Recent uploads updated:', this.recentUploads);
                } else {
                    console.error('Failed to load Excel files:', response.status);
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                }
            } catch (error) {
                console.error('Error loading Excel files:', error);
            }
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            
            if (diffHours < 1) {
                return 'Baru saja';
            } else if (diffHours < 24) {
                return `${diffHours} jam yang lalu`;
            } else if (diffDays === 1) {
                return '1 hari yang lalu';
            } else {
                return `${diffDays} hari yang lalu`;
            }
        },
        
        handleFileSelect(event) {
            this.selectedFile = event.target.files[0];
        },
        
        handleFileDrop(event) {
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.selectedFile = files[0];
            }
        },
        
        removeFile() {
            this.selectedFile = null;
            document.getElementById('file-input').value = '';
        },
        
        async uploadExcel() {
            if (!this.selectedFile) {
                this.showMessage('Pilih file Excel terlebih dahulu', 'error');
                return;
            }
            
            this.loading = true;
            this.showProgress = true;
            this.progressPercent = 0;
            this.progressText = 'Mempersiapkan upload...';
            
            const formData = new FormData();
            formData.append('excel_file', this.selectedFile);
            formData.append('jenis_data', this.formData.jenis_data);
            formData.append('bulan', this.formData.bulan);
            formData.append('tahun', this.formData.tahun);
            formData.append('skip_errors', this.formData.skip_errors);
            formData.append('update_existing', this.formData.update_existing);
            formData.append('validate_data', this.formData.validate_data);
            
            // Remove test parameter for production
            // formData.append('test', 'true');
            
            try {
                this.progressText = 'Mengupload file...';
                this.progressPercent = 30;
                
                // Debug CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                console.log('CSRF Token:', csrfToken);
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                const response = await fetch('/api/excel/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                this.progressText = 'Memproses data...';
                this.progressPercent = 70;
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Response status:', response.status);
                console.log('Response headers:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    const textResponse = await response.text();
                    console.log('Non-JSON response:', textResponse.substring(0, 500));
                    throw new Error('Server mengembalikan response HTML, bukan JSON. Status: ' + response.status);
                }
                
                const result = await response.json();
                
                this.progressPercent = 100;
                this.progressText = 'Selesai!';
                
                this.uploadResult = result;
                
                if (result.success) {
                    this.showMessage(result.message, 'success');
                    // Reset form
                    this.resetForm();
                    // Reload recent uploads
                    await this.loadRecentUploads();
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
            
            this.loading = false;
        },
        
        resetForm() {
            this.selectedFile = null;
            this.formData = {
                jenis_data: '',
                bulan: '',
                tahun: new Date().getFullYear(),
                skip_errors: true,
                update_existing: false,
                validate_data: true
            };
            document.getElementById('file-input').value = '';
        },
        
        formatFileSize(bytes) {
            if (!bytes) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
                this.messageType = '';
            }, 5000);
        },
        
        async downloadFile(filename) {
            try {
                console.log('Downloading file:', filename);
                
                const response = await fetch(`/api/excel/download/${filename}`, {
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
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showMessage('File berhasil didownload', 'success');
                } else {
                    const errorData = await response.json();
                    this.showMessage(errorData.error || 'Gagal download file', 'error');
                }
            } catch (error) {
                console.error('Download error:', error);
                this.showMessage('Terjadi kesalahan saat download: ' + error.message, 'error');
            }
        },
        
        async deleteFile(filename) {
            if (!confirm(`Apakah Anda yakin ingin menghapus file "${filename}"?`)) {
                return;
            }
            
            try {
                console.log('Deleting file:', filename);
                
                const response = await fetch(`/api/excel/delete/${filename}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.showMessage(result.message || 'File berhasil dihapus', 'success');
                    // Reload recent uploads
                    await this.loadRecentUploads();
                } else {
                    const errorData = await response.json();
                    this.showMessage(errorData.error || 'Gagal menghapus file', 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showMessage('Terjadi kesalahan saat menghapus: ' + error.message, 'error');
            }
        }
    }));
});
</script>
@endsection

