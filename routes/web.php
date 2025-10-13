<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManajemenController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanKaryawanController;
use App\Http\Controllers\JobPekerjaanController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAtasan()) {
            return redirect()->route('atasan.dashboard');
        } else {
            return redirect()->route('karyawan.dashboard');
        }
    })->name('dashboard');

    Route::get('/atasan/dashboard', [DashboardController::class, 'adminIndex'])->name('atasan.dashboard');
    Route::get('/karyawan/dashboard', [DashboardController::class, 'kelompokIndex'])->name('karyawan.dashboard');
    
    // Laporan Routes
    Route::get('/kelompok/laporan', [App\Http\Controllers\LaporanKaryawanController::class, 'index'])->name('kelompok.laporan');
    
    // Job Pekerjaan Routes
    Route::get('/kelompok/job-pekerjaan', function () {
        return view('dashboard.job-pekerjaan');
    })->name('kelompok.job-pekerjaan');

    // Atasan Management Routes
    Route::get('/atasan/manajemen', [ManajemenController::class, 'index'])->name('atasan.manajemen');
    Route::get('/atasan/kelompok', [ManajemenController::class, 'kelompok'])->name('atasan.kelompok');
    Route::get('/atasan/karyawan', [ManajemenController::class, 'karyawan'])->name('atasan.karyawan');

    Route::get('/atasan/pemantauan-laporan', [App\Http\Controllers\PemantauanLaporanController::class, 'index'])->name('atasan.pemantauan-laporan');

    Route::get('/atasan/statistik-prediksi', [App\Http\Controllers\PrediksiController::class, 'index'])->name('atasan.statistik-prediksi');
    
    // Excel Management Routes
    Route::get('/atasan/excel', [App\Http\Controllers\ExcelController::class, 'index'])->name('atasan.excel.index');
    Route::get('/atasan/excel/upload', [App\Http\Controllers\ExcelController::class, 'upload'])->name('atasan.excel.upload');
    Route::get('/atasan/excel/create', [App\Http\Controllers\ExcelController::class, 'create'])->name('atasan.excel.create');
    
    // Admin Settings Routes
    Route::get('/atasan/pengaturan', [App\Http\Controllers\SettingsController::class, 'adminIndex'])->name('atasan.settings');
    
    // Export Data Routes
    Route::get('/atasan/export-data', [App\Http\Controllers\ExportDataController::class, 'index'])->name('atasan.export-data');
    
    // Kelompok Settings Routes
    Route::get('/kelompok/pengaturan', [App\Http\Controllers\SettingsController::class, 'kelompokIndex'])->name('kelompok.settings');
    
    // Dashboard Routes (duplicate removed - already defined above)

// Test route
Route::get('/test-modal', function () {
    return view('test-modal');
});

