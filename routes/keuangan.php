<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\keuanganController;

// Route::get('/admin/login', function () {
//     return view('auth.admin-login');
// })->name('admin.login');

// Route::get('/admin/login', [AdminController::class, 'login'])
//             ->name('admin.login');

Route::middleware(['auth', 'role:keuangan'])
    ->prefix('keuangan')
    ->name('keuangan.')
    ->group(function () {

        Route::get('/p', [keuanganController::class, 'index'])
            ->name('dashboard');
         
        Route::get('/', [keuanganController::class, 'showpengadaan'])
            ->name('pengadaan');
        Route::get('/pengadaan/{id}/detail', [KeuanganController::class, 'detail'])->name('pengadaan.detail');
        Route::put('keuangan/verifikasi/lengkap/{id}', [KeuanganController::class, 'verifikasiLengkap'])->name('verifikasi.lengkap');
        Route::put('keuangan/verifikasi/sebagian/{id}', [KeuanganController::class, 'verifikasiSebagian'])->name('verifikasi.sebagian');
        Route::put('keuangan/tolak/{id}', [KeuanganController::class, 'tolak'])->name('tolak');
        Route::get('/report/pengadaan', [KeuanganController::class, 'reportPengadaan'])->name('reportPengadaan');
        Route::get('/pengadaan/export', [KeuanganController::class, 'exportExcel'])
            ->name('export.csv');

    });

