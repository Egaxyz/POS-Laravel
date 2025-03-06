<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(){
        $penjualan = Penjualan::all();

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