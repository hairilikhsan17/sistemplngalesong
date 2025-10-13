<!-- Modal Karyawan -->
<div x-show="showKaryawanModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">Tambah Karyawan</h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan</label>
                <input
                    type="text"
                    x-model="karyawanForm.nama"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Masukkan nama karyawan"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelompok</label>
                <select
                    x-model="karyawanForm.kelompok_id"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Pilih Kelompok</option>
                    @foreach($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id }}">{{ $kelompok->nama_kelompok }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button
                @click="showKaryawanModal = false"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Batal
            </button>
            <button
                @click="saveKaryawan()"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                Simpan
            </button>
        </div>
    </div>
</div>





