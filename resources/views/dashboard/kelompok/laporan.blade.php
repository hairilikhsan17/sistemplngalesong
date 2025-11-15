@extends('layouts.dashboard')

@section('title', 'Input Laporan')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="laporanData()">
    <!-- Header -->
    <div class="mb-6 lg:mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 sm:p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i data-lucide="file-text" class="w-8 h-8 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Input Laporan Kerja</h1>
                        <p class="text-blue-100 mt-1 text-sm sm:text-base">Kelola laporan kerja harian Anda dengan mudah dan efisien</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <button @click="showForm = true; resetForm()" 
                            class="flex items-center bg-white text-blue-600 px-4 sm:px-6 py-2.5 rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-md hover:shadow-lg font-medium min-h-[44px]">
                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                        <span>Tambah Laporan</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Total Laporan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['totalLaporan'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Laporan Hari Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['laporanHariIni'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Laporan Bulan Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['laporanBulanIni'] ?? 0 }}</p>
                    </div>
                </div>
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

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg mb-6 border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="filter" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Filter Laporan</h3>
                    <p class="text-xs text-gray-500">Saring data laporan sesuai kebutuhan</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('kelompok.laporan') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ request('tanggal') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Hari -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Hari
                    </label>
                    <select name="hari" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                        <option value="">Semua Hari</option>
                        <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                        <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        <option value="Minggu" {{ request('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                    </select>
                </div>

                <!-- Filter Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Nama
                    </label>
                    <input type="text" 
                           name="nama" 
                           value="{{ request('nama') }}"
                           placeholder="Cari nama..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Instansi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="building" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Instansi
                    </label>
                    <input type="text" 
                           name="instansi" 
                           value="{{ request('instansi') }}"
                           placeholder="Cari instansi..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('kelompok.laporan') }}" 
                       class="px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Modal -->
    <div x-show="showForm" 
         x-transition
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 backdrop-blur-sm"
         @click.self="closeForm()"
         @keydown.escape="closeForm()">
        <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i :data-lucide="editingId ? 'edit-2' : 'plus'" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900" x-text="editingId ? 'Edit Laporan' : 'Tambah Laporan Baru'"></h3>
                        <p class="text-sm text-gray-500" x-text="editingId ? 'Perbarui informasi laporan kerja' : 'Isi form di bawah untuk menambahkan laporan baru'"></p>
                    </div>
                </div>
                <button @click="closeForm()" 
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            

            <form @submit.prevent="saveLaporan()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Hari -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Hari
                        </label>
                        <select x-model="formData.hari" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
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
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Tanggal
                        </label>
                        <input type="date" 
                               x-model="formData.tanggal"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               required>
                    </div>

                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="user" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Nama Karyawan
                        </label>
                        <select x-model="formData.nama" 
                                @change="onNamaChange()"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                                required>
                            <option value="">Pilih Nama Karyawan</option>
                            <template x-for="karyawan in karyawans" :key="karyawan.id">
                                <option :value="karyawan.nama" x-text="karyawan.nama"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5 flex items-center">
                            <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                            Pilih nama karyawan dari kelompok Anda
                        </p>
                    </div>

                    <!-- Instansi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="building" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Instansi
                        </label>
                        <input type="text" 
                               x-model="formData.instansi"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="Masukkan nama instansi"
                               required>
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="briefcase" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Jabatan
                        </label>
                        <input type="text" 
                               x-model="formData.jabatan"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="Masukkan jabatan"
                               required>
                    </div>

                    <!-- Alamat Tujuan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                            Alamat/Tujuan
                        </label>
                        <input type="text" 
                               x-model="formData.alamat_tujuan"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400"
                               placeholder="Masukkan alamat atau lokasi tujuan pekerjaan"
                               required>
                    </div>
                </div>

                <!-- Dokumentasi -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="file-text" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Dokumentasi
                    </label>
                    <textarea x-model="formData.dokumentasi"
                              rows="4"
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white hover:border-gray-400 resize-none"
                              placeholder="Masukkan dokumentasi kerja, catatan, atau keterangan tambahan (opsional)"></textarea>
                    <p class="text-xs text-gray-500 mt-1.5">Jelaskan detail pekerjaan yang dilakukan</p>
                </div>

                <!-- File Upload -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="upload" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        Foto/File Dokumentasi
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-200 cursor-pointer"
                         @click="document.getElementById('fileInput').click()">
                        <input type="file" 
                               id="fileInput"
                               @change="handleFileSelect($event)"
                               accept="image/*,.pdf"
                               class="hidden">
                        <div x-show="!selectedFile && !currentFile">
                            <div class="flex justify-center mb-3">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i data-lucide="upload" class="w-8 h-8 text-blue-600"></i>
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk upload foto atau file dokumentasi</p>
                            <p class="text-xs text-gray-500">Format: JPG, PNG, PDF (Maksimal: 5MB)</p>
                        </div>
                        <div x-show="selectedFile" class="flex items-center justify-center space-x-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i data-lucide="file-check" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <p class="text-sm font-medium text-gray-900" x-text="selectedFile?.name"></p>
                                <p class="text-xs text-gray-500" x-text="(selectedFile?.size / 1024).toFixed(2) + ' KB'"></p>
                            </div>
                            <button type="button" 
                                    @click.stop="removeFile()"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    <div x-show="currentFile && !selectedFile" class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i data-lucide="file" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="currentFile"></p>
                                    <p class="text-xs text-gray-500">File saat ini</p>
                                </div>
                            </div>
                            <a :href="'/api/laporan-karyawan/' + editingId + '/download'" 
                               target="_blank"
                               class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-1">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-3 sm:space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" 
                            @click="closeForm()"
                            class="w-full sm:w-auto px-6 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all font-medium min-h-[44px]">
                        <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] shadow-md hover:shadow-lg font-medium">
                        <span x-show="loading" class="flex items-center justify-center">
                            <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>
                            Menyimpan...
                        </span>
                        <span x-show="!loading" class="flex items-center">
                            <i :data-lucide="editingId ? 'save' : 'check'" class="w-4 h-4 mr-2"></i>
                            <span x-text="editingId ? 'Perbarui Laporan' : 'Simpan Laporan'"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="list" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Laporan</h3>
                        <p class="text-xs text-gray-500">Semua laporan kerja yang telah Anda buat</p>
                    </div>
                </div>
                <button @click="loadLaporans()" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center space-x-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto" style="position: relative;">
            <table class="min-w-full divide-y divide-gray-200" style="position: relative;">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hari/Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Instansi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Alamat Tujuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dokumentasi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporans as $index => $laporan)
                        <tr class="hover:bg-blue-50/50 transition-colors border-b border-gray-100" style="position: relative;">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                    {{ $laporans->firstItem() + $index }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $laporan->hari }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->isoFormat('DD MMM YYYY') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $laporan->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $laporan->jabatan }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="building" class="w-4 h-4 text-purple-600"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $laporan->instansi }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-orange-600 flex-shrink-0"></i>
                                    <span class="text-sm text-gray-900 line-clamp-2">{{ Str::limit($laporan->alamat_tujuan, 50) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="space-y-1">
                                    @if($laporan->dokumentasi)
                                        <div>{{ Str::limit($laporan->dokumentasi, 30) }}</div>
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
                                        <div class="text-gray-400">-</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2 relative z-10">
                                    <!-- Download Button - Lebih Kentara -->
                                    @if($laporan->file_path)
                                    <button type="button"
                                            @click.stop="downloadFile('{{ $laporan->id }}')"
                                            class="relative z-20 inline-flex items-center justify-center w-10 h-10 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg group border-2 border-purple-600 cursor-pointer pointer-events-auto"
                                            style="pointer-events: auto !important; position: relative; z-index: 20;"
                                            title="Download File">
                                        <i data-lucide="download" class="w-5 h-5 group-hover:scale-110 transition-transform pointer-events-none"></i>
                                    </button>
                                    @else
                                    <button type="button"
                                            disabled
                                            class="inline-flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed opacity-50"
                                            title="Tidak ada file">
                                        <i data-lucide="download" class="w-5 h-5"></i>
                                    </button>
                                    @endif
                                    
                                    <!-- Delete Button - Lebih Kentara -->
                                    <button type="button"
                                            @click.stop="deleteLaporan('{{ $laporan->id }}')" 
                                            class="relative z-20 inline-flex items-center justify-center w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg group border-2 border-red-600 cursor-pointer pointer-events-auto"
                                            style="pointer-events: auto !important; position: relative; z-index: 20;"
                                            title="Hapus Laporan">
                                        <i data-lucide="trash-2" class="w-5 h-5 group-hover:scale-110 transition-transform pointer-events-none"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-gray-100 rounded-full mb-4">
                                        <i data-lucide="file-text" class="w-16 h-16 text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-700 text-lg font-semibold mb-2">Belum ada laporan</p>
                                    <p class="text-gray-500 text-sm mb-4">Mulai dengan membuat laporan kerja pertama Anda</p>
                                    <button @click="showForm = true; resetForm()" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                        Tambah Laporan Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($laporans->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($laporans->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Sebelumnya
                    </span>
                    @else
                    <a href="{{ $laporans->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif
                
                    @if($laporans->hasMorePages())
                    <a href="{{ $laporans->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Selanjutnya
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Selanjutnya
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $laporans->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-medium">{{ $laporans->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-medium">{{ $laporans->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        @if($laporans->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </span>
                        @else
                            <a href="{{ $laporans->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </a>
                        @endif
                        
                        @php
                            $currentPage = $laporans->currentPage();
                            $lastPage = $laporans->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $laporans->appends(request()->except('page'))->url(1) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            @if($start > 2)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                        @endif
                        
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                            <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $laporans->appends(request()->except('page'))->url($page) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $page }}
                            </a>
                            @endif
                        @endfor
                        
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="{{ $laporans->appends(request()->except('page'))->url($lastPage) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $lastPage }}
                            </a>
                        @endif
                        
                        @if($laporans->hasMorePages())
                            <a href="{{ $laporans->appends(request()->except('page'))->nextPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laporanData', () => ({
        laporans: [],
        karyawans: [],
        kelompok: null,
        showForm: false,
        editingId: null,
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
            // Don't load laporans from API - data is already loaded from server-side
            // await this.loadLaporans();
            await this.loadKaryawans();
            // Reinitialize lucide icons
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
            
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
                    this.showMessage('Laporan berhasil diperbarui!', 'success');
                } else {
                    this.showMessage('Laporan berhasil ditambahkan!', 'success');
                }
                
                // Reload page to refresh data from server-side pagination
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                this.closeForm();
                
            } catch (error) {
                console.error('Error saving laporan:', error);
                this.showMessage('Gagal menyimpan laporan: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
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
                
                this.showMessage('Laporan berhasil dihapus!', 'success');
                
                // Reload page to refresh data from server-side pagination
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
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
