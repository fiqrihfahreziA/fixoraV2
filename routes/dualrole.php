<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DualroleController;

/*
|--------------------------------------------------------------------------
| MULTIROLE — Atasan dengan role tambahan
|--------------------------------------------------------------------------
| /multirole        → halaman pilih mode
| /multirole1/*     → atasan × penerima
| /multirole2/*     → atasan × ppk
| /multirole3/*     → atasan × keuangan
*/

// Halaman pilih mode (semua atasan dengan role2 boleh masuk)
// Route::middleware(['auth', 'dual.role'])
//     ->prefix('multirole')->name('multirole.')
//     ->group(function () {
//         Route::get('/', [DualroleController::class, 'index'])->name('dashboard');
//     });

// ATASAN × PENERIMA
Route::middleware(['auth', 'dual.role:penerima'])
    ->prefix('multirole1')->name('multirole1.')
    ->group(function () {
        Route::get('/atasan', [DualroleController::class, 'atasan'])->name('atasan');
        Route::get('/penerima', [DualroleController::class, 'penerima'])->name('penerima');
    });

// ATASAN × PPK
Route::middleware(['auth', 'dual.role:ppk'])
    ->prefix('multirole2')->name('multirole2.')
    ->group(function () {
        Route::get('/atasan', [DualroleController::class, 'atasan'])->name('atasan');
        Route::get('/ppk', [DualroleController::class, 'ppk'])->name('ppk');
    });

// ATASAN × KEUANGAN
Route::middleware(['auth', 'dual.role:keuangan'])
    ->prefix('multirole3')->name('multirole3.')
    ->group(function () {
        Route::get('/atasan', [DualroleController::class, 'atasan'])->name('atasan');
        Route::get('/keuangan', [DualroleController::class, 'keuangan'])->name('keuangan');
    });