<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Route untuk verifikasi 2FA
    Route::get('/verify-2fa', [AdminAuthController::class, 'showVerifyTwoFactorForm'])->name('admin.verify-2fa');
    Route::post('/verify-2fa', [AdminAuthController::class, 'verifyTwoFactor'])->middleware('throttle:5,1')->name('admin.verify-2fa.submit');

    Route::middleware(['admin', 'twofactor'])->group(function () {
        Route::get('/enable-2fa', [AdminAuthController::class, 'enableTwoFactorAuthentication'])->name('admin.enable-2fa');
        Route::post('/confirm-2fa', [AdminAuthController::class, 'confirmTwoFactor'])->name('admin.confirm-2fa');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    });
});

