@extends('layouts.dashboard')

@section('title', 'Dashboard Atasan - PLN Galesong')
@section('page-title', 'Dashboard Atasan')

@section('content')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <i data-lucide="users" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Kelompok</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalKelompok }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Karyawan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalKaryawan }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i data-lucide="file-text" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Laporan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalLaporan }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i data-lucide="briefcase" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Job</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalJobs }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="flex border-b border-gray-200">
                <button
                    x-data="{ activeTab: 'kelompok' }"
                    @click="activeTab = 'kelompok'"
                    :class="activeTab === 'kelompok' ? 'border-b-2 border-amber-600 text-amber-600' : 'text-gray-600 hover:text-gray-900'"
                    class="flex items-center gap-2 px-6 py-4 font-medium transition-colors"
                >
                    <i data-lucide="users" class="w-5 h-5"></i>
                    Kelompok & Karyawan
                </button>
                <button
                    x-data="{ activeTab: 'laporan' }"
                    @click="activeTab = 'laporan'"
                    :class="activeTab === 'laporan' ? 'border-b-2 border-amber-600 text-amber-600' : 'text-gray-600 hover:text-gray-900'"
                    class="flex items-center gap-2 px-6 py-4 font-medium transition-colors"
                >
                    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                    Pemantauan Laporan
                </button>
                <button
                    x-data="{ activeTab: 'prediksi' }"
                    @click="activeTab = 'prediksi'"
                    :class="activeTab === 'prediksi' ? 'border-b-2 border-amber-600 text-amber-600' : 'text-gray-600 hover:text-gray-900'"
                    class="flex items-center gap-2 px-6 py-4 font-medium transition-colors"
                >
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                    Statistik & Prediksi
                </button>
            </div>

            <div class="p-6">
                <!-- Kelompok & Karyawan Tab -->
                <div x-show="activeTab === 'kelompok'" x-cloak>
                    @include('dashboard.atasan.kelompok')
                </div>

                <!-- Laporan Tab -->
                <div x-show="activeTab === 'laporan'" x-cloak>
                    @include('dashboard.atasan.laporan')
                </div>

                <!-- Prediksi Tab -->
                <div x-show="activeTab === 'prediksi'" x-cloak>
                    @include('dashboard.atasan.prediksi')
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            activeTab: 'kelompok'
        }));
    });
</script>
@endsection


