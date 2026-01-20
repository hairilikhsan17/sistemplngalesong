@extends('layouts.dashboard')

@section('title', 'Pemantauan Laporan')

@section('content')
<div class="p-3 sm:p-4 lg:p-6">
    <!-- Header -->
    <div class="mb-6 lg:mb-8">
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-xl shadow-lg p-6 sm:p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i data-lucide="eye" class="w-8 h-8 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Pemantauan Laporan</h1>
                        <p class="text-amber-100 mt-1 text-sm sm:text-base">Pantau semua laporan kerja dari seluruh kelompok</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Total Laporan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['totalLaporan'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Laporan Hari Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['laporanHariIni'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-600">Laporan Bulan Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statistics['laporanBulanIni'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg mb-6 border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i data-lucide="filter" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Filter Laporan</h3>
                    <p class="text-xs text-gray-500">Saring data laporan sesuai kebutuhan</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('atasan.pemantauan-laporan') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-amber-600"></i>
                        Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ request('tanggal') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Hari -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1 text-amber-600"></i>
                        Hari
                    </label>
                    <select name="hari" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white hover:border-gray-400">
                        <option value="">Semua Hari</option>
                        <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                        <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        <option value="Minggu" {{ request('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                    </select>
                </div>

                <!-- Filter Kelompok -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="users" class="w-4 h-4 inline mr-1 text-amber-600"></i>
                        Kelompok
                    </label>
                    <select name="kelompok_id" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white hover:border-gray-400">
                        <option value="">Semua Kelompok</option>
                        @foreach($kelompoks as $kelompok)
                            <option value="{{ $kelompok->id }}" {{ request('kelompok_id') == $kelompok->id ? 'selected' : '' }}>
                                {{ $kelompok->nama_kelompok }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1 text-amber-600"></i>
                        Nama
                    </label>
                    <select name="nama" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white hover:border-gray-400">
                        <option value="">Semua Nama</option>
                        @foreach($namaKaryawans as $nama)
                            <option value="{{ $nama }}" {{ request('nama') == $nama ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Instansi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i data-lucide="building" class="w-4 h-4 inline mr-1 text-amber-600"></i>
                        Instansi
                    </label>
                    <input type="text" 
                           name="instansi" 
                           value="{{ request('instansi') }}"
                           placeholder="Cari instansi..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all bg-white hover:border-gray-400">
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('atasan.pemantauan-laporan') }}" 
                       class="px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all font-medium shadow-md hover:shadow-lg flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <i data-lucide="list" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Laporan</h3>
                        <p class="text-xs text-gray-500">Semua laporan kerja dari seluruh kelompok</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('atasan.pemantauan-laporan.export', request()->all()) }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg font-medium transform hover:scale-105">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 mr-2"></i>
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal Deskripsi Jenis Kegiatan -->
        <div id="deskripsiModal" style="display: none;" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-amber-100 rounded-lg">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Deskripsi Jenis Kegiatan</h3>
                            <p class="text-sm text-gray-500">Detail kegiatan yang dilakukan</p>
                        </div>
                    </div>
                    <button onclick="tutupDeskripsiModal()" 
                            class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <p id="deskripsiContent" class="text-sm text-gray-700 whitespace-pre-wrap"></p>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button onclick="tutupDeskripsiModal()"
                            class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all font-medium min-h-[44px]">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hari/Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">KELOMPOK</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Instansi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu Mulai Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jenis Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu Selesai Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Durasi Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Alamat Tujuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Dokumentasi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporans as $index => $laporan)
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-amber-100 text-amber-700 rounded-full text-sm font-semibold">
                                    {{ $laporans->firstItem() + $index }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-amber-600"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $laporan->hari }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->isoFormat('DD MMM YYYY') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="p-1.5 bg-amber-100 rounded-lg">
                                        <i data-lucide="users" class="w-4 h-4 text-amber-600"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $laporan->kelompok->nama_kelompok ?? '-' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($laporan->nama)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $laporan->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($laporan->instansi)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="building" class="w-4 h-4 text-blue-600"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $laporan->instansi }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                @if($laporan->alamat_tujuan)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-blue-600 flex-shrink-0"></i>
                                        <div class="truncate" title="{{ $laporan->alamat_tujuan }}">{{ Str::limit($laporan->alamat_tujuan, 50) }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($laporan->waktu_mulai_kegiatan)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($laporan->waktu_mulai_kegiatan)->format('H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $laporan->jenis_kegiatan ?? '-' }}
                                    </span>
                                    @if($laporan->jenis_kegiatan === 'Jenis Kegiatan lainnya' && $laporan->deskripsi_kegiatan)
                                        <button type="button"
                                                onclick="lihatDeskripsiGangguan(@js($laporan->deskripsi_kegiatan))"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg transition-all duration-200 hover:shadow-md"
                                                title="Lihat Deskripsi Jenis Kegiatan">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($laporan->waktu_selesai_kegiatan)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-green-600"></i>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($laporan->waktu_selesai_kegiatan)->format('H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="hourglass" class="w-4 h-4 text-indigo-600"></i>
                                    <span class="font-medium">{{ number_format($laporan->durasi_waktu ?? 0, 2) }} jam</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($laporan->lokasi)
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 flex-shrink-0"></i>
                                        <div class="truncate" title="{{ $laporan->lokasi }}">{{ Str::limit($laporan->lokasi, 50) }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($laporan->file_path)
                                    <a href="/api/pemantauan-laporan/{{ $laporan->id }}/download" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                                        <i data-lucide="file" class="w-4 h-4 mr-1"></i>
                                        <span class="text-xs">Lihat File</span>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Button -->
                                    <button type="button"
                                            onclick="editLaporan('{{ $laporan->id }}')" 
                                            class="inline-flex items-center justify-center w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg"
                                            title="Edit Laporan">
                                        <i data-lucide="edit-2" class="w-5 h-5"></i>
                                    </button>
                                    
                                    <!-- Download Button -->
                                    @if($laporan->file_path)
                                    <a href="/api/pemantauan-laporan/{{ $laporan->id }}/download" 
                                       target="_blank"
                                       class="inline-flex items-center justify-center w-10 h-10 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg"
                                       title="Download File">
                                        <i data-lucide="download" class="w-5 h-5"></i>
                                    </a>
                                    @else
                                    <button type="button"
                                            disabled
                                            class="inline-flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed opacity-50"
                                            title="Tidak ada file">
                                        <i data-lucide="download" class="w-5 h-5"></i>
                                    </button>
                                    @endif
                                    
                                    <!-- Delete Button -->
                                    <button type="button"
                                            onclick="hapusLaporan('{{ $laporan->id }}')" 
                                            class="inline-flex items-center justify-center w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 hover:shadow-lg"
                                            title="Hapus Laporan">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-gray-100 rounded-full mb-4">
                                        <i data-lucide="file-text" class="w-16 h-16 text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-700 text-lg font-semibold mb-2">Belum ada laporan</p>
                                    <p class="text-gray-500 text-sm">Tidak ada laporan yang ditemukan berdasarkan filter yang dipilih</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($laporans->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($laporans->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $laporans->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif
                
                @if($laporans->hasMorePages())
                    <a href="{{ $laporans->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Selanjutnya
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">
                        Selanjutnya
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ $laporans->firstItem() ?? 0 }}</span>
                        sampai
                        <span class="font-medium">{{ $laporans->lastItem() ?? 0 }}</span>
                        dari
                        <span class="font-medium">{{ $laporans->total() }}</span>
                        hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        @if($laporans->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </span>
                        @else
                            <a href="{{ $laporans->appends(request()->except('page'))->previousPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </a>
                        @endif
                        
                        @php
                            $currentPage = $laporans->currentPage();
                            $lastPage = $laporans->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp
                        
                        @if($start > 1)
                            <a href="{{ $laporans->appends(request()->except('page'))->url(1) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            @if($start > 2)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                        @endif
                        
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                            <span class="relative inline-flex items-center px-4 py-2 border border-amber-500 bg-amber-50 text-sm font-medium text-amber-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $laporans->appends(request()->except('page'))->url($page) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $page }}
                            </a>
                            @endif
                        @endfor
                        
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="{{ $laporans->appends(request()->except('page'))->url($lastPage) }}" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $lastPage }}
                            </a>
                        @endif
                        
                        @if($laporans->hasMorePages())
                            <a href="{{ $laporans->appends(request()->except('page'))->nextPageUrl() }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-not-allowed">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4 backdrop-blur-sm hidden">
        <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-4xl max-h-[95vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="edit-2" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Edit Laporan</h3>
                        <p class="text-sm text-gray-500">Perbarui informasi laporan kerja</p>
                    </div>
                </div>
                <button onclick="tutupEdit()" 
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="editForm" onsubmit="simpanEdit(event)">
                <div id="editContent" class="space-y-4">
                    <!-- Content will be loaded here -->
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="tutupEdit()" 
                            class="px-6 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-medium shadow-md hover:shadow-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
});

let currentLaporanId = null;

async function editLaporan(id) {
    currentLaporanId = id;
    try {
        const response = await fetch(`/api/pemantauan-laporan/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) throw new Error('Gagal memuat data');
        
        const laporan = await response.json();
        const tanggalFormatted = new Date(laporan.tanggal).toISOString().split('T')[0];
        
        // Format waktu
        let waktuMulai = '';
        let waktuSelesai = '';
        if (laporan.waktu_mulai_kegiatan) {
            try {
                const waktuMulaiDate = new Date('2000-01-01 ' + laporan.waktu_mulai_kegiatan);
                waktuMulai = waktuMulaiDate.toTimeString().slice(0, 5);
            } catch (e) {
                waktuMulai = laporan.waktu_mulai_kegiatan;
            }
        }
        if (laporan.waktu_selesai_kegiatan) {
            try {
                const waktuSelesaiDate = new Date('2000-01-01 ' + laporan.waktu_selesai_kegiatan);
                waktuSelesai = waktuSelesaiDate.toTimeString().slice(0, 5);
            } catch (e) {
                waktuSelesai = laporan.waktu_selesai_kegiatan;
            }
        }
        
        const durasiDisplay = laporan.durasi_waktu ? parseFloat(laporan.durasi_waktu).toFixed(2) : '0.00';
        
        const content = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                    <select name="hari" id="editHari" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500" required>
                        <option value="Senin" ${laporan.hari === 'Senin' ? 'selected' : ''}>Senin</option>
                        <option value="Selasa" ${laporan.hari === 'Selasa' ? 'selected' : ''}>Selasa</option>
                        <option value="Rabu" ${laporan.hari === 'Rabu' ? 'selected' : ''}>Rabu</option>
                        <option value="Kamis" ${laporan.hari === 'Kamis' ? 'selected' : ''}>Kamis</option>
                        <option value="Jumat" ${laporan.hari === 'Jumat' ? 'selected' : ''}>Jumat</option>
                        <option value="Sabtu" ${laporan.hari === 'Sabtu' ? 'selected' : ''}>Sabtu</option>
                        <option value="Minggu" ${laporan.hari === 'Minggu' ? 'selected' : ''}>Minggu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="${tanggalFormatted}" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="${laporan.nama || ''}" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Instansi <span class="text-red-500">*</span></label>
                    <input type="text" name="instansi" value="${laporan.instansi || ''}" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Masuk <span class="text-red-500">*</span></label>
                    <input type="time" name="alamat_tujuan" value="${laporan.alamat_tujuan || ''}" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Kegiatan</label>
                    <select name="jenis_kegiatan" id="editJenisKegiatan" onchange="toggleDeskripsiEdit()" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih Jenis Kegiatan</option>
                        <option value="Perbaikan Meteran" ${laporan.jenis_kegiatan === 'Perbaikan Meteran' ? 'selected' : ''}>Perbaikan Meteran</option>
                        <option value="Perbaikan Sambungan Rumah" ${laporan.jenis_kegiatan === 'Perbaikan Sambungan Rumah' ? 'selected' : ''}>Perbaikan Sambungan Rumah</option>
                        <option value="Pemeriksaan Gardu" ${laporan.jenis_kegiatan === 'Pemeriksaan Gardu' ? 'selected' : ''}>Pemeriksaan Gardu</option>
                        <option value="Jenis Kegiatan lainnya" ${laporan.jenis_kegiatan === 'Jenis Kegiatan lainnya' ? 'selected' : ''}>Jenis Kegiatan lainnya</option>
                    </select>
                </div>
                <div class="md:col-span-2" id="editDeskripsiContainer">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi Kegiatan 
                        <span id="editDeskripsiRequired" class="text-red-500" style="display: ${laporan.jenis_kegiatan === 'Jenis Kegiatan lainnya' ? 'inline' : 'none'};">*</span>
                    </label>
                    <textarea name="deskripsi_kegiatan" id="editDeskripsiKegiatan" rows="4" 
                              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"
                              placeholder="${laporan.jenis_kegiatan === 'Jenis Kegiatan lainnya' ? 'Masukkan deskripsi detail penanganan gangguan yang dilakukan...' : 'Masukkan deskripsi detail kegiatan yang dilakukan (opsional)'}">${laporan.deskripsi_kegiatan || ''}</textarea>
                    <p class="text-xs text-gray-500 mt-1.5" id="editDeskripsiInfo">
                        ${laporan.jenis_kegiatan === 'Jenis Kegiatan lainnya' ? 'Wajib diisi untuk jenis kegiatan Jenis Kegiatan lainnya' : 'Opsional - Anda dapat menambahkan deskripsi detail kegiatan jika diperlukan'}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Mulai Kegiatan</label>
                    <input type="time" name="waktu_mulai_kegiatan" id="editWaktuMulai" value="${waktuMulai}" 
                           onchange="hitungDurasiEdit()"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Selesai Kegiatan</label>
                    <input type="time" name="waktu_selesai_kegiatan" id="editWaktuSelesai" value="${waktuSelesai}" 
                           onchange="hitungDurasiEdit()"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1.5">Durasi akan dihitung otomatis</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi Waktu (jam)</label>
                    <input type="text" id="editDurasiDisplay" value="${durasiDisplay}" readonly
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1.5">Dihitung otomatis dari waktu mulai dan selesai</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Tujuan</label>
                    <input type="text" name="lokasi" value="${laporan.lokasi || ''}" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">File Dokumentasi</label>
                    <input type="file" name="file" accept="image/*,.pdf" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1.5">Format: JPG, PNG, PDF (Maksimal: 5MB). Kosongkan jika tidak ingin mengubah file.</p>
                    ${laporan.file_path ? `
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700">File saat ini: <a href="/api/pemantauan-laporan/${laporan.id}/download" target="_blank" class="text-blue-600 hover:underline">Download</a></p>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        document.getElementById('editContent').innerHTML = content;
        document.getElementById('editModal').classList.remove('hidden');
        
        // Set required attribute untuk deskripsi jika Jenis Kegiatan lainnya
        setTimeout(() => {
            toggleDeskripsiEdit();
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);
    } catch (error) {
        SwalHelper.error('Gagal ❌', 'Gagal memuat data untuk edit: ' + error.message);
    }
}

function toggleDeskripsiEdit() {
    const jenisKegiatan = document.getElementById('editJenisKegiatan');
    const deskripsiField = document.getElementById('editDeskripsiKegiatan');
    const requiredSpan = document.getElementById('editDeskripsiRequired');
    const infoText = document.getElementById('editDeskripsiInfo');
    
    if (jenisKegiatan && deskripsiField && requiredSpan && infoText) {
        if (jenisKegiatan.value === 'Jenis Kegiatan lainnya') {
            deskripsiField.required = true;
            requiredSpan.style.display = 'inline';
            infoText.textContent = 'Wajib diisi untuk jenis kegiatan Jenis Kegiatan lainnya';
            deskripsiField.placeholder = 'Masukkan deskripsi detail penanganan gangguan yang dilakukan...';
        } else {
            deskripsiField.required = false;
            requiredSpan.style.display = 'none';
            infoText.textContent = 'Opsional - Anda dapat menambahkan deskripsi detail kegiatan jika diperlukan';
            deskripsiField.placeholder = 'Masukkan deskripsi detail kegiatan yang dilakukan (opsional)';
        }
    }
}

function hitungDurasiEdit() {
    const waktuMulai = document.getElementById('editWaktuMulai');
    const waktuSelesai = document.getElementById('editWaktuSelesai');
    const durasiDisplay = document.getElementById('editDurasiDisplay');
    
    if (waktuMulai && waktuSelesai && durasiDisplay && waktuMulai.value && waktuSelesai.value) {
        const [mulaiJam, mulaiMenit] = waktuMulai.value.split(':').map(Number);
        const [selesaiJam, selesaiMenit] = waktuSelesai.value.split(':').map(Number);
        
        let mulaiTotal = mulaiJam * 60 + mulaiMenit;
        let selesaiTotal = selesaiJam * 60 + selesaiMenit;
        
        // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
        if (selesaiTotal < mulaiTotal) {
            selesaiTotal += 24 * 60; // Tambah 24 jam
        }
        
        const durasiMenit = selesaiTotal - mulaiTotal;
        const durasiJam = durasiMenit / 60;
        
        durasiDisplay.value = durasiJam.toFixed(2);
    } else {
        if (durasiDisplay) {
            durasiDisplay.value = '0.00';
        }
    }
}

function tutupEdit() {
    document.getElementById('editModal').classList.add('hidden');
    currentLaporanId = null;
}

async function simpanEdit(event) {
    event.preventDefault();
    if (!currentLaporanId) return;
    
    // Validasi deskripsi jika Jenis Kegiatan lainnya dipilih
    const jenisKegiatan = document.getElementById('editJenisKegiatan');
    const deskripsiKegiatan = document.getElementById('editDeskripsiKegiatan');
    
    if (jenisKegiatan && jenisKegiatan.value === 'Jenis Kegiatan lainnya' && (!deskripsiKegiatan || !deskripsiKegiatan.value.trim())) {
        SwalHelper.error('Gagal ❌', 'Deskripsi Jenis Kegiatan lainnya wajib diisi!');
        return;
    }
    
    const formData = new FormData(event.target);
    formData.append('_method', 'PUT');
    
    try {
        const response = await fetch(`/api/pemantauan-laporan/${currentLaporanId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            if (result.errors) {
                let errorMessage = 'Gagal memperbarui laporan:<br>';
                for (const [field, messages] of Object.entries(result.errors)) {
                    errorMessage += `- ${messages.join(', ')}<br>`;
                }
                SwalHelper.error('Gagal ❌', errorMessage);
            } else {
                throw new Error(result.message || 'Gagal memperbarui laporan');
            }
            return;
        }
        
        await SwalHelper.update('Berhasil 🎉', 'Laporan berhasil diperbarui!');
        tutupEdit();
        window.location.reload();
    } catch (error) {
        SwalHelper.error('Gagal ❌', 'Gagal memperbarui laporan: ' + error.message);
    }
}

async function hapusLaporan(id) {
    const result = await SwalHelper.confirmDelete('⚠️ Konfirmasi Penghapusan', 'Apakah Anda yakin ingin menghapus laporan ini?');
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(`/api/pemantauan-laporan/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal menghapus laporan');
            }
            
            await SwalHelper.success('Berhasil 🎉', 'Laporan berhasil dihapus!');
            window.location.reload();
        } catch (error) {
            SwalHelper.error('Gagal ❌', 'Gagal menghapus laporan: ' + error.message);
        }
    }
}

function lihatDeskripsiGangguan(deskripsi) {
    const modal = document.getElementById('deskripsiModal');
    const content = document.getElementById('deskripsiContent');
    
    if (modal && content) {
        content.textContent = deskripsi;
        modal.style.display = 'flex';
        
        setTimeout(() => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);
    }
}

function tutupDeskripsiModal() {
    const modal = document.getElementById('deskripsiModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        tutupDeskripsiModal();
    }
});
</script>
@endsection

