<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\BarangKeluarController;
use App\Http\Controllers\Api\PermintaanBarangController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (JWT Guard api)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    // Auth profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Kelola Akun User (Super Admin only)
    Route::apiResource('users', UserController::class);

    // Kelola Barang Masuk
    Route::apiResource('barangs', BarangController::class);

    // Kelola Barang Keluar
    Route::apiResource('barang-keluars', BarangKeluarController::class);

    // Kelola Permintaan Barang
    Route::get('permintaan-barangs/next-no-surat', [PermintaanBarangController::class, 'nextNoSurat']);
    Route::apiResource('permintaan-barangs', PermintaanBarangController::class);
});
