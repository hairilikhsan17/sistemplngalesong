@extends('layouts.dashboard')

@section('title', 'Statistik Kegiatan')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="statistikPage()">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    📊 Statistik {{ $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan' }}
                </h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">
                    Analisis statistik {{ $tipe === 'laporan' ? 'laporan karyawan' : 'job pekerjaan' }} per kelompok dan bulan
                </p>
            </div>
            <button @click="refreshData()" 
                    class="flex items-center bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base min-h-[44px]">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                <span>Refresh Data</span>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Data</label>
                <select x-model="filters.tipe" @change="changeTipe()" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="laporan">Laporan Karyawan</option>
                    <option value="job">Job Pekerjaan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kelompok</label>
                <select x-model="filters.kelompok" @change="loadData()" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua Kelompok</option>
                    <template x-for="kelompok in kelompokList" :key="kelompok">
                        <option :value="kelompok" x-text="kelompok"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Bulan</label>
                <input type="month" x-model="filters.bulan" @change="loadData()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button @click="resetFilters()" 
                        class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Info Alert (jika belum ada data) -->
    <div x-show="!loading && rekapTabel.length === 0 && !dataLoaded" 
         class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <i data-lucide="alert-circle" class="w-6 h-6 text-yellow-600 mr-3 mt-1"></i>
            <div>
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Belum Ada Data Kegiatan</h3>
                <p class="text-sm text-yellow-700 mb-3">
                    Untuk melihat statistik, Anda perlu memasukkan data kegiatan terlebih dahulu.
                </p>
                <div class="mt-4">
                    <p class="text-sm font-medium text-yellow-800 mb-2">Cara memasukkan data:</p>
                    <ol class="list-decimal list-inside text-sm text-yellow-700 space-y-1">
                        <li>Jalankan seeder: <code class="bg-yellow-100 px-2 py-1 rounded">php artisan db:seed --class=KegiatanSeeder</code></li>
                        <li>Atau masukkan data manual melalui tinker atau form input</li>
                        <li>Pastikan nama kelompok sesuai dengan yang terdaftar di sistem</li>
                    </ol>
                </div>
                <div class="mt-4">
                    <a href="{{ asset('PANDUAN_STATISTIK_DAN_PREDIKSI.md') }}" 
                       target="_blank"
                       class="text-sm text-yellow-800 underline hover:text-yellow-900">
                        📖 Baca panduan lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6" x-show="dataLoaded || rekapTabel.length > 0">
        <!-- Jumlah Kegiatan per Bulan -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4" x-text="tipe === 'laporan' ? 'Jumlah Laporan per Bulan' : 'Jumlah Job Pekerjaan per Bulan'"></h2>
            <div class="relative h-64 sm:h-80">
                <canvas id="chartJumlahKegiatan"></canvas>
            </div>
        </div>

        <!-- Rata-rata Durasi per Bulan (hanya untuk Job Pekerjaan) -->
        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6" x-show="tipe === 'job'">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Rata-rata Waktu Penyelesaian per Bulan (Hari)</h2>
            <div class="relative h-64 sm:h-80">
                <canvas id="chartRataDurasi"></canvas>
            </div>
        </div>
        <!-- Info untuk Laporan (tidak ada durasi) -->
        <div class="bg-blue-50 rounded-lg shadow-md p-4 sm:p-6" x-show="tipe === 'laporan'">
            <div class="flex items-center">
                <i data-lucide="info" class="w-6 h-6 text-blue-600 mr-3"></i>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Statistik Laporan Karyawan</h3>
                    <p class="text-sm text-blue-700">
                        Grafik menunjukkan jumlah laporan karyawan per bulan. 
                        Laporan karyawan adalah data harian, sehingga tidak memiliki durasi penyelesaian.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Tabel -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Rekap Kegiatan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi (Hari)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" x-show="!loading">
                    <template x-for="(item, index) in rekapTabel" :key="index">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="item.kelompok"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="item.tanggal_mulai"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="item.tanggal_selesai"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium" x-text="item.durasi"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div x-show="loading" class="text-center py-8">
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto text-gray-400"></i>
                <p class="mt-2 text-sm text-gray-500">Memuat data...</p>
            </div>
            <div x-show="!loading && rekapTabel.length === 0 && dataLoaded" class="text-center py-8">
                <p class="text-sm text-gray-500">Tidak ada data untuk filter yang dipilih</p>
                <p class="text-xs text-gray-400 mt-2">Coba ubah filter atau pilih periode lain</p>
            </div>
        </div>
    </div>
</div>

<script>
function statistikPage() {
    return {
        filters: {
            tipe: '{{ $tipe }}',
            kelompok: '{{ $kelompokFilter }}',
            bulan: '{{ $bulanFilter }}',
        },
        tipe: '{{ $tipe }}',
        kelompokList: @json($kelompokList ?? []),
        rekapTabel: [],
        loading: false,
        dataLoaded: false,
        chartJumlahKegiatan: null,
        chartRataDurasi: null,

        init() {
            this.loadData();
            this.$nextTick(() => {
                lucide.createIcons();
            });
        },

        changeTipe() {
            // Redirect ke halaman dengan tipe yang dipilih
            window.location.href = `{{ route('admin.statistik.index') }}?tipe=${this.filters.tipe}`;
        },

        async loadData() {
            this.loading = true;
            this.dataLoaded = false;
            try {
                const response = await fetch(`{{ route('admin.statistik.data') }}?tipe=${this.filters.tipe}&kelompok=${this.filters.kelompok}&bulan=${this.filters.bulan}`);
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    this.tipe = data.tipe || this.filters.tipe;
                    this.rekapTabel = data.rekap_tabel;
                    this.dataLoaded = true;
                    
                    // Update charts only if there's data
                    if (data.jumlah_kegiatan.data.length > 0 || data.rata_durasi.data.length > 0) {
                        this.updateCharts(data);
                    }
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.dataLoaded = false;
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    lucide.createIcons();
                });
            }
        },

        updateCharts(data) {
            const tipeLabel = data.tipe === 'laporan' ? 'Laporan' : 'Job Pekerjaan';
            
            // Update Jumlah Kegiatan Chart
            if (this.chartJumlahKegiatan) {
                this.chartJumlahKegiatan.destroy();
            }
            const ctx1 = document.getElementById('chartJumlahKegiatan').getContext('2d');
            this.chartJumlahKegiatan = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: data.jumlah_kegiatan.labels,
                    datasets: [{
                        label: `Jumlah ${tipeLabel}`,
                        data: data.jumlah_kegiatan.data,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Update Rata Durasi Chart (hanya untuk job)
            if (data.tipe === 'job') {
                if (this.chartRataDurasi) {
                    this.chartRataDurasi.destroy();
                }
                const ctx2 = document.getElementById('chartRataDurasi');
                if (ctx2) {
                    this.chartRataDurasi = new Chart(ctx2.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: data.rata_durasi.labels,
                            datasets: [{
                                label: 'Rata-rata Waktu Penyelesaian (Hari)',
                                data: data.rata_durasi.data,
                                borderColor: 'rgba(16, 185, 129, 1)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }
        },

        resetFilters() {
            this.filters.kelompok = 'all';
            this.filters.bulan = '';
            // Jangan reset tipe, biarkan sesuai yang dipilih
            this.loadData();
        },

        refreshData() {
            this.loadData();
        }
    }
}
</script>
@endsection

