<?php

namespace App\Http\Controllers;

use App\Models\AjukanMenu;
use App\Models\BahanBaku;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return view('Home.index');
    } 


    public function adminDashboard() {
    return view('Admin.dashboard');
}
    public function memberDashboard() {
    $user = auth()->user(); // Ambil user yang sedang login sebagai objek User
    $data = AjukanMenu::where('user_id', $user->id)->with('user')->orderBy('nama_makanan', 'asc')->paginate(5);

    return view('Member.dashboard', compact('data', 'user'));
}


public function managerDashboard() {
  // Ambil data penjualan dan kelompokkan berdasarkan bulan
    $penjualan = Penjualan::selectRaw('MONTH(tanggal) as bulan, SUM(total_harga) as total')
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();

    // Konversi angka bulan ke nama bulan
    $penjualan = $penjualan->map(function ($item) {
        return [
            'bulan' => Carbon::create()->month($item->bulan)->translatedFormat('F'),
            'total' => $item->total
        ];
    });
    
    $pembelian = Pembelian::selectRaw('MONTH(tanggal_pembelian) as bulan, SUM(total_harga) as total')
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();

    $pembelian = $pembelian->map(function ($item) {
        return [
            'bulan' => Carbon::create()->month($item->bulan)->translatedFormat('F'),
            'total' => $item->total
        ];
    });
    return view('manager.dashboard', compact('penjualan', 'pembelian'));

}

public function karyawanDashboard() {
    $transaksi = Penjualan::where('status_penjualan', 'Proses')
        ->orderBy('tanggal', 'desc')
        ->paginate(5, ['*'], 'transaksi_page'); // Ubah nama parameter page

    // Ambil stok bahan baku yang tersedia
    $bahanBaku = BahanBaku::orderBy('stok', 'asc')
        ->paginate(5, ['*'], 'bahan_page'); 

    return view('Karyawan.dashboard', compact('transaksi', 'bahanBaku'));
}

}