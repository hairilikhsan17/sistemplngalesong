@extends('layouts.dashboard')

@section('title', 'Prediksi Waktu Penyelesaian')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="prediksiPage()">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    🔮 Prediksi {{ $tipe === 'laporan' ? 'Laporan Karyawan' : 'Job Pekerjaan' }}
                </h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">
                    Prediksi {{ $tipe === 'laporan' ? 'jumlah laporan' : 'waktu penyelesaian' }} menggunakan metode Holt-Winters
                </p>
            </div>
        </div>
    </div>

    <!-- Form Prediksi -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Form Prediksi</h2>
        <form @submit.prevent="generatePrediksi()" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Data</label>
                    <select x-model="form.tipe" @change="changeTipe()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="laporan">Laporan Karyawan</option>
                        <option value="job">Job Pekerjaan</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih jenis data yang akan diprediksi</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelompok</label>
                    <select x-model="form.kelompok" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kelompok</option>
                        <option value="all">Semua Kelompok</option>
                        <template x-for="kelompok in kelompokList" :key="kelompok">
                            <option :value="kelompok" x-text="kelompok"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih kelompok atau "Semua Kelompok" untuk prediksi agregat</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Target</label>
                    <input type="month" x-model="form.bulan_target" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alpha (α)</label>
                    <input type="number" x-model="form.alpha" step="0.1" min="0" max="1" required
                           placeholder="0.2"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Level smoothing (0-1)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Beta (β)</label>
                    <input type="number" x-model="form.beta" step="0.1" min="0" max="1" required
                           placeholder="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Trend smoothing (0-1)</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gamma (γ)</label>
                    <input type="number" x-model="form.gamma" step="0.1" min="0" max="1" required
                           placeholder="0.1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Seasonal smoothing (0-1)</p>
                </div>
                <div class="flex items-end">
                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed min-h-[44px]">
                        <span x-show="!loading">🎯 Generate Prediksi</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Alert Message -->
    <div x-show="message" 
         x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="mb-6 border rounded-lg px-4 py-3">
        <div class="flex items-center">
            <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mr-3"></i>
            <p class="text-sm font-medium" x-text="message"></p>
        </div>
    </div>

    <!-- Hasil Prediksi -->
    <div x-show="hasilPrediksi" class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Hasil Prediksi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600">📅 Bulan yang Diprediksi</p>
                <p class="text-2xl font-bold text-gray-900" x-text="hasilPrediksi.bulan"></p>
            </div>
            <div class="bg-indigo-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600">📋 Jenis Data</p>
                <p class="text-2xl font-bold text-gray-900" x-text="hasilPrediksi.tipe_label || 'N/A'"></p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600">👥 Kelompok</p>
                <p class="text-2xl font-bold text-gray-900" x-text="hasilPrediksi.kelompok || 'N/A'"></p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600">📈 Hasil Prediksi</p>
                <p class="text-2xl font-bold text-gray-900" 
                   x-text="hasilPrediksi.hasil_label ? hasilPrediksi.hasil_prediksi + ' ' + hasilPrediksi.hasil_label : hasilPrediksi.hasil_prediksi + ' hari'"></p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-600">🎯 Akurasi Model (MAPE)</p>
                <p class="text-2xl font-bold text-gray-900" x-text="hasilPrediksi.akurasi + '%'"></p>
            </div>
        </div>
        <div class="flex gap-4">
            <button @click="exportData('pdf')" 
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                Unduh PDF
            </button>
            <button @click="exportData('excel')" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 inline mr-2"></i>
                Unduh Excel
            </button>
            <button @click="resetData()" 
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                Reset Data
            </button>
        </div>
    </div>

    <!-- Chart Prediksi -->
    <div x-show="hasilPrediksi" class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Grafik Prediksi</h2>
        <div class="relative h-64 sm:h-96">
            <canvas id="chartPrediksi"></canvas>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModal" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="showDetailModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Detail Prediksi</h2>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                
                <div x-show="detailPrediksi" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600">Bulan yang Diprediksi</p>
                            <p class="text-xl font-bold text-gray-900" x-text="detailPrediksi?.bulan || 'N/A'"></p>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600">Jenis Data</p>
                            <p class="text-xl font-bold text-gray-900" x-text="detailPrediksi?.tipe_label || 'N/A'"></p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600">Kelompok</p>
                            <p class="text-xl font-bold text-gray-900" x-text="detailPrediksi?.kelompok || 'N/A'"></p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600">Hasil Prediksi</p>
                            <p class="text-xl font-bold text-gray-900" 
                               x-text="detailPrediksi?.hasil_prediksi ? (detailPrediksi.hasil_prediksi + ' ' + (detailPrediksi.hasil_label || 'hari')) : 'N/A'"></p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600">Akurasi Model (MAPE)</p>
                            <p class="text-xl font-bold text-gray-900" x-text="detailPrediksi?.akurasi ? (detailPrediksi.akurasi + '%') : 'N/A'"></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-600">Metode</p>
                            <p class="text-xl font-bold text-gray-900" x-text="detailPrediksi?.metode || 'N/A'"></p>
                        </div>
                    </div>
                    
                    <div x-show="detailPrediksi?.params" class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Parameter Algoritma</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Alpha (α)</p>
                                <p class="text-lg font-bold text-gray-900" x-text="detailPrediksi?.params?.alpha || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Beta (β)</p>
                                <p class="text-lg font-bold text-gray-900" x-text="detailPrediksi?.params?.beta || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Gamma (γ)</p>
                                <p class="text-lg font-bold text-gray-900" x-text="detailPrediksi?.params?.gamma || 'N/A'"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">Tanggal Dibuat</p>
                        <p class="text-lg font-medium text-gray-900" x-text="detailPrediksi?.created_at || 'N/A'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Predictions Table -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Prediksi Terakhir</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Data</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Prediksi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akurasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-if="latestPredictions.length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                Belum ada data prediksi
                            </td>
                        </tr>
                    </template>
                    <template x-for="prediksi in latestPredictions" :key="prediksi.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" x-text="prediksi.bulan"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-medium" x-text="prediksi.tipe_label"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium" x-text="prediksi.kelompok"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">
                                <span x-text="parseFloat(prediksi.hasil_prediksi).toFixed(2) + ' ' + prediksi.hasil_label"></span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="parseFloat(prediksi.akurasi).toFixed(2) + '%'"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="prediksi.metode"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="prediksi.created_at"></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <button @click="viewDetail(prediksi.id)" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                            title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="deletePrediksi(prediksi.id)" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                            title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function prediksiPage() {
    return {
        form: {
            tipe: '{{ $tipe }}',
            kelompok: '',
            bulan_target: '',
            alpha: 0.2,
            beta: 0.1,
            gamma: 0.1,
        },
        tipe: '{{ $tipe }}',
        kelompokList: @json($kelompokList ?? []),
        loading: false,
        message: '',
        messageType: 'success',
        hasilPrediksi: null,
        chartPrediksi: null,
        showDetailModal: false,
        detailPrediksi: null,
        latestPredictions: @json($formattedPredictions ?? []),

        init() {
            this.$nextTick(() => {
                lucide.createIcons();
            });
        },

        async loadLatestPredictions() {
            try {
                const url = `/admin/prediksi/latest?tipe=${this.tipe}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.latestPredictions = result.data;
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                }
            } catch (error) {
                console.error('Error loading latest predictions:', error);
            }
        },

        changeTipe() {
            // Redirect ke halaman dengan tipe yang dipilih
            window.location.href = `{{ route('admin.prediksi.index') }}?tipe=${this.form.tipe}`;
        },

        async generatePrediksi() {
            this.loading = true;
            this.message = '';
            this.hasilPrediksi = null;

            try {
                const response = await fetch('{{ route("admin.prediksi.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (result.success) {
                    this.message = result.message;
                    this.messageType = 'success';
                    this.tipe = result.data.tipe || this.form.tipe;
                    this.hasilPrediksi = {
                        bulan: result.data.bulan,
                        kelompok: result.data.kelompok || 'N/A',
                        tipe_label: result.data.tipe_label || 'N/A',
                        hasil_prediksi: result.data.hasil_prediksi,
                        hasil_label: result.data.hasil_label || 'hari',
                        akurasi: result.data.akurasi
                    };
                    // Update chart - ensure it's rendered after DOM update
                    // Use a small delay to ensure the chart container is fully visible
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.updateChart(result.data);
                        }, 100);
                    });
                    // Refresh latest predictions table
                    await this.loadLatestPredictions();
                } else {
                    this.message = result.message;
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    lucide.createIcons();
                });
            }
        },

        updateChart(data) {
            // Wait for chart container to be visible
            const chartCanvas = document.getElementById('chartPrediksi');
            if (!chartCanvas) {
                console.error('Chart canvas not found');
                return;
            }

            if (this.chartPrediksi) {
                this.chartPrediksi.destroy();
            }

            const ctx = chartCanvas.getContext('2d');
            
            // Prepare data
            const historisData = data.historis.map((val, idx) => {
                return { x: data.labels[idx], y: val };
            });
            const prediksiData = data.prediksi.map((val, idx) => {
                return val !== null ? { x: data.labels[idx], y: val } : null;
            }).filter(v => v !== null);

            this.chartPrediksi = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: (data.tipe === 'laporan' ? 'Data Historis (Jumlah Laporan)' : 'Data Historis (Waktu Penyelesaian)'),
                            data: data.historis,
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.2
                        },
                        {
                            label: (data.tipe === 'laporan' ? 'Prediksi (Jumlah Laporan)' : 'Prediksi (Waktu Penyelesaian)'),
                            data: data.prediksi,
                            borderColor: 'rgba(239, 68, 68, 1)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: data.tipe === 'laporan' ? 'Jumlah Laporan' : 'Waktu Penyelesaian (Hari)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        },

        async exportData(format) {
            if (!this.hasilPrediksi) return;
            
            // Construct URL manually since format is dynamic
            const baseUrl = '{{ url("/admin/prediksi/export") }}';
            window.open(`${baseUrl}/${format}`, '_blank');
        },

        async resetData() {
            if (!confirm('Apakah Anda yakin ingin menghapus data prediksi?')) return;

            try {
                const response = await fetch('{{ route("admin.prediksi.reset") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.message = result.message;
                    this.messageType = 'success';
                    this.hasilPrediksi = null;
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    this.message = result.message;
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
            }

            this.$nextTick(() => {
                lucide.createIcons();
            });
        },

        async viewDetail(id) {
            try {
                const url = `/admin/prediksi/${id}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.detailPrediksi = result.data;
                    this.showDetailModal = true;
                    this.$nextTick(() => {
                        lucide.createIcons();
                        // Generate chart for detail if needed
                        if (result.data.chart_data) {
                            this.updateDetailChart(result.data.chart_data);
                        }
                    });
                } else {
                    this.message = result.message || 'Gagal memuat detail prediksi';
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
            }
        },

        async deletePrediksi(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus prediksi ini?')) return;

            try {
                const url = `/admin/prediksi/${id}`;
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.message = result.message;
                    this.messageType = 'success';
                    // Remove item from table without reload
                    this.latestPredictions = this.latestPredictions.filter(p => p.id !== id);
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                } else {
                    this.message = result.message || 'Gagal menghapus prediksi';
                    this.messageType = 'error';
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
            }

            this.$nextTick(() => {
                lucide.createIcons();
            });
        },

        updateDetailChart(data) {
            // This can be used to show chart in detail modal if needed
            // For now, we'll just display the data in the modal
        }
    }
}
</script>
@endsection

