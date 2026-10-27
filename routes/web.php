<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\DualroleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::get('/p', function () {
    return view('welcome');
});



Route::get('/', [AuthenticatedSessionController::class, 'create'])
            ->name('dashboard');


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Halaman pilih mode
Route::middleware(['auth', 'dual.role'])
    ->prefix('multirole')->name('multirole.')
    ->group(function () {
        Route::get('/', [DualroleController::class, 'index'])->name('dashboard');
    });

// ATASAN × PENERIMA
// Route::middleware(['auth', 'dual.role:penerima'])
//     ->prefix('multirole1')->name('multirole1.')
//     ->group(function () {
//         // Kalau mau /multirole1 langsung nampilin halaman atasan:
//         Route::get('/', [DualroleController::class, 'atasan'])->name('root'); 
        
//         Route::get('/atasan', [DualroleController::class, 'atasan'])->name('atasan');
//         Route::get('/penerima', [DualroleController::class, 'penerima'])->name('penerima');
//     });
Route::middleware(['auth', 'dual.role:penerima'])
    ->prefix('multirole1')->name('multirole1.')
    ->group(function () {
        
        // 1. INI UNTUK HALAMAN CARD PILIH MODE (/multirole1)
        Route::get('/', [DualroleController::class, 'index'])->name('dashboard'); 
        
        // 2. INI UNTUK PAS KLIK CARD ATASAN (/multirole1/atasan)
        Route::get('/atasan', [AtasanController::class, 'index'])->name('atasan');
        
        // 3. INI UNTUK PAS KLIK CARD PENERIMA (/multirole1/penerima)
        Route::get('/penerima', [PenerimaController::class, 'Showpermintaan'])->name('penerima');
        
    });

// ATASAN × PPK
Route::middleware(['auth', 'dual.role:ppk'])
    ->prefix('multirole2')->name('multirole2.')
    ->group(function () {
        Route::get('/', [DualroleController::class, 'index'])->name('dashboard'); 
        
        // 2. INI UNTUK PAS KLIK CARD ATASAN (/multirole1/atasan)
        Route::get('/atasan', [AtasanController::class, 'atasan'])->name('atasan');
        
        // 3. INI UNTUK PAS KLIK CARD PENERIMA (/multirole1/penerima)
        Route::get('/ppk', [PenerimaController::class, 'ppk'])->name('ppk');


    });

// ATASAN × KEUANGAN
Route::middleware(['auth', 'dual.role:keuangan'])
    ->prefix('multirole3')->name('multirole3.')
    ->group(function () {
        Route::get('/', [DualroleController::class, 'index'])->name('dashboard'); 
        
        // 2. INI UNTUK PAS KLIK CARD ATASAN (/multirole1/atasan)
        Route::get('/atasan', [AtasanController::class, 'atasan'])->name('atasan');
        
        // 3. INI UNTUK PAS KLIK CARD PENERIMA (/multirole1/penerima)
        Route::get('/keuangan', [PenerimaController::class, 'keuangan'])->name('keuangan');


       
    });


// Route::middleware(['auth', 'role:pemohon'])->get('/pemohon', [PemohonController::class, 'index']);
// Route::middleware(['auth', 'role:penerima'])->get('/penerima', [PenerimaController::class, 'index']);
// Route::middleware(['auth', 'role:atasan'])->get('/atasan', [AtasanController::class, 'index']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/pemohon.php';
require __DIR__.'/penerima.php';
require __DIR__.'/direktur.php';
require __DIR__.'/atasan.php';
require __DIR__.'/keuangan.php';
require __DIR__.'/dualrole.php';
require __DIR__.'/ppk.php';
require __DIR__.'/kpa.php';