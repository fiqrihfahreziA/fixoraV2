<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;



// Route::get('/admin/login', function () {
//     return view('auth.admin-login');
// })->name('admin.login');

// Route::get('/admin/login', [AdminController::class, 'login'])
//             ->name('admin.login');

Route::middleware(['auth', 'role:penerima'])
    ->prefix('penerima')
    ->name('penerima.')
    ->group(function () {

    
        Route::get('/d', [PenerimaController::class, 'index'])
            ->name('dashboard');

        Route::get('/chart', [PenerimaController::class, 'chart'])
            ->name('chart');

         
        Route::get('/', [PenerimaController::class, 'showpermintaan'])
            ->name('permintaan');
            
        Route::post('/permintaann', [PenerimaController::class, 'storebarang'])->name('permintaan.store');

        // Rute untuk halaman edit permintaan
        Route::get('/permintaann/edit/{id}', [PenerimaController::class, 'edit'])->name('permintaan.edit');

        Route::put('/permintaanne/update/{id}', [PenerimaController::class, 'updatee'])->name('permintaan.update');
        // Route::delete('/penerima/permintaann/{id}', [PenerimaController::class, 'destroypermintaan'])->name('permintaan.destroy');
         Route::get('/laporann', [PenerimaController::class, 'laporan'])->name('laporan');
         
        Route::get('/penerima/permintaan/{id}/view', [PenerimaController::class, 'view'])
            ->name('permintaan.view');
        Route::delete('/permintaan-penerima/{id}',[PenerimaController::class, 'destroy'])->name('permintaan.destroyy');

        Route::get('/penerima/preview', [PenerimaController::class, 'preview'])->name('preview');

        Route::get('/penerima/export-csv', [PenerimaController::class, 'exportCsv'])
    ->name('export.csv');

        Route::get('/notif-pending', function () {
            $bidangId = Auth::user()->karyawan->bidang_id ?? null;


        return response()->json([
            'permintaan' => \App\Models\RequestModel::where('request_type','permintaan')
                ->where('status','pending')
                  ->where('bidang_id', $bidangId)
                ->count(),

            'perbaikan' => \App\Models\RequestModel::where('request_type','perbaikan')
                ->where('status','pending')
                  ->where('bidang_id', $bidangId)
                ->count(),
                ]);
            });

            Route::get('permintaan/{id}/gambar', [PemohonController::class, 'lihatGambar'])
             ->name('permintaan.gambar');

           

    // PENGADAAN
      Route::get('/pengadaan/export', [PenerimaController::class, 'exportExcel'])
    ->name('pengadaan.export');
      Route::get('/', [PenerimaController::class, 'chartpengadaan'])
            ->name('chartp');
       // Detail pengajuan
    // Detail pengajuan
    Route::get('/pengadaan/{id}', [PenerimaController::class, 'showpengadaan'])->name('pengadaan.show');
    
    // ===== UPDATE PENGAJUAN (TERIMA/TOLAK) =====
    // Route::put('/pengadaan/{id}', [PenerimaController::class, 'update'])->name('pengadaan.update');
     Route::put('/pengadaan/{id}/verifikasi', [PenerimaController::class, 'verifikasi'])
        ->name('pengadaan.verifikasi');

    Route::get('/report/pengadaan', [PenerimaController::class, 'reportPengadaan'])->name('reportPengadaan');

    Route::get('/chartss/pengadaan', [PenerimaController::class, 'chartspengadaan'])->name('chartPengadaan');
  
    });

