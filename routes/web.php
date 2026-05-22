<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('employee')) {
            return redirect()->route('employee.dashboard');
        }
        if (auth()->user()->hasRole('owner')) {
            return redirect()->route('owner.dashboard');
        }
        if (auth()->user()->hasRole('customer')) {
            return redirect()->route('customer.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('dashboard', function () {
            return view('customer.dashboard');
        })->name('dashboard');
        Route::get('store', [CustomerController::class, 'store'])->name('store');
        Route::get('orders', [CustomerController::class, 'orders'])->name('orders');
        Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
        Route::post('profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/resend-verification', [CustomerController::class, 'resendVerification'])->name('profile.resend');
    });

    Route::middleware(['employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('dashboard', function () {
            return view('employee.dashboard');
        })->name('dashboard');
        Route::get('quotes', function () {
            return view('employee.quotes');
        })->name('quotes');
        Route::get('jobs', function () {
            return view('employee.jobs');
        })->name('jobs');
    });

    Route::middleware(['owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('dashboard', function () {
            return view('owner.dashboard');
        })->name('dashboard');
        Route::get('quotes', function () {
            return view('owner.quotes');
        })->name('quotes');
        Route::get('employees', function () {
            return view('owner.employees');
        })->name('employees');
        Route::get('jobs', function () {
            return view('owner.jobs');
        })->name('jobs');
    });
});

require __DIR__.'/settings.php';
