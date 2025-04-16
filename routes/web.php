<?php

use App\Http\Controllers\AjukanMenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MenuBahanBakuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Absen;
use App\Models\AjukanMenu;
use App\Models\BahanBaku;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\Feature\AuthControllerTest;


Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
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
// Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware(['role:manager'])->group(function () {
    Route::get('/manager/dashboard', [homeController::class, 'managerDashboard'])->name('manager.dashboard');
    
    
    Route::get('/manager/laporan-bahan', [BahanBakuController::class, 'laporan'])->name('manager.laporan-bahan');
    Route::get('/manager/laporan-bahan/pdf', function () {
            $bahan = BahanBaku::all(); 
            $pdf = Pdf::loadView('manager.Laporan_Bahan_Baku.pdf', compact('bahan'));
            return $pdf->download('laporan-bahan.pdf');
    });
    
    Route::get('/manager/laporan-pembelian', [PembelianController::class, 'laporan'])->name('manager.laporan-pembelian');
    Route::get('/manager/laporan-pembelian/pdf', function () {
            $pembelian = Pembelian::all(); 
            $pdf = Pdf::loadView('manager.Laporan_Pembelian.pdf', compact('pembelian'));
            return $pdf->download('laporan-pembelian.pdf');
    });
    Route::get('/manager/laporan-pembelian/excel', [PembelianController ::class, 'exportExcel'])->name('manager.pembelian-excel');

    Route::get('/manager/laporan-penjualan', [PenjualanController::class, 'laporan'])->name('manager.laporan-penjualan');
    Route::get('/manager/laporan-penjualan/pdf', function () {
        $penjualan = Penjualan::all(); 
        $pdf = Pdf::loadView('manager.Laporan_Penjualan.pdf', compact('penjualan'));
        return $pdf->download('laporan-penjualan.pdf');
    });
    Route::get('/manager/laporan-penjualan/excel', [PenjualanController::class, 'exportExcel'])->name('manager.penjualan-excel');
    
    Route::get('/manager/test',  [ShiftController::class, 'test'])->name('manager.absen.index');
    Route::get('/manager/absen',  [ShiftController::class, 'absen'])->name('manager.absen.index');
    Route::post('/manager/absen', [ShiftController::class, 'absenMasuk']);
    Route::patch('/manager/absen/{id}',  [ShiftController::class, 'updateAbsen']);
    Route::delete('/manager/absen/{id}', [ShiftController::class, 'deleteAbsen']);
    Route::get('/manager/absen/import', [ShiftController::class, 'showImportForm'])->name('absen.import.form');
    Route::post('/manager/absen/import', [ShiftController::class, 'import'])->name('absen.import');
    Route::post('/manager/absen/export', [ShiftController ::class, 'exportExcel'])->name('absen.export');
    Route::get('manager/absensi/pdf', function () {
        $absen = Absen::all(); 
        $pdf = Pdf::loadView('manager.Absensi.pdf', compact('absen'));

        return $pdf->download('absen.pdf');
    });
    Route::get('/manager/absen/{id}/edit', [ShiftController::class, 'edit']);

Route::post('/manager/absen/{id}/selesai', [ShiftController::class, 'markAsDone'])->name('absen.selesai');


    Route::get('/manager/user',  [UserController::class, 'index'])->name('manager.user');
    Route::post('/manager/user', [UserController::class, 'store']);
    Route::patch('/manager/user/{id}', [UserController::class, 'update']);
    Route::delete('/manager/user/{id}', [UserController::class, 'destroy']);
    Route::get('/manager/user/import', [UserController::class, 'showImportForm'])->name('user.import.form');
    Route::post('/manager/user/import', [UserController::class, 'import'])->name('user.import');
    

    Route::get('/manager/supplier', [SupplierController::class, 'index'])->name('manager.supplier');
    Route::post('/manager/supplier', [SupplierController::class, 'store']);
    Route::patch('/manager/supplier/{id}', [SupplierController::class, 'update']);
    Route::delete('/manager/supplier/{id}', [SupplierController::class, 'destroy']);
    Route::get('/manager/supplier/import', [SupplierController::class, 'showImportForm'])->name('supplier.import.form');
    Route::post('/manager/supplier/import', [SupplierController::class, 'import'])->name('supplier.import');

});

Route::middleware(['role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [homeController::class, 'karyawanDashboard'])->name('karyawan.dashboard');
Route::get('/get-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    
    if (!File::exists($logFile)) {
        return response()->json(['logs' => []]);
    }

    $logs = File::get($logFile);
    $logsArray = explode("\n", trim($logs)); // Pisahkan berdasarkan baris

    // Ambil 10 log terbaru
    $latestLogs = array_slice($logsArray, -10);

    return response()->json(['logs' => $latestLogs]);
});
    Route::post('/log-update', [LogController::class, 'update'])->name('log-update');

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
    Route::get('/karyawan/penjualan/struk/{no_faktur}', [PenjualanController::class, 'cetakStruk'])->name('penjualan.struk');

    Route::post('/karyawan/menu-bahan-baku', [MenuBahanBakuController::class, 'store'])->name('menu.bahan-baku.store');
    
    Route::get('/karyawan/pengajuan', [AjukanMenuController::class, 'index'])->name('karyawan.ajukan');
    Route::post('/karyawan/pengajuan', [AjukanMenuController::class, 'store'])->name('karyawan.ajukan.store');
    Route::patch('/karyawan/pengajuan/selesai/{id}', [AjukanMenuController::class, 'selesai'])->name('karyawan.ajukan.selesai');
    Route::patch('/karyawan/pengajuan/batalkan/{id}', [AjukanMenuController::class, 'batal'])->name('karyawan.ajukan.batalkan');

    Route::get('/karyawan/member', [MemberController::class, 'index'])->name('karyawan.member');
    Route::post('/karyawan/member', [MemberController::class, 'store'])->name('karyawan.member.store');
    Route::patch('/karyawan/member/{id}', [MemberController::class, 'update'])->name('karyawan.member.update');
    Route::delete('/karyawan/member/{id}', [MemberController::class, 'destroy'])->name('karyawan.member.delete');

    Route::get('karyawan/pengajuan/pdf', function () {
        $pengajuan = AjukanMenu::all(); 
        $pdf = Pdf::loadView('karyawan.pengajuan.pdf', compact('pengajuan'));
        return $pdf->download('pengajuan.pdf');
    });
    Route::get('/karyawan/pengajuan/excel', [AjukanMenuController::class, 'exportExcel'])->name('karyawan.pengajuan-excel');
    

    Route::get('/karyawan/bahan-baku', [BahanBakuController::class, 'index'])->name('karyawan.bahan-baku');
    Route::post('/karyawan/bahan-baku', [BahanBakuController::class, 'store']);
    Route::patch('/karyawan/bahan-baku/{id}', [BahanBakuController::class, 'update']);
    Route::delete('/karyawan/bahan-baku/{id}', [BahanBakuController::class, 'destroy'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
});
Route::middleware(['role:member'])->group(function () {
    Route::get('/member/dashboard', [homeController::class, 'memberDashboard'])->name('member.dashboard');
    
    Route::get('/member/pengajuan', [AjukanMenuController::class, 'index'])->name('member.ajukan');
    Route::post('/member/pengajuan', [AjukanMenuController::class, 'store'])->name('member.ajukan.store');
    Route::patch('/member/pengajuan/{id}', [AjukanMenuController::class, 'edit'])->name('member.ajukan.edit');
    Route::delete('/member/pengajuan/{id}', [AjukanMenuController::class, 'destroy'])->name('member.ajukan.delete');
});