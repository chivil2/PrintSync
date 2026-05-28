<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\QuoteController;
use App\Models\Quote;
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
        Route::get('orders/{order}', [CustomerController::class, 'showOrder'])->name('orders.show');
        Route::delete('orders/{order}', [CustomerController::class, 'destroyOrder'])->name('orders.destroy');
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

            $quotesCount = Quote::count();
            $pendingQuotes = Quote::where('status', 'draft')->count();
            $sentQuotes = Quote::where('status', 'sent')->count();
            $acceptedQuotes = Quote::where('status', 'accepted')->count();
            $totalRevenue = Quote::where('status', 'accepted')->sum('total');

            $completedJobs = ServiceJob::where('status', 'completed')->count();
            $completedJobRevenue = Quote::whereHas('serviceJob', function ($query) {
                $query->where('status', 'completed');
            })->where('status', 'accepted')->sum('total');
            $averageOrderValue = $completedJobs > 0 ? $completedJobRevenue / $completedJobs : 0;
            $highestOrderValue = Quote::whereHas('serviceJob', function ($query) {
                $query->where('status', 'completed');
            })->where('status', 'accepted')->max('total') ?? 0;

            $serviceTypes = ServiceJob::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type');

            $employees = User::role('employee')
                ->where('employee_status', 'active')
                ->withCount(['serviceJobs as assigned_jobs_count' => function ($query) {
                    $query->where('status', 'in_progress');
                }])
                ->get();

            return view('owner.dashboard', [
                'jobsCount' => $jobsCount,
                'recentJobs' => $recentJobs,
                'customersCount' => $customersCount,
                'recentCustomers' => $recentCustomers,
                'jobsByStatus' => $jobsByStatus,
                'quotesCount' => $quotesCount,
                'pendingQuotes' => $pendingQuotes,
                'sentQuotes' => $sentQuotes,
                'acceptedQuotes' => $acceptedQuotes,
                'totalRevenue' => $totalRevenue,
                'completedJobs' => $completedJobs,
                'completedJobRevenue' => $completedJobRevenue,
                'averageOrderValue' => $averageOrderValue,
                'highestOrderValue' => $highestOrderValue,
                'serviceTypes' => $serviceTypes,
                'employees' => $employees,
            ]);
        })->name('dashboard');
        Route::get('quotes', [QuoteController::class, 'ownerIndex'])->name('quotes');
        Route::get('quotes/{quote}/edit', [QuoteController::class, 'ownerEdit'])->name('quotes.edit');
        Route::put('quotes/{quote}', [QuoteController::class, 'ownerUpdate'])->name('quotes.update');
        Route::post('quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
        Route::get('employees', [OwnerController::class, 'employees'])->name('employees');
        Route::get('employees/create', [OwnerController::class, 'createEmployee'])->name('employees.create');
        Route::post('employees', [OwnerController::class, 'storeEmployee'])->name('employees.store');
        Route::get('employees/{employee}/edit', [OwnerController::class, 'editEmployee'])->name('employees.edit');
        Route::put('employees/{employee}', [OwnerController::class, 'updateEmployee'])->name('employees.update');
        Route::patch('employees/{employee}/toggle-status', [OwnerController::class, 'toggleEmployeeStatus'])->name('employees.toggle-status');
        Route::delete('employees/{employee}', [OwnerController::class, 'destroyEmployee'])->name('employees.destroy');
        Route::get('jobs', [OwnerController::class, 'jobs'])->name('jobs');
        Route::get('jobs/{job}', [OwnerController::class, 'showJob'])->name('jobs.show');
        Route::patch('jobs/{job}/assign', [OwnerController::class, 'assignEmployee'])->name('jobs.assign');
    });
});

require __DIR__.'/settings.php';
