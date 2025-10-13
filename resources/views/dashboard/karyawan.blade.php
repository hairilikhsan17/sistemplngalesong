@extends('layouts.dashboard')

@section('title', 'Dashboard Karyawan - PLN Galesong')
@section('page-title', 'Dashboard Karyawan')

@section('content')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Kelompok</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $kelompok->nama_kelompok ?? '-' }}</p>
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
                        <p class="text-2xl font-bold text-gray-900">{{ $laporanCount }}</p>
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
                        <p class="text-2xl font-bold text-gray-900">{{ $jobsCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Shift</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $kelompok->shift ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="flex border-b border-gray-200">
                <button
                    x-data="{ activeTab: 'dashboard' }"
                    @click="activeTab = 'dashboard'"
                    :class="activeTab === 'dashboard' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="flex items-center gap-2 px-6 py-4 font-medium transition-colors"
                >
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    Dashboard
                </button>
                <button
                    x-data="{ activeTab: 'laporan' }"
                    @click="activeTab = 'laporan'"
                    :class="activeTab === 'laporan' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="flex items-center gap-2 px-6 py-4 font-medium transition-colors"
                >
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    Input Laporan
                </button>
                <button
                    x-data="{ activeTab: 'job' }"
                    @click="activeTab = 'job'"
                    :class="activeTab === 'job' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                    class="flex items-center gap-2 px-6 py-4 font-medium transition-colors"
                >
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                    Input Job Pekerjaan
                </button>
            </div>

            <div class="p-6">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" x-cloak>
                    @include('dashboard.karyawan.dashboard')
                </div>

                <!-- Laporan Tab -->
                <div x-show="activeTab === 'laporan'" x-cloak>
                    @include('dashboard.karyawan.laporan')
                </div>

                <!-- Job Tab -->
                <div x-show="activeTab === 'job'" x-cloak>
                    @include('dashboard.karyawan.job')
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('karyawanDashboard', () => ({
            activeTab: 'dashboard'
        }));
    });
</script>
@endsection


