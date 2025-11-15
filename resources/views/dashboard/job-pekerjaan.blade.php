@extends('layouts.dashboard')

@section('title', 'Job Pekerjaan')

@section('content')
<!-- Header -->
<div class="bg-white shadow-sm border-b mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 py-4 sm:py-6 px-3 sm:px-4 lg:px-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Job Pekerjaan</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola data pekerjaan dan aktivitas kelompok</p>
        </div>
        <button onclick="openCreateModal()" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center transition-colors w-full sm:w-auto min-h-[44px]">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah Job
        </button>
    </div>
</div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8 px-3 sm:px-0">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="zap" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Job Pekerjaan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['totalJob'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Waktu (jam)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['totalWaktu'] ?? 0, 1) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i data-lucide="calendar" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['hariIni'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i data-lucide="map-pin" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Lokasi Berbeda</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['lokasiBerbeda'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="bg-white rounded-lg shadow mb-4 sm:mb-6 mx-3 sm:mx-0">
            <div class="p-4 sm:p-6">
                <div class="flex flex-col md:flex-row gap-3 sm:gap-4">
                    <form method="GET" action="{{ route('kelompok.job-pekerjaan') }}" class="flex flex-col md:flex-row gap-3 sm:gap-4 w-full">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Job</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan lokasi atau hari..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent form-input-mobile">
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                            <select name="day" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent form-input-mobile">
                                <option value="">Semua Hari</option>
                                <option value="Senin" {{ request('day') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                <option value="Selasa" {{ request('day') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                <option value="Rabu" {{ request('day') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                <option value="Kamis" {{ request('day') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                <option value="Jumat" {{ request('day') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                <option value="Sabtu" {{ request('day') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                <option value="Minggu" {{ request('day') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>
                        <div class="w-full md:w-48 flex items-end gap-2">
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors min-h-[44px] flex items-center justify-center shadow-md hover:shadow-lg">
                                <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                                <span>Cari</span>
                            </button>
                            <a href="{{ route('kelompok.job-pekerjaan') }}" 
                               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors min-h-[44px] flex items-center justify-center shadow-md hover:shadow-lg">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="bg-white rounded-lg shadow mx-3 sm:mx-0">
            <div class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Daftar Job Pekerjaan</h3>
            </div>
            <div class="overflow-x-auto -mx-3 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Perbaikan KWH</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pemeliharaan Pengkabelan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pengecekan Gardu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Penanganan Gangguan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu (jam)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($jobPekerjaans as $index => $job)
                            <tr class="hover:bg-blue-50/50 transition-colors border-b border-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                        {{ $jobPekerjaans->firstItem() + $index }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($job->tanggal)->locale('id')->isoFormat('DD MMM YYYY') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="calendar-days" class="w-4 h-4 text-purple-600"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $job->hari }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $job->perbaikan_kwh }}">
                                        <i data-lucide="zap" class="w-4 h-4 text-yellow-600 inline mr-1"></i>
                                        {{ Str::limit($job->perbaikan_kwh, 40) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $job->pemeliharaan_pengkabelan }}">
                                        <i data-lucide="cable" class="w-4 h-4 text-orange-600 inline mr-1"></i>
                                        {{ Str::limit($job->pemeliharaan_pengkabelan, 40) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $job->pengecekan_gardu }}">
                                        <i data-lucide="building" class="w-4 h-4 text-green-600 inline mr-1"></i>
                                        {{ Str::limit($job->pengecekan_gardu, 40) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $job->penanganan_gangguan }}">
                                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600 inline mr-1"></i>
                                        {{ Str::limit($job->penanganan_gangguan, 40) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-red-600 flex-shrink-0"></i>
                                        <span class="line-clamp-2">{{ Str::limit($job->lokasi, 50) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-indigo-600"></i>
                                        <span class="font-medium">{{ $job->waktu_penyelesaian }} jam</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- View Button - Lebih Kentara -->
                                        <button onclick="viewJob('{{ $job->id }}')" 
                                                class="inline-flex items-center justify-center w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg group border-2 border-green-600"
                                                title="Lihat Detail">
                                            <i data-lucide="eye" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                        </button>
                                        
                                        <!-- Edit Button - Lebih Kentara -->
                                        <button onclick="editJob('{{ $job->id }}')" 
                                                class="inline-flex items-center justify-center w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg group border-2 border-blue-600"
                                                title="Edit Job">
                                            <i data-lucide="edit-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                        </button>
                                        
                                        <!-- Delete Button - Lebih Kentara -->
                                        <button onclick="deleteJob('{{ $job->id }}')" 
                                                class="inline-flex items-center justify-center w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg group border-2 border-red-600"
                                                title="Hapus Job">
                                            <i data-lucide="trash-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-4 bg-gray-100 rounded-full mb-4">
                                            <i data-lucide="briefcase" class="w-16 h-16 text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-700 text-lg font-semibold mb-2">Belum ada job pekerjaan</p>
                                        <p class="text-gray-500 text-sm mb-4">Mulai dengan membuat job pekerjaan pertama Anda</p>
                                        <button onclick="openCreateModal()" 
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                            Tambah Job Pertama
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($jobPekerjaans->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($jobPekerjaans->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $jobPekerjaans->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif
                
                @if($jobPekerjaans->hasMorePages())
                    <a href="{{ $jobPekerjaans->appends(request()->except('page'))->nextPageUrl() }}" 
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
                        <span class="font-medium">{{ $jobPekerjaans->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-medium">{{ $jobPekerjaans->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-medium">{{ $jobPekerjaans->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        @if($jobPekerjaans->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </span>
                        @else
                            <a href="{{ $jobPekerjaans->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </a>
                        @endif
                        
                        @php
                            $currentPage = $jobPekerjaans->currentPage();
                            $lastPage = $jobPekerjaans->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $jobPekerjaans->appends(request()->except('page'))->url(1) }}" 
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
                            <a href="{{ $jobPekerjaans->appends(request()->except('page'))->url($page) }}" 
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
                            <a href="{{ $jobPekerjaans->appends(request()->except('page'))->url($lastPage) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $lastPage }}
                            </a>
                        @endif
                        
                        @if($jobPekerjaans->hasMorePages())
                            <a href="{{ $jobPekerjaans->appends(request()->except('page'))->nextPageUrl() }}" 
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
</div>

<!-- Create/Edit Modal -->
<div id="job-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-3 sm:p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 id="modal-title" class="text-base sm:text-lg font-medium text-gray-900">Tambah Job Pekerjaan</h3>
                    <button onclick="closeModal()" 
                            class="text-gray-400 hover:text-gray-600 p-2 rounded-md hover:bg-gray-100 min-w-[44px] min-h-[44px] flex items-center justify-center">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            
            <form id="job-form" class="p-4 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                        <select id="hari" name="hari" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" id="lokasi" name="lokasi" required placeholder="Masukkan lokasi pekerjaan"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Perbaikan KWH <span class="text-red-500">*</span></label>
                        <textarea id="perbaikan_kwh" name="perbaikan_kwh" required rows="3" placeholder="Contoh: Ganti KWH rusak di rumah warga RT 02 RW 03"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pemeliharaan Pengkabelan <span class="text-red-500">*</span></label>
                        <textarea id="pemeliharaan_pengkabelan" name="pemeliharaan_pengkabelan" required rows="3" placeholder="Contoh: Perbaikan jalur kabel utama arah Gardu PLN Galesong"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pengecekan Gardu <span class="text-red-500">*</span></label>
                        <textarea id="pengecekan_gardu" name="pengecekan_gardu" required rows="3" placeholder="Contoh: Pemeriksaan tegangan 220V di Gardu C 15"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Penanganan Gangguan <span class="text-red-500">*</span></label>
                        <textarea id="penanganan_gangguan" name="penanganan_gangguan" required rows="3" placeholder="Contoh: Pohon tumbang mengenai kabel listrik di Jl. Poros Galesong"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Penyelesaian (jam) <span class="text-red-500">*</span></label>
                        <input type="number" id="waktu_penyelesaian" name="waktu_penyelesaian" required min="0" step="0.5" placeholder="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="submit-btn"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Detail Modal -->
<div id="view-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Detail Job Pekerjaan</h3>
                    <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
            <div id="view-modal-content" class="p-6">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end">
                <button onclick="closeViewModal()" 
                        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus job pekerjaan ini?</p>
                <p class="text-sm text-gray-500 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Batal
                </button>
                <button onclick="confirmDeleteJob()" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let jobs = [];
let currentJobId = null;
let isEditMode = false;

// Load jobs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Data is already loaded from server-side, no need to call loadJobs()
    // loadJobs(); 
    setupEventListeners();
    
    // Reinitialize lucide icons
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
});

function setupEventListeners() {
    // Form submission
    document.getElementById('job-form').addEventListener('submit', handleFormSubmit);
}

function loadJobs() {
    const searchTerm = document.getElementById('search-input').value;
    const dayFilter = document.getElementById('day-filter').value;
    
    showLoading();
    
    let url = '/api/job-pekerjaan';
    const params = new URLSearchParams();
    if (searchTerm) params.append('search', searchTerm);
    if (dayFilter) params.append('day', dayFilter);
    if (params.toString()) url += '?' + params.toString();
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     window.csrfToken || 
                     document.querySelector('input[name="_token"]')?.value;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        jobs = data;
        renderJobsTable();
        updateStats();
        hideLoading();
    })
    .catch(error => {
        console.error('Error loading jobs:', error);
        showError('Gagal memuat data job pekerjaan');
        hideLoading();
    });
}

function renderJobsTable() {
    const tbody = document.getElementById('jobs-table-body');
    const noData = document.getElementById('no-data');
    
    if (jobs.length === 0) {
        tbody.innerHTML = '';
        noData.classList.remove('hidden');
        return;
    }
    
    noData.classList.add('hidden');
    tbody.innerHTML = jobs.map((job, index) => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${String(index + 1).padStart(3, '0')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDate(job.tanggal)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${job.hari || '-'}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                <div class="truncate" title="${job.perbaikan_kwh}">
                    ${job.perbaikan_kwh}
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                <div class="truncate" title="${job.pemeliharaan_pengkabelan}">
                    ${job.pemeliharaan_pengkabelan}
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                <div class="truncate" title="${job.pengecekan_gardu}">
                    ${job.pengecekan_gardu}
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                <div class="truncate" title="${job.penanganan_gangguan}">
                    ${job.penanganan_gangguan}
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                ${job.lokasi}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${job.waktu_penyelesaian} jam
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                <div class="flex items-center justify-center space-x-1">
                    <!-- View Button -->
                    <button onclick="viewJob('${job.id}')" 
                            class="inline-flex items-center px-2 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                            title="Lihat Detail">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="ml-1 text-xs font-medium hidden sm:inline">Lihat</span>
                    </button>
                    
                    <!-- Edit Button -->
                    <button onclick="editJob('${job.id}')" 
                            class="inline-flex items-center px-2 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                            title="Edit Job">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="ml-1 text-xs font-medium hidden sm:inline">Edit</span>
                    </button>
                    
                    <!-- Delete Button -->
                    <button onclick="deleteJob('${job.id}')" 
                            class="inline-flex items-center px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md transition-all duration-200 hover:shadow-md group"
                            title="Hapus Job">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span class="ml-1 text-xs font-medium hidden sm:inline">Hapus</span>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    // Re-initialize Lucide icons
    lucide.createIcons();
}

function updateStats() {
    const totalJobs = jobs.length;
    const totalWaktu = jobs.reduce((sum, job) => sum + (job.waktu_penyelesaian || 0), 0);
    
    // Count jobs for today - get today's date in YYYY-MM-DD format
    const today = new Date();
    const todayString = today.getFullYear() + '-' + 
                       String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(today.getDate()).padStart(2, '0');
    
    console.log('Today string:', todayString);
    console.log('All jobs:', jobs);
    
    const todayJobs = jobs.filter(job => {
        // Convert job date to YYYY-MM-DD format for comparison
        const jobDate = new Date(job.tanggal);
        const jobDateString = jobDate.getFullYear() + '-' + 
                             String(jobDate.getMonth() + 1).padStart(2, '0') + '-' + 
                             String(jobDate.getDate()).padStart(2, '0');
        
        console.log('Job date:', job.tanggal, '->', jobDateString, 'matches today:', jobDateString === todayString);
        return jobDateString === todayString;
    }).length;
    
    console.log('Today jobs count:', todayJobs);
    
    // Count unique locations
    const uniqueLocations = new Set(jobs.map(job => job.lokasi)).size;
    
    document.getElementById('total-jobs').textContent = totalJobs;
    document.getElementById('total-waktu').textContent = totalWaktu;
    document.getElementById('today-jobs').textContent = todayJobs;
    document.getElementById('total-lokasi').textContent = uniqueLocations;
}

function openCreateModal() {
    isEditMode = false;
    currentJobId = null;
    document.getElementById('modal-title').textContent = 'Tambah Job Pekerjaan';
    document.getElementById('submit-btn').textContent = 'Simpan';
    document.getElementById('job-form').reset();
    document.getElementById('job-modal').classList.remove('hidden');
}

function editJob(jobId) {
    // Load job data from API
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     window.csrfToken || 
                     document.querySelector('input[name="_token"]')?.value;
    
    fetch(`/api/job-pekerjaan/${jobId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(job => {
        isEditMode = true;
        currentJobId = jobId;
        document.getElementById('modal-title').textContent = 'Edit Job Pekerjaan';
        document.getElementById('submit-btn').textContent = 'Update';
        
        // Fill form with job data
        document.getElementById('tanggal').value = job.tanggal ? job.tanggal.split('T')[0] : '';
        document.getElementById('hari').value = job.hari || '';
        document.getElementById('lokasi').value = job.lokasi || '';
        document.getElementById('perbaikan_kwh').value = job.perbaikan_kwh || '';
        document.getElementById('pemeliharaan_pengkabelan').value = job.pemeliharaan_pengkabelan || '';
        document.getElementById('pengecekan_gardu').value = job.pengecekan_gardu || '';
        document.getElementById('penanganan_gangguan').value = job.penanganan_gangguan || '';
        document.getElementById('waktu_penyelesaian').value = job.waktu_penyelesaian || '';
        
        document.getElementById('job-modal').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error loading job:', error);
        showError('Gagal memuat data job pekerjaan');
    });
}

function viewJob(jobId) {
    // Load job data from API
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     window.csrfToken || 
                     document.querySelector('input[name="_token"]')?.value;
    
    fetch(`/api/job-pekerjaan/${jobId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(job => {
        const content = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal</label>
                        <p class="text-gray-900 font-medium">${formatDate(job.tanggal)}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Hari</label>
                        <p class="text-gray-900 font-medium">${job.hari || '-'}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Lokasi</label>
                        <p class="text-gray-900 font-medium">${job.lokasi || '-'}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Perbaikan KWH</label>
                        <p class="text-gray-900">${job.perbaikan_kwh || '-'}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Pemeliharaan Pengkabelan</label>
                        <p class="text-gray-900">${job.pemeliharaan_pengkabelan || '-'}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Pengecekan Gardu</label>
                        <p class="text-gray-900">${job.pengecekan_gardu || '-'}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Penanganan Gangguan</label>
                        <p class="text-gray-900">${job.penanganan_gangguan || '-'}</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Waktu Penyelesaian</label>
                        <p class="text-gray-900 font-medium">${job.waktu_penyelesaian || 0} jam</p>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('view-modal-content').innerHTML = content;
        document.getElementById('view-modal').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error loading job:', error);
        showError('Gagal memuat data job pekerjaan');
    });
}

function deleteJob(jobId) {
    console.log('deleteJob called with ID:', jobId);
    if (!jobId) {
        console.error('No jobId provided to deleteJob function');
        showError('ID job tidak valid');
        return;
    }
    
    // Validate job ID format (should be UUID)
    if (typeof jobId !== 'string' || jobId.length < 10) {
        console.error('Invalid job ID format:', jobId);
        showError('Format ID job tidak valid');
        return;
    }
    
    currentJobId = jobId;
    console.log('Setting currentJobId to:', currentJobId);
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('job-modal').classList.add('hidden');
    document.getElementById('job-form').reset();
}

function closeViewModal() {
    document.getElementById('view-modal').classList.add('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    currentJobId = null;
}

function confirmDeleteJob() {
    if (!currentJobId) {
        console.error('No currentJobId found');
        showError('ID job tidak ditemukan');
        return;
    }
    
    console.log('Deleting job with ID:', currentJobId);
    
    // Show loading state
    const deleteBtn = document.querySelector('#delete-modal button[onclick="confirmDeleteJob()"]');
    const originalText = deleteBtn.textContent;
    deleteBtn.textContent = 'Menghapus...';
    deleteBtn.disabled = true;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     window.csrfToken || 
                     document.querySelector('input[name="_token"]')?.value;
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showError('CSRF token tidak ditemukan. Silakan refresh halaman.');
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = false;
        return;
    }
    
    console.log('Making DELETE request to:', `/api/job-pekerjaan/${currentJobId}`);
    console.log('CSRF Token:', csrfToken);
    
    fetch(`/api/job-pekerjaan/${currentJobId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Delete response status:', response.status);
        console.log('Delete response headers:', response.headers);
        
        if (!response.ok) {
            // Try to get error message from response
            return response.text().then(text => {
                console.log('Error response text:', text);
                let errorMessage = `HTTP ${response.status}`;
                try {
                    const errorData = JSON.parse(text);
                    errorMessage = errorData.message || errorData.error || errorMessage;
                } catch (e) {
                    errorMessage = text || errorMessage;
                }
                throw new Error(errorMessage);
            });
        }
        
        // Try to parse JSON response
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.log('Non-JSON response:', text);
                return { success: true, message: 'Job pekerjaan berhasil dihapus' };
            });
        }
    })
    .then(data => {
        console.log('Delete response data:', data);
        
        // Check for success in various response formats
        if (data.success === true || data.success === 'true' || data.id) {
            showSuccess('Job pekerjaan berhasil dihapus');
            closeDeleteModal();
            // Reload page to refresh server-side pagination
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.message || data.error || 'Gagal menghapus job pekerjaan');
        }
    })
    .catch(error => {
        console.error('Error deleting job:', error);
        showError('Gagal menghapus job pekerjaan: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = false;
    });
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    const url = isEditMode ? `/api/job-pekerjaan/${currentJobId}` : '/api/job-pekerjaan';
    const method = isEditMode ? 'PUT' : 'POST';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     window.csrfToken || 
                     document.querySelector('input[name="_token"]')?.value;
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showError('CSRF token tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.id || data.success) {
            showSuccess(isEditMode ? 'Job pekerjaan berhasil diupdate' : 'Job pekerjaan berhasil ditambahkan');
            closeModal();
            // Reload page to refresh server-side pagination
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.error || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error saving job:', error);
        showError('Gagal menyimpan job pekerjaan');
    });
}

function showLoading() {
    document.getElementById('loading-spinner').classList.remove('hidden');
    document.getElementById('jobs-table-body').innerHTML = '';
    document.getElementById('no-data').classList.add('hidden');
}

function hideLoading() {
    document.getElementById('loading-spinner').classList.add('hidden');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showSuccess(message) {
    // Create a success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function showError(message) {
    // Create an error notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}
</script>
@endsection
