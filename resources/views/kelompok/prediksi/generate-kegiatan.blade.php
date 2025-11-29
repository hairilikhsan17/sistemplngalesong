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
                <div class="mt-3 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        <strong>Kelompok:</strong> {{ $kelompok->nama_kelompok }} ({{ $kelompok->shift }})
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Generate Prediksi -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Form Generate Prediksi</h2>
        <form @submit.prevent="generatePrediksi()" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kelompok
                    </label>
                    <input type="text" 
                           value="{{ $kelompok->nama_kelompok }} ({{ $kelompok->shift }})" 
                           disabled
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Prediksi menggunakan data historis dari kelompok Anda</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kegiatan <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <select x-model="form.jenis_kegiatan"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="all">Semua Kegiatan</option>
                        @foreach($jenisKegiatan as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Kosongkan atau pilih "Semua Kegiatan" untuk prediksi semua jenis</p>
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
         class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200"
         style="display: none;">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">📋 Hasil Prediksi Kegiatan - <span id="kelompok-label" x-text="kelompokName || '{{ $kelompok->nama_kelompok }}'"></span></h2>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="prediksiTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kegiatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prediksi (Jam)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Prediksi (Besok)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MAPE</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Generate</th>
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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('generatePrediksiKegiatan', () => ({
        form: {
            jenis_kegiatan: 'all',
        },
        loading: false,
        message: '',
        messageType: 'success',
        chartData: null,
        tableData: null,
        kelompokName: '{{ $kelompok->nama_kelompok }}',
        chart: null,
        dataTable: null,
        showResults: false,

        async generatePrediksi() {
            this.loading = true;
            this.message = '';
            this.chartData = null;
            this.tableData = null;
            this.showResults = false;

            try {
                const response = await fetch('{{ route("kelompok.prediksi.generate-kegiatan.post") }}', {
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

        async loadPrediksiByKelompok() {
            this.loading = true;
            this.message = '';

            try {
                const response = await fetch(`{{ route('kelompok.prediksi.getPrediksiKegiatanByKelompok') }}`, {
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
                                label: function(context) {
                                    return 'Prediksi: ' + context.parsed.y + ' jam';
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
                                callback: function(value) {
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${row.jenis_kegiatan}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(row.prediksi_jam || 0).toFixed(2)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.tanggal_prediksi}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${mapeValue < 20 ? 'text-green-600 font-semibold' : mapeValue < 40 ? 'text-yellow-600 font-semibold' : 'text-red-600 font-semibold'}">${mapeValue.toFixed(2)}%</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.waktu_generate}</td>
                `;
                tbody.appendChild(tr);
            });

            // Initialize DataTable with export buttons
            this.dataTable = $('#prediksiTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        className: 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg',
                        title: 'Hasil Prediksi Kegiatan - ' + this.kelompokName
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        className: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg',
                        title: 'Hasil Prediksi Kegiatan - ' + this.kelompokName,
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }
                ],
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

