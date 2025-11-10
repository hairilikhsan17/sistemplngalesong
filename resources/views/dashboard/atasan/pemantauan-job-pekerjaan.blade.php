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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
</div>

<script>
// Alpine.js data
document.addEventListener('alpine:init', () => {
    Alpine.data('pemantauanJobData', () => ({
        // Filter data
        filterTanggal: '',
        filterHari: '',
        filterKelompok: '',
        
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



