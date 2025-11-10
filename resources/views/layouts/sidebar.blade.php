<!-- Sidebar Component -->
<div class="h-full flex flex-col">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 bg-gradient-to-r from-amber-600 to-orange-600 flex-shrink-0 relative">
        <div class="flex items-center flex-1 min-w-0">
            <div class="flex-shrink-0">
                <i data-lucide="zap" class="w-8 h-8 text-white"></i>
            </div>
            <div class="ml-3 min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-white truncate">PLN Galesong</h1>
                <p class="text-xs text-amber-100 hidden sm:block">Sistem Prediksi</p>
            </div>
        </div>
        <button x-on:click.stop.prevent="$dispatch('close-sidebar')"
                x-on:touchstart.stop
                x-on:touchend.stop.prevent="$dispatch('close-sidebar')"
                class="lg:hidden text-white hover:text-amber-200 p-2 rounded-md hover:bg-amber-700 active:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-white transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center ml-2 cursor-pointer select-none"
                aria-label="Close sidebar"
                type="button"
                style="touch-action: manipulation; -webkit-tap-highlight-color: rgba(255,255,255,0.3); position: relative; z-index: 100;">
            <i data-lucide="x" class="w-6 h-6 pointer-events-none"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="px-6 py-4 bg-gray-50 border-b">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->role === 'atasan')
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                                 alt="{{ Auth::user()->name ?? Auth::user()->username }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-white"></i>
                            </div>
                        @endif
                    @else
                        @if(Auth::user()->kelompok && Auth::user()->kelompok->avatar)
                            <img src="{{ asset('storage/avatars/' . Auth::user()->kelompok->avatar) }}" 
                                 alt="{{ Auth::user()->kelompok->nama_kelompok }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5 text-white"></i>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="ml-3">
                @if(Auth::user()->role === 'atasan')
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? Auth::user()->username }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                @else
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->kelompok->nama_kelompok ?? Auth::user()->username }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    @if(Auth::user()->kelompok)
                        <p class="text-xs text-gray-500">{{ Auth::user()->kelompok->shift }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6 px-3 flex-1 overflow-y-auto">
        @if(Auth::user()->isAtasan())
            <!-- Atasan Menu -->
            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('atasan.dashboard') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors min-h-[44px] {{ request()->routeIs('atasan.dashboard') ? 'bg-amber-100 text-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Manajemen Kelompok & Karyawan -->
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                            Manajemen
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="ml-6 space-y-1">
                        <a href="{{ route('atasan.manajemen') }}" 
                           x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('atasan.manajemen') ? 'bg-amber-50 text-amber-700' : '' }}">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                            <span>Kelompok & Karyawan</span>
                        </a>
                    </div>
                </div>

                <!-- Pemantauan Laporan -->
                <a href="{{ route('atasan.pemantauan-laporan') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('atasan.pemantauan-laporan') ? 'bg-amber-100 text-amber-700' : '' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Pemantauan Laporan</span>
                </a>

                <!-- Pemantauan Job Pekerjaan -->
                <a href="{{ route('atasan.pemantauan-job-pekerjaan') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('atasan.pemantauan-job-pekerjaan') ? 'bg-amber-100 text-amber-700' : '' }}">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Pemantauan Job Pekerjaan</span>
                </a>

                <!-- Statistik & Prediksi -->
                <a href="{{ route('atasan.statistik-prediksi') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('atasan.statistik-prediksi') ? 'bg-amber-100 text-amber-700' : '' }}">
                    <i data-lucide="trending-up" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Statistik & Prediksi</span>
                </a>

                <!-- Export Data -->
                <a href="{{ route('atasan.export-data') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('atasan.export-data') ? 'bg-amber-100 text-amber-700' : '' }}">
                    <i data-lucide="download" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Export Data</span>
                </a>

                <!-- Upload Excel -->
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center">
                            <i data-lucide="upload" class="w-5 h-5 mr-3"></i>
                            Upload Excel
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="ml-6 space-y-1">
                        <a href="{{ route('atasan.excel.index') }}" 
                           x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px]">
                            <i data-lucide="settings" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                            <span>Manajemen Excel</span>
                        </a>
                        <a href="{{ route('atasan.excel.upload') }}" 
                           x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px]">
                            <i data-lucide="file-plus" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                            <span>Upload Data Bulan Ini</span>
                        </a>
                        <a href="{{ route('atasan.excel.create') }}" 
                           x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px]">
                            <i data-lucide="plus-circle" class="w-4 h-4 mr-3 flex-shrink-0"></i>
                            <span>Buat File Excel Baru</span>
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- Karyawan Menu -->
            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('karyawan.dashboard') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors min-h-[44px] {{ request()->routeIs('karyawan.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Input Laporan -->
                <a href="{{ route('kelompok.laporan') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('kelompok.laporan') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Input Laporan</span>
                </a>

                <!-- Input Job Pekerjaan -->
                <a href="{{ route('kelompok.job-pekerjaan') }}" 
                   x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] {{ request()->routeIs('kelompok.job-pekerjaan') ? 'bg-blue-100 text-blue-700' : '' }}">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span>Input Job Pekerjaan</span>
                </a>

                <!-- Lihat Prediksi -->
                <a href="#" onclick="showTab('prediksi')" 
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="trending-up" class="w-5 h-5 mr-3"></i>
                    Lihat Prediksi
                </a>

                <!-- Export Data Kelompok -->
                <a href="#" onclick="exportKelompokData()" 
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-lucide="download" class="w-5 h-5 mr-3"></i>
                    Export Data Kelompok
                </a>
            </div>
        @endif

        <!-- Divider -->
        <div class="my-6 border-t border-gray-200"></div>

        <!-- Settings -->
        @if(auth()->user()->role === 'atasan')
        <a href="{{ route('atasan.settings') }}" 
           x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
           class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px]">
            <i data-lucide="settings" class="w-5 h-5 mr-3 flex-shrink-0"></i>
            <span>Pengaturan</span>
        </a>
        @else
        <a href="{{ route('kelompok.settings') }}" 
           x-on:click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
           class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px]">
            <i data-lucide="settings" class="w-5 h-5 mr-3 flex-shrink-0"></i>
            <span>Pengaturan</span>
        </a>
        @endif

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" 
                    class="flex items-center w-full px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors min-h-[44px]">
                <i data-lucide="log-out" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                <span>Keluar</span>
            </button>
        </form>
    </nav>

    <!-- Sidebar Footer -->
    <div class="mt-auto p-4 bg-gray-50 border-t">
        <div class="text-xs text-gray-500 text-center">
            <p class="truncate">PLN Unit Induk Distribusi</p>
            <p class="truncate">Sulselrabar</p>
        </div>
    </div>
</div>

<script>
// Global functions for sidebar interactions
function showTab(tabName) {
    // This will be handled by the main dashboard components
    if (typeof window.dashboard !== 'undefined') {
        window.dashboard.activeTab = tabName;
    }
}

function exportAllData() {
    // Export all data functionality
    window.open('/api/export/all', '_blank');
}

function exportByKelompok() {
    // Show modal to select kelompok
    const kelompokId = prompt('Masukkan ID Kelompok untuk export:');
    if (kelompokId) {
        window.open(`/api/export/kelompok?kelompok_id=${kelompokId}`, '_blank');
    }
}

function exportKelompokData() {
    // Export kelompok data for karyawan
    window.open('/api/export/my-kelompok', '_blank');
}

function uploadExcel() {
    // Upload Excel functionality
    showAlert('Fitur upload Excel akan segera tersedia', 'info');
}

function createNewExcel() {
    // Create new Excel file functionality
    showAlert('Fitur buat file Excel baru akan segera tersedia', 'info');
}

// Mobile sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }
}
</script>
