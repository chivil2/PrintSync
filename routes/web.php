<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerChatController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OwnerChatController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrintbuddyController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceController;
use App\Models\PrintingService;
use App\Models\TechnicalService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $printing = PrintingService::where('is_active', true)
        ->latest()
        ->take(6)
        ->get()
        ->map(fn ($s) => array_merge($s->toArray(), ['type' => 'printing']));

    $technical = TechnicalService::where('is_active', true)
        ->latest()
        ->take(6)
        ->get()
        ->map(fn ($s) => array_merge($s->toArray(), ['type' => 'technical']));

    $featuredServices = $printing
        ->merge($technical)
        ->sortByDesc('created_at')
        ->take(8)
        ->values();

    return view('landing', compact('featuredServices'));
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
        Route::post('quotes/{quote}/negotiate', [QuoteController::class, 'negotiate'])->name('quotes.negotiate');
        Route::post('quotes/{quote}/cancel', [QuoteController::class, 'cancelOrder'])->name('quotes.cancel');

        Route::get('quotes/{quote}/pay', [PaymentController::class, 'show'])->name('quotes.pay');
        Route::post('quotes/{quote}/payments', [PaymentController::class, 'store'])->name('quotes.payments.store');
        Route::get('payments/{payment}', [PaymentController::class, 'showPayment'])->name('payments.show');

        Route::get('chat', [CustomerChatController::class, 'index'])->name('chat.index');
        Route::get('chat/{conversation}', [CustomerChatController::class, 'show'])->name('chat.show');
        Route::post('chat/{conversation}/messages', [CustomerChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('chat/{conversation}/read', [CustomerChatController::class, 'markRead'])->name('chat.read');
        Route::get('chat/{conversation}/poll', [CustomerChatController::class, 'poll'])->name('chat.poll');
        Route::get('quotes/{quote}/chat', [CustomerChatController::class, 'openForQuote'])->name('chat.open-for-quote');
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
        Route::post('notifications/{id}/read', [OwnerController::class, 'markNotificationRead'])->name('notifications.read');
        Route::get('notifications/unread', [OwnerController::class, 'unreadNotifications'])->name('notifications.unread');
        Route::get('quotes', [QuoteController::class, 'ownerIndex'])->name('quotes');
        Route::get('quotes/{quote}/view', [QuoteController::class, 'ownerView'])->name('quotes.view');
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
        Route::get('jobs-by-month', [OwnerController::class, 'getJobsByMonth'])->name('jobs.by-month');
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
        Route::get('payments', [PaymentController::class, 'ownerIndex'])->name('payments');
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('printbuddy', [PrintbuddyController::class, 'index'])->name('printbuddy');
        Route::post('printbuddy/notes', [PrintbuddyController::class, 'storeNote'])->name('printbuddy.notes.store');
        Route::delete('printbuddy/notes/{note}', [PrintbuddyController::class, 'destroyNote'])->name('printbuddy.notes.destroy');
        Route::post('printbuddy/api-key', [PrintbuddyController::class, 'saveApiKey'])->name('printbuddy.api-key');

        Route::get('reports', [OwnerController::class, 'reports'])->name('reports');
        Route::get('reports/monthly-orders', [OwnerController::class, 'getMonthlyOrderDetails'])->name('reports.monthly-orders');
        Route::get('chat', [OwnerChatController::class, 'index'])->name('chat.index');
        Route::get('chat/{conversation}', [OwnerChatController::class, 'show'])->name('chat.show');
        Route::post('chat/{conversation}/messages', [OwnerChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('chat/{conversation}/read', [OwnerChatController::class, 'markRead'])->name('chat.read');
        Route::get('chat/{conversation}/poll', [OwnerChatController::class, 'poll'])->name('chat.poll');

        Route::get('profile', [OwnerController::class, 'profile'])->name('profile');
    });
});

// MCP API routes for PrintBuddy (protected with API key)
Route::middleware(['api.key'])->prefix('api/printbuddy')->name('printbuddy.')->group(function () {
    Route::get('/services', [PrintbuddyController::class, 'getServices'])->name('services');
    Route::get('/inventory', [PrintbuddyController::class, 'getInventory'])->name('inventory');
    Route::get('/employees', [PrintbuddyController::class, 'getEmployees'])->name('employees');
    Route::get('/jobs', [PrintbuddyController::class, 'getJobs'])->name('jobs');
    Route::get('/quotes', [PrintbuddyController::class, 'getQuotes'])->name('quotes');
});

require __DIR__.'/settings.php';
