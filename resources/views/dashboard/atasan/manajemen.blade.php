@extends('layouts.dashboard')

@section('title', 'Manajemen Kelompok & Karyawan')

@section('content')
<div class="p-3 sm:p-4 lg:p-6" x-data="manajemenData()">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manajemen Kelompok & Karyawan</h1>
        <p class="text-gray-600 mt-2 text-sm sm:text-base">Kelola data kelompok dan anggota karyawan</p>
    </div>

    <!-- Notification -->
    <div x-show="message" 
         x-transition
         :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
         class="mb-6 border rounded-lg px-4 py-3">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i :data-lucide="messageType === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
        </div>
    </div>


    <!-- Tabs -->
    <div class="mb-4 sm:mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4 sm:space-x-8 overflow-x-auto">
                <button @click="activeTab = 'kelompok'" 
                        :class="activeTab === 'kelompok' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm min-h-[44px] flex items-center">
                    📋 Kelompok
                </button>
                <button @click="activeTab = 'karyawan'" 
                        :class="activeTab === 'karyawan' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm min-h-[44px] flex items-center">
                    👥 Karyawan
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div x-show="activeTab === 'kelompok'">
        <!-- Kelompok Tab -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900">Data Kelompok</h3>
                    <a href="{{ route('atasan.kelompok') }}" 
                       class="bg-amber-600 hover:bg-amber-700 text-white px-3 sm:px-4 py-2 rounded-lg text-sm font-medium inline-block text-center min-h-[44px] flex items-center justify-center">
                        ➕ <span class="ml-1">Tambah Kelompok</span>
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto -mx-3 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelompok</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Username</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($kelompoks as $kelompok)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ substr($kelompok->id, 0, 8) }}...</td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $kelompok->nama_kelompok }}</td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $kelompok->shift }}</td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 hidden sm:table-cell">
                                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ strtolower(str_replace(' ', '', $kelompok->nama_kelompok)) }}</code>
                                    </td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $kelompok->karyawan->count() }}</td>
                                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                            <button @click="editKelompok('{{ $kelompok->id }}', '{{ $kelompok->nama_kelompok }}', '{{ $kelompok->shift }}')" 
                                                    class="text-amber-600 hover:text-amber-900 sm:mr-3 min-h-[32px] sm:min-h-0">Edit</button>
                                            <button @click="hapusKelompok('{{ $kelompok->id }}')" 
                                                    class="text-red-600 hover:text-red-900 min-h-[32px] sm:min-h-0">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-500 text-center">
                                        Belum ada data kelompok
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
  <!-- Karyawan Tab -->
  <div x-show="activeTab === 'karyawan'">
  <div class="bg-white rounded-lg shadow">
    <div class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 border-b border-gray-200">
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Data Karyawan</h3>
        <a href="{{ route('atasan.karyawan') }}"
           class="bg-amber-600 hover:bg-amber-700 text-white px-3 sm:px-5 py-2 rounded-lg text-sm font-medium text-center min-h-[44px] flex items-center justify-center">
          ➕ <span class="ml-1">Tambah Karyawan</span>
        </a>
      </div>
    </div>

    <div class="overflow-x-auto -mx-3 sm:mx-0">
      <div class="inline-block min-w-full align-middle">
        <div class="overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-amber-100">
              <tr>
                <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-amber-900 uppercase tracking-wider">ID</th>
                <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-amber-900 uppercase tracking-wider">Nama</th>
                <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-amber-900 uppercase tracking-wider">Kelompok</th>
                <th class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-amber-900 uppercase tracking-wider">Status</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              @forelse($karyawans as $karyawan)
              <tr class="hover:bg-amber-50 transition-colors duration-150">
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ substr($karyawan->id, 0, 8) }}...</td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $karyawan->nama }}</td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $karyawan->kelompok->nama_kelompok ?? '-' }}</td>
                <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Aktif
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="px-3 sm:px-6 py-4 text-xs sm:text-sm text-gray-500 text-center">
                  Belum ada data karyawan
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Kelompok -->
<div x-show="showKelompokModal" 
     x-transition
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape="showKelompokModal = false">
    <div class="flex items-center justify-center min-h-screen pt-4 px-3 sm:px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showKelompokModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <form @submit.prevent="isEditing ? updateKelompok() : tambahKelompok()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900" x-text="isEditing ? 'Edit Kelompok' : 'Tambah Kelompok Baru'"></h3>
                        <button type="button" 
                                @click="showKelompokModal = false; resetFormKelompok()"
                                class="text-gray-400 hover:text-gray-600 p-2 rounded-md hover:bg-gray-100 min-w-[44px] min-h-[44px] flex items-center justify-center">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelompok</label>
                            <input type="text" x-model="formKelompok.nama_kelompok" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm form-input-mobile" 
                                   placeholder="Masukkan nama kelompok" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                            <select x-model="formKelompok.shift" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm form-input-mobile" required>
                                <option value="">Pilih Shift</option>
                                <option value="Shift 1">Shift 1 (08.00 - 19.00)</option>
                                <option value="Shift 2">Shift 2 (19.00 - 07.00)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Login Kelompok</label>
                            <input type="password" x-model="formKelompok.password" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm form-input-mobile" 
                                   placeholder="Masukkan password untuk login kelompok" 
                                   :required="!isEditing">
                            <p class="mt-1 text-xs text-gray-500" x-show="!isEditing">
                                Password ini akan digunakan untuk login kelompok<br>
                                <strong>Username:</strong> <span x-text="formKelompok.nama_kelompok ? formKelompok.nama_kelompok.toLowerCase().replace(/\\s+/g, '') : ''"></span>
                            </p>
                            <p class="mt-1 text-xs text-gray-500" x-show="isEditing">
                                Kosongkan jika tidak ingin mengubah password
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-sm sm:text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 disabled:opacity-50 min-h-[44px]">
                        <span x-show="!loading" x-text="isEditing ? 'Perbarui' : 'Simpan'"></span>
                        <span x-show="loading">Loading...</span>
                    </button>
                    <button type="button" @click="showKelompokModal = false; resetFormKelompok()" 
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm sm:text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 min-h-[44px]">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div x-show="showKaryawanModal" 
     x-transition 
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape="showKaryawanModal = false">
    <div class="flex items-center justify-center min-h-screen pt-4 px-3 sm:px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showKaryawanModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <form @submit.prevent="isEditingKaryawan ? updateKaryawan() : tambahKaryawan()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900" x-text="isEditingKaryawan ? 'Edit Karyawan' : 'Tambah Karyawan Baru'"></h3>
                        <button type="button" 
                                @click="showKaryawanModal = false; resetFormKaryawan()"
                                class="text-gray-400 hover:text-gray-600 p-2 rounded-md hover:bg-gray-100 min-w-[44px] min-h-[44px] flex items-center justify-center">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan</label>
                            <input type="text" x-model="formKaryawan.nama" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm form-input-mobile" 
                                   placeholder="Masukkan nama karyawan" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelompok</label>
                            <select x-model="formKaryawan.kelompok_id" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm form-input-mobile" required>
                                <option value="">Pilih Kelompok</option>
                                @foreach($kelompoks as $kelompok)
                                <option value="{{ $kelompok->id }}">{{ $kelompok->nama_kelompok }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select x-model="formKaryawan.status" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm form-input-mobile" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-sm sm:text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 disabled:opacity-50 min-h-[44px]">
                        <span x-show="!loading" x-text="isEditingKaryawan ? 'Perbarui' : 'Simpan'"></span>
                        <span x-show="loading">Loading...</span>
                    </button>
                    <button type="button" @click="showKaryawanModal = false; resetFormKaryawan()" 
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm sm:text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 min-h-[44px]">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    window.activeTab = tabName;
}

