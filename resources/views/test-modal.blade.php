@extends('layouts.app')

@section('title', 'Test Modal')

@section('content')
<div class="p-6" x-data="{ showModal: false }">
    <h1 class="text-2xl font-bold mb-4">Test Modal</h1>
    
    <button @click="showModal = true" class="bg-blue-500 text-white px-4 py-2 rounded">
        Buka Modal
    </button>
    
    <p class="mt-4">Modal Status: <span x-text="showModal"></span></p>
    
    <!-- Modal -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Test Modal</h3>
                    <p>Modal berfungsi dengan baik!</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showModal = false" class="bg-red-500 text-white px-4 py-2 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



