<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = $request->user();

    // 🔐 LOGIN KHUSUS ADMIN
    if ($request->input('login_as') === 'admin' && $user->role !== 'admin') {
        Auth::logout();
        return back()->withErrors(['email' => 'Akun ini bukan admin.']);
    }

    // 🔐 ADMIN TIDAK BOLEH LOGIN LEWAT USER LOGIN
    if (! $request->has('login_as') && $user->role === 'admin') {
        Auth::logout();
        return back()->withErrors(['email' => 'Admin harus login lewat halaman admin.']);
    }

    // 🔁 ATASAN DENGAN ROLE TAMBAHAN → arahkan ke multirole sesuai kombinasi
    if ($user->role === 'atasan' && $prefix = $user->multirolePrefix()) {
        return redirect()->intended('/' . $prefix);
    }

    // 🔁 REDIRECT SESUAI ROLE
    return match ($user->role) {
        'admin'    => redirect('/admin'),
        'penerima' => redirect('/penerima'),
        'atasan'   => redirect('/atasan'),
        'keuangan' => redirect('/keuangan'),
        'direktur' => redirect('/direktur'),
        'ppk'      => redirect('/ppk'),
        'kpa'      => redirect('/kpa'),
        default    => redirect('/pemohon'),
    };
}

//     public function store(LoginRequest $request): RedirectResponse
// {
//     $request->authenticate();
//     $request->session()->regenerate();

//     $user = $request->user();

//     // 🔐 LOGIN KHUSUS ADMIN
//     if ($request->input('login_as') === 'admin' && $user->role !== 'admin') {
//         Auth::logout();

//         return back()->withErrors([
//             'email' => 'Akun ini bukan admin.',
//         ]);
//     }

//     // 🔐 ADMIN TIDAK BOLEH LOGIN LEWAT USER LOGIN
//     if (! $request->has('login_as') && $user->role === 'admin') {
//         Auth::logout();

//         return back()->withErrors([
//             'email' => 'Admin harus login lewat halaman admin.',
//         ]);
//     }

//     if ($user->role === 'atasan' && $user->role2 === 'penerima') {
//     return redirect()->intended('/multirole');
// }

//     // 🔁 REDIRECT SESUAI ROLE
//     return match ($user->role) {
//         'admin'      => redirect('/admin'),
//         'penerima'   => redirect('/penerima'),
//         'atasan'     => redirect('/atasan'),
//         'keuangan'   => redirect('/keuangan'),
//         'direktur'   => redirect('/direktur'),
//         'ppk'        => redirect('/ppk'),
//         'kpa'        => redirect('/kpa'),
//         // 'direktur'   => redirect('/direktur'),
//         default      => redirect('/pemohon'),
//     };
// }

    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
