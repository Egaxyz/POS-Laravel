<?php

use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/',[HomeController::class, 'index']);

Route::get('/user', [UserController::class, 'index'])->name('User');
Route::post('/user', [UserController::class, 'store']);
Route::patch('/user/{id}', [UserController::class, 'update']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

Route::get('/menu', [MenuController::class, 'index'])->name('Menu');
Route::post('/menu', [MenuController::class, 'store']);
Route::patch('/menu/{id}', [MenuController::class, 'update']);
Route::delete('/menu/{id}', [MenuController::class, 'destroy']);

Route::get('/supplier', [SupplierController::class, 'index'])->name('Supplier');
Route::post('/supplier', [SupplierController::class, 'store']);
Route::patch('/supplier/{id}', [SupplierController::class, 'update']);
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy']);

Route::get('/bahan-baku', [BahanBakuController::class, 'index'])->name('Bahan-Baku');
Route::post('/bahan-baku', [BahanBakuController::class, 'store']);
Route::patch('/bahan-baku/{id}', [BahanBakuController::class, 'update']);
Route::delete('/bahan-baku/{id}', [BahanBakuController::class, 'destroy'])
    ->withoutMiddleware([VerifyCsrfToken::class]);