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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" x-model="filterTanggal" 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                    <select x-model="filterHari" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        <option value="">Semua Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
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
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Daftar Laporan</h3>
                    <p class="text-sm text-gray-600 mt-1">Semua laporan kerja yang telah Anda buat</p>
                </div>
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
        
        <div class="overflow-x-auto" style="position: relative;">
            <table class="min-w-full divide-y divide-gray-200" style="position: relative;">
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
                    @forelse($laporanKaryawans as $index => $laporan)
                    <tr class="hover:bg-gray-50 transition-colors" style="position: relative;">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $laporanKaryawans->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-700">{{ $laporan->hari ?? '-' }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $laporan->nama ?? '-' }}</span>
                                @if($laporan->jabatan)
                                <span class="text-xs text-gray-500">{{ $laporan->jabatan }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $laporan->instansi }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $laporan->alamat_tujuan }}">
                                {{ $laporan->alamat_tujuan }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="space-y-1 max-w-xs">
                                @if($laporan->dokumentasi)
                                    <div class="text-xs text-gray-600 truncate" title="{{ $laporan->dokumentasi }}">
                                        {{ Str::limit($laporan->dokumentasi, 40) }}
                                    </div>
                                @endif
                                @if($laporan->file_path)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="file" class="w-4 h-4 text-blue-600"></i>
                                        <a href="/api/laporan-karyawan/{{ $laporan->id }}/download" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-700 text-xs underline">
                                            Lihat File
                                        </a>
                                    </div>
                                @endif
                                @if(!$laporan->dokumentasi && !$laporan->file_path)
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2 relative z-10">
                                <!-- Tombol Lihat Detail -->
                                <button type="button" 
                                        @click.stop="lihatDetail('{{ $laporan->id }}')" 
                                        class="relative z-20 p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 cursor-pointer shadow-sm hover:shadow-md pointer-events-auto"
                                        style="pointer-events: auto !important; position: relative; z-index: 20;"
                                        title="Lihat Detail">
                                    <i data-lucide="eye" class="w-5 h-5 pointer-events-none"></i>
                                </button>
                                
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        @click.stop="bukaFormEdit('{{ $laporan->id }}')" 
                                        class="relative z-20 p-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors duration-200 cursor-pointer shadow-sm hover:shadow-md pointer-events-auto"
                                        style="pointer-events: auto !important; position: relative; z-index: 20;"
                                        title="Edit">
                                    <i data-lucide="pencil" class="w-5 h-5 pointer-events-none"></i>
                                </button>
                                
                                <!-- Tombol Hapus -->
                                <button type="button" 
                                        @click.stop="hapusLaporan('{{ $laporan->id }}')" 
                                        class="relative z-20 p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200 cursor-pointer shadow-sm hover:shadow-md pointer-events-auto"
                                        style="pointer-events: auto !important; position: relative; z-index: 20;"
                                        title="Hapus">
                                    <i data-lucide="trash-2" class="w-5 h-5 pointer-events-none"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
                @if($laporanKaryawans->hasPages())
                    @if($laporanKaryawans->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $laporanKaryawans->previousPageUrl() }}" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($laporanKaryawans->hasMorePages())
                        <a href="{{ $laporanKaryawans->nextPageUrl() }}" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                            Next
                        </span>
                    @endif
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $laporanKaryawans->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-medium">{{ $laporanKaryawans->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-medium">{{ $laporanKaryawans->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    @if($laporanKaryawans->hasPages())
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            @if($laporanKaryawans->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $laporanKaryawans->previousPageUrl() }}" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    Previous
                                </a>
                            @endif
                            
                            @php
                                $currentPage = $laporanKaryawans->currentPage();
                                $lastPage = $laporanKaryawans->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp
                            
                            @if($start > 1)
                                <a href="{{ $laporanKaryawans->url(1) }}" 
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
                                <span class="relative inline-flex items-center px-4 py-2 border border-amber-500 bg-amber-50 text-sm font-medium text-amber-600">
                                    {{ $page }}
                                </span>
                                @else
                                <a href="{{ $laporanKaryawans->url($page) }}" 
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
                                <a href="{{ $laporanKaryawans->url($lastPage) }}" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    {{ $lastPage }}
                                </a>
                            @endif
                            
                            @if($laporanKaryawans->hasMorePages())
                                <a href="{{ $laporanKaryawans->nextPageUrl() }}" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    Next
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                    Next
                                </span>
                            @endif
                        </nav>
                    @endif
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

<!-- Modal Detail Laporan - Form Baru -->
<div x-show="showDetailModal" 
     x-cloak
     @click.away="showDetailModal = false"
     @keydown.escape.window="showDetailModal = false"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] overflow-y-auto"
     style="display: none;"
     x-bind:style="showDetailModal ? 'display: flex !important;' : 'display: none !important;'">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
         @click="showDetailModal = false"
         style="z-index: 9998;"></div>
    
    <!-- Modal Content -->
    <div class="relative z-[10000] w-full max-w-4xl mx-auto my-8 px-4"
         @click.stop>
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Detail Laporan Kerja</h3>
                            <p class="text-sm text-blue-100">Informasi lengkap laporan kerja</p>
                        </div>
                    </div>
                    <button type="button"
                            @click="showDetailModal = false" 
                            class="p-2 text-white hover:bg-white/20 rounded-lg transition-colors cursor-pointer"
                            style="pointer-events: auto !important; position: relative; z-index: 10001;">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="bg-white px-6 py-6 max-h-[70vh] overflow-y-auto">
                <!-- Loading State -->
                <div x-show="!detailLaporan && loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
                    <p class="mt-4 text-gray-600 font-medium">Memuat data laporan...</p>
                </div>
                
                <!-- Error State -->
                <div x-show="!detailLaporan && !loading" class="text-center py-12">
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-red-600 mx-auto mb-3"></i>
                        <p class="text-red-700 font-medium">Gagal memuat data laporan</p>
                    </div>
                </div>
                
                <!-- Detail Content -->
                <div x-show="detailLaporan" class="space-y-6">
                    <!-- Informasi Utama -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border-2 border-blue-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                                <label class="text-xs font-bold text-blue-700 uppercase tracking-wide">Tanggal</label>
                            </div>
                            <p class="text-lg font-bold text-gray-900" x-text="detailLaporan.tanggal ? new Date(detailLaporan.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border-2 border-purple-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i data-lucide="calendar-days" class="w-5 h-5 text-purple-600"></i>
                                <label class="text-xs font-bold text-purple-700 uppercase tracking-wide">Hari</label>
                            </div>
                            <p class="text-lg font-bold text-gray-900" x-text="detailLaporan.hari || '-'"></p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border-2 border-green-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                                <label class="text-xs font-bold text-green-700 uppercase tracking-wide">Kelompok</label>
                            </div>
                            <p class="text-lg font-bold text-gray-900" x-text="detailLaporan.kelompok?.nama_kelompok || '-'"></p>
                        </div>
                        
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-5 border-2 border-amber-200">
                            <div class="flex items-center space-x-2 mb-2">
                                <i data-lucide="user" class="w-5 h-5 text-amber-600"></i>
                                <label class="text-xs font-bold text-amber-700 uppercase tracking-wide">Nama Karyawan</label>
                            </div>
                            <p class="text-lg font-bold text-gray-900" x-text="detailLaporan.nama || '-'"></p>
                        </div>
                    </div>
                    
                    <!-- Informasi Detail -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                            <div class="flex items-center space-x-2 mb-3">
                                <i data-lucide="briefcase" class="w-5 h-5 text-gray-600"></i>
                                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Jabatan</label>
                            </div>
                            <p class="text-base text-gray-900 font-medium" x-text="detailLaporan.jabatan || '-'"></p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                            <div class="flex items-center space-x-2 mb-3">
                                <i data-lucide="building" class="w-5 h-5 text-gray-600"></i>
                                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Instansi</label>
                            </div>
                            <p class="text-base text-gray-900 font-medium" x-text="detailLaporan.instansi || '-'"></p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                            <div class="flex items-center space-x-2 mb-3">
                                <i data-lucide="map-pin" class="w-5 h-5 text-gray-600"></i>
                                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Alamat/Tujuan</label>
                            </div>
                            <p class="text-base text-gray-900 font-medium" x-text="detailLaporan.alamat_tujuan || '-'"></p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                            <div class="flex items-center space-x-2 mb-3">
                                <i data-lucide="file-text" class="w-5 h-5 text-gray-600"></i>
                                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Dokumentasi</label>
                            </div>
                            <div class="mt-2 text-sm text-gray-900 whitespace-pre-wrap bg-white p-4 rounded-lg border border-gray-300 min-h-[120px] max-h-[200px] overflow-y-auto" 
                                 x-text="detailLaporan.dokumentasi || '-'"></div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-xl p-5 border-2 border-blue-200" x-show="detailLaporan.file_path">
                            <div class="flex items-center space-x-2 mb-3">
                                <i data-lucide="paperclip" class="w-5 h-5 text-blue-600"></i>
                                <label class="text-sm font-bold text-blue-700 uppercase tracking-wide">File Lampiran</label>
                            </div>
                            <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-blue-300">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <i data-lucide="file" class="w-6 h-6 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="detailLaporan.file_path ? detailLaporan.file_path.split('/').pop() : ''"></p>
                                        <p class="text-xs text-gray-500">File dokumentasi</p>
                                    </div>
                                </div>
                                <a :href="'/api/laporan-karyawan/' + detailLaporan.id + '/download'" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                                   style="pointer-events: auto !important;">
                                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" 
                        @click="showDetailModal = false" 
                        class="inline-flex items-center px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                        style="pointer-events: auto !important; position: relative; z-index: 10001;">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Laporan -->
