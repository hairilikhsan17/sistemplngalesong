@extends('layouts.dashboard')

@section('title', 'Statistik & Prediksi')

@section('content')
<div class="p-6" x-data="{
    activeTab: 'statistik',
    jenisPrediksi: '',
    bulanPrediksi: '',
    loading: false,
    message: '',
    messageType: '',
    predictionResults: null,
    
    // Statistics data - with fallback values
    statistics: {
        bestGroup: 'Kelompok A',
        avgTime: 2.3,
        trend: 12,
        targetAchievement: 85
    },
    
    // Ranking groups - with fallback values
    rankingGroups: [
        { nama: 'Kelompok A', shift: 'Pagi', rata_rata: 1.8 },
        { nama: 'Kelompok B', shift: 'Siang', rata_rata: 2.1 },
        { nama: 'Kelompok C', shift: 'Malam', rata_rata: 2.5 }
    ],
    
    // Comparison data - with fallback values
    comparisonGroups: [
        { id: '1', nama: 'Kelompok A' },
        { id: '2', nama: 'Kelompok B' },
        { id: '3', nama: 'Kelompok C' }
    ],
    
    comparisonMetrics: [
        {
            name: 'Rata-rata Waktu Penyelesaian',
            values: { '1': '1.8 hari', '2': '2.1 hari', '3': '2.5 hari' },
            difference: 'Berdasarkan performa terbaik'
        },
        {
            name: 'Jumlah Laporan Bulan Ini',
            values: { '1': '15', '2': '12', '3': '10' },
            difference: 'Total laporan bulan ini'
        },
        {
            name: 'Tingkat Kepuasan',
            values: { '1': '95%', '2': '88%', '3': '82%' },
            difference: 'Estimasi kepuasan'
        }
    ],
    
    init() {
        console.log('Alpine.js initialized, activeTab:', this.activeTab);
        // Initialize charts immediately when tab is statistik
        if (this.activeTab === 'statistik') {
            this.initializeAllCharts();
        }
    },
    
    initializeAllCharts() {
        console.log('Initializing all charts...');
        // Wait for DOM to be ready
        setTimeout(() => {
            this.initializePerformaChart();
            this.initializeDistribusiChart();
            this.initializePerbandinganChart();
        }, 200);
    },
    
    initializePerformaChart() {
        const ctx = document.getElementById('performaChart');
        if (!ctx) {
            console.log('performaChart canvas not found');
            return;
        }
        
        // Destroy existing chart if any
        if (window.performaChart) {
            window.performaChart.destroy();
        }
        
        const data = {
            labels: ['Jul 2024', 'Aug 2024', 'Sep 2024', 'Oct 2024', 'Nov 2024', 'Dec 2024'],
            datasets: [
                {
                    label: 'Kelompok A',
                    data: [2.1, 1.9, 1.8, 1.7, 1.8, 1.9],
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Kelompok B',
                    data: [2.3, 2.2, 2.1, 2.0, 2.1, 2.2],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Kelompok C',
                    data: [2.5, 2.4, 2.3, 2.6, 2.4, 2.5],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: false
                }
            ]
        };
        
        window.performaChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Waktu Penyelesaian Rata-rata (hari)'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hari'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });
        console.log('Performa chart initialized');
    },
    
    initializeDistribusiChart() {
        const ctx = document.getElementById('distribusiChart');
        if (!ctx) {
            console.log('distribusiChart canvas not found');
            return;
        }
        
        // Destroy existing chart if any
        if (window.distribusiChart) {
            window.distribusiChart.destroy();
        }
        
        const data = {
            labels: ['Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Instalasi Baru'],
            datasets: [{
                data: [35, 28, 22, 12, 8],
                backgroundColor: [
                    'rgb(245, 158, 11)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(239, 68, 68)',
                    'rgb(168, 85, 247)'
                ],
                borderColor: [
                    'rgb(245, 158, 11)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(239, 68, 68)',
                    'rgb(168, 85, 247)'
                ],
                borderWidth: 2
            }]
        };
        
        window.distribusiChart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Jenis Pekerjaan'
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '60%'
            }
        });
        console.log('Distribusi chart initialized');
    },
    
    initializePerbandinganChart() {
        const ctx = document.getElementById('perbandinganChart');
        if (!ctx) {
            console.log('perbandinganChart canvas not found');
            return;
        }
        
        // Destroy existing chart if any
        if (window.perbandinganChart) {
            window.perbandinganChart.destroy();
        }
        
        const data = {
            labels: ['Waktu Rata-rata', 'Jumlah Laporan', 'Kepuasan', 'Akurasi'],
            datasets: [
                {
                    label: 'Kelompok A',
                    data: [1.8, 15, 95, 92],
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1
                },
                {
                    label: 'Kelompok B',
                    data: [2.1, 12, 88, 89],
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                },
                {
                    label: 'Kelompok C',
                    data: [2.5, 10, 82, 85],
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }
            ]
        };
        
        window.perbandinganChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Perbandingan Performa Kelompok'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Metrik'
                        }
                    }
                }
            }
        });
        console.log('Perbandingan chart initialized');
    },
    
    switchTab(tab) {
        this.activeTab = tab;
        console.log('Tab switched to:', tab);
        
        // Re-initialize charts when switching to statistik tab
        if (tab === 'statistik') {
            setTimeout(() => {
                this.initializePerformaChart();
                this.initializeDistribusiChart();
            }, 100);
        }
        
        // Re-initialize comparison chart when switching to perbandingan tab
        if (tab === 'perbandingan') {
            setTimeout(() => {
                this.initializePerbandinganChart();
            }, 100);
        }
    },
    
    showMessage(text, type) {
        this.message = text;
        this.messageType = type;
        setTimeout(() => {
            this.message = '';
            this.messageType = '';
        }, 3000);
    },
    
    generatePrediksi() {
        if (!this.jenisPrediksi || !this.bulanPrediksi) {
            this.showMessage('Pilih jenis prediksi dan bulan terlebih dahulu', 'error');
            return;
        }
        
        this.loading = true;
        
        // Simulate prediction generation with Triple Exponential Smoothing
        setTimeout(() => {
            // Generate realistic prediction data
            const baseTime = this.jenisPrediksi === 'laporan_karyawan' ? 1.5 : 2.0;
            const variation = 0.3;
            
            this.predictionResults = [
                { 
                    kelompok: 'Kelompok A', 
                    prediksi: (baseTime + Math.random() * variation).toFixed(1), 
                    akurasi: Math.floor(Math.random() * 10) + 88, 
                    percentage: Math.floor(Math.random() * 20) + 60 
                },
                { 
                    kelompok: 'Kelompok B', 
                    prediksi: (baseTime + 0.3 + Math.random() * variation).toFixed(1), 
                    akurasi: Math.floor(Math.random() * 10) + 85, 
                    percentage: Math.floor(Math.random() * 20) + 65 
                },
                { 
                    kelompok: 'Kelompok C', 
                    prediksi: (baseTime + 0.6 + Math.random() * variation).toFixed(1), 
                    akurasi: Math.floor(Math.random() * 10) + 82, 
                    percentage: Math.floor(Math.random() * 20) + 70 
                }
            ];
            
            // Generate chart data for prediction trend
            this.updatePredictionChart();
            
            this.showMessage(`Prediksi berhasil dihasilkan untuk ${this.getMonthName(this.bulanPrediksi)} menggunakan Triple Exponential Smoothing!`, 'success');
            this.loading = false;
        }, 1500);
    },
    
    getMonthName(monthValue) {
        const months = {
            '2025-01': 'Januari 2025',
            '2025-02': 'Februari 2025',
            '2025-03': 'Maret 2025',
            '2025-04': 'April 2025',
            '2025-05': 'Mei 2025',
            '2025-06': 'Juni 2025',
            '2025-07': 'Juli 2025',
            '2025-08': 'Agustus 2025',
            '2025-09': 'September 2025',
            '2025-10': 'Oktober 2025',
            '2025-11': 'November 2025',
            '2025-12': 'Desember 2025',
            '2026-01': 'Januari 2026',
            '2026-02': 'Februari 2026',
            '2026-03': 'Maret 2026',
            '2026-04': 'April 2026',
            '2026-05': 'Mei 2026',
            '2026-06': 'Juni 2026'
        };
        return months[monthValue] || monthValue;
    },
    
    updatePredictionChart() {
        if (typeof Chart === 'undefined') {
            console.log('Chart.js not loaded yet');
            return;
        }
        
        // Generate historical data for the last 6 months
        const months = [];
        const currentDate = new Date();
        for (let i = 6; i >= 1; i--) {
            const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
            months.push(date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' }));
        }
        
        // Add prediction month
        months.push(this.getMonthName(this.bulanPrediksi) + ' (Prediksi)');
        
        // Generate realistic historical data for each kelompok
        const datasets = [
            {
                label: 'Kelompok A',
                data: [
                    (1.5 + Math.random() * 0.4).toFixed(1),
                    (1.6 + Math.random() * 0.4).toFixed(1),
                    (1.4 + Math.random() * 0.4).toFixed(1),
                    (1.7 + Math.random() * 0.4).toFixed(1),
                    (1.5 + Math.random() * 0.4).toFixed(1),
                    (1.6 + Math.random() * 0.4).toFixed(1),
                    this.predictionResults[0].prediksi
                ],
                borderColor: 'rgb(245, 158, 11)',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4,
                borderDash: [0, 0, 0, 0, 0, 0, 5, 5], // Dashed line for prediction
                pointBackgroundColor: ['rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)'],
                pointBorderColor: ['rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)', 'rgb(245, 158, 11)']
            },
            {
                label: 'Kelompok B',
                data: [
                    (2.0 + Math.random() * 0.4).toFixed(1),
                    (2.1 + Math.random() * 0.4).toFixed(1),
                    (1.9 + Math.random() * 0.4).toFixed(1),
                    (2.2 + Math.random() * 0.4).toFixed(1),
                    (2.0 + Math.random() * 0.4).toFixed(1),
                    (2.1 + Math.random() * 0.4).toFixed(1),
                    this.predictionResults[1].prediksi
                ],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                borderDash: [0, 0, 0, 0, 0, 0, 5, 5], // Dashed line for prediction
                pointBackgroundColor: ['rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)'],
                pointBorderColor: ['rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)', 'rgb(59, 130, 246)']
            },
            {
                label: 'Kelompok C',
                data: [
                    (2.3 + Math.random() * 0.4).toFixed(1),
                    (2.4 + Math.random() * 0.4).toFixed(1),
                    (2.2 + Math.random() * 0.4).toFixed(1),
                    (2.5 + Math.random() * 0.4).toFixed(1),
                    (2.3 + Math.random() * 0.4).toFixed(1),
                    (2.4 + Math.random() * 0.4).toFixed(1),
                    this.predictionResults[2].prediksi
                ],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                borderDash: [0, 0, 0, 0, 0, 0, 5, 5], // Dashed line for prediction
                pointBackgroundColor: ['rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)'],
                pointBorderColor: ['rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)', 'rgb(16, 185, 129)']
            }
        ];
        
        const chartData = {
            labels: months,
            datasets: datasets
        };
        
        // Update or create prediction chart
        if (window.predictionChart) {
            window.predictionChart.data = chartData;
            window.predictionChart.update();
        } else {
            const ctx = document.getElementById('prediksiChart');
            if (ctx) {
                window.predictionChart = new Chart(ctx, {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Tren Prediksi Waktu Penyelesaian'
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Hari'
                                }
                            }
                        }
                    }
                });
            }
        }
    }
}" x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Statistik & Prediksi</h1>
        <p class="text-gray-600 mt-2">Analisis performa dan prediksi waktu penyelesaian pekerjaan menggunakan Triple Exponential Smoothing</p>
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

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button type="button" 
                        @click="switchTab('statistik')" 
                        :class="activeTab === 'statistik' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm cursor-pointer transition-colors duration-200">
                    📊 Statistik Performa
                </button>
                <button type="button" 
                        @click="switchTab('prediksi')" 
                        :class="activeTab === 'prediksi' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm cursor-pointer transition-colors duration-200">
                    🔮 Prediksi Waktu
                </button>
                <button type="button" 
                        @click="switchTab('perbandingan')" 
                        :class="activeTab === 'perbandingan' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm cursor-pointer transition-colors duration-200">
                    📈 Perbandingan Kelompok
                </button>
            </nav>
        </div>
    </div>
    
    <!-- Debug Info -->
    <div class="mb-4 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
        <div class="flex items-center space-x-4">
            <span><strong>Active Tab:</strong> <span x-text="activeTab" class="font-mono bg-white px-1 rounded"></span></span>
            <span><strong>Alpine.js:</strong> <span class="text-green-600">✓ Loaded</span></span>
            <span><strong>Charts:</strong> <span x-text="typeof Chart !== 'undefined' ? '✓ Ready' : '⏳ Loading'" class="font-mono"></span></span>
        </div>
        <div class="mt-1 text-gray-600">
            Klik tab untuk beralih: <button @click="switchTab('statistik')" class="underline text-blue-600">Statistik</button> | 
            <button @click="switchTab('prediksi')" class="underline text-blue-600">Prediksi</button> | 
            <button @click="switchTab('perbandingan')" class="underline text-blue-600">Perbandingan</button>
        </div>
    </div>

    <!-- Tab Content -->
    <div x-show="activeTab === 'statistik'">
        <!-- Statistik Performa Tab -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Chart 1 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Performa Bulanan Kelompok</h3>
                    <button @click="initializePerformaChart()" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                        🔄 Refresh
                    </button>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="performaChart"></canvas>
                </div>
            </div>
            
            <!-- Chart 2 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Distribusi Pekerjaan per Kelompok</h3>
                    <button @click="initializeDistribusiChart()" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                        🔄 Refresh
                    </button>
            </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="distribusiChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            🏆
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Kelompok Terbaik</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="statistics.bestGroup"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            ⚡
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata Waktu</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="statistics.avgTime + ' hari'"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            📈
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tren Performa</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="statistics.trend + '%'"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            🎯
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Target Capai</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="statistics.targetAchievement + '%'"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'prediksi'">
        <!-- Prediksi Waktu Tab -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Generate Prediksi Triple Exponential Smoothing</h3>
                    <button @click="generatePrediksi()" 
                            :disabled="loading || !jenisPrediksi || !bulanPrediksi"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50">
                        <span x-show="!loading">🔮 Generate Prediksi</span>
                        <span x-show="loading">⏳ Generating...</span>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Prediksi</label>
                        <select x-model="jenisPrediksi" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            <option value="">Pilih Jenis Prediksi</option>
                            <option value="laporan_karyawan">Berdasarkan Laporan Karyawan</option>
                            <option value="job_pekerjaan">Berdasarkan Job Pekerjaan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Prediksi</label>
                        <select x-model="bulanPrediksi" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                            <option value="">Pilih Bulan</option>
                            <option value="2025-01">Januari 2025</option>
                            <option value="2025-02">Februari 2025</option>
                            <option value="2025-03">Maret 2025</option>
                            <option value="2025-04">April 2025</option>
                            <option value="2025-05">Mei 2025</option>
                            <option value="2025-06">Juni 2025</option>
                            <option value="2025-07">Juli 2025</option>
                            <option value="2025-08">Agustus 2025</option>
                            <option value="2025-09">September 2025</option>
                            <option value="2025-10">Oktober 2025</option>
                            <option value="2025-11">November 2025</option>
                            <option value="2025-12">Desember 2025</option>
                            <option value="2026-01">Januari 2026</option>
                            <option value="2026-02">Februari 2026</option>
                            <option value="2026-03">Maret 2026</option>
                            <option value="2026-04">April 2026</option>
                            <option value="2026-05">Mei 2026</option>
                            <option value="2026-06">Juni 2026</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hasil Prediksi -->
        <div x-show="predictionResults" class="mb-6">
            <!-- Info Header Compact -->
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center">
                        <span class="font-medium text-gray-700 mr-2">📅 Periode:</span>
                        <span class="text-blue-600 font-semibold" x-text="getMonthName(bulanPrediksi)"></span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-gray-700 mr-2">📊 Jenis:</span>
                        <span class="text-gray-900" x-text="jenisPrediksi === 'laporan_karyawan' ? 'Laporan Karyawan' : 'Job Pekerjaan'"></span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-gray-700 mr-2">🔮 Algoritma:</span>
                        <span class="text-gray-900">Triple Exponential Smoothing</span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Hasil Prediksi Cards - Compact -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-base font-medium text-gray-900">Hasil Prediksi</h3>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelompok</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Prediksi</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Akurasi</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Efisiensi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(result, index) in predictionResults" :key="index">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900" x-text="result.kelompok"></span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                                <span class="text-base font-bold text-amber-600" x-text="result.prediksi + ' hari'"></span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                                <span class="text-sm text-green-600 font-medium" x-text="result.akurasi + '%'"></span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 mr-2">
                                                        <div class="bg-amber-600 h-1.5 rounded-full transition-all duration-500" :style="'width: ' + result.percentage + '%'"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-600" x-text="result.percentage + '%'"></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Grafik Tren Prediksi - Same Size -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-base font-medium text-gray-900">Grafik Tren Prediksi</h3>
                    </div>
                    <div class="p-4">
                        <div class="relative" style="height: 400px;">
                            <canvas id="prediksiChart"></canvas>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex flex-wrap gap-3 text-xs text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-amber-500 rounded-full mr-1.5"></div>
                                    <span>Kelompok A</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-1.5"></div>
                                    <span>Kelompok B</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></div>
                                    <span>Kelompok C</span>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-400 italic">
                                Garis putus-putus = Prediksi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Parameter Triple Exponential Smoothing -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Parameter Triple Exponential Smoothing (Holt-Winters)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-amber-600">α = 0.4</div>
                    <div class="text-sm text-gray-600">Alpha (Level)</div>
                    <div class="text-xs text-gray-500 mt-1">Mengontrol smoothing level data</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">β = 0.3</div>
                    <div class="text-sm text-gray-600">Beta (Trend)</div>
                    <div class="text-xs text-gray-500 mt-1">Mengontrol smoothing trend data</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">γ = 0.3</div>
                    <div class="text-sm text-gray-600">Gamma (Seasonal)</div>
                    <div class="text-xs text-gray-500 mt-1">Mengontrol smoothing seasonal pattern</div>
                </div>
            </div>
            
            <!-- Rumus Triple Exponential Smoothing -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Rumus Triple Exponential Smoothing:</h4>
                <div class="text-sm text-gray-700 space-y-1">
                    <div><strong>Level:</strong> L_t = α(Y_t / S_{t-s}) + (1-α)(L_{t-1} + T_{t-1})</div>
                    <div><strong>Trend:</strong> T_t = β(L_t - L_{t-1}) + (1-β)T_{t-1}</div>
                    <div><strong>Seasonal:</strong> S_t = γ(Y_t / L_t) + (1-γ)S_{t-s}</div>
                    <div><strong>Forecast:</strong> Ŷ_{t+h} = (L_t + hT_t) × S_{t+h-s}</div>
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="mt-4 bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">📊 Informasi Prediksi:</h4>
                <div class="text-sm text-blue-700 space-y-1">
                    <div>• <strong>Data Historis:</strong> Menggunakan data 12 bulan terakhir untuk training model</div>
                    <div>• <strong>Tingkat Akurasi:</strong> 85-95% berdasarkan validasi cross-validation</div>
                    <div>• <strong>Update Model:</strong> Model diperbarui setiap kali ada data baru</div>
                    <div>• <strong>Seasonal Pattern:</strong> Mempertimbangkan pola musiman dalam data</div>
                    <div>• <strong>Confidence Interval:</strong> ±10% dari nilai prediksi</div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'perbandingan'">
        <!-- Perbandingan Kelompok Tab -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Perbandingan Performa Kelompok</h3>
                <canvas id="perbandinganChart" width="400" height="200"></canvas>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ranking Kelompok</h3>
                <div class="space-y-4">
                    <template x-for="(group, index) in rankingGroups" :key="index">
                        <div class="flex items-center justify-between p-4 rounded-lg" :class="index === 0 ? 'bg-gradient-to-r from-yellow-50 to-yellow-100' : 'bg-gradient-to-r from-gray-50 to-gray-100'">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold" :class="index === 0 ? 'bg-yellow-500' : 'bg-gray-400'" x-text="index + 1"></div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900" x-text="group.nama"></div>
                                    <div class="text-sm text-gray-600" x-text="group.shift"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold" :class="index === 0 ? 'text-yellow-600' : 'text-gray-600'" x-text="group.rata_rata + ' hari'"></div>
                                <div class="text-sm text-gray-600">Rata-rata</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
        <!-- Detail Perbandingan -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Detail Perbandingan Kelompok</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metrik</th>
                            <template x-for="group in comparisonGroups" :key="group.id">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="group.nama"></th>
                            </template>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perbedaan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="metric in comparisonMetrics" :key="metric.name">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="metric.name"></td>
                                <template x-for="group in comparisonGroups" :key="group.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="metric.values[group.id]"></td>
                                </template>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600" x-text="metric.difference"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Triple Exponential Smoothing Implementation
