<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('store', [CustomerController::class, 'store'])->name('store');
        Route::get('orders', [CustomerController::class, 'orders'])->name('orders');
        Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
        Route::post('profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/resend-verification', [CustomerController::class, 'resendVerification'])->name('profile.resend');
    });
});

require __DIR__.'/settings.php';
