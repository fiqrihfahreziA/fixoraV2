<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\PpkController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;



// Route::get('/admin/login', function () {
//     return view('auth.admin-login');
// })->name('admin.login');

// Route::get('/admin/login', [AdminController::class, 'login'])
//             ->name('admin.login');

Route::middleware(['auth', 'role:ppk'])
    ->prefix('ppk')
    ->name('ppk.')
    ->group(function () {
        Route::get('/', [PpkController::class, 'index'])
            ->name('dashboard');
     Route::get('/pengadaan/{id}', [PpkController::class, 'detail'])->name('pengadaan.detail');
       // Aksi verifikasi (PUT)
    Route::put('/verifikasi/{id}', [PpkController::class, 'verifikasi'])
            ->name('verifikasi');

    });

