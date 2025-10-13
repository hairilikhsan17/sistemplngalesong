<div x-data="laporanForm()" class="max-w-4xl">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Input Laporan Karyawan</h2>

    <form @submit.prevent="submitLaporan" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                <select
                    x-model="formData.hari"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                    <option value="">Pilih Hari</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                <input
                    type="text"
                    x-model="formData.nama"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Nama karyawan"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Instansi</label>
                <input
                    type="text"
                    x-model="formData.instansi"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                <input
                    type="text"
                    x-model="formData.jabatan"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Jabatan/Posisi"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat/Tujuan</label>
                <input
                    type="text"
                    x-model="formData.alamat_tujuan"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Lokasi pekerjaan"
                    required
                >
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dokumentasi</label>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Upload File
                    <input
                        type="file"
                        @change="handleFileUpload"
                        class="hidden"
                        accept="image/*"
                    >
                </label>
                <span x-show="formData.dokumentasi" x-text="formData.dokumentasi" class="text-sm text-gray-600"></span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Upload foto dokumentasi pekerjaan</p>
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
                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Laporan'"></span>
            </button>
        </div>
    </form>
</div>

<script>
function laporanForm() {
    return {
        submitting: false,
        formData: {
            hari: '',
            tanggal: '',
            nama: '',
            instansi: 'PLN Galesong',
            jabatan: '',
            alamat_tujuan: '',
            dokumentasi: ''
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.formData.dokumentasi = file.name;
            }
        },

        async submitLaporan() {
            this.submitting = true;

            try {
                const response = await fetch('/api/laporan-karyawan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify(this.formData)
                });

                if (response.ok) {
                    showAlert('Laporan berhasil disimpan', 'success');
                    this.resetForm();
                } else {
                    const error = await response.json();
                    showAlert(error.error || 'Gagal menyimpan laporan', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan', 'error');
            } finally {
                this.submitting = false;
            }
        },

        resetForm() {
            this.formData = {
                hari: '',
                tanggal: '',
                nama: '',
                instansi: 'PLN Galesong',
                jabatan: '',
                alamat_tujuan: '',
                dokumentasi: ''
            };
        }
    };
}
</script>





