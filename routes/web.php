<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\OwnerController;
use App\Models\ServiceJob;
use App\Models\User;
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
    Route::get('/owner/register', [AuthController::class, 'showOwnerRegistrationForm'])->name('owner.register');
    Route::post('/owner/register', [AuthController::class, 'registerOwner']);
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
        Route::get('dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::get('quotes', [EmployeeController::class, 'quotes'])->name('quotes');
        Route::get('jobs', [EmployeeController::class, 'jobs'])->name('jobs');
    });

    Route::middleware(['owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('dashboard', function () {
            $jobsCount = ServiceJob::count();
            $recentJobs = ServiceJob::with(['customer', 'employee'])
                ->latest()
                ->take(10)
                ->get();

            $customersCount = User::role('customer')->count();
            $recentCustomers = User::role('customer')
                ->latest()
                ->take(10)
                ->get();

            $jobsByStatus = ServiceJob::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            return view('owner.dashboard', [
                'jobsCount' => $jobsCount,
                'recentJobs' => $recentJobs,
                'customersCount' => $customersCount,
                'recentCustomers' => $recentCustomers,
                'jobsByStatus' => $jobsByStatus,
            ]);
        })->name('dashboard');
        Route::get('quotes', [OwnerController::class, 'quotes'])->name('quotes');
        Route::get('employees', [OwnerController::class, 'employees'])->name('employees');
        Route::get('employees/create', [OwnerController::class, 'createEmployee'])->name('employees.create');
        Route::post('employees', [OwnerController::class, 'storeEmployee'])->name('employees.store');
        Route::get('jobs', [OwnerController::class, 'jobs'])->name('jobs');
        Route::patch('jobs/{job}/assign', [OwnerController::class, 'assignEmployee'])->name('jobs.assign');
    });
});

require __DIR__.'/settings.php';
