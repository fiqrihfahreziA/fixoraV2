<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\direkturController;

// Route::get('/admin/login', function () {
//     return view('auth.admin-login');
// })->name('admin.login');

// Route::get('/admin/login', [AdminController::class, 'login'])
//             ->name('admin.login');

Route::middleware(['auth', 'role:direktur'])
    ->prefix('direktur')
    ->name('direktur.')
    ->group(function () {

        Route::get('/p', [direkturController::class, 'index'])
            ->name('dashboard');
         
        Route::get('/', [direkturController::class, 'showpengadaan'])
            ->name('pengadaan');
        Route::get('/pengadaan/{id}/detail', [direkturController::class, 'detail'])->name('pengadaan.detail');
        
        Route::put('/pengadaan/{id}/setujui', [direkturController::class, 'setujui'])->name('setujui');
        Route::put('/pengadaan/{id}/setujui-sebagian', [direkturController::class, 'setujuiSebagian'])->name('setujui.sebagian');
        Route::put('/pengadaan/{id}/tunda', [direkturController::class, 'tunda'])->name('tunda');
        Route::put('/pengadaan/{id}/tolak', [direkturController::class, 'tolak'])->name('tolak');
        Route::get('pengadaan/{id}/cetak', [direkturController::class, 'cetak'])->name('pengadaan.cetak');
        Route::get('/reporth/pengadaan', [direkturController::class, 'reportPengadaan'])->name('reportPengadaan');
        Route::get('/pengadaannn/export', [direkturController::class, 'exportExcel'])
            ->name('export.csv');
             Route::get('/profile-saya', [direkturController::class, 'editprofile'])->name('profile.karyawan.edit');
         Route::put('/profile-saya', [direkturController::class, 'updateprofile'])->name('profile.karyawan.update');
      

    });

