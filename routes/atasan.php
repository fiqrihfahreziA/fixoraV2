<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;



// Route::get('/admin/login', function () {
//     return view('auth.admin-login');
// })->name('admin.login');

// Route::get('/admin/login', [AdminController::class, 'login'])
//             ->name('admin.login');

Route::middleware(['auth', 'role:atasan'])
    ->prefix('atasan')
    ->name('atasan.')
    ->group(function () {

        Route::get('/p', [AtasanController::class, 'index'])
            ->name('dashboard');
         
        Route::get('/permintaann/atasan', [AtasanController::class, 'showpermintaann'])
            ->name('permintaan');
        
        Route::get('/permintaann/edit/{id}', [AtasanController::class, 'edit'])->name('permintaan.edit');
        Route::put('/permintaannea/update/{id}', [AtasanController::class, 'update'])->name('permintaan.update');   
      
        Route::get('/atasan/permintaan/{id}/view', [AtasanController::class, 'view'])
            ->name('permintaan.view');
        Route::get('permintaan/{id}/gambar', [AtasanController::class, 'lihatGambar'])->name('permintaan.gambar');
        // Route::delete('/permintaan-penerima/{id}',[PenerimaController::class, 'destroy'])->name('permintaan.destroyy');

        // pengadaan
        Route::get('/pengadaan/export', [AtasanController::class, 'exportExcel'])
    ->name('pengadaan.export');
        Route::get('/pengadaan/chart', [AtasanController::class, 'chartIndex'])
            ->name('pengadaan.chart');

            // Route untuk mendapatkan data chart (AJAX)
        Route::get('/pengadaan/chart-data', [AtasanController::class, 'getChartData'])
                ->name('pengadaan.chart-data');

        Route::get('/', [AtasanController::class, 'pengadaanshow'])
            ->name('pengadaan');
        
        Route::get('/pengadaan/{id}', [AtasanController::class, 'showpengadaan'])->name('pengadaan.show');
        Route::put('/pengadaan/{id}/verifikasi', [AtasanController::class, 'verifikasi'])
            ->name('pengadaan.verifikasi');
      
        Route::get('/reporta/pengadaan', [AtasanController::class, 'reportPengadaan'])->name('reportPengadaan');

        Route::get('/chartssa/pengadaan', [AtasanController::class, 'chartspengadaan'])->name('chartPengadaan');
        Route::get('/atasan/pengadaan/{id}/edit-item', [AtasanController::class, 'editItem'])
        ->name('pengadaan.editItem');

        Route::put('/pengadaan/{id}/update-item', [AtasanController::class, 'updateItem'])
            ->name('pengadaan.updateItem');

    });