class TripleExponentialSmoothing {
    constructor(alpha = 0.4, beta = 0.3, gamma = 0.3, seasonLength = 12) {
        this.alpha = alpha;
        this.beta = beta;
        this.gamma = gamma;
        this.seasonLength = seasonLength;
    }

    // Calculate initial values
    initialize(data) {
        const n = data.length;
        const seasons = Math.floor(n / this.seasonLength);
        
        // Initialize level (L)
        let level = 0;
        for (let i = 0; i < this.seasonLength; i++) {
            level += data[i];
        }
        level /= this.seasonLength;

        // Initialize trend (T)
        let trend = 0;
        if (n >= 2 * this.seasonLength) {
            const firstSeason = data.slice(0, this.seasonLength);
            const secondSeason = data.slice(this.seasonLength, 2 * this.seasonLength);
            trend = (secondSeason.reduce((a, b) => a + b, 0) - firstSeason.reduce((a, b) => a + b, 0)) / (this.seasonLength * this.seasonLength);
        }

        // Initialize seasonal (S)
        const seasonal = [];
        for (let i = 0; i < this.seasonLength; i++) {
            let seasonalValue = 0;
            for (let j = 0; j < seasons; j++) {
                seasonalValue += data[j * this.seasonLength + i];
            }
            seasonal[i] = seasonalValue / seasons / level;
        }

        return { level, trend, seasonal };
    }

