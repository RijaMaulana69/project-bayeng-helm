<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage in route: middleware('role:owner') or middleware('role:karyawan')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth('web')->user();

        if (!$user || !$user->role) {
            return redirect()->route('admin.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = strtolower($user->role->nama_role);

        if (!in_array($userRole, array_map('strtolower', $roles))) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
