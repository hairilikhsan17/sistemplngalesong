@extends('layouts.dashboard')

@section('title', 'Input Laporan')

@section('content')
<div class="p-6" x-data="laporanData()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Input Laporan Kerja</h1>
                <p class="text-gray-600 mt-2">Kelola laporan kerja harian Anda</p>
            </div>
            <div class="flex items-center space-x-4">
                <button @click="showForm = true; resetForm()" 
                        class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Tambah Laporan
                </button>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div x-show="message" x-transition class="mb-6">
        <div :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'" 
             class="border rounded-lg p-4">
            <div class="flex items-center">
                <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mr-3"></i>
                <span x-text="message"></span>
            </div>
        </div>
    </div>

    <!-- Form Modal -->
    <div x-show="showForm" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900" x-text="editingId ? 'Edit Laporan' : 'Tambah Laporan Baru'"></h3>
                <button @click="closeForm()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Debug Info -->
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>Debug Info:</strong><br>
                    Editing ID: <span x-text="editingId || 'null'"></span><br>
                    Form Data: <span x-text="JSON.stringify(formData)"></span>
                </p>
            </div>

            <form @submit.prevent="saveLaporan()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hari -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                        <select x-model="formData.hari" 
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Debug: <span x-text="formData.hari"></span></p>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" 
                               x-model="formData.tanggal"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                        <select x-model="formData.nama" 
                                @change="onNamaChange()"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="">Pilih Nama Karyawan</option>
                            <template x-for="karyawan in karyawans" :key="karyawan.id">
                                <option :value="karyawan.nama" x-text="karyawan.nama"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih nama karyawan dari kelompok Anda</p>
                        <p class="text-xs text-gray-500 mt-1">Debug: <span x-text="formData.nama"></span></p>
                    </div>

                    <!-- Instansi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instansi</label>
                        <input type="text" 
                               x-model="formData.instansi"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan instansi"
                               required>
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                        <input type="text" 
                               x-model="formData.jabatan"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan jabatan"
                               required>
                    </div>

                    <!-- Alamat Tujuan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat/Tujuan</label>
                        <input type="text" 
                               x-model="formData.alamat_tujuan"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan alamat tujuan"
                               required>
                    </div>
                </div>

                <!-- Dokumentasi -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dokumentasi</label>
                    <textarea x-model="formData.dokumentasi"
                              rows="4"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Masukkan dokumentasi kerja (opsional)"></textarea>
                </div>

                <!-- File Upload -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto/File Dokumentasi</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <input type="file" 
                               id="fileInput"
                               @change="handleFileSelect($event)"
                               accept="image/*,.pdf"
                               class="hidden">
                        <div x-show="!selectedFile">
                            <i data-lucide="upload" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-sm text-gray-600 mb-2">Klik untuk upload foto atau file dokumentasi</p>
                            <button type="button" 
                                    @click="document.getElementById('fileInput').click()"
                                    class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Pilih File
                            </button>
                            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, PDF (Max: 5MB)</p>
                        </div>
                        <div x-show="selectedFile" class="flex items-center justify-center space-x-4">
                            <i data-lucide="file" class="w-6 h-6 text-blue-600"></i>
                            <span class="text-sm text-gray-900" x-text="selectedFile?.name"></span>
                            <button type="button" 
                                    @click="removeFile()"
                                    class="text-red-600 hover:text-red-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div x-show="currentFile && !selectedFile" class="mt-2">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="file" class="w-4 h-4 text-gray-600"></i>
                            <span class="text-sm text-gray-600" x-text="currentFile"></span>
                            <a :href="'/api/laporan-karyawan/' + editingId + '/download'" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-700 text-sm">
                                <i data-lucide="download" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" 
                            @click="closeForm()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="loading"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="loading" class="flex items-center">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                            Menyimpan...
                        </span>
                        <span x-show="!loading" x-text="editingId ? 'Perbarui' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Laporan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari/Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instansi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumentasi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(laporan, index) in laporans" :key="laporan.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="laporan.hari"></div>
                                <div class="text-sm text-gray-500" x-text="new Date(laporan.tanggal).toLocaleDateString('id-ID')"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="laporan.nama"></div>
                                <div class="text-sm text-gray-500" x-text="laporan.jabatan"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="laporan.instansi"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="laporan.alamat_tujuan"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="space-y-1">
                                    <div x-show="laporan.dokumentasi">
                                        <span x-text="laporan.dokumentasi.substring(0, 30) + (laporan.dokumentasi.length > 30 ? '...' : '')"></span>
                                    </div>
                                    <div x-show="laporan.file_path" class="flex items-center space-x-2">
                                        <i data-lucide="file" class="w-4 h-4 text-blue-600"></i>
                                        <a :href="'/api/laporan-karyawan/' + laporan.id + '/download'" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-700 text-xs">
                                            Lihat File
                                        </a>
                                    </div>
                                    <div x-show="!laporan.dokumentasi && !laporan.file_path" class="text-gray-400">-</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <!-- View Button -->
                                    <button @click="viewLaporan(laporan)" 
                                            class="inline-flex items-center px-2 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                                            title="Lihat Detail">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span class="ml-1 text-xs font-medium hidden sm:inline">Lihat</span>
                                    </button>
                                    
                                    <!-- Edit Button -->
                                    <button @click="editLaporan(laporan)" 
                                            class="inline-flex items-center px-2 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                                            title="Edit Laporan">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="ml-1 text-xs font-medium hidden sm:inline">Edit</span>
                                    </button>
                                    
                                    <!-- Download Button (if file exists) -->
                                    <button x-show="laporan.file_path" 
                                            @click="downloadFile(laporan.id)"
                                            class="inline-flex items-center px-2 py-1.5 bg-purple-500 hover:bg-purple-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                                            title="Download File">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="ml-1 text-xs font-medium hidden sm:inline">Download</span>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <button @click="deleteLaporan(laporan.id)" 
                                            class="inline-flex items-center px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                                            title="Hapus Laporan">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span class="ml-1 text-xs font-medium hidden sm:inline">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="!laporans.length">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">Belum ada laporan</p>
                                <p class="text-gray-400 text-sm">Klik tombol "Tambah Laporan" untuk membuat laporan pertama</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200" x-show="laporans.length > 0">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan <span x-text="laporans.length"></span> dari <span x-text="laporans.length"></span> laporan
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="loadLaporans()" 
                            class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View Detail Laporan -->
    <div x-show="showViewModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Detail Laporan Kerja</h3>
                <button @click="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div x-show="selectedLaporan" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Hari</label>
                        <p class="text-gray-900 font-medium" x-text="selectedLaporan?.hari"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
                        <p class="text-gray-900 font-medium" x-text="selectedLaporan ? new Date(selectedLaporan.tanggal).toLocaleDateString('id-ID') : ''"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama</label>
                        <p class="text-gray-900 font-medium" x-text="selectedLaporan?.nama"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan</label>
                        <p class="text-gray-900 font-medium" x-text="selectedLaporan?.jabatan"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Instansi</label>
                        <p class="text-gray-900 font-medium" x-text="selectedLaporan?.instansi"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Alamat Tujuan</label>
                        <p class="text-gray-900 font-medium" x-text="selectedLaporan?.alamat_tujuan"></p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Dokumentasi</label>
                    <p class="text-gray-900" x-text="selectedLaporan?.dokumentasi || '-'"></p>
                </div>
                
                <div x-show="selectedLaporan?.file_path" class="bg-gray-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-600 mb-1">File Dokumentasi</label>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="file" class="w-5 h-5 text-blue-600"></i>
                        <span class="text-gray-900" x-text="selectedLaporan?.file_path ? selectedLaporan.file_path.split('/').pop() : ''"></span>
                        <button @click="downloadFile(selectedLaporan.id)" 
                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                            Download
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                <button @click="closeViewModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanData', () => ({
        laporans: [],
        karyawans: [],
        kelompok: null,
        showForm: false,
        showViewModal: false,
        editingId: null,
        selectedLaporan: null,
        loading: false,
        message: '',
        messageType: '',
        formData: {
            hari: '',
            tanggal: '',
            nama: '',
            instansi: '',
            jabatan: '',
            alamat_tujuan: '',
            dokumentasi: ''
        },
        selectedFile: null,
        currentFile: null,
        
        async init() {
            await this.loadLaporans();
            await this.loadKaryawans();
        },
        
        async loadLaporans() {
            try {
                console.log('Loading laporans...');
                const response = await fetch('/api/laporan-karyawan', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('API Response:', result);
                
                // Ensure laporans is always an array
                this.laporans = Array.isArray(result) ? result : [];
                console.log('Laporans loaded:', this.laporans);
                console.log('Laporans count:', this.laporans.length);
            } catch (error) {
                console.error('Error loading laporans:', error);
                this.showMessage('Gagal memuat data laporan: ' + error.message, 'error');
                // Ensure laporans is always an array even on error
                this.laporans = [];
            }
        },
        
        async loadKaryawans() {
            try {
                const response = await fetch('/api/karyawan', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                // Filter karyawan berdasarkan kelompok user yang login
                this.karyawans = Array.isArray(result) ? result : [];
                console.log('Karyawans loaded:', this.karyawans);
            } catch (error) {
                console.error('Error loading karyawans:', error);
                this.karyawans = [];
            }
        },
        
        async saveLaporan() {
            this.loading = true;
            console.log('Saving laporan...', this.formData);
            console.log('Editing ID:', this.editingId);
            
            try {
                const url = this.editingId ? `/api/laporan-karyawan/${this.editingId}` : '/api/laporan-karyawan';
                const method = this.editingId ? 'PUT' : 'POST';
                console.log('URL:', url, 'Method:', method);
                
                // Create FormData for file upload
                const formData = new FormData();
                formData.append('hari', this.formData.hari);
                formData.append('tanggal', this.formData.tanggal);
                formData.append('nama', this.formData.nama);
                formData.append('instansi', this.formData.instansi);
                formData.append('jabatan', this.formData.jabatan);
                formData.append('alamat_tujuan', this.formData.alamat_tujuan);
                formData.append('dokumentasi', this.formData.dokumentasi);
                
                if (this.selectedFile) {
                    formData.append('file', this.selectedFile);
                }
                
                // Add _method for Laravel to recognize PUT request
                if (this.editingId) {
                    formData.append('_method', 'PUT');
                }
                
                const response = await fetch(url, {
                    method: 'POST', // Always use POST for FormData
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Error response:', errorData);
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Save response:', result);
                
                if (this.editingId) {
                    // Update existing laporan
                    const index = this.laporans.findIndex(l => l.id === this.editingId);
                    if (index !== -1) {
                        this.laporans[index] = result;
                    }
                    this.showMessage('Laporan berhasil diperbarui!', 'success');
                } else {
                    // Add new laporan
                    this.laporans.unshift(result);
                    this.showMessage('Laporan berhasil ditambahkan!', 'success');
                }
                
                // Reload data from server to ensure consistency
                await this.loadLaporans();
                this.closeForm();
                
            } catch (error) {
                console.error('Error saving laporan:', error);
                this.showMessage('Gagal menyimpan laporan: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        editLaporan(laporan) {
            console.log('Editing laporan:', laporan);
            this.editingId = laporan.id;
            this.formData = {
                hari: laporan.hari || '',
                tanggal: laporan.tanggal || '',
                nama: laporan.nama || '',
                instansi: laporan.instansi || '',
                jabatan: laporan.jabatan || '',
                alamat_tujuan: laporan.alamat_tujuan || '',
                dokumentasi: laporan.dokumentasi || ''
            };
            this.selectedFile = null;
            this.currentFile = laporan.file_path ? laporan.file_path.split('/').pop() : null;
            this.showForm = true;
            console.log('Form data set:', this.formData);
        },
        
        async deleteLaporan(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/laporan-karyawan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Remove from list
                this.laporans = this.laporans.filter(l => l.id !== id);
                this.showMessage('Laporan berhasil dihapus!', 'success');
                
                // Reload data from server to ensure consistency
                await this.loadLaporans();
                
            } catch (error) {
                console.error('Error deleting laporan:', error);
                this.showMessage('Gagal menghapus laporan: ' + error.message, 'error');
            }
        },
        
        closeForm() {
            this.showForm = false;
            this.editingId = null;
            this.selectedFile = null;
            this.currentFile = null;
            this.resetForm();
            console.log('Form closed and reset');
        },
        
        closeViewModal() {
            this.showViewModal = false;
            this.selectedLaporan = null;
        },
        
        resetForm() {
            this.formData = {
                hari: '',
                tanggal: new Date().toISOString().split('T')[0],
                nama: '',
                instansi: 'PLN Galesong',
                jabatan: '',
                alamat_tujuan: '',
                dokumentasi: ''
            };
            this.selectedFile = null;
            this.currentFile = null;
            // Reset file input
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.value = '';
            }
            console.log('Form reset');
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    this.showMessage('File terlalu besar. Maksimal 5MB.', 'error');
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    this.showMessage('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.', 'error');
                    return;
                }
                
                this.selectedFile = file;
            }
        },
        
        removeFile() {
            this.selectedFile = null;
            document.getElementById('fileInput').value = '';
        },
        
        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
                this.messageType = '';
            }, 5000);
        },
        
        onNamaChange() {
            // Auto-fill jabatan when nama is selected
            const selectedKaryawan = this.karyawans.find(k => k.nama === this.formData.nama);
            if (selectedKaryawan) {
                // Set default jabatan if not available
                this.formData.jabatan = selectedKaryawan.jabatan || 'Karyawan';
            }
        },
        
        viewLaporan(laporan) {
            // Show laporan details in modal
            this.selectedLaporan = laporan;
            this.showViewModal = true;
        },
        
        downloadFile(id) {
            // Download file from server
            const link = document.createElement('a');
            link.href = `/api/laporan-karyawan/${id}/download`;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }));
});
</script>
@endsection
