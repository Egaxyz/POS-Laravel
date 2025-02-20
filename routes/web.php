<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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


    Route::get('/karyawan/bahan-baku', [BahanBakuController::class, 'index'])->name('karyawan.bahan-baku');
    Route::post('/karyawan/bahan-baku', [BahanBakuController::class, 'store']);
    Route::patch('/karyawan/bahan-baku/{id}', [BahanBakuController::class, 'update']);
    Route::delete('/karyawan/bahan-baku/{id}', [BahanBakuController::class, 'destroy'])
        ->withoutMiddleware([VerifyCsrfToken::class]);
});