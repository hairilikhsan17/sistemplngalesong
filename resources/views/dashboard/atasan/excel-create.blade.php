@extends('layouts.dashboard')

@section('title', 'Buat Template Excel')

@section('content')
<div class="p-6" x-data="excelCreate()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Template Excel</h1>
                <p class="text-gray-600 mt-2">Buat template Excel untuk input data dengan format yang sudah disesuaikan</p>
            </div>
            <a href="{{ route('atasan.excel.index') }}" 
               class="flex items-center text-gray-600 hover:text-gray-900">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke Manajemen Excel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Create Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Form Buat Template</h2>
            
            <form @submit.prevent="createTemplate()" class="space-y-6">
                <!-- Template Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Template <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" x-model="formData.jenis_data" value="laporan_karyawan" class="sr-only">
                            <div :class="formData.jenis_data === 'laporan_karyawan' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                 class="border-2 rounded-lg p-4 transition-colors">
                                <div class="flex items-center">
                                    <i data-lucide="users" class="w-6 h-6 text-blue-600 mr-3"></i>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Laporan Karyawan</h3>
                                        <p class="text-sm text-gray-600">Template untuk laporan kerja karyawan</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative cursor-pointer">
                            <input type="radio" x-model="formData.jenis_data" value="job_pekerjaan" class="sr-only">
                            <div :class="formData.jenis_data === 'job_pekerjaan' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                                 class="border-2 rounded-lg p-4 transition-colors">
                                <div class="flex items-center">
                                    <i data-lucide="briefcase" class="w-6 h-6 text-green-600 mr-3"></i>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Job Pekerjaan</h3>
                                        <p class="text-sm text-gray-600">Template untuk job pekerjaan</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Period -->
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

                <!-- Group Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kelompok
                    </label>
                    <select x-model="formData.kelompok_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id }}">{{ $kelompok->nama_kelompok }} ({{ $kelompok->shift }})</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Pilih kelompok tertentu atau biarkan kosong untuk semua kelompok</p>
                </div>

                <!-- Template Options -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Opsi Template</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.include_sample_data" class="mr-3">
                            <div>
                                <span class="text-sm text-gray-700">Include data sample</span>
                                <p class="text-xs text-gray-500">Tambahkan data contoh untuk memudahkan input</p>
                            </div>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.include_validation" class="mr-3">
                            <div>
                                <span class="text-sm text-gray-700">Include data validation</span>
                                <p class="text-xs text-gray-500">Tambahkan dropdown untuk nama karyawan dan kelompok</p>
                            </div>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="formData.include_formulas" class="mr-3">
                            <div>
                                <span class="text-sm text-gray-700">Include formulas</span>
                                <p class="text-xs text-gray-500">Tambahkan rumus untuk perhitungan otomatis</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="!formData.jenis_data || !formData.bulan || !formData.tahun || loading"
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    <i data-lucide="file-plus" class="w-4 h-4 mr-2" x-show="!loading"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Membuat Template...' : 'Buat Template Excel'"></span>
                </button>
            </form>
        </div>

        <!-- Preview & Instructions -->
        <div class="space-y-6">
            <!-- Template Preview -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Preview Template</h2>
                
                <div class="border rounded-lg overflow-hidden">
                    <div class="bg-gray-100 px-4 py-2 border-b">
                        <div class="flex items-center">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600 mr-2"></i>
                            <span class="text-sm font-medium" x-text="getTemplateName()"></span>
                        </div>
                    </div>
                    
                    <!-- Sample Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="px-3 py-2 text-xs font-medium">A</th>
                                    <th class="px-3 py-2 text-xs font-medium">B</th>
                                    <th class="px-3 py-2 text-xs font-medium">C</th>
                                    <th class="px-3 py-2 text-xs font-medium">D</th>
                                    <th class="px-3 py-2 text-xs font-medium">E</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="bg-blue-50">
                                    <td class="px-3 py-2 font-medium">Nama Karyawan</td>
                                    <td class="px-3 py-2 font-medium">Kelompok</td>
                                    <td class="px-3 py-2 font-medium">Tanggal</td>
                                    <td class="px-3 py-2 font-medium">Waktu (Hari)</td>
                                    <td class="px-3 py-2 font-medium">Keterangan</td>
                                </tr>
                                <template x-if="formData.include_sample_data">
                                    <tr class="border-t">
                                        <td class="px-3 py-2">Ahmad Fajar</td>
                                        <td class="px-3 py-2">Kelompok 1</td>
                                        <td class="px-3 py-2">2024-01-15</td>
                                        <td class="px-3 py-2">2</td>
                                        <td class="px-3 py-2">Perbaikan KWH</td>
                                    </tr>
                                </template>
                                <tr class="border-t">
                                    <td class="px-3 py-2 text-gray-400">...</td>
                                    <td class="px-3 py-2 text-gray-400">...</td>
                                    <td class="px-3 py-2 text-gray-400">...</td>
                                    <td class="px-3 py-2 text-gray-400">...</td>
                                    <td class="px-3 py-2 text-gray-400">...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>Kolom yang akan dibuat:</strong></p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li><strong>A:</strong> Nama Karyawan <span x-show="formData.include_validation" class="text-blue-600">(Dropdown)</span></li>
                        <li><strong>B:</strong> Kelompok <span x-show="formData.include_validation" class="text-blue-600">(Dropdown)</span></li>
                        <li><strong>C:</strong> Tanggal (Format: YYYY-MM-DD)</li>
                        <li><strong>D:</strong> Waktu Penyelesaian (Hari)</li>
                        <li><strong>E:</strong> Keterangan/Deskripsi</li>
                    </ul>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Panduan Template</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-green-600">1</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Pilih Jenis Data</h3>
                            <p class="text-sm text-gray-600">Pilih antara Laporan Karyawan atau Job Pekerjaan sesuai kebutuhan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-green-600">2</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Tentukan Periode</h3>
                            <p class="text-sm text-gray-600">Pilih bulan dan tahun untuk template yang akan dibuat</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-green-600">3</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Konfigurasi Opsi</h3>
                            <p class="text-sm text-gray-600">Aktifkan opsi yang diperlukan untuk template yang lebih mudah digunakan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-green-600">4</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">Download & Gunakan</h3>
                            <p class="text-sm text-gray-600">Template akan otomatis terdownload setelah dibuat</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Templates -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Template Terbaru</h2>
                <div class="space-y-3">
                    <template x-for="template in recentTemplates" :key="template.id">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="template.name"></p>
                                    <p class="text-xs text-gray-500" x-text="template.created_at"></p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="downloadTemplate(template.name)" 
                                        class="text-blue-600 hover:text-blue-800">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </button>
                                <button @click="deleteTemplate(template.name)" 
                                        class="text-red-600 hover:text-red-800">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="recentTemplates.length === 0" class="text-center py-4">
                        <i data-lucide="file-plus" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada template</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <i data-lucide="check" class="w-6 h-6 text-green-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Template Berhasil Dibuat!</h3>
                <p class="text-sm text-gray-600 mb-4" x-text="successMessage"></p>
                
                <div class="flex space-x-3">
                    <button @click="downloadCreatedTemplate()" 
                            class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="download" class="w-4 h-4 mr-2 inline"></i>
                        Download
                    </button>
                    <button @click="showSuccessModal = false" 
                            class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                        Tutup
                    </button>
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
    Alpine.data('excelCreate', () => ({
        loading: false,
        message: '',
        messageType: '',
        showSuccessModal: false,
        successMessage: '',
        createdTemplateUrl: '',
        
        formData: {
            jenis_data: '',
            bulan: '',
            tahun: new Date().getFullYear(),
            kelompok_id: '',
            include_sample_data: true,
            include_validation: true,
            include_formulas: false
        },
        
        recentTemplates: [
            // Sample data - in real implementation, this would be loaded from API
            {
                id: 1,
                name: 'laporan_karyawan_template_Januari_2024.xlsx',
                created_at: '2 jam yang lalu'
            },
            {
                id: 2,
                name: 'job_pekerjaan_template_Februari_2024.xlsx',
                created_at: '1 hari yang lalu'
            }
        ],
        
        getTemplateName() {
            if (!this.formData.jenis_data || !this.formData.bulan || !this.formData.tahun) {
                return 'template_excel.xlsx';
            }
            return `${this.formData.jenis_data}_template_${this.formData.bulan}_${this.formData.tahun}.xlsx`;
        },
        
        async createTemplate() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/excel/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.successMessage = result.message;
                    this.createdTemplateUrl = result.file_url;
                    this.showSuccessModal = true;
                    this.showMessage(result.message, 'success');
                } else {
                    this.showMessage(result.message, 'error');
                }
                
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat membuat template', 'error');
            }
            
            this.loading = false;
        },
        
        downloadCreatedTemplate() {
            if (this.createdTemplateUrl) {
                window.open(this.createdTemplateUrl, '_blank');
            }
            this.showSuccessModal = false;
        },
        
        async downloadTemplate(fileName) {
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
                    this.showMessage('Gagal mengunduh template', 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat mengunduh template', 'error');
            }
        },
        
        async deleteTemplate(fileName) {
            if (!confirm('Yakin ingin menghapus template ini?')) {
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
                    // Remove from recent templates
                    this.recentTemplates = this.recentTemplates.filter(t => t.name !== fileName);
                } else {
                    this.showMessage(result.message, 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan saat menghapus template', 'error');
            }
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


