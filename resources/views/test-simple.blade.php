<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Alpine.js</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6" x-data="{ showModal: false }">
        <h1 class="text-2xl font-bold mb-4">Test Alpine.js Modal</h1>
        
        <div class="space-y-4">
            <button @click="showModal = true" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Buka Modal
            </button>
            
            <p class="text-sm text-gray-600">
                Status Modal: <span x-text="showModal ? 'TERBUKA' : 'TERTUTUP'" class="font-bold"></span>
            </p>
            
            <button @click="alert('Alpine.js berfungsi!')" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Test Alert
            </button>
        </div>
        
        <!-- Modal -->
        <div x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Modal Test</h3>
                        <p>Modal berfungsi dengan baik!</p>
                        <p class="text-sm text-gray-600 mt-2">Alpine.js sudah terintegrasi dengan benar.</p>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showModal = false" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-md mx-auto mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Test Console</h2>
        <p class="text-sm text-gray-600 mb-4">Buka Developer Tools (F12) dan lihat Console untuk debug info.</p>
        <button onclick="console.log('JavaScript works!')" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
            Test Console Log
        </button>
    </div>
    
    <script>
        console.log('Test page loaded');
        console.log('Alpine.js available:', typeof Alpine !== 'undefined');
    </script>
</body>
</html>



