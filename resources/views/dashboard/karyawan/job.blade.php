<div x-data="jobForm()" class="max-w-4xl">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Input Job Pekerjaan</h2>

    <form @submit.prevent="submitJob" class="space-y-6">
        <!-- Job Types -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Perbaikan KWH</label>
                <input
                    type="number"
                    x-model="formData.perbaikan_kwh"
                    min="0"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                    required
                >
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pemeliharaan Pengkabelan</label>
                <input
                    type="number"
                    x-model="formData.pemeliharaan_pengkabelan"
                    min="0"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                    required
                >
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pengecekan Gardu</label>
                <input
                    type="number"
                    x-model="formData.pengecekan_gardu"
                    min="0"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                    required
                >
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Penanganan Gangguan</label>
                <input
                    type="number"
                    x-model="formData.penanganan_gangguan"
                    min="0"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                    required
                >
            </div>
        </div>

        <!-- Location and Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                <input
                    type="text"
                    x-model="formData.lokasi"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Lokasi pekerjaan"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input
                    type="date"
                    x-model="formData.tanggal"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Data</label>
                <input
                    type="text"
                    x-model="formData.bulan_data"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Contoh: Januari 2025"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Penyelesaian (Jam)</label>
                <input
                    type="number"
                    x-model="formData.waktu_penyelesaian"
                    min="0"
                    step="0.5"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0"
                    required
                >
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Ringkasan Job</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-blue-600 font-medium">Perbaikan KWH:</span>
                    <span x-text="formData.perbaikan_kwh || 0" class="ml-1"></span>
                </div>
                <div>
                    <span class="text-blue-600 font-medium">Pemeliharaan:</span>
                    <span x-text="formData.pemeliharaan_pengkabelan || 0" class="ml-1"></span>
                </div>
                <div>
                    <span class="text-blue-600 font-medium">Pengecekan Gardu:</span>
                    <span x-text="formData.pengecekan_gardu || 0" class="ml-1"></span>
                </div>
                <div>
                    <span class="text-blue-600 font-medium">Penanganan Gangguan:</span>
                    <span x-text="formData.penanganan_gangguan || 0" class="ml-1"></span>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-blue-600 font-medium">Total Waktu:</span>
                <span x-text="formData.waktu_penyelesaian || 0" class="ml-1"></span> jam
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <button
                type="button"
                @click="resetForm"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
                Reset
            </button>
            <button
                type="submit"
                :disabled="submitting"
                class="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i data-lucide="save" class="w-4 h-4"></i>
                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Job'"></span>
            </button>
        </div>
    </form>
</div>

<script>
function jobForm() {
    return {
        submitting: false,
        formData: {
            perbaikan_kwh: 0,
            pemeliharaan_pengkabelan: 0,
            pengecekan_gardu: 0,
            penanganan_gangguan: 0,
            lokasi: '',
            bulan_data: '',
            tanggal: '',
            waktu_penyelesaian: 0
        },

        async submitJob() {
            this.submitting = true;

            try {
                const response = await fetch('/api/job-pekerjaan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.ok) {
                    showAlert('Job pekerjaan berhasil disimpan', 'success');
                    this.resetForm();
                } else {
                    const error = await response.json();
                    showAlert(error.error || 'Gagal menyimpan job pekerjaan', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan', 'error');
            } finally {
                this.submitting = false;
            }
        },

        resetForm() {
            this.formData = {
                perbaikan_kwh: 0,
                pemeliharaan_pengkabelan: 0,
                pengecekan_gardu: 0,
                penanganan_gangguan: 0,
                lokasi: '',
                bulan_data: '',
                tanggal: '',
                waktu_penyelesaian: 0
            };
        }
    };
}
</script>