// Alpine.js data
document.addEventListener('alpine:init', () => {
    Alpine.data('manajemenData', () => ({
        activeTab: 'kelompok',
        showKelompokModal: false,
        showKaryawanModal: false,
        isEditing: false,
        isEditingKaryawan: false,
        editingId: null,
        
        init() {
            console.log('Alpine.js manajemenData initialized');
            console.log('Initial showKelompokModal:', this.showKelompokModal);
            console.log('Initial showKaryawanModal:', this.showKaryawanModal);
        },
        formKelompok: {
            nama_kelompok: '',
            shift: '',
            password: ''
        },
        formKaryawan: {
            nama: '',
            kelompok_id: '',
            status: 'aktif'
        },
        loading: false,
        message: '',
        messageType: '',
        
        async tambahKelompok() {
            this.loading = true;
            try {
                const response = await fetch('/api/kelompok', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formKelompok)
                });
                
                const result = await response.json();
                if (response.ok) {
                    this.showMessage(result.message || 'Kelompok berhasil ditambahkan!', 'success');
                    this.showKelompokModal = false;
                    this.resetFormKelompok();
                    location.reload();
                } else {
                    this.showMessage(result.message || 'Gagal menambahkan kelompok', 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan', 'error');
            }
            this.loading = false;
        },
        
        async editKelompok(id, nama, shift) {
            console.log('Editing kelompok:', { id, nama, shift });
            this.isEditing = true;
            this.editingId = id;
            this.formKelompok = {
                nama_kelompok: nama,
                shift: shift,
                password: ''
            };
            this.showKelompokModal = true;
            console.log('Form data set:', this.formKelompok);
        },
        
        async updateKelompok() {
            this.loading = true;
            try {
                console.log('Updating kelompok with data:', this.formKelompok);
                
                const response = await fetch(`/api/kelompok/${this.editingId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formKelompok)
                });
                
                const result = await response.json();
                console.log('Update response:', result);
                
                if (response.ok) {
                    this.showMessage(result.message || 'Kelompok berhasil diperbarui!', 'success');
                    this.showKelompokModal = false;
                    this.resetFormKelompok();
                    location.reload();
                } else {
                    const errorMessage = result.message || (result.errors ? JSON.stringify(result.errors) : 'Gagal memperbarui kelompok');
                    this.showMessage('Error: ' + errorMessage, 'error');
                }
            } catch (error) {
                console.error('Update error:', error);
                this.showMessage('Terjadi kesalahan: ' + error.message, 'error');
            }
            this.loading = false;
        },
        
        async hapusKelompok(id) {
            const result = await SwalHelper.confirmDelete(
                '⚠️ Hapus Kelompok?',
                'Apakah Anda yakin ingin menghapus kelompok ini? Semua data karyawan dan laporan dalam kelompok ini juga akan ikut terhapus.'
            );
            
            if (result.isConfirmed) {
                try {
                    console.log('Deleting kelompok:', id);
                    
                    const response = await fetch(`/api/kelompok/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const result = await response.json();
                    console.log('Delete kelompok response:', result);
                    
                    if (response.ok) {
                        SwalHelper.success('Terhapus! 🗑️', result.message || 'Kelompok berhasil dihapus!').then(() => {
                            location.reload();
                        });
                    } else {
                        const errorMessage = result.message || 'Gagal menghapus kelompok';
                        SwalHelper.error('Gagal ❌', errorMessage);
                    }
                } catch (error) {
                    console.error('Delete kelompok error:', error);
                    SwalHelper.error('Gagal ❌', 'Terjadi kesalahan: ' + error.message);
                }
            }
        },
        
        async tambahKaryawan() {
            this.loading = true;
            try {
                const response = await fetch('/api/karyawan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formKaryawan)
                });
                
                const result = await response.json();
                if (response.ok) {
                    this.showMessage(result.message || 'Karyawan berhasil ditambahkan!', 'success');
                    this.showKaryawanModal = false;
                    this.resetFormKaryawan();
                    location.reload();
                } else {
                    this.showMessage(result.message || 'Gagal menambahkan karyawan', 'error');
                }
            } catch (error) {
                this.showMessage('Terjadi kesalahan', 'error');
            }
            this.loading = false;
        },
        
        async editKaryawan(id, nama, kelompokId) {
            console.log('Editing karyawan:', { id, nama, kelompokId });
            this.isEditingKaryawan = true;
            this.editingId = id;
            this.formKaryawan = {
                nama: nama,
                kelompok_id: kelompokId,
                status: 'aktif'
            };
            this.showKaryawanModal = true;
            console.log('Karyawan form data set:', this.formKaryawan);
            console.log('isEditingKaryawan set to:', this.isEditingKaryawan);
        },
        
        async updateKaryawan() {
            this.loading = true;
            try {
                console.log('Updating karyawan with data:', this.formKaryawan);
                
                const response = await fetch(`/api/karyawan/${this.editingId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formKaryawan)
                });
                
                const result = await response.json();
                console.log('Update karyawan response:', result);
                
                if (response.ok) {
                    this.showMessage(result.message || 'Karyawan berhasil diperbarui!', 'success');
                    this.showKaryawanModal = false;
                    this.isEditingKaryawan = false;
                    this.resetFormKaryawan();
                    location.reload();
                } else {
                    const errorMessage = result.message || (result.errors ? JSON.stringify(result.errors) : 'Gagal memperbarui karyawan');
                    this.showMessage('Error: ' + errorMessage, 'error');
                }
            } catch (error) {
                console.error('Update karyawan error:', error);
                this.showMessage('Terjadi kesalahan: ' + error.message, 'error');
            }
            this.loading = false;
        },
        
        async hapusKaryawan(id) {
            const result = await SwalHelper.confirmDelete(
                '⚠️ Hapus Karyawan?',
                'Apakah Anda yakin ingin menghapus karyawan ini?'
            );
            
            if (result.isConfirmed) {
                try {
                    console.log('Deleting karyawan:', id);
                    
                    const response = await fetch(`/api/karyawan/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const result = await response.json();
                    console.log('Delete karyawan response:', result);
                    
                    if (response.ok) {
                        SwalHelper.success('Terhapus! 🗑️', result.message || 'Karyawan berhasil dihapus!').then(() => {
                            location.reload();
                        });
                    } else {
                        const errorMessage = result.message || 'Gagal menghapus karyawan';
                        SwalHelper.error('Gagal ❌', errorMessage);
                    }
                } catch (error) {
                    console.error('Delete karyawan error:', error);
                    SwalHelper.error('Gagal ❌', 'Terjadi kesalahan: ' + error.message);
                }
            }
        },
        
        
        resetFormKelompok() {
            this.formKelompok = {
                nama_kelompok: '',
                shift: '',
                password: ''
            };
            this.isEditing = false;
            this.editingId = null;
        },
        
        resetFormKaryawan() {
            this.formKaryawan = {
                nama: '',
                kelompok_id: '',
                status: 'aktif'
            };
            this.isEditingKaryawan = false;
            this.editingId = null;
        },
        
        showMessage(text, type) {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
                this.messageType = '';
            }, 3000);
        }
    }));
});
</script>
@endsection
