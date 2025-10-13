<div class="space-y-6">
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