<div x-show="showEditModal" 
     x-cloak
     x-transition
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-bind:style="showEditModal ? 'display: block;' : 'display: none;'">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i data-lucide="edit-2" class="w-6 h-6 text-white"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-white">Edit Laporan</h3>
                    </div>
                    <button @click="showEditModal = false" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            
            <form @submit.prevent="updateLaporan()">
                <div class="bg-white px-6 py-6">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input type="date" x-model="formLaporan.tanggal" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hari</label>
                            <input type="text" x-model="formLaporan.hari" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   placeholder="Contoh: Senin">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" x-model="formLaporan.nama" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   placeholder="Masukkan nama karyawan" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                            <input type="text" x-model="formLaporan.jabatan" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                   placeholder="Masukkan jabatan">
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
                
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" @click="showEditModal = false" 
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="loading"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent text-sm font-medium rounded-md text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50">
                        <i data-lucide="check" class="w-4 h-4 mr-2" x-show="!loading"></i>
                        <span x-show="!loading">Perbarui</span>
                        <span x-show="loading">Memproses...</span>
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
        filterTanggal: '',
        filterHari: '',
        filterKelompok: '',
        
        // Modal states
        showDokumentasiModal: false,
        showEditModal: false,
        showDetailModal: false,
        
        // Form data
        formLaporan: {
            nama: '',
            instansi: '',
            alamat_tujuan: '',
            dokumentasi: '',
            tanggal: '',
            hari: '',
            jabatan: ''
        },
        
        // Detail data
        detailLaporan: null,
        
        // Pagination - initialized from server
        currentPage: {{ $laporanKaryawans->currentPage() }},
        totalPages: {{ $laporanKaryawans->lastPage() }},
        totalRecords: {{ $laporanKaryawans->total() }},
        perPage: {{ $laporanKaryawans->perPage() }},
        
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
            console.log('Functions available:', {
                lihatDetail: typeof this.lihatDetail,
                editLaporan: typeof this.editLaporan,
                hapusLaporan: typeof this.hapusLaporan
            });
            
            // Alpine.js sudah terhubung langsung dengan @click di tombol
            
            // Get filter values from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            this.filterTanggal = urlParams.get('tanggal') || '';
            this.filterHari = urlParams.get('hari') || '';
            this.filterKelompok = urlParams.get('kelompok') || '';
            
            console.log('Initializing with filters:', {
                tanggal: this.filterTanggal,
                hari: this.filterHari,
                kelompok: this.filterKelompok,
                url: window.location.search
            });
            
            // Initialize lucide icons
            if (typeof lucide !== 'undefined') {
                setTimeout(() => {
                    lucide.createIcons();
                }, 100);
            }
            
            // Load statistics immediately
            this.loadStatistics();
            
            // Also load statistics after a delay to ensure everything is ready
            setTimeout(() => {
                console.log('Loading statistics after delay...');
                this.loadStatistics();
            }, 1000);
        },
        
        // Computed properties - using server-side data
        get startRecord() {
            return {{ $laporanKaryawans->firstItem() ?? 0 }};
        },
        
        get endRecord() {
            return {{ $laporanKaryawans->lastItem() ?? 0 }};
        },
        
        // Filter functions
        async applyFilter() {
            console.log('Apply filter:', {
                tanggal: this.filterTanggal,
                hari: this.filterHari,
                kelompok: this.filterKelompok
            });
            
            // Reload halaman dengan filter parameters
            const params = new URLSearchParams();
            if (this.filterTanggal) params.append('tanggal', this.filterTanggal);
            if (this.filterHari) params.append('hari', this.filterHari);
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
                    tanggal: this.filterTanggal,
                    hari: this.filterHari,
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
                if (this.filterTanggal || this.filterHari || this.filterKelompok) {
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
        
        async lihatDetail(id) {
            try {
                console.log('lihatDetail called with id:', id);
                if (!id) {
                    console.error('ID is missing');
                    this.showMessage('ID laporan tidak ditemukan', 'error');
                    return;
                }
                
                // Reset state
                this.loading = true;
                this.detailLaporan = null;
                
                // Show modal first to display loading state
                this.showDetailModal = true;
                console.log('showDetailModal set to:', this.showDetailModal);
                
                // Force Alpine.js to update DOM
                await this.$nextTick();
                
                // Force modal to show with inline style
                setTimeout(() => {
                    const modalElement = document.querySelector('[x-show="showDetailModal"]');
                    if (modalElement) {
                        modalElement.style.display = 'flex';
                        modalElement.style.zIndex = '9999';
                    }
                }, 50);
                
                // Fetch data from API
                const response = await fetch(`/api/laporan-karyawan/${id}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result && result.id) {
                    this.detailLaporan = result;
                    console.log('Detail data loaded:', this.detailLaporan);
                    
                    // Reinitialize lucide icons after modal is shown
                    setTimeout(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }, 200);
                } else {
                    this.showDetailModal = false;
                    const errorMsg = result?.message || result?.error || 'Gagal memuat detail laporan';
                    this.showMessage(errorMsg, 'error');
                }
            } catch (error) {
                console.error('Error loading detail:', error);
                this.showDetailModal = false;
                this.showMessage('Terjadi kesalahan saat memuat detail: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async bukaFormEdit(id) {
            try {
                console.log('bukaFormEdit called with id:', id);
                if (!id) {
                    this.showMessage('ID laporan tidak ditemukan', 'error');
                    return;
                }
                
                this.loading = true;
                this.editingId = id;
                
                // Load data dari API
                const response = await fetch(`/api/laporan-karyawan/${id}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result) {
                    // Set form data dari API response
                    this.formLaporan = {
                        nama: result.nama || '',
                        instansi: result.instansi || '',
                        alamat_tujuan: result.alamat_tujuan || '',
                        dokumentasi: result.dokumentasi || '',
                        tanggal: result.tanggal || '',
                        hari: result.hari || '',
                        jabatan: result.jabatan || ''
                    };
                    
                    console.log('Form data loaded:', this.formLaporan);
                    
                    // Show modal
                    this.showEditModal = true;
                    
                    // Force Alpine.js to update
                    await this.$nextTick();
                    
                    // Reinitialize lucide icons
                    setTimeout(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }, 100);
                } else {
                    const errorMsg = result?.message || result?.error || 'Gagal memuat data laporan';
                    this.showMessage(errorMsg, 'error');
                }
            } catch (error) {
                console.error('Error loading edit data:', error);
                this.showMessage('Terjadi kesalahan saat memuat data: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async editLaporan(id, nama, instansi, alamatTujuan, dokumentasi, tanggal, hari, jabatan) {
            try {
                console.log('editLaporan called');
                console.log('Parameters:', { id, nama, instansi, alamatTujuan, dokumentasi, tanggal, hari, jabatan });
                
                // Convert ID to number if it's a string
                id = typeof id === 'string' ? parseInt(id) : id;
                
                this.editingId = id;
                this.formLaporan = {
                    nama: (nama && nama !== 'null' && nama !== 'undefined') ? String(nama) : '',
                    instansi: (instansi && instansi !== 'null' && instansi !== 'undefined') ? String(instansi) : '',
                    alamat_tujuan: (alamatTujuan && alamatTujuan !== 'null' && alamatTujuan !== 'undefined') ? String(alamatTujuan) : '',
                    dokumentasi: (dokumentasi && dokumentasi !== 'null' && dokumentasi !== 'undefined') ? String(dokumentasi) : '',
                    tanggal: (tanggal && tanggal !== 'null' && tanggal !== 'undefined') ? String(tanggal) : '',
                    hari: (hari && hari !== 'null' && hari !== 'undefined') ? String(hari) : '',
                    jabatan: (jabatan && jabatan !== 'null' && jabatan !== 'undefined') ? String(jabatan) : ''
                };
                
                console.log('Form data set:', this.formLaporan);
                
                // Show modal first
                this.showEditModal = true;
                console.log('showEditModal set to:', this.showEditModal);
                
                // Force Alpine.js to update the DOM
                await this.$nextTick();
                
                // Double check modal is shown
                setTimeout(() => {
                    console.log('Modal should be visible now. showEditModal:', this.showEditModal);
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 100);
            } catch (error) {
                console.error('Error in editLaporan:', error);
                console.error('Error stack:', error.stack);
                this.showMessage('Terjadi kesalahan saat membuka form edit: ' + error.message, 'error');
            }
        },
        
        async updateLaporan() {
            try {
                this.loading = true;
                
                const response = await fetch(`/api/laporan-karyawan/${this.editingId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(this.formLaporan)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showMessage(result.message || 'Laporan berhasil diperbarui!', 'success');
                    this.showEditModal = false;
                    // Reload page to refresh data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showMessage(result.message || 'Laporan berhasil dihapus!', 'success');
                    // Reload page to refresh data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
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
                const params = new URLSearchParams();
                if (this.filterTanggal) params.append('tanggal', this.filterTanggal);
                if (this.filterHari) params.append('hari', this.filterHari);
                if (this.filterKelompok) params.append('kelompok', this.filterKelompok);
                
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
        
        // Pagination functions - using server-side pagination
        async goToPage(page) {
            const params = new URLSearchParams(window.location.search);
            params.set('page', page);
            if (this.filterTanggal) params.set('tanggal', this.filterTanggal);
            if (this.filterHari) params.set('hari', this.filterHari);
            if (this.filterKelompok) params.set('kelompok', this.filterKelompok);
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        },
        
        async previousPage() {
            const params = new URLSearchParams(window.location.search);
            const currentPage = parseInt(params.get('page') || '1');
            if (currentPage > 1) {
                params.set('page', currentPage - 1);
                if (this.filterTanggal) params.set('tanggal', this.filterTanggal);
                if (this.filterHari) params.set('hari', this.filterHari);
                if (this.filterKelompok) params.set('kelompok', this.filterKelompok);
                window.location.href = `${window.location.pathname}?${params.toString()}`;
            }
        },
        
        async nextPage() {
            const params = new URLSearchParams(window.location.search);
            const currentPage = parseInt(params.get('page') || '1');
            if (currentPage < this.totalPages) {
                params.set('page', currentPage + 1);
                if (this.filterTanggal) params.set('tanggal', this.filterTanggal);
                if (this.filterHari) params.set('hari', this.filterHari);
                if (this.filterKelompok) params.set('kelompok', this.filterKelompok);
                window.location.href = `${window.location.pathname}?${params.toString()}`;
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

// Tidak perlu fungsi global lagi karena sudah menggunakan Alpine.js langsung

// Initialize icons setelah page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 200);
});
</script>
@endsection