@extends('layouts.dashboard')

@section('title', 'Manajemen Kelompok')

@section('content')
<div x-data="kelompokManager()" class="space-y-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Kelompok</h1>
        <p class="text-gray-600 mt-2">Kelola data kelompok dan anggota karyawan</p>
    </div>

    <!-- Manajemen Kelompok -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Manajemen Kelompok</h2>
            <button
                @click="showKelompokModal = true; editingKelompok = null; formData = { nama_kelompok: '', shift: 'Shift 1', password: '' }"
                class="flex items-center gap-2 bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Kelompok
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($kelompoks as $kelompok)
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-4 border border-amber-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $kelompok->nama_kelompok }}</h3>
                        <p class="text-sm text-gray-600">{{ $kelompok->shift }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="editKelompok({{ json_encode($kelompok) }})"
                            class="text-blue-600 hover:text-blue-700"
                        >
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button
                            @click="deleteKelompok('{{ $kelompok->id }}')"
                            class="text-red-600 hover:text-red-700"
                        >
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Anggota: {{ $kelompok->karyawan->count() }} orang</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Manajemen Karyawan -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Manajemen Karyawan</h2>
            <button
                @click="showKaryawanModal = true; karyawanForm = { nama: '', kelompok_id: '' }"
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kelompoks as $kelompok)
                        @foreach($kelompok->karyawan as $karyawan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $karyawan->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kelompok->nama_kelompok }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button
                                    @click="deleteKaryawan('{{ $karyawan->id }}')"
                                    class="text-red-600 hover:text-red-700"
                                >
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals -->
    @include('dashboard.atasan.modals.kelompok')
    @include('dashboard.atasan.modals.karyawan')
</div>

<script>
function kelompokManager() {
    return {
        showKelompokModal: false,
        showKaryawanModal: false,
        editingKelompok: null,
        formData: { nama_kelompok: '', shift: 'Shift 1', password: '' },
        karyawanForm: { nama: '', kelompok_id: '' },

        editKelompok(kelompok) {
            this.editingKelompok = kelompok;
            this.formData = {
                nama_kelompok: kelompok.nama_kelompok,
                shift: kelompok.shift,
                password: ''
            };
            this.showKelompokModal = true;
        },

        async saveKelompok() {
            try {
                // Validasi form
                if (!this.formData.nama_kelompok.trim()) {
                    alert('Nama kelompok harus diisi');
                    return;
                }
                
                if (!this.formData.shift) {
                    alert('Shift harus dipilih');
                    return;
                }
                
                if (!this.editingKelompok && !this.formData.password.trim()) {
                    alert('Password harus diisi untuk kelompok baru');
                    return;
                }
                
                const url = this.editingKelompok 
                    ? `/api/kelompok/${this.editingKelompok.id}`
                    : '/api/kelompok';
                
                const method = this.editingKelompok ? 'PUT' : 'POST';
                
                console.log('Sending data:', this.formData);
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                const result = await response.json();
                console.log('Response:', result);

                if (response.ok) {
                    alert(result.message || 'Kelompok berhasil disimpan');
                    this.showKelompokModal = false;
                    location.reload();
                } else {
                    const errorMessage = result.message || result.errors ? JSON.stringify(result.errors) : 'Gagal menyimpan kelompok';
                    alert('Error: ' + errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        },

        async deleteKelompok(id) {
            if (!confirm('Yakin ingin menghapus kelompok ini?')) return;

            try {
                const response = await fetch(`/api/kelompok/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    alert('Kelompok berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus kelompok');
                }
            } catch (error) {
                alert('Terjadi kesalahan');
            }
        },

        async saveKaryawan() {
            try {
                const response = await fetch('/api/karyawan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.karyawanForm)
                });

                if (response.ok) {
                    alert('Karyawan berhasil ditambahkan');
                    location.reload();
                } else {
                    alert('Gagal menambahkan karyawan');
                }
            } catch (error) {
                alert('Terjadi kesalahan');
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

                if (response.ok) {
                    alert('Karyawan berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus karyawan');
                }
            } catch (error) {
                alert('Terjadi kesalahan');
            }
        }
    };
}
</script>
@endsection