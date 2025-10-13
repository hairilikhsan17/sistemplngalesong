@extends('layouts.dashboard')

@section('title', 'Pemantauan Laporan')

@section('content')
<div class="p-6" x-data="pemantauanData()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pemantauan Laporan</h1>
        <p class="text-gray-600 mt-2">Pantau semua laporan kerja dari semua kelompok</p>
    </div>

    <!-- Notification -->
    <div x-show="message" 
         x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="mb-6 border rounded-lg px-4 py-3">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Laporan</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select x-model="filterBulan" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select x-model="filterTahun" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        <option value="">Semua Tahun</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelompok</label>
                    <select x-model="filterKelompok" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id }}">{{ $kelompok->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button @click="applyFilter()" 
                            class="w-full bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        🔍 Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        📋
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Laporan</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.totalLaporan || {{ $statistics['totalLaporan'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        ✅
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Laporan Hari Ini</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.laporanHariIni || {{ $statistics['laporanHariIni'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        ⏰
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.pendingReview || {{ $statistics['pendingReview'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        📊
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Avg per Hari</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.avgPerHari || {{ $statistics['avgPerHari'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Data Laporan Karyawan</h3>
                <div class="flex space-x-2">
                    <button @click="exportLaporan()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        📥 Export CSV
                    </button>
                    <button @click="refreshData()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        🔄 Refresh
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instansi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat/Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumentasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporanKaryawans as $laporan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->tanggal }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->nama ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->kelompok->nama_kelompok ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->instansi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->alamat_tujuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="space-y-1">
                                @if($laporan->dokumentasi)
                                    <div class="text-xs text-gray-600">
                                        {{ Str::limit($laporan->dokumentasi, 30) }}
                                    </div>
                                @endif
                                @if($laporan->file_path)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="file" class="w-4 h-4 text-blue-600"></i>
                                        <a href="/api/laporan-karyawan/{{ $laporan->id }}/download" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-700 text-xs">
                                            Lihat File
                                        </a>
                                    </div>
                                @endif
                                @if(!$laporan->dokumentasi && !$laporan->file_path)
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            
                            <button @click="hapusLaporan('{{ $laporan->id }}')" 
                                    class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada data laporan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="previousPage()" 
                        :disabled="currentPage === 1"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                    Previous
                </button>
                <button @click="nextPage()" 
                        :disabled="currentPage === totalPages"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium" x-text="startRecord"></span>
                        sampai
                        <span class="font-medium" x-text="endRecord"></span>
                        dari
                        <span class="font-medium" x-text="totalRecords"></span>
                        hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <button @click="previousPage()" 
                                :disabled="currentPage === 1"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            Previous
                        </button>
                        <template x-for="page in visiblePages" :key="page">
                            <button @click="goToPage(page)" 
                                    :class="page === currentPage ? 'bg-amber-50 border-amber-500 text-amber-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <span x-text="page"></span>
                        </button>
                        </template>
                        <button @click="nextPage()" 
                                :disabled="currentPage === totalPages"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            Next
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Dokumentasi -->
<div x-show="showDokumentasiModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDokumentasiModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Dokumentasi Pekerjaan</h3>
                
                <div class="grid grid-cols-2 gap-4" x-show="selectedDokumentasi.length > 0">
                    <template x-for="(dok, index) in selectedDokumentasi" :key="index">
                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                            <div class="text-gray-500 text-sm mb-2">Foto <span x-text="index + 1"></span></div>
                            <img :src="dok" :alt="'Foto ' + (index + 1)" class="w-full h-32 object-cover rounded">
                    </div>
                    </template>
                    </div>
                
                <div x-show="selectedDokumentasi.length === 0" class="text-center py-8">
                    <div class="text-gray-500">Tidak ada dokumentasi tersedia</div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" @click="showDokumentasiModal = false" 
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Laporan -->
<div x-show="showEditModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="updateLaporan()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Laporan</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" x-model="formLaporan.nama" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   placeholder="Masukkan nama karyawan" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instansi</label>
                            <input type="text" x-model="formLaporan.instansi" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   placeholder="Masukkan nama instansi" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat/Tujuan</label>
                            <input type="text" x-model="formLaporan.alamat_tujuan" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   placeholder="Masukkan alamat/tujuan" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dokumentasi</label>
                            <textarea x-model="formLaporan.dokumentasi" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                      rows="3" placeholder="Masukkan dokumentasi kerja"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span x-show="!loading">Perbarui</span>
                        <span x-show="loading">Loading...</span>
                    </button>
                    <button type="button" @click="showEditModal = false" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Alpine.js data
document.addEventListener('alpine:init', () => {
    Alpine.data('pemantauanData', () => ({
        // Filter data
        filterBulan: '',
        filterTahun: '',
        filterKelompok: '',
        
        // Modal states
        showDokumentasiModal: false,
        showEditModal: false,
        
        // Form data
        formLaporan: {
            nama: '',
            instansi: '',
            alamat_tujuan: '',
            dokumentasi: ''
        },
        
        // Pagination
        currentPage: 1,
        totalPages: 1,
        totalRecords: 0,
        perPage: 10,
        
        // Statistics - Initialize with server-side data
        statistics: {
            totalLaporan: {{ $statistics['totalLaporan'] ?? 0 }},
            laporanHariIni: {{ $statistics['laporanHariIni'] ?? 0 }},
            pendingReview: {{ $statistics['pendingReview'] ?? 0 }},
            avgPerHari: {{ $statistics['avgPerHari'] ?? 0 }}
        },
        
        // Selected data
        selectedDokumentasi: [],
        editingId: null,
        
        // UI states
        loading: false,
        message: '',
        messageType: '',
        
        init() {
            console.log('Alpine.js initialized!');
            
            // Get filter values from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            this.filterBulan = urlParams.get('bulan') || '';
            this.filterTahun = urlParams.get('tahun') || '';
            this.filterKelompok = urlParams.get('kelompok') || '';
            
            console.log('Initializing with filters:', {
                bulan: this.filterBulan,
                tahun: this.filterTahun,
                kelompok: this.filterKelompok,
                url: window.location.search
            });
            
            // Load statistics immediately
            this.loadStatistics();
            
            // Also load statistics after a delay to ensure everything is ready
            setTimeout(() => {
                console.log('Loading statistics after delay...');
                this.loadStatistics();
            }, 1000);
        },
        
        // Computed properties
        get startRecord() {
            return ((this.currentPage - 1) * this.perPage) + 1;
        },
        
        get endRecord() {
            return Math.min(this.currentPage * this.perPage, this.totalRecords);
        },
        
        get visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
        
        // Filter functions
        async applyFilter() {
            console.log('Apply filter:', {
                bulan: this.filterBulan,
                tahun: this.filterTahun,
                kelompok: this.filterKelompok
            });
            
            // Reload halaman dengan filter parameters
            const params = new URLSearchParams();
            if (this.filterBulan) params.append('bulan', this.filterBulan);
            if (this.filterTahun) params.append('tahun', this.filterTahun);
            if (this.filterKelompok) params.append('kelompok', this.filterKelompok);
            
            const queryString = params.toString();
            const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
            
            window.location.href = url;
        },
        
        // Data loading functions
        async loadData() {
            try {
                this.loading = true;
                const params = new URLSearchParams({
                    page: this.currentPage,
                    per_page: this.perPage,
                    bulan: this.filterBulan,
                    tahun: this.filterTahun,
                    kelompok: this.filterKelompok
                });
                
                // Build URL with filters
                let url = `/atasan/pemantauan-laporan?${params}`;
                
                // Redirect to the same page with filter parameters
                window.location.href = url;
                
            } catch (error) {
                console.error('Error loading data:', error);
                this.showMessage('Terjadi kesalahan saat memuat data', 'error');
            }
            this.loading = false;
        },
        
        async loadStatistics() {
            try {
                console.log('Statistics loaded from controller:', this.statistics);
                // Data sudah di-load dari controller, tidak perlu API call
                // Jika ada filter, kita bisa update statistik secara manual
                if (this.filterBulan || this.filterTahun || this.filterKelompok) {
                    console.log('Filter applied, statistics will be updated when data is loaded');
                    // Statistik akan di-update ketika data di-load dengan filter
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        },
        
        // CRUD functions
        async lihatDokumentasi(id) {
            try {
                const response = await fetch(`/api/laporan-karyawan/${id}/dokumentasi`);
                const result = await response.json();
                
                if (response.ok) {
                    this.selectedDokumentasi = result.dokumentasi || [];
                    this.showDokumentasiModal = true;
                } else {
                    this.showMessage('Gagal memuat dokumentasi', 'error');
                }
            } catch (error) {
                console.error('Error loading dokumentasi:', error);
                this.showMessage('Terjadi kesalahan saat memuat dokumentasi', 'error');
            }
        },
        
        async editLaporan(id, nama, instansi, alamatTujuan, dokumentasi) {
            this.editingId = id;
            this.formLaporan = {
                nama: nama,
                instansi: instansi,
                alamat_tujuan: alamatTujuan,
                dokumentasi: dokumentasi
            };
            this.showEditModal = true;
        },
        
        async updateLaporan() {
            try {
                this.loading = true;
                
                const response = await fetch(`/api/laporan-karyawan/${this.editingId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formLaporan)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showMessage(result.message || 'Laporan berhasil diperbarui!', 'success');
                    this.showEditModal = false;
                    this.loadData();
                    this.loadStatistics();
                } else {
                    const errorMessage = result.message || (result.errors ? JSON.stringify(result.errors) : 'Gagal memperbarui laporan');
                    this.showMessage('Error: ' + errorMessage, 'error');
                }
            } catch (error) {
                console.error('Update error:', error);
                this.showMessage('Terjadi kesalahan: ' + error.message, 'error');
            }
            this.loading = false;
        },
        
        async hapusLaporan(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus laporan ini?')) return;
            
            try {
                const response = await fetch(`/api/laporan-karyawan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showMessage(result.message || 'Laporan berhasil dihapus!', 'success');
                    this.loadData();
                    this.loadStatistics();
                } else {
                    this.showMessage(result.message || 'Gagal menghapus laporan', 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showMessage('Terjadi kesalahan: ' + error.message, 'error');
            }
        },
        
        // Export function
        async exportLaporan() {
            try {
                const params = new URLSearchParams({
                    bulan: this.filterBulan,
                    tahun: this.filterTahun,
                    kelompok: this.filterKelompok
                });
                
                const response = await fetch(`/api/export/laporan-karyawan?${params}`);
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `laporan-karyawan-${new Date().toISOString().split('T')[0]}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showMessage('Export berhasil! File CSV dapat dibuka dengan Excel.', 'success');
                } else {
                    const errorData = await response.json();
                    this.showMessage(errorData.error || 'Gagal export data', 'error');
                }
            } catch (error) {
                console.error('Export error:', error);
                this.showMessage('Terjadi kesalahan saat export: ' + error.message, 'error');
            }
        },
        
        // Refresh function
        async refreshData() {
            await this.loadData();
            await this.loadStatistics();
            this.showMessage('Data berhasil diperbarui!', 'success');
        },
        
        // Pagination functions
        async goToPage(page) {
            this.currentPage = page;
            await this.loadData();
        },
        
        async previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                await this.loadData();
            }
        },
        
        async nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                await this.loadData();
            }
        },
        
        // Utility functions
        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
                this.messageType = '';
            }, 3000);
        }
    }));
});
</script>
@endsection