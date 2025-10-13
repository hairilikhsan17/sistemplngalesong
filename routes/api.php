<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for Laporan Karyawan
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/laporan-karyawan', [App\Http\Controllers\LaporanKaryawanController::class, 'getLaporans']);
    Route::post('/laporan-karyawan', [App\Http\Controllers\LaporanKaryawanController::class, 'store']);
    Route::get('/laporan-karyawan/{id}', [App\Http\Controllers\LaporanKaryawanController::class, 'show']);
    Route::put('/laporan-karyawan/{id}', [App\Http\Controllers\LaporanKaryawanController::class, 'update']);
    Route::delete('/laporan-karyawan/{id}', [App\Http\Controllers\LaporanKaryawanController::class, 'destroy']);
    Route::get('/laporan-karyawan/{id}/download', [App\Http\Controllers\LaporanKaryawanController::class, 'downloadFile']);
    
    // API Routes for Karyawan
    Route::get('/karyawan', [App\Http\Controllers\KaryawanController::class, 'index']);
    Route::post('/karyawan', [App\Http\Controllers\KaryawanController::class, 'store']);
    Route::get('/karyawan/{id}', [App\Http\Controllers\KaryawanController::class, 'show']);
    Route::put('/karyawan/{id}', [App\Http\Controllers\KaryawanController::class, 'update']);
    Route::delete('/karyawan/{id}', [App\Http\Controllers\KaryawanController::class, 'destroy']);
    
    // API Routes for Job Pekerjaan
    Route::get('/job-pekerjaan', [App\Http\Controllers\JobPekerjaanController::class, 'index']);
    Route::post('/job-pekerjaan', [App\Http\Controllers\JobPekerjaanController::class, 'store']);
    Route::get('/job-pekerjaan/{id}', [App\Http\Controllers\JobPekerjaanController::class, 'show']);
    Route::put('/job-pekerjaan/{id}', [App\Http\Controllers\JobPekerjaanController::class, 'update']);
    Route::delete('/job-pekerjaan/{id}', [App\Http\Controllers\JobPekerjaanController::class, 'destroy']);
});