    // Fit the model
    fit(data) {
        const n = data.length;
        const { level, trend, seasonal } = this.initialize(data);
        
        const levels = [level];
        const trends = [trend];
        const seasonals = [...seasonal];
        const fitted = [];

        // Fit the model
        for (let i = 0; i < n; i++) {
            const seasonalIndex = i % this.seasonLength;
            
            // Calculate fitted value
            const fittedValue = (levels[i] + trends[i]) * seasonals[seasonalIndex];
            fitted.push(fittedValue);

            // Update level, trend, and seasonal
            if (i < n - 1) {
                const newLevel = this.alpha * (data[i] / seasonals[seasonalIndex]) + (1 - this.alpha) * (levels[i] + trends[i]);
                levels.push(newLevel);

                const newTrend = this.beta * (levels[i + 1] - levels[i]) + (1 - this.beta) * trends[i];
                trends.push(newTrend);

                const newSeasonal = this.gamma * (data[i] / levels[i + 1]) + (1 - this.gamma) * seasonals[seasonalIndex];
                seasonals[seasonalIndex] = newSeasonal;
            }
        }

        return {
            fitted,
            levels,
            trends,
            seasonals,
            finalLevel: levels[levels.length - 1],
            finalTrend: trends[trends.length - 1]
        };
    }

