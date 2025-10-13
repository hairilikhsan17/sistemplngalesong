<div x-data="prediksiManager()" class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Statistik & Prediksi</h2>
        <button
            @click="showPrediksiModal = true"
            class="flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors"
        >
            <i data-lucide="plus" class="w-4 h-4"></i>
            Generate Prediksi
        </button>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Laporan Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Laporan Karyawan</h3>
            <canvas id="laporanChart" width="400" height="200"></canvas>
        </div>

        <!-- Job Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Trend Job Pekerjaan</h3>
            <canvas id="jobChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Prediksi Results -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Hasil Prediksi</h3>
        </div>
        
        @if($prediksis->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Prediksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan Prediksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Prediksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($prediksis as $prediksi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs rounded-full {{ $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $prediksi->jenis_prediksi === 'laporan_karyawan' ? 'Laporan Karyawan' : 'Job Pekerjaan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $prediksi->bulan_prediksi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="font-semibold">{{ $prediksi->hasil_prediksi }}</span>
                            {{ $prediksi->jenis_prediksi === 'laporan_karyawan' ? ' laporan' : ' jam' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $prediksi->kelompok ? $prediksi->kelompok->nama_kelompok : 'Semua Kelompok' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $prediksi->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button
                                @click="deletePrediksi('{{ $prediksi->id }}')"
                                class="text-red-600 hover:text-red-700"
                            >
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <i data-lucide="trending-up" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
            <p>Belum ada prediksi</p>
        </div>
        @endif
    </div>

    <!-- Modal Prediksi -->
    <div x-show="showPrediksiModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold mb-4">Generate Prediksi</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Prediksi</label>
                    <select x-model="prediksiForm.jenis_prediksi" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Pilih Jenis</option>
                        <option value="laporan_karyawan">Laporan Karyawan</option>
                        <option value="job_pekerjaan">Job Pekerjaan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Prediksi</label>
                    <input
                        type="text"
                        x-model="prediksiForm.bulan_prediksi"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Contoh: Februari 2025"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelompok (Opsional)</label>
                    <select x-model="prediksiForm.kelompok_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompoks as $kelompok)
                            <option value="{{ $kelompok->id }}">{{ $kelompok->nama_kelompok }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button
                    @click="showPrediksiModal = false"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Batal
                </button>
                <button
                    @click="generatePrediksi()"
                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                >
                    Generate
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function prediksiManager() {
    return {
        showPrediksiModal: false,
        prediksiForm: {
            jenis_prediksi: '',
            bulan_prediksi: '',
            kelompok_id: ''
        },

        async generatePrediksi() {
            try {
                const response = await fetch('/api/prediksi/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify(this.prediksiForm)
                });

                if (response.ok) {
                    showAlert('Prediksi berhasil dibuat', 'success');
                    location.reload();
                } else {
                    showAlert('Gagal membuat prediksi', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan', 'error');
            }
        },

        async deletePrediksi(id) {
            if (!confirmDelete('Yakin ingin menghapus prediksi ini?')) return;

            try {
                const response = await fetch(`/api/prediksi/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken
                    }
                });

                if (response.ok) {
                    showAlert('Prediksi berhasil dihapus', 'success');
                    location.reload();
                } else {
                    showAlert('Gagal menghapus prediksi', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan', 'error');
            }
        }
    };
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    // Laporan Chart
    const laporanCtx = document.getElementById('laporanChart');
    if (laporanCtx) {
        new Chart(laporanCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1
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

    // Job Chart
    const jobCtx = document.getElementById('jobChart');
    if (jobCtx) {
        new Chart(jobCtx, {
            type: 'bar',
            data: {
                labels: ['Perbaikan KWH', 'Pemeliharaan', 'Pengecekan Gardu', 'Penanganan Gangguan'],
                datasets: [{
                    label: 'Jumlah Job',
                    data: [8, 12, 6, 4],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
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
});
</script>





