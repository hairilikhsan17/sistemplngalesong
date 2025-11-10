@extends('layouts.dashboard')

@section('title', 'Dashboard Kelompok')

@section('content')
<div class="p-6" x-data="kelompokDashboard()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Kelompok</h1>
                <p class="text-gray-600 mt-2">Selamat datang, <span x-text="stats.nama_kelompok"></span> - <span x-text="stats.shift"></span></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                    <span x-text="currentTime"></span>
                </div>
                <button @click="refreshData()" 
                        class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Alert -->
    <div x-show="stats.performance_ranking && stats.performance_ranking.rank <= 3" 
         class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="award" class="w-5 h-5 text-green-600 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-green-800">Performa Terbaik!</h3>
                <p class="text-sm text-green-700">Kelompok Anda berada di peringkat <span x-text="stats.performance_ranking?.rank"></span> dari <span x-text="stats.performance_ranking?.total_groups"></span> kelompok</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Anggota -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Anggota</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total_anggota"></p>
                </div>
            </div>
        </div>

        <!-- Laporan Hari Ini -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="file-text" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.laporan_hari_ini"></p>
                </div>
            </div>
        </div>

        <!-- Laporan Bulan Ini -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.laporan_bulan_ini"></p>
                </div>
            </div>
        </div>

        <!-- Rata-rata Waktu -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rata-rata Waktu</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="(stats.avg_waktu_penyelesaian || 0).toFixed(1) + ' hari'"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Performance -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performa Harian (7 Hari Terakhir)</h3>
            <canvas id="dailyPerformanceChart" width="400" height="200"></canvas>
        </div>

        <!-- Weekly Trend -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Mingguan</h3>
            <canvas id="weeklyTrendChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Member Performance -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performa Anggota</h3>
            <canvas id="memberPerformanceChart" width="400" height="200"></canvas>
        </div>

        <!-- Job Distribution -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Jenis Pekerjaan</h3>
            <canvas id="jobDistributionChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Performance Overview -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Performa</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Peringkat Kelompok</span>
                    <span class="text-sm font-medium" x-text="stats.performance_ranking?.rank || '-' + ' dari ' + (stats.performance_ranking?.total_groups || 0)"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Job</span>
                    <span class="text-sm font-medium" x-text="stats.total_job"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Job Selesai</span>
                    <span class="text-sm font-medium" x-text="stats.job_selesai"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Pending Laporan</span>
                    <span class="text-sm font-medium" x-text="stats.laporan_pending"></span>
                </div>
            </div>
        </div>


        <!-- Recent Laporan -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Laporan Terbaru</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                <template x-for="laporan in stats.recent_laporan" :key="laporan.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="laporan.karyawan"></p>
                            <p class="text-xs text-gray-500" x-text="laporan.created_at"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900" x-text="laporan.waktu_penyelesaian + ' hari'"></p>
                            <span :class="{
                                'bg-green-100 text-green-800': laporan.status === 'selesai',
                                'bg-yellow-100 text-yellow-800': laporan.status === 'pending',
                                'bg-blue-100 text-blue-800': laporan.status === 'progress'
                            }" class="px-2 py-1 text-xs rounded-full" x-text="laporan.status"></span>
                        </div>
                    </div>
                </template>
                
                <div x-show="!stats.recent_laporan?.length" class="text-center py-4">
                    <i data-lucide="file-text" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada laporan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Tasks -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tugas Mendatang</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="task in stats.upcoming_tasks" :key="task.id">
                <div class="p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900" x-text="task.nama_pekerjaan"></h4>
                        <span :class="{
                            'bg-red-100 text-red-800': task.prioritas === 'tinggi',
                            'bg-yellow-100 text-yellow-800': task.prioritas === 'normal',
                            'bg-green-100 text-green-800': task.prioritas === 'rendah'
                        }" class="px-2 py-1 text-xs rounded-full" x-text="task.prioritas"></span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2" x-text="'Tanggal: ' + task.tanggal"></p>
                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800" x-text="task.status"></span>
                </div>
            </template>
            
            <div x-show="!stats.upcoming_tasks?.length" class="col-span-full text-center py-8">
                <i data-lucide="check-circle" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-500">Tidak ada tugas yang perlu diselesaikan</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('kelompok.laporan') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i data-lucide="file-plus" class="w-5 h-5 text-blue-600 mr-3"></i>
                <span class="text-sm font-medium text-blue-900">Buat Laporan</span>
            </a>
            
            <a href="#" onclick="showTab('job')" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i data-lucide="briefcase" class="w-5 h-5 text-green-600 mr-3"></i>
                <span class="text-sm font-medium text-green-900">Input Job</span>
            </a>
            

            
            <a href="{{ route('kelompok.settings') }}" 
               class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                <i data-lucide="settings" class="w-5 h-5 text-orange-600 mr-3"></i>
                <span class="text-sm font-medium text-orange-900">Pengaturan</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('kelompokDashboard', () => ({
        stats: {},
        currentTime: '',
        loading: false,
        charts: {},
        
        async init() {
            await this.loadStats();
            await this.loadChartData();
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            
            // Auto refresh every 5 minutes
            setInterval(() => {
                this.refreshData();
            }, 300000);
        },
        
        async loadStats() {
            try {
                const response = await fetch('/api/kelompok/dashboard-stats', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                this.stats = result.stats;
                console.log('Stats loaded:', this.stats);
            } catch (error) {
                console.error('Error loading stats:', error);
                this.stats = {
                    nama_kelompok: 'Error',
                    shift: 'Error',
                    total_anggota: 0,
                    laporan_hari_ini: 0,
                    laporan_bulan_ini: 0,
                    avg_waktu_penyelesaian: 0
                };
            }
        },
        
        async loadChartData() {
            try {
                const response = await fetch('/api/kelompok/dashboard-charts', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                this.initializeCharts(result);
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        },
        
        initializeCharts(data) {
            // Daily Performance Chart
            const dailyCtx = document.getElementById('dailyPerformanceChart').getContext('2d');
            this.charts.daily = new Chart(dailyCtx, {
                type: 'line',
                data: data.daily_performance,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Weekly Trend Chart
            const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
            this.charts.weekly = new Chart(weeklyCtx, {
                type: 'bar',
                data: data.weekly_trend,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Member Performance Chart
            const memberCtx = document.getElementById('memberPerformanceChart').getContext('2d');
            this.charts.member = new Chart(memberCtx, {
                type: 'bar',
                data: data.member_performance,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Job Distribution Chart
            const jobCtx = document.getElementById('jobDistributionChart').getContext('2d');
            this.charts.job = new Chart(jobCtx, {
                type: 'doughnut',
                data: data.job_distribution,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },
        
        async refreshData() {
            this.loading = true;
            await this.loadStats();
            await this.loadChartData();
            this.loading = false;
        },
        
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }));
});
</script>
@endsection
