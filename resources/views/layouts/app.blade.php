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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    @yield('content')
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // CSRF token for AJAX requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Utility functions
        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-md z-50 p-3 sm:p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'info' ? 'bg-blue-500 text-white' :
                'bg-gray-500 text-white'
            }`;
            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
        
        function confirmDelete(message = 'Yakin ingin menghapus?') {
            return confirm(message);
        }
    </script>
    
    @stack('scripts')
</body>
</html>
