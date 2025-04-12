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

    function paginateCollection($items, $perPage = 5, $pageName = 'supplier_page')
{
    $page = Paginator::resolveCurrentPage($pageName) ?: 1;
    $items = $items instanceof Collection ? $items : collect($items);
    $total = $items->count();
    $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();
    
    return new LengthAwarePaginator($currentPageItems, $total, $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => $pageName,
    ]);
}
    $loginTest = LoginTest::latest()->paginate(5, ['*'], 'login_page');

    return view('manager.dashboard', compact('penjualan', 'pembelian', 'loginTest'));

}

public function karyawanDashboard() {
    $transaksi = Penjualan::where('status_penjualan', 'Proses')
        ->orderBy('tanggal', 'desc')
        ->paginate(5, ['*'], 'transaksi_page'); // Ubah nama parameter page

    // Ambil stok bahan baku yang tersedia
    $bahanBaku = BahanBaku::orderBy('stok', 'asc')
        ->paginate(5, ['*'], 'bahan_page'); 
    function paginateCollection($items, $perPage = 5, $pageName = 'supplier_page')
{
    $page = Paginator::resolveCurrentPage($pageName) ?: 1;
    $items = $items instanceof Collection ? $items : collect($items);
    $total = $items->count();
    $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();
    
    return new LengthAwarePaginator($currentPageItems, $total, $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => $pageName,
    ]);
}
    $loginTest = LoginTest::latest()->paginate(5, ['*'], 'login_page');

    return view('Karyawan.dashboard', compact('transaksi', 'bahanBaku',));
}

}