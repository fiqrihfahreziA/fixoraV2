<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TelegramController;


Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::get('/admin/login', [AdminController::class, 'login'])
            ->name('admin.login');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/karyawan', [AdminController::class, 'karyawan'])
            ->name('karyawan');
        
        Route::post('/karyawan', [AdminController::class, 'store'])
            ->name('karyawan.store');

        Route::put('/karyawan/{id}', [AdminController::class, 'update'])
            ->name('karyawan.update');
        
        Route::delete('/karyawan/{id}', [AdminController::class, 'destroy'])
            ->name('karyawan.destroy');
        
        Route::get('/pengguna', [AdminController::class, 'akun'])
            ->name('pengguna');

        Route::post('/pengguna', [AdminController::class, 'storeuser'])
            ->name('pengguna.store');
        
        Route::post('/bidang', [AdminController::class, 'storebidang'])
            ->name('bidang.store');

        Route::delete('/pengguna/{id}', [AdminController::class, 'destroyakun'])
            ->name('users.destroy');
        
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])
            ->name('users.update');

            Route::get('/admin/users/export', [AdminController::class, 'exportAkun'])
            ->name('users.export');



    });

Route::post('/telegram/webhook', [
    TelegramController::class,
    'webhook'
])->name('telegram.webhook');