<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$level): Response
    {
        if (!Auth::check()) {
            alert()->error('Unauthorized', 'Anda harus login terlebih dahulu.');
            return redirect()->route('login');
        }

        // Ambil role user
        $userRole = Auth::user()->level->level_name ?? null;

        if (!$userRole) {
            alert()->error('Unauthorized', 'Anda tidak memiliki akses.');
            return redirect()->to('dashboard');
        }

        // Normalisasi semua role ke lowercase untuk perbandingan
        $userRoleNormalized = strtolower(trim($userRole));
        $allowedRoles = array_map(fn($r) => strtolower(trim($r)), $level);

        if (!in_array($userRoleNormalized, $allowedRoles, true)) {
            alert()->error('Unauthorized', 'Anda tidak memiliki akses');
            return redirect()->to('dashboard');
        }

        return $next($request);
    }
}
