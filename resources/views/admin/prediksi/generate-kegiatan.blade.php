@extends('layouts.dashboard')

@section('title', 'Generate Prediksi Kegiatan')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="generatePrediksiKegiatan()">
    <!-- Header -->
    <div class="mb-6 lg:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    🔮 Generate Prediksi Kegiatan
                </h1>
                <p class="text-gray-600 mt-2 text-sm sm:text-base">
                    Prediksi waktu penyelesaian kegiatan untuk hari besok menggunakan Triple Exponential Smoothing
                </p>
            </div>
        </div>
    </div>

    <!-- Form Generate Prediksi -->
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-xl shadow-xl p-6 mb-6 border border-blue-100">
        <div class="flex items-center mb-6">
            <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md mr-4">
                <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Form Generate Prediksi</h2>
        </div>
        <form @submit.prevent="generatePrediksi()" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="users" class="w-4 h-4 mr-2 text-blue-600"></i>
                        Kelompok <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <select x-model="form.kelompok_id" 
                                id="kelompok_id"
                                required
                                class="w-full px-4 py-3 pl-10 rounded-lg border-2 border-gray-300 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm hover:border-blue-400">
                            <option value="">Pilih Kelompok</option>
                            @foreach($kelompoksFormatted as $kelompok)
                                <option value="{{ $kelompok['id'] }}">{{ $kelompok['label'] }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="activity" class="w-4 h-4 mr-2 text-blue-600"></i>
                        Jenis Kegiatan <span class="text-gray-500 font-normal ml-1">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <select x-model="form.jenis_kegiatan"
                                class="w-full px-4 py-3 pl-10 rounded-lg border-2 border-gray-300 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm hover:border-blue-400">
                            <option value="all">Semua Kegiatan</option>
                            @foreach($jenisKegiatan as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 flex items-center">
                        <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                        Kosongkan atau pilih "Semua Kegiatan" untuk prediksi semua jenis
                    </p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="loading"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <span x-show="!loading">🎯 Generate Prediksi</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Alert Message -->
    <div x-show="message" 
         x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="mb-6 border rounded-lg px-4 py-3"
         style="display: none;">
        <div class="flex items-center">
            <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5 mr-3"></i>
            <p class="text-sm font-medium" x-text="message"></p>
        </div>
    </div>

    <!-- Chart Section -->
    <div x-show="chartData && showResults" 
         x-transition
         id="result-section"
         class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200"
         style="display: none;">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Grafik Perbandingan Prediksi Kegiatan</h2>
        <div class="relative h-96">
            <canvas id="prediksiChart"></canvas>
        </div>
    </div>

    <!-- Table Section -->
    <div x-show="tableData && tableData.length > 0 && showResults" 
         x-transition
         id="result-section-table"
         class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200"
         style="display: none;">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 border-b border-blue-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg mr-3">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Hasil Prediksi Kegiatan</h2>
                        <p class="text-sm text-blue-100 mt-0.5">
                            Kelompok: <span id="kelompok-label" x-text="kelompokName || 'Kelompok'" class="font-semibold"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 bg-gradient-to-br from-gray-50 to-white">
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table id="prediksiTable" class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Jenis Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Prediksi (Jam/Menit)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">Tanggal Prediksi (Besok)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200">MAPE</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu Generate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="prediksiTableBody">
                        <!-- Data akan diisi oleh DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('generatePrediksiKegiatan', () => ({
        form: {
            kelompok_id: '',
            jenis_kegiatan: 'all',
        },
        loading: false,
        message: '',
        messageType: 'success',
        chartData: null,
        tableData: null,
        kelompokName: '',
        chart: null,
        dataTable: null,
        showResults: false,

        formatJamMenit(decimalHours) {
            if (decimalHours === undefined || decimalHours === null || decimalHours === 0) return '0 menit';
            const hours = Math.floor(decimalHours);
            const minutes = Math.round((decimalHours - hours) * 60);
            
            if (hours < 1) {
                return `${minutes} menit`;
            } else {
                if (minutes === 0) {
                    return `${hours} jam`;
                } else {
                    return `${hours} jam ${minutes} menit`;
                }
            }
        },

        async generatePrediksi() {
            this.loading = true;
            this.message = '';
            this.chartData = null;
            this.tableData = null;
            this.showResults = false;

            try {
                const response = await fetch('{{ route("admin.prediksi.generate-kegiatan.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (result.success) {
                    this.message = result.message;
                    this.messageType = 'success';
                    this.chartData = result.chart;
                    this.tableData = result.table;
                    this.kelompokName = result.kelompok;
                    this.showResults = true;

                    // Wait for DOM update
                    await this.$nextTick();

                    // Initialize chart
                    setTimeout(() => {
                        this.initChart();
                    }, 200);

                    // Initialize DataTable
                    setTimeout(() => {
                        this.initDataTable();
                    }, 300);
                } else {
                    this.message = result.message || 'Gagal melakukan prediksi';
                    this.messageType = 'error';
                    this.showResults = false;
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            }
        },

        async loadPrediksiByKelompok(kelompokId) {
            if (!kelompokId) {
                this.showResults = false;
                this.chartData = null;
                this.tableData = null;
                return;
            }

            this.loading = true;
            this.message = '';

            try {
                const response = await fetch(`{{ route('admin.prediksi.getPrediksiKegiatanByKelompok') }}?kelompok_id=${kelompokId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.message = result.message || 'Data prediksi berhasil dimuat';
                    this.messageType = 'success';
                    this.chartData = result.chart;
                    this.tableData = result.table;
                    this.kelompokName = result.kelompok;
                    this.showResults = true;

                    // Wait for DOM update
                    await this.$nextTick();

                    // Initialize chart
                    setTimeout(() => {
                        this.initChart();
                    }, 200);

                    // Initialize DataTable
                    setTimeout(() => {
                        this.initDataTable();
                    }, 300);
                } else {
                    this.message = result.message || 'Tidak ada data prediksi untuk kelompok ini';
                    this.messageType = 'info';
                    this.showResults = false;
                }
            } catch (error) {
                this.message = 'Error: ' + error.message;
                this.messageType = 'error';
                this.showResults = false;
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            }
        },

        initChart() {
            const ctx = document.getElementById('prediksiChart');
            if (!ctx || !this.chartData) return;

            // Destroy existing chart
            if (this.chart) {
                this.chart.destroy();
            }

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: this.chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Perbandingan Prediksi Waktu Penyelesaian per Jenis Kegiatan',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    return 'Prediksi: ' + this.formatJamMenit(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Waktu (Jam)'
                            },
                            ticks: {
                                callback: (value) => {
                                    return value + ' jam';
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Jenis Kegiatan'
                            }
                        }
                    }
                }
            });
        },

        initDataTable() {
            // Destroy existing DataTable
            if ($.fn.DataTable.isDataTable('#prediksiTable')) {
                $('#prediksiTable').DataTable().destroy();
            }

            if (!this.tableData || this.tableData.length === 0) return;

            // Clear and populate table body
            const tbody = document.getElementById('prediksiTableBody');
            tbody.innerHTML = '';

            this.tableData.forEach(row => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                
                // Pastikan MAPE ada dan valid
                const mapeValue = row.mape !== undefined && row.mape !== null ? parseFloat(row.mape) : 0;
                
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">${row.jenis_kegiatan}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">${this.formatJamMenit(row.prediksi_jam || 0)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${row.tanggal_prediksi}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${mapeValue < 20 ? 'bg-green-100 text-green-800' : mapeValue < 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                            ${mapeValue.toFixed(2)}%
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">${row.waktu_generate}</td>
                `;
                tbody.appendChild(tr);
            });

            // Initialize DataTable without export buttons
            this.dataTable = $('#prediksiTable').DataTable({
                dom: 'frtip',
                order: [[0, 'asc']],
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    }));
});

// Chart dan tabel hanya muncul setelah button Generate Prediksi diklik
// Tidak ada auto-load saat halaman dibuka
</script>
@endsection

