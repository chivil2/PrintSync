<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PrintbuddyController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::post('/printbuddy/chat', [PrintbuddyController::class, 'chat'])->name('printbuddy.chat.api');

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
            return redirect()->route('customer.store');
        }

        abort(403, 'Unauthorized role');
    })->name('dashboard');

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('store', [CustomerController::class, 'store'])->name('store');
        Route::post('request-service', [CustomerController::class, 'requestService'])->name('request-service');
        Route::get('orders', [CustomerController::class, 'orders'])->name('orders');
        Route::get('orders/{order}', [CustomerController::class, 'showOrder'])->name('orders.show');
        Route::patch('orders/{order}/cancel', [CustomerController::class, 'cancelOrder'])->name('orders.cancel');
        Route::get('orders/{order}/invoice', [CustomerController::class, 'downloadInvoice'])->name('orders.invoice');
        Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
        Route::post('profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/resend-verification', [CustomerController::class, 'resendVerification'])->name('profile.resend');
        Route::get('quotes', [QuoteController::class, 'customerIndex'])->name('quotes');
        Route::get('quotes/{quote}', [QuoteController::class, 'customerShow'])->name('quotes.show');
        Route::post('quotes/{quote}/approve', [QuoteController::class, 'approve'])->name('quotes.approve');
        Route::post('quotes/{quote}/reject', [QuoteController::class, 'reject'])->name('quotes.reject');
    });

    Route::middleware(['employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::get('quotes', [EmployeeController::class, 'quotes'])->name('quotes');
        Route::get('jobs', [EmployeeController::class, 'jobs'])->name('jobs');
        Route::get('jobs/{job}', [EmployeeController::class, 'showJob'])->name('jobs.show');
        Route::patch('jobs/{job}', [EmployeeController::class, 'updateJobStatus'])->name('jobs.update');
        Route::get('jobs-by-month', [EmployeeController::class, 'getJobsByMonth'])->name('jobs.by-month');
    });

    Route::middleware(['owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('dashboard/earnings', [OwnerController::class, 'earningsByPeriod'])->name('dashboard.earnings');
        Route::get('quotes', [QuoteController::class, 'ownerIndex'])->name('quotes');
        Route::get('quotes/{quote}/view', [QuoteController::class, 'ownerView'])->name('quotes.view');
        Route::put('quotes/{quote}', [QuoteController::class, 'ownerUpdate'])->name('quotes.update');
        Route::post('quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
        Route::post('quotes/{quote}/approve', [QuoteController::class, 'ownerApprove'])->name('quotes.approve');
        Route::post('quotes/{quote}/reject', [QuoteController::class, 'ownerReject'])->name('quotes.reject');
        Route::get('employees', [OwnerController::class, 'employees'])->name('employees');
        Route::get('employees/create', [OwnerController::class, 'createEmployee'])->name('employees.create');
        Route::post('employees', [OwnerController::class, 'storeEmployee'])->name('employees.store');
        Route::get('employees/{employee}/edit', [OwnerController::class, 'editEmployee'])->name('employees.edit');
        Route::put('employees/{employee}', [OwnerController::class, 'updateEmployee'])->name('employees.update');
        Route::post('jobs/{job}/assign', [OwnerController::class, 'assignEmployeeApi'])->name('jobs.assign');
        Route::patch('employees/{employee}/toggle-status', [OwnerController::class, 'toggleEmployeeStatus'])->name('employees.toggle-status');
        Route::delete('employees/{employee}', [OwnerController::class, 'destroyEmployee'])->name('employees.destroy');
        Route::get('jobs', [OwnerController::class, 'jobs'])->name('jobs');
        Route::get('jobs/{job}', [OwnerController::class, 'showJob'])->name('jobs.show');
        Route::delete('jobs/{job}', [OwnerController::class, 'destroyJob'])->name('jobs.destroy');
        Route::resource('inventory', InventoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('services/{id}/{serviceType}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{id}/{serviceType}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{id}/{serviceType}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('reports', [ReportsController::class, 'index'])->name('reports');
    });
});

// MCP API routes for PrintBuddy (protected with API key)
Route::middleware(['api.key'])->prefix('api/printbuddy')->name('printbuddy.')->group(function () {
    Route::get('/services', [PrintbuddyController::class, 'getServices'])->name('services');
    Route::get('/inventory', [PrintbuddyController::class, 'getInventory'])->name('inventory');
});

require __DIR__.'/settings.php';
