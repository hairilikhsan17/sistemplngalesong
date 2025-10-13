@extends('layouts.dashboard')

@section('title', 'Manajemen Karyawan')

@section('content')
<div x-data="karyawanManager()" class="space-y-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Karyawan</h1>
        <p class="text-gray-600 mt-2">Kelola data karyawan dan anggota kelompok</p>
    </div>

    <!-- Manajemen Karyawan -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Data Karyawan</h2>
            <button
                @click="showKaryawanModal = true; karyawanForm = { nama: '', kelompok_id: '', status: 'aktif' }"
                class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Tambah Karyawan
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($karyawans as $karyawan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ substr($karyawan->id, 0, 8) }}...</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->kelompok->nama_kelompok ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button
                                @click="editKaryawan('{{ $karyawan->id }}', '{{ $karyawan->nama }}', '{{ $karyawan->kelompok_id }}')"
                                class="text-amber-600 hover:text-amber-900 mr-3"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteKaryawan('{{ $karyawan->id }}')"
                                class="text-red-600 hover:text-red-900"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Belum ada data karyawan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Manajemen Kelompok (Quick View) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Kelompok yang Tersedia</h2>
            <a href="{{ route('atasan.kelompok') }}" 
               class="text-sm text-blue-600 hover:text-blue-700">
                Kelola Kelompok →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($kelompoks as $kelompok)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $kelompok->nama_kelompok }}</h3>
                        <p class="text-sm text-gray-600">{{ $kelompok->shift }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Anggota: {{ $kelompok->karyawan->count() }} orang</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modals -->
    @include('dashboard.atasan.modals.karyawan')
</div>

<script>
function karyawanManager() {
    return {
        showKaryawanModal: false,
        editingKaryawan: null,
        karyawanForm: { nama: '', kelompok_id: '', status: 'aktif' },

        editKaryawan(id, nama, kelompokId) {
            this.editingKaryawan = id;
            this.karyawanForm = {
                nama: nama,
                kelompok_id: kelompokId,
                status: 'aktif'
            };
            this.showKaryawanModal = true;
        },

        async saveKaryawan() {
            try {
                // Validasi form
                if (!this.karyawanForm.nama.trim()) {
                    alert('Nama karyawan harus diisi');
                    return;
                }
                
                if (!this.karyawanForm.kelompok_id) {
                    alert('Kelompok harus dipilih');
                    return;
                }
                
                const url = this.editingKaryawan 
                    ? `/api/karyawan/${this.editingKaryawan}`
                    : '/api/karyawan';
                
                const method = this.editingKaryawan ? 'PUT' : 'POST';
                
                console.log('Sending data:', this.karyawanForm);
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.karyawanForm)
                });

                const result = await response.json();
                console.log('Response:', result);

                if (response.ok) {
                    alert(result.message || 'Karyawan berhasil disimpan');
                    this.showKaryawanModal = false;
                    location.reload();
                } else {
                    const errorMessage = result.message || result.errors ? JSON.stringify(result.errors) : 'Gagal menyimpan karyawan';
                    alert('Error: ' + errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        },

        async deleteKaryawan(id) {
            if (!confirm('Yakin ingin menghapus karyawan ini?')) return;

            try {
                const response = await fetch(`/api/karyawan/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                console.log('Response:', result);

                if (response.ok) {
                    alert(result.message || 'Karyawan berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus karyawan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        }
    };
}
</script>
@endsection


