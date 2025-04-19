<?php

namespace App\Http\Controllers;

use App\Models\AjukanMenu;
use App\Models\BahanBaku;
use App\Models\LoginTest;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
  /**
 * @brief Menampilkan halaman beranda utama.
 *
 * @return \Illuminate\View\View
 */
public function index() {
    return view('Home.index');
}

/**
 * @brief Menampilkan dashboard untuk admin.
 *
 * @return \Illuminate\View\View
 */
public function adminDashboard() {
    return view('Admin.dashboard');
}

/**
 * @brief Menampilkan dashboard untuk member (pengguna biasa).
 *
 * Menampilkan data menu yang diajukan oleh user yang sedang login.
 *
 * @return \Illuminate\View\View
 */
public function memberDashboard() {
    $user = auth()->user(); // Ambil user yang sedang login
    $data = AjukanMenu::where('user_id', $user->id)
        ->with('user')
        ->orderBy('nama_makanan', 'asc')
        ->paginate(5);

    return view('Member.dashboard', compact('data', 'user'));
}

/**
 * @brief Menampilkan dashboard untuk manager.
 *
 * Menampilkan data penjualan dan pembelian per bulan serta histori login user.
 *
 * @return \Illuminate\View\View
 */
public function managerDashboard() {
    $penjualan = Penjualan::selectRaw('MONTH(tanggal) as bulan, SUM(total_harga) as total')
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->map(function ($item) {
            return [
                'bulan' => Carbon::create()->month($item->bulan)->translatedFormat('F'),
                'total' => $item->total
            ];
        });

    $pembelian = Pembelian::selectRaw('MONTH(tanggal_pembelian) as bulan, SUM(total_harga) as total')
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->map(function ($item) {
            return [
                'bulan' => Carbon::create()->month($item->bulan)->translatedFormat('F'),
                'total' => $item->total
            ];
        });

    $loginTest = LoginTest::latest()->paginate(5, ['*'], 'login_page');

    return view('manager.dashboard', compact('penjualan', 'pembelian', 'loginTest'));
}

/**
 * @brief Menampilkan dashboard untuk karyawan.
 *
 * Menampilkan transaksi yang sedang diproses dan daftar bahan baku berdasarkan stok terendah.
 *
 * @return \Illuminate\View\View
 */
public function karyawanDashboard() {
    $transaksi = Penjualan::where('status_penjualan', 'Proses')
        ->orderBy('tanggal', 'desc')
        ->paginate(5, ['*'], 'transaksi_page');

    $bahanBaku = BahanBaku::orderBy('stok', 'asc')
        ->paginate(5, ['*'], 'bahan_page');

    $loginTest = LoginTest::latest()->paginate(5, ['*'], 'login_page');

    return view('Karyawan.dashboard', compact('transaksi', 'bahanBaku'));
}


}