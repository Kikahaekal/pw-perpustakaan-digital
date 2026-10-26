<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/books/{id}', [DashboardController::class, 'show'])->name('books.show');
    Route::post('/books/{id}/favorite', [DashboardController::class, 'toggleFavorite'])->name('books.favorite');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('api')->name('api.')->group(function () {
        Route::apiResource('bukus', BukuController::class);
        Route::apiResource('kategoris', KategoriController::class);
        Route::apiResource('peminjamans', PeminjamanController::class);
    });
});
