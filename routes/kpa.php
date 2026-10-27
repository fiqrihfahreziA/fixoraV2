<?php

use App\Http\Controllers\KpaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


Route::middleware(['auth', 'role:kpa'])
    ->prefix('kpa')
    ->name('kpa.')
    ->group(function () {
        Route::get('/', [KpaController::class, 'index'])
            ->name('dashboard');
     Route::get('/pengadaan/{id}', [KpaController::class, 'detail'])->name('pengadaan.detail');
       // Aksi verifikasi (PUT)
    Route::put('/verifikasi/{id}', [KpaController::class, 'verifikasi'])->name('verifikasi');
    //export excel
    Route::get('/kpa/pengadaan/export', [KpaController::class, 'exportExcel'])->name('pengadaan.export');
    Route::get('/kpa/reporta/pengadaan', [KpaController::class, 'reportPengadaan'])->name('reportPengadaan');
    });
