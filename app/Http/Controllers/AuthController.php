<?php

namespace App\Http\Controllers;

use App\Models\LoginTest;
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
        // Simpan hasil login yang gagal ke database
        LoginTest::create([
            'nama' => $request->nama,
            'password' => $request->password,
            'status' => false,
            'error_message' => 'Username tidak ditemukan'
        ]);

        return back()->withErrors('Username tidak ditemukan');
    }

    // Cek apakah password cocok
    if (!Hash::check($credentials['password'], $user->password)) {
        // Simpan hasil login yang gagal ke database
        LoginTest::create([
            'nama' => $request->nama,
            'password' => $request->password,
            'status' => false,
            'error_message' => 'Password salah'
        ]);

        return back()->withErrors('Password salah');
    }

    // Cek apakah akun aktif
    if (!$user->isActive()) {
        // Simpan hasil login yang gagal ke database
        LoginTest::create([
            'nama' => $request->nama,
            'password' => $request->password,
            'status' => false,
            'error_message' => 'Akun tidak aktif'
        ]);

        return back()->withErrors('Akun tidak aktif');
    }

    // Login user
    Auth::login($user);

    // Simpan hasil login yang berhasil ke database
    LoginTest::create([
        'nama' => $request->nama,
        'password' => $request->password,
        'status' => true,
        'error_message' => null
    ]);

    // Redirect berdasarkan role
    return match ($user->role) {
        'manager' => redirect()->route('manager.dashboard'),
        'karyawan' => redirect()->route('karyawan.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'member' => redirect()->route('member.dashboard'),
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