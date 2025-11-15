@extends('layouts.dashboard')

@section('title', 'Pemantauan Job Pekerjaan')

@section('content')
<div class="p-6" x-data="pemantauanJobData()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pemantauan Job Pekerjaan</h1>
        <p class="text-gray-600 mt-2">Pantau semua job pekerjaan dari semua kelompok</p>
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
            <h3 class="text-lg font-medium text-gray-900">Filter Job Pekerjaan</h3>
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
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        💼
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Job</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.totalJob || {{ $statistics['totalJob'] ?? 0 }}"></span>
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
                        <dt class="text-sm font-medium text-gray-500 truncate">Job Hari Ini</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.jobHariIni || {{ $statistics['jobHariIni'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                        ⏱️
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Waktu (jam)</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.totalWaktu || {{ $statistics['totalWaktu'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        ✓
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Kelompok Sudah Input</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.kelompokDenganJob || {{ $statistics['kelompokDenganJob'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                        ⚠️
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Kelompok Belum Input</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            <span x-text="statistics.kelompokTanpaJob || {{ $statistics['kelompokTanpaJob'] ?? 0 }}"></span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Kelompok Status Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Kelompok yang Sudah Input -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">✅ Kelompok yang Sudah Input Job</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-2">
                    @forelse($kelompokDenganJob as $kelompok)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900">{{ $kelompok->nama_kelompok }}</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ $kelompok->jobPekerjaan->count() }} job</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada kelompok yang input job</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kelompok yang Belum Input -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">⚠️ Kelompok yang Belum Input Job</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-2">
                    @forelse($kelompokTanpaJob as $kelompok)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900">{{ $kelompok->nama_kelompok }}</span>
                        </div>
                        <span class="text-xs text-red-500">Belum input</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Semua kelompok sudah input job</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Job Pekerjaan Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Data Job Pekerjaan</h3>
                <div class="flex space-x-2">
                    <button @click="exportJob()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        📥 Export Excel
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perbaikan KWH</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeliharaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengecekan Gardu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penanganan Gangguan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu (jam)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobPekerjaans as $index => $job)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ($jobPekerjaans->currentPage() - 1) * $jobPekerjaans->perPage() + $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $job->tanggal->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $job->hari ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $job->kelompok->nama_kelompok ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($job->lokasi, 30) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $job->perbaikan_kwh }}">
                                {{ Str::limit($job->perbaikan_kwh, 40) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $job->pemeliharaan_pengkabelan }}">
                                {{ Str::limit($job->pemeliharaan_pengkabelan, 40) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $job->pengecekan_gardu }}">
                                {{ Str::limit($job->pengecekan_gardu, 40) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                            <div class="truncate" title="{{ $job->penanganan_gangguan }}">
                                {{ Str::limit($job->penanganan_gangguan, 40) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $job->waktu_penyelesaian }} jam</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button @click="lihatDetail('{{ $job->id }}')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors duration-200 group"
                                        title="Lihat Detail">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium">Detail</span>
                                </button>
                                <button @click="editJob('{{ $job->id }}', '{{ addslashes($job->tanggal->format('Y-m-d')) }}', '{{ addslashes($job->hari ?? '') }}', '{{ addslashes($job->lokasi) }}', '{{ addslashes($job->perbaikan_kwh) }}', '{{ addslashes($job->pemeliharaan_pengkabelan) }}', '{{ addslashes($job->pengecekan_gardu) }}', '{{ addslashes($job->penanganan_gangguan) }}', '{{ $job->waktu_penyelesaian }}')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-md transition-colors duration-200 group"
                                        title="Edit Job">
                                    <i data-lucide="edit-2" class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium">Edit</span>
                                </button>
                                <button @click="hapusJob('{{ $job->id }}')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-md transition-colors duration-200 group"
                                        title="Hapus Job">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-medium">Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada data job pekerjaan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($jobPekerjaans->previousPageUrl())
                <a href="{{ $jobPekerjaans->previousPageUrl() }}" 
                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
                @else
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                    Previous
                </span>
                @endif
                
                @if($jobPekerjaans->nextPageUrl())
                <a href="{{ $jobPekerjaans->nextPageUrl() }}" 
                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
                @else
                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                    Next
                </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $jobPekerjaans->firstItem() }}</span>
                        sampai
                        <span class="font-medium">{{ $jobPekerjaans->lastItem() }}</span>
                        dari
                        <span class="font-medium">{{ $jobPekerjaans->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    {{ $jobPekerjaans->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Job Pekerjaan -->
    <div x-show="showDetailModal" x-cloak x-transition class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDetailModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i data-lucide="briefcase" class="w-6 h-6 text-white"></i>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-white">Detail Job Pekerjaan</h3>
                        </div>
                        <button @click="showDetailModal = false" 
                                class="text-white hover:text-gray-200 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="bg-white px-6 py-6">
                    <div x-show="!detailJob" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-4 text-gray-500">Memuat data...</p>
                    </div>
                    
                    <div class="space-y-6" x-show="detailJob">
                        <!-- Informasi Utama -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <label class="block text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">
                                    <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                    Tanggal
                                </label>
                                <p class="text-sm font-medium text-gray-900" x-text="detailJob.tanggal ? (new Date(detailJob.tanggal).toLocaleDateString('id-ID') || detailJob.tanggal) : '-'"></p>
                            </div>
                            
                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                                <label class="block text-xs font-semibold text-purple-700 uppercase tracking-wide mb-1">
                                    <i data-lucide="calendar-days" class="w-3 h-3 inline mr-1"></i>
                                    Hari
                                </label>
                                <p class="text-sm font-medium text-gray-900" x-text="detailJob.hari || '-'"></p>
                            </div>
                            
                            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                <label class="block text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">
                                    <i data-lucide="users" class="w-3 h-3 inline mr-1"></i>
                                    Kelompok
                                </label>
                                <p class="text-sm font-medium text-gray-900" x-text="detailJob.kelompok?.nama_kelompok || '-'"></p>
                            </div>
                            
                            <div class="bg-amber-50 rounded-lg p-4 border border-amber-100">
                                <label class="block text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                    Lokasi
                                </label>
                                <p class="text-sm font-medium text-gray-900" x-text="detailJob.lokasi || '-'"></p>
                            </div>
                            
                            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                                <label class="block text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-1">
                                    <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                    Waktu Penyelesaian
                                </label>
                                <p class="text-sm font-medium text-gray-900" x-text="(detailJob.waktu_penyelesaian || 0) + ' jam'"></p>
                            </div>
                        </div>
                        
                        <!-- Informasi Detail -->
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">
                                    <i data-lucide="zap" class="w-3 h-3 inline mr-1"></i>
                                    Perbaikan KWH
                                </label>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="detailJob.perbaikan_kwh || '-'"></p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">
                                    <i data-lucide="settings" class="w-3 h-3 inline mr-1"></i>
                                    Pemeliharaan Pengkabelan
                                </label>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="detailJob.pemeliharaan_pengkabelan || '-'"></p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">
                                    <i data-lucide="search" class="w-3 h-3 inline mr-1"></i>
                                    Pengecekan Gardu
                                </label>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="detailJob.pengecekan_gardu || '-'"></p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">
                                    <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                    Penanganan Gangguan
                                </label>
                                <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="detailJob.penanganan_gangguan || '-'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="button" @click="showDetailModal = false" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Job Pekerjaan -->
    <div x-show="showEditModal" x-cloak x-transition class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i data-lucide="edit-2" class="w-6 h-6 text-white"></i>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-white">Edit Job Pekerjaan</h3>
                        </div>
                        <button @click="showEditModal = false" 
                                class="text-white hover:text-gray-200 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
                
                <form @submit.prevent="updateJob()">
                    <div class="bg-white px-6 py-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                    <input type="date" x-model="formJob.tanggal" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                           required>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hari</label>
                                    <input type="text" x-model="formJob.hari" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                           placeholder="Contoh: Senin" required>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                <input type="text" x-model="formJob.lokasi" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="Masukkan lokasi" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Perbaikan KWH</label>
                                <textarea x-model="formJob.perbaikan_kwh" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                          rows="3" placeholder="Masukkan perbaikan KWH" required></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pemeliharaan Pengkabelan</label>
                                <textarea x-model="formJob.pemeliharaan_pengkabelan" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                          rows="3" placeholder="Masukkan pemeliharaan pengkabelan" required></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pengecekan Gardu</label>
                                <textarea x-model="formJob.pengecekan_gardu" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                          rows="3" placeholder="Masukkan pengecekan gardu" required></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Penanganan Gangguan</label>
                                <textarea x-model="formJob.penanganan_gangguan" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                          rows="3" placeholder="Masukkan penanganan gangguan" required></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Waktu Penyelesaian (jam)</label>
                                <input type="number" x-model="formJob.waktu_penyelesaian" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm" 
                                       placeholder="Masukkan waktu penyelesaian" min="0" required>
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
</div>

<script>
// Alpine.js data
document.addEventListener('alpine:init', () => {
    Alpine.data('pemantauanJobData', () => ({
        // Filter data
        filterTanggal: '',
        filterHari: '',
        filterKelompok: '',
        
        // Modal states
        showDetailModal: false,
        showEditModal: false,
        
        // Form data
        formJob: {
            tanggal: '',
            hari: '',
            lokasi: '',
            perbaikan_kwh: '',
            pemeliharaan_pengkabelan: '',
            pengecekan_gardu: '',
            penanganan_gangguan: '',
            waktu_penyelesaian: 0
        },
        
        // Detail data
        detailJob: null,
        
        // Editing ID
        editingId: null,
        
        // Statistics - Initialize with server-side data
        statistics: {
            totalJob: {{ $statistics['totalJob'] ?? 0 }},
            jobHariIni: {{ $statistics['jobHariIni'] ?? 0 }},
            totalWaktu: {{ $statistics['totalWaktu'] ?? 0 }},
            kelompokDenganJob: {{ $statistics['kelompokDenganJob'] ?? 0 }},
            kelompokTanpaJob: {{ $statistics['kelompokTanpaJob'] ?? 0 }}
        },
        
        // UI states
        loading: false,
        message: '',
        messageType: '',
        
        init() {
            // Get filter values from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            this.filterTanggal = urlParams.get('tanggal') || '';
            this.filterHari = urlParams.get('hari') || '';
            this.filterKelompok = urlParams.get('kelompok') || '';
            
            // Load statistics
            this.loadStatistics();
        },
        
        // Filter functions
        async applyFilter() {
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
        async loadStatistics() {
            try {
                const params = new URLSearchParams();
                if (this.filterTanggal) params.append('tanggal', this.filterTanggal);
                if (this.filterHari) params.append('hari', this.filterHari);
                if (this.filterKelompok) params.append('kelompok', this.filterKelompok);
                
                const response = await fetch(`/api/pemantauan-job-pekerjaan/statistics?${params}`);
                const result = await response.json();
                
                if (response.ok) {
                    this.statistics = result;
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        },
        
        // Export function
        async exportJob() {
            try {
                const params = new URLSearchParams();
                if (this.filterTanggal) params.append('tanggal', this.filterTanggal);
                if (this.filterHari) params.append('hari', this.filterHari);
                if (this.filterKelompok) params.append('kelompok', this.filterKelompok);
                
                window.location.href = `/api/export/job-pekerjaan?${params}`;
            } catch (error) {
                console.error('Export error:', error);
                this.showMessage('Terjadi kesalahan saat export: ' + error.message, 'error');
            }
        },
        
        // Refresh function
        async refreshData() {
            await this.loadStatistics();
            window.location.reload();
        },
        
        // CRUD functions
        async lihatDetail(id) {
            try {
                this.loading = true;
                this.detailJob = null;
                this.showDetailModal = true;
                
                const response = await fetch(`/api/job-pekerjaan/${id}`);
                const result = await response.json();
                
                if (response.ok) {
                    this.detailJob = result;
                    // Reinitialize lucide icons after modal is shown
                    setTimeout(() => {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }, 100);
                } else {
                    this.showDetailModal = false;
                    const errorMsg = result.message || result.error || 'Gagal memuat detail job';
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
        
        async editJob(id, tanggal, hari, lokasi, perbaikan_kwh, pemeliharaan_pengkabelan, pengecekan_gardu, penanganan_gangguan, waktu_penyelesaian) {
            this.editingId = id;
            this.formJob = {
                tanggal: tanggal || '',
                hari: hari || '',
                lokasi: lokasi || '',
                perbaikan_kwh: perbaikan_kwh || '',
                pemeliharaan_pengkabelan: pemeliharaan_pengkabelan || '',
                pengecekan_gardu: pengecekan_gardu || '',
                penanganan_gangguan: penanganan_gangguan || '',
                waktu_penyelesaian: parseInt(waktu_penyelesaian) || 0
            };
            this.showEditModal = true;
            // Reinitialize lucide icons after modal is shown
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        },
        
        async updateJob() {
            try {
                this.loading = true;
                
                const response = await fetch(`/api/job-pekerjaan/${this.editingId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(this.formJob)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showMessage('Job pekerjaan berhasil diperbarui!', 'success');
                    this.showEditModal = false;
                    // Reload page to refresh data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    const errorMessage = result.message || (result.errors ? JSON.stringify(result.errors) : 'Gagal memperbarui job pekerjaan');
                    this.showMessage('Error: ' + errorMessage, 'error');
                }
            } catch (error) {
                console.error('Update error:', error);
                this.showMessage('Terjadi kesalahan: ' + error.message, 'error');
            }
            this.loading = false;
        },
        
        async hapusJob(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus job pekerjaan ini?')) return;
            
            try {
                const response = await fetch(`/api/job-pekerjaan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showMessage(result.message || 'Job pekerjaan berhasil dihapus!', 'success');
                    // Reload page to refresh data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showMessage(result.message || result.error || 'Gagal menghapus job pekerjaan', 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showMessage('Terjadi kesalahan: ' + error.message, 'error');
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





