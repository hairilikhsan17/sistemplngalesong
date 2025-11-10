@extends('layouts.app')

@section('title', 'Login - PLN Galesong')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center p-4 sm:p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-6 sm:p-8">
        <div class="flex items-center justify-center mb-8">
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-4 rounded-xl">
                <i data-lucide="zap" class="w-12 h-12 text-white"></i>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">
            PLN Galesong
        </h1>
        <p class="text-center text-gray-600 mb-8">
            Sistem Prediksi Waktu Penyelesaian Kegiatan Lapangan
        </p>

        <form method="POST" action="{{ route('login') }}" class="space-y-6" autocomplete="off">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ $errors->first('username') ?: $errors->first() }}
                </div>
            @endif

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all @error('username') border-red-500 @enderror"
                    placeholder="Masukkan username"
                    required
                    autofocus
                    autocomplete="off"
                    value=""
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                    placeholder="Masukkan password"
                    required
                    autocomplete="off"
                    value=""
                >
            </div>

            <button
                type="submit"
                class="w-full bg-gradient-to-r from-amber-500 to-orange-600 text-white py-3 rounded-lg font-semibold hover:from-amber-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl"
            >
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>PLN Unit Induk Distribusi Sulselrabar</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clear all form fields when page loads
    document.getElementById('username').value = '';
    document.getElementById('password').value = '';
    
    // Prevent browser autofill
    setTimeout(function() {
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
    }, 100);
    
    // Clear fields on page focus (in case browser tries to autofill)
    window.addEventListener('focus', function() {
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
    });
});
</script>
@endsection


