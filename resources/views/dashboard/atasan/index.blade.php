@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="adminDashboard()">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">Monitor seluruh aktivitas dan performa sistem PLN Galesong</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-3 sm:space-x-4">
                <div class="text-xs sm:text-sm text-gray-500 hidden sm:block">
                    <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                    <span x-text="currentTime"></span>
                </div>
                <button @click="refreshData()" 
                        class="flex items-center bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base min-h-[44px]">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    <span class="hidden sm:inline">Refresh</span>
                    <span class="sm:hidden">Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- System Health Alert -->
    <div x-show="systemHealth.status === 'warning'" 
         class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-yellow-800">System Warning</h3>
                <p class="text-sm text-yellow-700">Ada <span x-text="systemHealth.pending_rate"></span>% laporan yang masih pending</p>
            </div>
        </div>
    </div>

    <div x-show="systemHealth.status === 'critical'" 
         class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-red-800">System Critical</h3>
                <p class="text-sm text-red-700">Ada <span x-text="systemHealth.pending_rate"></span>% laporan yang masih pending - Perhatian!</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Kelompok -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Kelompok</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total_kelompok"></p>
                </div>
            </div>
        </div>

        <!-- Total Karyawan -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="user-check" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.total_karyawan"></p>
                </div>
            </div>
        </div>

        <!-- Laporan Hari Ini -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.laporan_hari_ini"></p>
                </div>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Review</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.laporan_pending"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Monthly Reports Trend -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Trend Laporan Bulanan</h3>
            <div class="chart-container">
                <canvas id="monthlyReportsChart"></canvas>
            </div>
        </div>

        <!-- Group Performance -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Performa Kelompok</h3>
            <div class="chart-container">
                <canvas id="groupPerformanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Job Completion Status -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Status Penyelesaian Job</h3>
            <div class="chart-container">
                <canvas id="jobCompletionChart"></canvas>
            </div>
        </div>

        <!-- Prediction Accuracy -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Akurasi Prediksi</h3>
            <div class="chart-container">
                <canvas id="predictionAccuracyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <!-- Performance Overview -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Performa</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Rata-rata Waktu Penyelesaian</span>
                    <span class="text-sm font-medium" x-text="stats.avg_waktu_penyelesaian + ' hari'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Kelompok Terbaik</span>
                    <span class="text-sm font-medium" x-text="stats.best_performing_group"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completion Rate</span>
                    <span class="text-sm font-medium" x-text="stats.system_health?.completion_rate + '%'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Prediksi</span>
                    <span class="text-sm font-medium" x-text="stats.total_prediksi"></span>
                </div>
            </div>
        </div>

        <!-- Latest Prediction -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prediksi Terbaru</h3>
            <div x-show="stats.latest_prediction" class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Kelompok</span>
                    <span class="text-sm font-medium" x-text="stats.latest_prediction?.kelompok"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Jenis</span>
                    <span class="text-sm font-medium" x-text="stats.latest_prediction?.jenis"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Bulan</span>
                    <span class="text-sm font-medium" x-text="stats.latest_prediction?.bulan"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Prediksi Waktu</span>
                    <span class="text-sm font-medium" x-text="stats.latest_prediction?.prediksi_waktu + ' hari'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Akurasi</span>
                    <span class="text-sm font-medium" x-text="stats.latest_prediction?.akurasi + '%'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Dibuat</span>
                    <span class="text-sm font-medium" x-text="stats.latest_prediction?.created_at"></span>
                </div>
            </div>
            <div x-show="!stats.latest_prediction" class="text-center py-4">
                <i data-lucide="trending-up" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                <p class="text-sm text-gray-500">Belum ada prediksi</p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                <template x-for="activity in stats.recent_activities" :key="activity.time">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div :class="{
                                'bg-blue-100 text-blue-600': activity.type === 'laporan',
                                'bg-purple-100 text-purple-600': activity.type === 'prediksi'
                            }" class="w-6 h-6 rounded-full flex items-center justify-center">
                                <i :data-lucide="activity.type === 'laporan' ? 'file-text' : 'trending-up'" class="w-3 h-3"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900" x-text="activity.message"></p>
                            <p class="text-xs text-gray-500" x-text="activity.time"></p>
                        </div>
                    </div>
                </template>
                
                <div x-show="!stats.recent_activities?.length" class="text-center py-4">
                    <i data-lucide="activity" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 sm:mt-8 bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('atasan.manajemen') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i data-lucide="users" class="w-5 h-5 text-blue-600 mr-3"></i>
                <span class="text-sm font-medium text-blue-900">Kelola Kelompok</span>
            </a>
            
            <a href="{{ route('atasan.pemantauan-laporan') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i data-lucide="file-text" class="w-5 h-5 text-green-600 mr-3"></i>
                <span class="text-sm font-medium text-green-900">Pantau Laporan</span>
            </a>
            
            <a href="{{ route('atasan.statistik-prediksi') }}" 
               class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <i data-lucide="trending-up" class="w-5 h-5 text-purple-600 mr-3"></i>
                <span class="text-sm font-medium text-purple-900">Lihat Prediksi</span>
            </a>
            
            <a href="{{ route('atasan.settings') }}" 
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
    Alpine.data('adminDashboard', () => ({
        stats: @json($stats ?? []),
        chartData: @json($chartData ?? []),
        systemHealth: {},
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
            // Data sudah di-load dari controller, tidak perlu fetch lagi
            console.log('Stats loaded from controller:', this.stats);
            this.systemHealth = this.stats.system_health || { status: 'excellent', completion_rate: 100 };
        },
        
        async loadChartData() {
            try {
                console.log('Chart data loaded from controller:', this.chartData);
                this.initializeCharts(this.chartData);
            } catch (error) {
                console.error('Error loading chart data:', error);
            }
        },
        
        initializeCharts(data) {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            };
            
            // Monthly Reports Chart
            const monthlyCtx = document.getElementById('monthlyReportsChart');
            if (monthlyCtx) {
                this.charts.monthly = new Chart(monthlyCtx, {
                    type: 'line',
                    data: data.monthly_reports,
                    options: {
                        ...chartOptions,
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }
            
            // Group Performance Chart
            const groupCtx = document.getElementById('groupPerformanceChart');
            if (groupCtx) {
                this.charts.group = new Chart(groupCtx, {
                    type: 'bar',
                    data: data.group_performance,
                    options: {
                        ...chartOptions,
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }
            
            // Job Completion Chart
            const jobCtx = document.getElementById('jobCompletionChart');
            if (jobCtx) {
                this.charts.job = new Chart(jobCtx, {
                    type: 'doughnut',
                    data: data.job_completion,
                    options: {
                        ...chartOptions,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 10,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Prediction Accuracy Chart
            const predCtx = document.getElementById('predictionAccuracyChart');
            if (predCtx) {
                this.charts.prediction = new Chart(predCtx, {
                    type: 'line',
                    data: data.prediction_accuracy,
                    options: {
                        ...chartOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            }
            
            // Handle window resize for charts
            window.addEventListener('resize', () => {
                Object.values(this.charts).forEach(chart => {
                    if (chart) {
                        chart.resize();
                    }
                });
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
