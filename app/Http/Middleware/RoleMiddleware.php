<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
/**
 * @brief Middleware untuk memeriksa akses berdasarkan peran (role) pengguna dan status aktifitasnya.
 *
 *
 * @param \Illuminate\Http\Request $request Request yang diterima.
 * @param \Closure $next Fungsi penanganan berikutnya dalam middleware pipeline.
 * @param string ...$roles Daftar peran yang diizinkan untuk mengakses route ini.
 *
 * @return \Illuminate\Http\Response|mixed Response atau redirect berdasarkan hasil pengecekan akses.
 *
 * @throws \Illuminate\Auth\AuthenticationException Jika pengguna tidak terautentikasi.
 * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException Jika pengguna tidak memiliki akses.
 */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Cek apakah pengguna aktif
        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif');
        }

        // Cek apakah role pengguna cocok dengan salah satu role yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $response = $next($request);

    return $response->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
                    ->header('Pragma','no-cache')
                    ->header('Expires','Sat, 01 Jan 1990 00:00:00 GMT');
    }
}