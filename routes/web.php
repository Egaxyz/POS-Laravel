<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuBahanBakuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // Redirect berdasarkan role
        if ($user->role === 'superuser') {
            return redirect()->route('superuser.dashboard');
        } elseif ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        } elseif ($user->role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        }

        // Jika role tidak dikenali, logout & arahkan ke login
        Auth::logout();
        return redirect()->route('login')->with('error', 'Role tidak valid.');
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware(['role:manager'])->group(function () {
    Route::get('/manager/dashboard', [homeController::class, 'managerDashboard'])->name('manager.dashboard');
    
    Route::get('/manager/user', [UserController::class, 'index'])->name('manager.user');
    
    Route::get('/manager/laporan-pembelian', [PembelianController::class, 'laporan'])->name('manager.laporan-pembelian');
    Route::get('/manager/laporan-pembelian/pdf', function () {
            $pembelian = Pembelian::all(); 
            $pdf = Pdf::loadView('manager.Laporan_Pembelian.pdf', compact('pembelian'));
            return $pdf->download('laporan-pembelian.pdf');
    });
    Route::get('/manager/laporan-penjualan', [PenjualanController::class, 'laporan'])->name('manager.laporan-penjualan');
    Route::get('/manager/laporan-penjualan/pdf', function () {
            $penjualan = Penjualan::all(); 
            $pdf = Pdf::loadView('manager.Laporan_Penjualan.pdf', compact('penjualan'));
            return $pdf->download('laporan-penjualan.pdf');
    });
        Route::post('/manager/user', [UserController::class, 'store']);
        Route::patch('/manager/user/{id}', [UserController::class, 'update']);
        Route::delete('/manager/user/{id}', [UserController::class, 'destroy']);
        
        Route::get('/manager/supplier', [SupplierController::class, 'index'])->name('manager.supplier');
        Route::post('/manager/supplier', [SupplierController::class, 'store']);
        Route::patch('/manager/supplier/{id}', [SupplierController::class, 'update']);
        Route::delete('/manager/supplier/{id}', [SupplierController::class, 'destroy']);
});

Route::middleware(['role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [homeController::class, 'karyawanDashboard'])->name('karyawan.dashboard');

    Route::get('/karyawan/menu', [MenuController::class, 'index'])->name('karyawan.menu');
    Route::post('/karyawan/menu', [MenuController::class, 'store']);
    Route::patch('/karyawan/menu/{id}', [MenuController::class, 'update']);
    Route::delete('/karyawan/menu/{id}', [MenuController::class, 'destroy']);

    
    Route::get('/karyawan/pembelian', [PembelianController::class, 'index'])->name('karyawan.pembelian');
    Route::post('/karyawan/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
    Route::patch('/karyawan/pembelian/{id}', [PembelianController::class, 'update']);
    Route::delete('/karyawan/pembelian/{id}', [PembelianController::class, 'destroy']);
    Route::patch('/karyawan/pembelian/selesai/{id}', [PembelianController::class, 'selesai']);
    Route::patch('/karyawan/pembelian/batalkan/{id}', [PembelianController::class, 'batal']);
    
    Route::get('/karyawan/penjualan', [PenjualanController::class, 'index'])->name('karyawan.penjualan');
    Route::post('/karyawan/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::patch('/karyawan/penjualan/{id}', [PenjualanController::class, 'update']);
    Route::delete('/karyawan/penjualan/{id}', [PenjualanController::class, 'destroy']);
    Route::patch('/karyawan/penjualan/selesai/{id}', [PenjualanController::class, 'selesai']);
    Route::patch('/karyawan/penjualan/batalkan/{id}', [PenjualanController::class, 'batal']);
    
    Route::post('/karyawan/menu-bahan-baku', [MenuBahanBakuController::class, 'store'])->name('menu.bahan-baku.store');

    Route::get('/karyawan/bahan-baku', [BahanBakuController::class, 'index'])->name('karyawan.bahan-baku');
    Route::post('/karyawan/bahan-baku', [BahanBakuController::class, 'store']);
    Route::patch('/karyawan/bahan-baku/{id}', [BahanBakuController::class, 'update']);
    Route::delete('/karyawan/bahan-baku/{id}', [BahanBakuController::class, 'destroy'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
});