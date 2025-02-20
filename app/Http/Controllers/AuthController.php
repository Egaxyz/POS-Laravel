<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login// Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama' => 'required',
            'password' => 'required'
        ]);

        // Cari user berdasarkan nama
        $user = User::where('nama', $credentials['nama'])->first();

        if (!$user) {
            return back()->withErrors('Username tidak ditemukan');
        }

        //Cek apakah password cocok
        // if (!Hash::check($credentials['password'], $user->password)) {
        //     return back()->withErrors('Password salah');
        // }

        // Cek apakah akun aktif
        if (!$user->isActive()) {
            return back()->withErrors('Akun tidak aktif');
        }

        // Login user
        Auth::login($user);

        // Redirect berdasarkan role
        return match ($user->role) {
            'manager' => redirect()->route('manager.dashboard'),
            'karyawan' => redirect()->route('karyawan.dashboard'),
            'superuser' => redirect()->route('superuser.dashboard'),
            default => redirect()->route('login')->with('error', 'Role tidak dikenali'),
        };
    }

// Fungsi untuk logout
    public function logout(Request $request)
    {
        // Logout pengguna
        Auth::logout();

        // Hapus session untuk menghindari user login kembali saat refresh halaman
        $request->session()->invalidate();

        // Regenerasi session ID untuk meningkatkan keamanan
        $request->session()->regenerateToken();

        // Redirect ke halaman login setelah logout
        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }


}