// Simple test route
Route::get('/test-simple', function () {
    return view('test-simple');
});


    // API Routes for AJAX
    Route::prefix('api')->middleware(['auth'])->group(function () {
        // Kelompok Routes
        Route::get('/kelompok', [KelompokController::class, 'index']);
        Route::post('/kelompok', [KelompokController::class, 'store']);
        Route::put('/kelompok/{id}', [KelompokController::class, 'update']);
        Route::delete('/kelompok/{id}', [KelompokController::class, 'destroy']);

        // Karyawan Routes
        Route::get('/karyawan', [KaryawanController::class, 'index']);
        Route::post('/karyawan', [KaryawanController::class, 'store']);
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);

        // Laporan Karyawan Routes - moved to api.php
        
        // Pemantauan Laporan Routes
        Route::get('/laporan-karyawan/statistics', [App\Http\Controllers\PemantauanLaporanController::class, 'getStatistics']);
        Route::get('/laporan-karyawan/{id}/dokumentasi', [App\Http\Controllers\PemantauanLaporanController::class, 'getDokumentasi']);
        Route::get('/export/laporan-karyawan', [App\Http\Controllers\PemantauanLaporanController::class, 'exportExcel']);

        // Job Pekerjaan Routes
        Route::get('/job-pekerjaan', [JobPekerjaanController::class, 'index']);
        Route::post('/job-pekerjaan', [JobPekerjaanController::class, 'store']);
        Route::get('/job-pekerjaan/{id}', [JobPekerjaanController::class, 'show']);
        Route::put('/job-pekerjaan/{id}', [JobPekerjaanController::class, 'update']);
        Route::delete('/job-pekerjaan/{id}', [JobPekerjaanController::class, 'destroy']);

        // Prediksi Routes
        Route::get('/prediksi', [PrediksiController::class, 'index']);
        Route::post('/prediksi/generate', [App\Http\Controllers\PrediksiController::class, 'generate']);
        Route::delete('/prediksi/{id}', [App\Http\Controllers\PrediksiController::class, 'destroy']);

        // Statistics Routes
        Route::get('/statistik/overview', [App\Http\Controllers\PrediksiController::class, 'getOverview']);
        Route::get('/statistik/ranking', [App\Http\Controllers\PrediksiController::class, 'getRanking']);
        Route::get('/statistik/comparison', [App\Http\Controllers\PrediksiController::class, 'getComparison']);

        // Chart Routes
        Route::get('/charts/performa', [App\Http\Controllers\PrediksiController::class, 'getChartPerforma']);
        Route::get('/charts/distribusi', [App\Http\Controllers\PrediksiController::class, 'getChartDistribusi']);
        Route::get('/charts/perbandingan', [App\Http\Controllers\PrediksiController::class, 'getChartPerbandingan']);

        // Excel Routes
        Route::post('/excel/upload', [App\Http\Controllers\ExcelController::class, 'store']);
        Route::post('/excel/generate', [App\Http\Controllers\ExcelController::class, 'generate']);
        Route::get('/excel/download/{fileName}', [App\Http\Controllers\ExcelController::class, 'download']);
        Route::delete('/excel/delete/{fileName}', [App\Http\Controllers\ExcelController::class, 'destroy']);
        Route::get('/excel/files', [App\Http\Controllers\ExcelController::class, 'getFiles']);
        
        // Test route
        Route::get('/excel/test', function() {
            return response()->json(['message' => 'API Excel test route working']);
        });

        // Admin Settings API Routes
        Route::post('/admin/settings', [App\Http\Controllers\SettingsController::class, 'updateAdminSettings']);
        Route::post('/admin/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile']);
        Route::get('/admin/system-stats', [App\Http\Controllers\SettingsController::class, 'getSystemStats']);
        Route::post('/admin/backup', [App\Http\Controllers\SettingsController::class, 'backupSystem']);
        Route::post('/admin/restore', [App\Http\Controllers\SettingsController::class, 'restoreSystem']);

        // Kelompok Settings API Routes
        Route::post('/kelompok/settings', [App\Http\Controllers\SettingsController::class, 'updateKelompokSettings']);
        Route::post('/kelompok/account', [App\Http\Controllers\SettingsController::class, 'updateAccount']);
        Route::post('/kelompok/notifications', [App\Http\Controllers\SettingsController::class, 'updateNotifications']);
        Route::post('/kelompok/work-schedule', [App\Http\Controllers\SettingsController::class, 'updateWorkSchedule']);
        Route::get('/kelompok/monthly-reports', [App\Http\Controllers\SettingsController::class, 'getMonthlyReports']);

        // Dashboard API Routes
        Route::get('/admin/dashboard-stats', [App\Http\Controllers\DashboardController::class, 'getAdminStats']);
        Route::get('/admin/dashboard-charts', [App\Http\Controllers\DashboardController::class, 'getAdminChartData']);
        Route::get('/kelompok/dashboard-stats', [App\Http\Controllers\DashboardController::class, 'getKelompokStats']);
        Route::get('/kelompok/dashboard-charts', [App\Http\Controllers\DashboardController::class, 'getKelompokChartData']);

        // Laporan API Routes - moved to api.php

        // Export Routes
        Route::get('/export/all', [ExportController::class, 'exportAllData']);
        Route::get('/export/kelompok', [ExportController::class, 'exportByKelompok']);
        Route::get('/export/my-kelompok', [ExportController::class, 'exportKelompokData']);
        
        // Export Data Routes
        Route::get('/export-data/all', [App\Http\Controllers\ExportDataController::class, 'exportAllData']);
        Route::get('/export-data/kelompok', [App\Http\Controllers\ExportDataController::class, 'exportByKelompok']);
        Route::get('/kelompok', [App\Http\Controllers\ExportDataController::class, 'getKelompokList']);
    });
});
