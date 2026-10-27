<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class DualRoleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Belum login');
        }

        $allowedSecondaryRoles = ['penerima', 'ppk', 'keuangan'];

        if ($user->role === 'atasan' && in_array($user->role2, $allowedSecondaryRoles, true)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak');
    }
}