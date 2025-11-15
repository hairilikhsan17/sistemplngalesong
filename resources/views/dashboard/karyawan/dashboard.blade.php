<div class="space-y-6">
    <!-- Performance Chart for All Kelompok -->
    @if(isset($performanceData) && count($performanceData) > 0)
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">📊 Grafik Peringkat Performa Kelompok</h3>
            <p class="text-sm text-gray-500 mt-1">Perbandingan performa semua kelompok berdasarkan data asli</p>
        </div>
        <div class="p-6">
            <div class="relative" id="kelompokPerformanceChart" style="min-height: 400px;"></div>
            <div class="mt-4 text-xs text-gray-500">
                <p><strong>Keterangan:</strong> Skor dihitung berdasarkan Laporan (40%), Rata-rata Waktu Penyelesaian (30%), dan Konsistensi (30%)</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">📊 Grafik Peringkat Performa Kelompok</h3>
            <p class="text-sm text-gray-500 mt-1">Belum ada data performa</p>
        </div>
        <div class="p-6">
            <p class="text-gray-500 text-center">Data performa belum tersedia</p>
        </div>
    </div>
    @endif

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Laporan -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Laporan Terbaru</h3>
            </div>
            
            @if($recentLaporan->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($recentLaporan as $laporan)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $laporan->nama }}</p>
                            <p class="text-sm text-gray-600">{{ $laporan->alamat_tujuan }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $laporan->hari }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-8 text-center text-gray-500">
                <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                <p>Belum ada laporan</p>
            </div>
            @endif
        </div>

        <!-- Recent Jobs -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Job Terbaru</h3>
            </div>
            
            @if($recentJobs->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($recentJobs as $job)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $job->lokasi }}</p>
                            <p class="text-sm text-gray-600">{{ $job->bulan_data }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-900">{{ $job->waktu_penyelesaian }} jam</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($job->tanggal)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-8 text-center text-gray-500">
                <i data-lucide="briefcase" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                <p>Belum ada job pekerjaan</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Prediksi Section -->
    @if($prediksis->count() > 0)
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Prediksi untuk Kelompok Anda</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            @foreach($prediksis as $prediksi)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-800">
                        {{ $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'Prediksi Laporan' : 'Prediksi Job' }}
                    </h4>
                    <span class="px-2 py-1 text-xs rounded-full {{ $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $prediksi->bulan_prediksi }}
                    </span>
                </div>
                <div class="text-2xl font-bold text-blue-600 mb-1">
                    {{ $prediksi->hasil_prediksi }}
                    <span class="text-sm font-normal text-gray-600">
                        {{ $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'laporan' : 'jam' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    Dibuat: {{ $prediksi->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Minggu Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentLaporan->where('tanggal', '>=', now()->startOfWeek())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laporan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentLaporan->where('tanggal', '>=', now()->startOfMonth())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Jam Kerja</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentJobs->sum('waktu_penyelesaian') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($performanceData) && count($performanceData) > 0)
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get performance data from PHP
    const dataKelompok = @json($performanceData);
    
    // Debug: Check data
    console.log('Data Kelompok:', dataKelompok);
    console.log('Data length:', dataKelompok ? dataKelompok.length : 0);
    
    if (!dataKelompok || dataKelompok.length === 0) {
        console.warn('No performance data available');
        return;
    }
    
    // Get current user's kelompok for highlighting
    const currentKelompok = @json(isset($kelompok) && $kelompok ? $kelompok->nama_kelompok : null);
    
    // Format data for ApexCharts horizontal bar
    const chartData = dataKelompok.map(item => ({
        x: item.nama,
        y: item.skor || 0
    }));
    
    console.log('Chart Data:', chartData);
    
    // Color: highlight current kelompok
    const colors = dataKelompok.map(item => {
        return item.nama === currentKelompok ? '#22c55e' : '#3b82f6';
    });
    
    // Check if ApexCharts is loaded
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts is not loaded');
        return;
    }
    
    const chartElement = document.getElementById('kelompokPerformanceChart');
    if (!chartElement) {
        console.error('Chart element not found');
        return;
    }
    
    try {
        const options = {
            series: [{
                name: 'Skor Performa',
                data: chartData
            }],
            chart: {
                type: 'bar',
                height: 400,
                horizontal: true,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: {
                        position: 'right'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(2) + '/100';
                },
                style: {
                    fontSize: '12px',
                    colors: ['#1f2937']
                }
            },
            xaxis: {
                title: {
                    text: 'Skor Performa (0-100)'
                },
                min: 0,
                max: 100,
                labels: {
                    formatter: function(val) {
                        return val + '/100';
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Kelompok'
                }
            },
            colors: colors,
            tooltip: {
                y: {
                    formatter: function(val) {
                        return 'Skor: ' + val.toFixed(2) + '/100';
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            }
        };
        
        const chart = new ApexCharts(chartElement, options);
        chart.render();
        
        console.log('ApexCharts initialized successfully with', chartData.length, 'groups');
    } catch (error) {
        console.error('Error initializing ApexCharts:', error);
    }
});
</script>
@endif

