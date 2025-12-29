<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <title>@yield('title', 'PLN Galesong')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Chart.js - Load before Alpine.js to ensure it's available -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Verify Chart.js is loaded immediately
        (function() {
            function checkChartJs() {
                if (typeof Chart !== 'undefined') {
                    console.log('✅ Chart.js loaded successfully, version:', Chart.version || 'unknown');
                    window.chartJsLoaded = true;
                } else {
                    console.warn('⚠️ Chart.js not yet loaded, will check again on window load');
                    window.chartJsLoaded = false;
                }
            }
            
            // Check immediately
            checkChartJs();
            
            // Also check on window load
            window.addEventListener('load', function() {
                checkChartJs();
            });
        })();
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* SweetAlert2 Custom Styling */
        .swal2-backdrop-show {
            backdrop-filter: blur(8px);
            background: rgba(0, 0, 0, 0.4) !important;
        }
        
        .swal2-popup {
            border-radius: 1rem !important;
            padding: 2rem !important;
        }
        
        .swal2-title {
            font-weight: 700 !important;
            color: #1f2937 !important;
        }
        
        .swal2-html-container {
            color: #4b5563 !important;
        }
        
        .swal2-confirm {
            background-color: #f59e0b !important; /* amber-500 */
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            padding: 0.75rem 1.5rem !important;
        }
        
        .swal2-cancel {
            background-color: #ef4444 !important; /* red-500 */
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            padding: 0.75rem 1.5rem !important;
        }

        .swal2-deny {
            background-color: #6b7280 !important; /* gray-500 */
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            padding: 0.75rem 1.5rem !important;
        }
        
        /* Custom Animations */
        .swal2-show {
            animation: swal2-show 0.3s ease-out !important;
        }
        
        @keyframes swal2-show {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-gray-50" 
      x-data="{ sidebarOpen: false }" 
      @keydown.escape="sidebarOpen = false"
      @close-sidebar.window="sidebarOpen = false">
    <!-- Sidebar - Must be before overlay in DOM for proper z-index stacking -->
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0"
           aria-label="Sidebar"
           @click.stop>
        @include('layouts.sidebar')
    </aside>
    
    <!-- Mobile Sidebar Overlay - Behind sidebar, covers only area to the right -->
    <div id="overlay" 
         x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-y-0 left-64 right-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
         @click="sidebarOpen = false"
         x-cloak>
    </div>
    
    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Navigation Bar -->
        <div class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-14 sm:h-16 px-3 sm:px-4 lg:px-8">
                <!-- Mobile menu button -->
                <button id="openSidebar"
                        @click="sidebarOpen = !sidebarOpen" 
                        class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500 min-w-[44px] min-h-[44px] flex items-center justify-center relative z-50"
                        aria-label="Toggle sidebar"
                        aria-expanded="false"
                        :aria-expanded="sidebarOpen"
                        type="button">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                
                <!-- Page Title -->
                <div class="flex-1 ml-2 sm:ml-0">
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-900 truncate">@yield('page-title', 'Dashboard')</h1>
                </div>
                
                <!-- Right side items -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Notifications -->
                    <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 min-w-[44px] min-h-[44px] flex items-center justify-center">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @click.away="open = false"
                                class="flex items-center space-x-1 sm:space-x-2 p-1.5 sm:p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 min-h-[44px]">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset(Auth::user()->avatar) }}" 
                                         alt="{{ Auth::user()->name ?? Auth::user()->username }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    @if(Auth::user()->role === 'atasan')
                                        <div class="w-full h-full bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                            <i data-lucide="user" class="w-4 h-4 text-white"></i>
                                        </div>
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <i data-lucide="users" class="w-4 h-4 text-white"></i>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <span class="hidden sm:block text-sm font-medium truncate max-w-[120px]">
                                @if(Auth::user()->role === 'atasan')
                                    {{ Auth::user()->name ?? Auth::user()->username }}
                                @else
                                    {{ Auth::user()->kelompok->nama_kelompok ?? Auth::user()->username }}
                                @endif
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 hidden sm:block"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                            @if(Auth::user()->role === 'atasan')
                                <a href="{{ route('atasan.settings') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                   @click="open = false">
                                    <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                    Profil
                                </a>
                                <a href="{{ route('atasan.settings') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                   @click="open = false">
                                    <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                                    Pengaturan
                                </a>
                            @else
                                <a href="{{ route('kelompok.settings') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                   @click="open = false">
                                    <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                                    Profil Kelompok
                                </a>
                                <a href="{{ route('kelompok.settings') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                   @click="open = false">
                                    <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                                    Pengaturan
                                </a>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <main class="flex-1 min-h-screen">
            <div class="py-4 sm:py-6">
                <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // CSRF token for AJAX requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Re-initialize Lucide icons after Alpine loads (for icons in sidebar)
        document.addEventListener('alpine:init', function() {
            setTimeout(function() {
                lucide.createIcons();
            }, 100);
        });
        
        // SweetAlert2 Helper
        const SwalHelper = {
            success: (title, text) => {
                return Swal.fire({
                    title: title || 'Berhasil 🎉',
                    text: text,
                    icon: 'success',
                    iconColor: '#10b981',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#f59e0b',
                    timer: 3000,
                    timerProgressBar: true
                });
            },
            error: (title, text) => {
                return Swal.fire({
                    title: title || 'Gagal ❌',
                    text: text || 'Terjadi kesalahan. Silakan coba lagi.',
                    icon: 'error',
                    iconColor: '#ef4444',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#6b7280'
                });
            },
            confirmDelete: (title, text) => {
                return Swal.fire({
                    title: title || '⚠️ Konfirmasi Penghapusan',
                    text: text || 'Yakin mau hapus data ini? Data yang sudah dihapus nggak bisa dikembalikan.',
                    icon: 'warning',
                    iconColor: '#f59e0b',
                    showCancelButton: true,
                    confirmButtonText: '🗑️ Hapus',
                    cancelButtonText: '❌ Batal',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true
                });
            },
            update: (title, text) => {
                return Swal.fire({
                    title: title || 'Perubahan Disimpan ✔️',
                    text: text || 'Data berhasil diperbarui.',
                    icon: 'success',
                    iconColor: '#10b981',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#f59e0b',
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        };

        // Utility functions (Legacy support & Global access)
        function showAlert(message, type = 'success') {
            if (type === 'success') {
                SwalHelper.success('Berhasil 🎉', message);
            } else if (type === 'error') {
                SwalHelper.error('Gagal ❌', message);
            } else {
                Swal.fire({
                    text: message,
                    icon: type,
                    confirmButtonColor: '#f59e0b'
                });
            }
        }
        
        function confirmDelete(message = 'Yakin ingin menghapus?') {
            // Note: This legacy function returns a boolean for synchronous confirm()
            // For SweetAlert2, we should use SwalHelper.confirmDelete().then(...)
            console.warn('confirmDelete() is deprecated. Use SwalHelper.confirmDelete() instead.');
            return confirm(message);
        }

        // Auto-show session messages
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                SwalHelper.success('Berhasil 🎉', "{{ session('success') }}");
            @endif

            @if(session('error'))
                SwalHelper.error('Gagal ❌', "{{ session('error') }}");
            @endif

            @if(session('status'))
                SwalHelper.update('Perubahan Disimpan ✔️', "{{ session('status') }}");
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>



