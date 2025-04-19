<?php

namespace App\Http\Controllers;

use App\Models\LoginTest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
        /**
     * Menampilkan halaman login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses permintaan login pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama' => 'required',
            'password' => 'required'
        ]);

        // Cari user berdasarkan nama
        $user = User::where('nama', $credentials['nama'])->first();

        // Jika user tidak ditemukan
        if (!$user) {
            LoginTest::create([
                'nama' => $request->nama,
                'password' => $request->password,
                'status' => false,
                'error_message' => 'Username tidak ditemukan'
            ]);

            return back()->withErrors('Username tidak ditemukan');
        }

        // Jika password salah
        if (!Hash::check($credentials['password'], $user->password)) {
            LoginTest::create([
                'nama' => $request->nama,
                'password' => $request->password,
                'status' => false,
                'error_message' => 'Password salah'
            ]);

            return back()->withErrors('Password salah');
        }

        // Jika akun tidak aktif
        if (!$user->isActive()) {
            LoginTest::create([
                'nama' => $request->nama,
                'password' => $request->password,
                'status' => false,
                'error_message' => 'Akun tidak aktif'
            ]);

            return back()->withErrors('Akun tidak aktif');
        }

        // Login pengguna
        Auth::login($user);

        // Simpan log login berhasil
        LoginTest::create([
            'nama' => $request->nama,
            'password' => $request->password,
            'status' => true,
            'error_message' => null
        ]);

        // Redirect berdasarkan peran (role)
        return match ($user->role) {
            'manager' => redirect()->route('manager.dashboard'),
            'karyawan' => redirect()->route('karyawan.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'member' => redirect()->route('member.dashboard'),
            default => redirect()->route('login')->with('error', 'Role tidak dikenali'),
        };
    }

    /**
     * Melakukan proses logout pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout pengguna dari sistem
        Auth::logout();

        // Menghapus session saat ini
        $request->session()->invalidate();

        // Regenerasi token session baru untuk keamanan
        $request->session()->regenerateToken();

        // Redirect ke halaman login
        return redirect()->route('login')->with('success', 'Anda berhasil logout');
    }

}