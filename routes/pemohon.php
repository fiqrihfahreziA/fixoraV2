<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\direkturController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;



// Route::get('/admin/login', function () {
//     return view('auth.admin-login');
// })->name('admin.login');

// Route::get('/admin/login', [AdminController::class, 'login'])
//             ->name('admin.login');

Route::middleware(['auth', 'role:pemohon'])
    ->prefix('pemohon')
    ->name('pemohon.')
    ->group(function () {

        Route::get('/p', [PemohonController::class, 'index'])
            ->name('dashboard');
 
        Route::get('/permintaan', [PemohonController::class, 'showpermintaan'])
            ->name('permintaan');
            
        Route::post('/permintaan', [PemohonController::class, 'storebarang'])->name('permintaan.store');

        // Rute untuk halaman edit permintaan
        Route::get('/permintaan/edit/{id}', [PemohonController::class, 'edit'])->name('permintaan.edit');


        Route::put('/permintaan/update/{id}', [PemohonController::class, 'update'])->name('permintaan.update');
        Route::delete('/pemohon/permintaan/{id}', [PemohonController::class, 'destroypermintaan'])->name('permintaan.destroy');

        Route::get('permintaan/{id}/gambar', [PemohonController::class, 'lihatGambar'])->name('permintaan.gambar');
        
        //pengadaan 
        
        Route::get('/', [PemohonController::class, 'showpengadaan'])
            ->name('pengadaan');
        Route::get('pengadaan/create', [PemohonController::class, 'create'])->name('pengadaan.create');
      

        Route::post('/pemohon/pengadaan/store', [PemohonController::class, 'storePengadaan'])
            ->name('pengadaan.store');
        
             // Submit pengajuan (draft -> diajukan)
             // ✅ ROUTE SUBMIT - PASTIKAN INI ADA
             Route::post('/pengadaan/{id}/submit', [PemohonController::class, 'submit'])
        ->name('pengadaan.submit');

         Route::get('/pengadaan/{id}/edit', [PemohonController::class, 'edittt'])
        ->name('pengadaan.edit');
    
    Route::put('/pengadaan/{id}', [PemohonController::class, 'updatePengadaan'])
        ->name('pengadaan.update');
     Route::get('pengadaan/{id}/cetak', [direkturController::class, 'cetak'])->name('pengadaan.cetak');
     Route::put('/pengadaan/{id}/selesai', [PemohonController::class, 'selesai'])->name('pengadaan.selesai');
 

    });

