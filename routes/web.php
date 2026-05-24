<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/database', [DatabaseController::class, 'index'])->name('database');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/admin/register', [AuthController::class, 'showAdminRegistrationForm'])->name('admin.register');
    Route::post('/admin/register', [AuthController::class, 'registerAdmin']);
});

Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');

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
        Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('store', [CustomerController::class, 'store'])->name('store');
        Route::post('request-service', [CustomerController::class, 'requestService'])->name('request-service');
        Route::get('orders', [CustomerController::class, 'orders'])->name('orders');
        Route::delete('orders/{order}', [CustomerController::class, 'destroyOrder'])->name('orders.destroy');
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
        Route::get('employees', [AdminController::class, 'employees'])->name('employees');
        Route::get('jobs', function () {
            return view('owner.jobs');
        })->name('jobs');
    });
});

require __DIR__.'/settings.php';