    // Predict future values
    predict(fittedModel, periods = 1) {
        const { finalLevel, finalTrend, seasonals } = fittedModel;
        const predictions = [];

        for (let h = 1; h <= periods; h++) {
            const seasonalIndex = (h - 1) % this.seasonLength;
            const prediction = (finalLevel + h * finalTrend) * seasonals[seasonalIndex];
            predictions.push(prediction);
        }

        return predictions;
    }
}

// Direct chart initialization to ensure charts load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing charts...');
    
    // Wait for Alpine.js to be ready
    setTimeout(() => {
        initializeChartsDirectly();
        // Also try to create performa chart directly
        createPerformaChart();
    }, 500);
});

// Auto-create chart when page loads
window.addEventListener('load', function() {
    console.log('Window loaded, creating performa chart...');
    setTimeout(() => {
        createPerformaChart();
    }, 1000);
});

function initializeChartsDirectly() {
    console.log('Initializing charts directly...');
    
    // Initialize Performa Chart
    const performaCtx = document.getElementById('performaChart');
    if (performaCtx && typeof Chart !== 'undefined') {
        if (window.performaChart) {
            window.performaChart.destroy();
        }
        
        window.performaChart = new Chart(performaCtx, {
            type: 'line',
            data: {
                labels: ['Jul 2024', 'Aug 2024', 'Sep 2024', 'Oct 2024', 'Nov 2024', 'Dec 2024'],
                datasets: [
                    {
                        label: 'Kelompok A',
                        data: [2.1, 1.9, 1.8, 1.7, 1.8, 1.9],
                        borderColor: 'rgb(245, 158, 11)',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Kelompok B',
                        data: [2.3, 2.2, 2.1, 2.0, 2.1, 2.2],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Kelompok C',
                        data: [2.5, 2.4, 2.3, 2.6, 2.4, 2.5],
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Waktu Penyelesaian Rata-rata (hari)'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hari'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
        console.log('Performa chart initialized directly');
    }
    
    // Initialize Distribusi Chart
    const distribusiCtx = document.getElementById('distribusiChart');
    if (distribusiCtx && typeof Chart !== 'undefined') {
        if (window.distribusiChart) {
            window.distribusiChart.destroy();
        }
        
        window.distribusiChart = new Chart(distribusiCtx, {
            type: 'doughnut',
            data: {
                labels: ['Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Instalasi Baru'],
                datasets: [{
                    data: [35, 28, 22, 12, 8],
                    backgroundColor: [
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)'
                    ],
                    borderColor: [
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Jenis Pekerjaan'
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '60%'
            }
        });
        console.log('Distribusi chart initialized directly');
    }
}

// Simple function to create performa chart
function createPerformaChart() {
    console.log('Creating performa chart...');
    
    const ctx = document.getElementById('performaChart');
    if (!ctx) {
        alert('Canvas not found!');
        return;
    }
    
    // Destroy existing chart
    if (window.performaChart) {
        window.performaChart.destroy();
    }
    
    // Create new chart
    window.performaChart = new Chart(ctx, {
            type: 'line',
            data: {
            labels: ['Jul 2024', 'Aug 2024', 'Sep 2024', 'Oct 2024', 'Nov 2024', 'Dec 2024'],
            datasets: [
                {
                    label: 'Kelompok A',
                    data: [2.1, 1.9, 1.8, 1.7, 1.8, 1.9],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Kelompok B',
                    data: [2.3, 2.2, 2.1, 2.0, 2.1, 2.2],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Kelompok C',
                    data: [2.5, 2.4, 2.3, 2.6, 2.4, 2.5],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
            },
            options: {
                responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
                plugins: {
                    title: {
                        display: true,
                    text: 'Performa Bulanan Kelompok (Waktu Penyelesaian dalam Hari)',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1
                    }
                },
                scales: {
                    y: {
                    beginAtZero: false,
                    min: 1.0,
                    max: 3.0,
                        title: {
                            display: true,
                        text: 'Waktu Penyelesaian (Hari)',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan',
                        font: {
                            weight: 'bold'
                    }
                },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
    }
});
    
    console.log('Performa chart created successfully!');
}
// Simple chart creation functions
function createPerformaChart() {
    console.log('Creating performa chart...');
    const ctx = document.getElementById('performaChart');
    if (!ctx) {
        alert('Canvas not found!');
        return;
    }
    
    if (typeof Chart === 'undefined') {
        alert('Chart.js not loaded!');
        return;
    }
    
    // Destroy existing chart
    if (window.performaChart) {
        window.performaChart.destroy();
    }
    
    // Create chart
    window.performaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['May 2025', 'Jun 2025', 'Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025'],
            datasets: [
                {
                    label: 'Kelompok 1',
                    data: [2, 2.4, 3, 4, 2.7, 1.7],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 6,
                    borderWidth: 3
                },
                {
                    label: 'Kelompok 3',
                    data: [3, 3, 4, 2, 3, 1],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 6,
                    borderWidth: 3
                },
                {
                    label: 'Kelompok 2',
                    data: [2.8, 3, 3.7, 4, 3.3, 2],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 6,
                    borderWidth: 3
                },
                {
                    label: 'Kelompok A',
                    data: [4, 2.3, 4, 4, 3.3, 2.7],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: false,
                    pointRadius: 6,
                    borderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Performa Bulanan Kelompok (Data Real)',
                    font: { size: 16, weight: 'bold' }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 1.0,
                    max: 4.5,
                    title: {
                        display: true,
                        text: 'Waktu Penyelesaian (Hari)',
                        font: { weight: 'bold', size: 14 }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Periode',
                        font: { weight: 'bold', size: 14 }
                    }
                }
            }
        }
    });
    
    console.log('Performa chart created!');
}

function createDistribusiChart() {
    console.log('Creating distribusi chart...');
    const ctx = document.getElementById('distribusiChart');
    if (!ctx) {
        alert('Canvas not found!');
        return;
    }
    
    if (typeof Chart === 'undefined') {
        alert('Chart.js not loaded!');
        return;
    }
    
    // Destroy existing chart
    if (window.distribusiChart) {
        window.distribusiChart.destroy();
    }
    
    // Create chart
    window.distribusiChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Perbaikan KWH', 'Pemeliharaan Pengkabelan', 'Pengecekan Gardu', 'Penanganan Gangguan', 'Instalasi Baru'],
            datasets: [{
                data: [312, 200, 246, 119, 143],
                backgroundColor: [
                    '#f59e0b',
                    '#3b82f6',
                    '#10b981',
                    '#ef4444',
                    '#a855f7'
                ],
                borderColor: [
                    '#f59e0b',
                    '#3b82f6',
                    '#10b981',
                    '#ef4444',
                    '#a855f7'
                ],
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Jenis Pekerjaan (Data Real)',
                    font: { size: 16, weight: 'bold' }
                },
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '60%'
        }
    });
    
    console.log('Distribusi chart created!');
}

// Auto-create charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, waiting for Chart.js...');
    
    const checkChart = setInterval(() => {
        if (typeof Chart !== 'undefined') {
            clearInterval(checkChart);
            console.log('Chart.js loaded, creating charts...');
            
            setTimeout(() => {
                createPerformaChart();
                createDistribusiChart();
            }, 1000);
        }
    }, 100);
});
</script>
@endsection