<!-- Modal Kelompok -->
<div x-show="showKelompokModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">
            <span x-text="editingKelompok ? 'Edit Kelompok' : 'Tambah Kelompok'"></span>
        </h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelompok</label>
                <input
                    type="text"
                    x-model="formData.nama_kelompok"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    placeholder="Contoh: Kelompok 1"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                <select
                    x-model="formData.shift"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                >
                    <option value="Shift 1">Shift 1 (08.00 - 19.00)</option>
                    <option value="Shift 2">Shift 2 (19.00 - 07.00)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password Login Kelompok</label>
                <input
                    type="password"
                    x-model="formData.password"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    placeholder="Masukkan password untuk login kelompok"
                    :required="!editingKelompok"
                >
                <p class="mt-1 text-xs text-gray-500" x-show="!editingKelompok">
                    Password ini akan digunakan untuk login kelompok<br>
                    <strong>Username:</strong> <span x-text="formData.nama_kelompok ? formData.nama_kelompok.toLowerCase().replace(/\s+/g, '') : ''"></span>
                </p>
                <p class="mt-1 text-xs text-gray-500" x-show="editingKelompok">
                    Kosongkan jika tidak ingin mengubah password
                </p>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button
                @click="showKelompokModal = false"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
                Batal
            </button>
            <button
                @click="saveKelompok()"
                class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors"
            >
                Simpan
            </button>
        </div>
    </div>
</div>



