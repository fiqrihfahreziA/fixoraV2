<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DualroleController extends Controller
{
    /**
     * Dashboard pilih mode.
     * Tampilkan tombol Atasan + tombol role2 (penerima/ppk/keuangan).
     */
    public function index()
    {
        $authUser = Auth::user();

        // prefix sesuai role2 (multirole1/2/3)
        $prefix = $authUser->multirolePrefix();

        if (! $prefix) {
            abort(403, 'Anda tidak punya role tambahan.');
        }

        return view('dualrole.dashboard', [
            'authuser' => $authUser,
            'prefix'   => $prefix,
        ]);
    }

    /**
     * Mode Atasan (dari multirole1/2/3).
     */
    public function atasan(Request $request)
        {
            // Panggil method pengadaanshow, bukan index
            return app(AtasanController::class)->pengadaanshow($request);
        }
    /**
     * Mode Penerima.
     */
    public function penerima(Request $request)
    {
        return app(PenerimaController::class)->chartpengadaan($request);
    }

    /**
     * Mode PPK.
     */
    public function ppk(Request $request)
    {
        return app(PpkController::class)->index($request);
    }

    /**
     * Mode Keuangan.
     */
    public function keuangan(Request $request)
    {
        return app(keuanganController::class)->showpengadaan($request);
    }
}