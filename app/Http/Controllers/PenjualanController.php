<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index() {
$penjualan = Penjualan::with(['details.menu'])
    ->orderByRaw("CASE WHEN status_penjualan = 'Proses' THEN 0 ELSE 1 END")
    ->orderBy('tanggal', 'desc')
    ->paginate(5);

    $user = auth()->user();

    if ($user->role == 'superuser') {
        return view('superuser/Penjualan/index', compact('penjualan'));
    } elseif ($user->role == 'karyawan') {
        return view('karyawan/Penjualan/index', compact('penjualan'));
    } else {
        abort(403, 'Unauthorized action.');
    }
}



}