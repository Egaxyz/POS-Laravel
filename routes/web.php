<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/',[HomeController::class, 'index']);

Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store'])
    ->withoutMiddleware([VerifyCsrfToken::class]);
Route::put('/user/{id}', [UserController::class, 'update'])
    ->withoutMiddleware([VerifyCsrfToken::class]);
Route::delete('/user/{id}', [UserController::class, 'destroy'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/supplier', [SupplierController::class, 'index']);
Route::post('/supplier', [SupplierController::class, 'store'])
    ->withoutMiddleware([VerifyCsrfToken::class]);
Route::put('/supplier/{id}', [SupplierController::class, 'update'])
    ->withoutMiddleware([VerifyCsrfToken::class]);
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])
    ->withoutMiddleware([VerifyCsrfToken::class]);