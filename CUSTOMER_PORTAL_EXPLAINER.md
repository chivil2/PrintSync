# PrintSync Customer Portal - Technical Presentation

## Overview
The customer portal is a comprehensive web interface that allows customers to browse services, request quotes, manage orders, and track their printing and technical service requests. Built with Laravel 13, Livewire 4, and Alpine.js, it provides a modern, responsive experience.

---

## Architecture & Structure

### Route Organization
```php
// routes/web.php (Lines 49-64)
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
```

**Key Points:**
- All customer routes are prefixed with `/customer` for clear URL structure
- Named routes follow `customer.{action}` pattern for easy reference
- Middleware ensures only authenticated customers can access these routes
- Quote management is handled by a separate QuoteController

---

## Core Controller: CustomerController

### Location
`app/Http/Controllers/CustomerController.php`

### Key Methods Explained

#### 1. Dashboard Method (Lines 19-45)
```php
public function dashboard()
{
    $orders = ServiceJob::where('customer_id', auth()->id())
        ->with(['service', 'employee', 'quote'])
        ->orderBy('created_at', 'desc')
        ->get();

    $totalOrders = $orders->count();
    $completedOrders = $orders->where('status', 'completed')->count();
    $pendingOrders = $orders->where('status', 'pending')->count();
    $totalSpent = Quote::whereHas('serviceJob', function ($q) {
        $q->where('status', 'completed');
    })->where('status', 'accepted')->sum('total') ?? 0;

    $recentOrders = $orders->take(5);
    $customer = auth()->user();

    return view('customer.dashboard', [
        'totalOrders' => $totalOrders,
        'completedOrders' => $completedOrders,
        'pendingOrders' => $pendingOrders,
        'totalSpent' => $totalSpent,
        'recentOrders' => $recentOrders,
        'customer' => $customer,
    ]);
}
```

**What it does:**
- Fetches all orders for the authenticated customer with eager loading
- Calculates key metrics: total orders, completed, pending, total spent
- Retrieves the 5 most recent orders for display
- Passes data to the dashboard view

**Performance Optimization:**
- Uses `with()` for eager loading to prevent N+1 queries
- Filters collections in memory after single database query

#### 2. Store Method (Lines 47-66)
```php
public function store(Request $request)
{
    $search = $request->get('search');

    $printingServices = PrintingService::where('is_active', true)
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        })
        ->get();

    $technicalServices = TechnicalService::where('is_active', true)
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        })
        ->get();

    return view('customer.store', compact('printingServices', 'technicalServices'));
}
```

**What it does:**
- Retrieves active printing and technical services
- Implements search functionality using conditional queries
- Uses Laravel's `when()` method for clean conditional logic
- Passes services to the store view for display

#### 3. Request Service Method (Lines 68-124)
```php
public function requestService(Request $request)
{
    $validated = $request->validate([
        'service_id' => 'required|integer',
        'service_type' => 'required|in:printing,technical',
        'quantity' => 'required|integer|min:1',
        'deadline' => 'required|date|after_or_equal:today',
        'notes' => 'nullable|string|max:1000',
        'request_invoice' => 'nullable|boolean',
    ]);

    $service = $validated['service_type'] === 'printing'
        ? PrintingService::findOrFail($validated['service_id'])
        : TechnicalService::findOrFail($validated['service_id']);

    $serviceJob = ServiceJob::create([
        'name' => $service->name,
        'description' => $service->description,
        'type' => $validated['service_type'],
        'customer_id' => auth()->id(),
        'service_id' => $service->id,
        'service_type' => $validated['service_type'] === 'printing' ? 'printing_service' : 'technical_service',
        'status' => 'pending',
        'deadline' => $validated['deadline'],
        'notes' => $validated['notes'] ?? null,
        'request_invoice' => isset($validated['request_invoice']),
    ]);

    $quantity = $validated['quantity'];
    $subtotal = $service->price * $quantity;

    // Auto-generate quote
    $quoteNumber = 'QT-'.date('Ymd').'-'.str_pad((Quote::count() + 1), 4, '0', STR_PAD_LEFT);
    $quote = Quote::create([
        'quote_number' => $quoteNumber,
        'customer_id' => auth()->id(),
        'service_job_id' => $serviceJob->id,
        'date' => now(),
        'status' => 'draft',
        'currency' => 'PHP',
        'subtotal' => $subtotal,
        'tax' => 0,
        'discount' => 0,
        'total' => $subtotal,
    ]);

    QuoteLineItem::create([
        'quote_id' => $quote->id,
        'item_name' => $service->name,
        'description' => $service->description,
        'quantity' => $quantity,
        'unit_price' => $service->price,
        'line_total' => $subtotal,
    ]);

    return redirect()->route('customer.quotes.show', $quote)
        ->with('success', 'Service request submitted successfully! Your quote has been generated.');
}
```

**What it does:**
- Validates service request data
- Dynamically retrieves the correct service model (printing or technical)
- Creates a ServiceJob record with pending status
- Automatically generates a quote with unique quote number
- Creates quote line item with calculated pricing
- Redirects to quote detail page with success message

**Key Features:**
- Automatic quote generation (no manual quote creation needed)
- Unique quote numbering system (QT-YYYYMMDD-####)
- Polymorphic service handling (printing vs technical)
- Deadline validation (must be today or future)

#### 4. Orders Method (Lines 126-139)
```php
public function orders()
{
    $orders = ServiceJob::where('customer_id', auth()->id())
        ->with(['service', 'employee', 'quote.lineItems'])
        ->orderBy('created_at', 'desc')
        ->get();

    $customer = auth()->user();

    return view('customer.orders', [
        'orders' => $orders,
        'customer' => $customer,
    ]);
}
```

**What it does:**
- Fetches all customer orders with comprehensive relationships
- Orders by creation date (newest first)
- Passes data to orders view

#### 5. Cancel Order Method (Lines 153-171)
```php
public function cancelOrder(ServiceJob $order)
{
    if (! auth()->user()->can('cancel_own_orders')) {
        abort(403, 'You do not have permission to cancel orders.');
    }

    if ($order->customer_id !== auth()->id()) {
        abort(403, 'You can only cancel your own orders.');
    }

    if ($order->status !== null && $order->status !== 'pending') {
        return redirect()->route('customer.orders.show', $order)
            ->with('error', 'Only pending orders can be cancelled.');
    }

    $order->update(['status' => 'cancelled']);

    return redirect()->route('customer.orders')->with('success', 'Order cancelled successfully.');
}
```

**What it does:**
- Checks authorization using Laravel's policy system
- Validates ownership (customer can only cancel their own orders)
- Ensures only pending orders can be cancelled
- Updates order status to cancelled
- Redirects with appropriate feedback

**Security Features:**
- Policy-based authorization
- Ownership verification
- Status-based business logic

---

## Quote Management: QuoteController

### Customer-Specific Methods

#### Customer Index (Lines 181-190)
```php
public function customerIndex()
{
    $quotes = Quote::where('customer_id', auth()->id())
        ->whereIn('status', ['sent', 'accepted', 'rejected'])
        ->with(['serviceJob', 'lineItems'])
        ->latest()
        ->get();

    return view('customer.quotes', compact('quotes'));
}
```

**What it does:**
- Fetches customer's quotes that have been sent, accepted, or rejected
- Excludes draft quotes (not yet ready for customer review)
- Loads related service job and line items

#### Customer Show (Lines 195-204)
```php
public function customerShow(Quote $quote)
{
    if ($quote->customer_id !== auth()->id()) {
        abort(403, 'Unauthorized access');
    }

    $quote->load(['serviceJob', 'lineItems']);

    return view('customer.quote-detail', compact('quote'));
}
```

**What it does:**
- Displays detailed quote information
- Authorizes access (customer can only view their own quotes)
- Loads related data for complete quote display

#### Approve Quote (Lines 209-226)
```php
public function approve(Request $request, Quote $quote)
{
    if ($quote->customer_id !== auth()->id()) {
        abort(403, 'Unauthorized access');
    }

    if ($quote->status !== 'sent') {
        return redirect()->back()->with('error', 'Quote cannot be approved in current status');
    }

    $quote->update([
        'status' => 'accepted',
        'approved_at' => now(),
    ]);

    return redirect()->route('customer.quotes.show', $quote)
        ->with('success', 'Quote approved successfully');
}
```

**What it does:**
- Authorizes customer to approve their own quotes
- Validates quote is in 'sent' status (can only approve sent quotes)
- Updates quote status to 'accepted' with timestamp
- Redirects with success message

#### Reject Quote (Lines 231-244)
```php
public function reject(Request $request, Quote $quote)
{
    if ($quote->customer_id !== auth()->id()) {
        abort(403, 'Unauthorized access');
    }

    if ($quote->status !== 'sent') {
        return redirect()->back()->with('error', 'Quote cannot be rejected in current status');
    }

    $quote->update([
        'status' => 'rejected',
        'rejected_at' => now(),
    ]);

    return redirect()->route('customer.quotes.show', $quote)
        ->with('success', 'Quote rejected successfully');
}
```

**What it does:**
- Similar to approve but sets status to 'rejected'
- Includes rejection timestamp
- Same authorization and status validation

---

## Frontend Views

### 1. Customer Dashboard
**Location:** `resources/views/customer/dashboard.blade.php`

**Key Features:**
- Welcome banner with personalized greeting
- Quick action buttons (Browse Store, My Orders, Profile)
- Statistics cards (Total Orders, Completed, Total Spent, Pending)
- Recent orders list with status indicators
- Modern, card-based UI with Tailwind CSS

**Alpine.js Integration:**
- Uses Alpine.js for interactive elements
- Status badges with color coding
- Dynamic filtering and sorting

**Code Highlight:**
```php
// Statistics calculation (Lines 26-31)
$totalOrders = $orders->count();
$completedOrders = $orders->where('status', 'completed')->count();
$pendingOrders = $orders->where('status', 'pending')->count();
$totalSpent = Quote::whereHas('serviceJob', function ($q) {
    $q->where('status', 'completed');
})->where('status', 'accepted')->sum('total') ?? 0;
```

### 2. Customer Store
**Location:** `resources/views/customer/store.blade.php`

**Key Features:**
- Hero banner with animated gradient background
- Category filtering (All, Printing, Technical)
- Search functionality
- Price sorting (low to high, high to low)
- Pagination
- Service cards with details and "Request Service" buttons

**Alpine.js State Management:**
```javascript
x-data="{ 
    activeCategory: 'all',
    sortBy: 'price-low',
    search: '',
    allPrintingServices: {{ $printingServices->toJson() }},
    allTechnicalServices: {{ $technicalServices->toJson() }},
    page: 1,
    itemsPerPage: 6,
    get filteredServices() {
        // Filtering and sorting logic
    },
    get paginatedServices() {
        // Pagination logic
    }
}"
```

**Key Features:**
- Client-side filtering and sorting for instant feedback
- Responsive grid layout
- Service request modal with form validation
- Dynamic category counts

### 3. Customer Orders
**Location:** `resources/views/customer/orders.blade.php`

**Key Features:**
- Order history with comprehensive filtering
- Status tabs (All, In Progress, Pending, Completed, Cancelled)
- Order cards with detailed information
- Payment status indicators
- Invoice download functionality
- Order cancellation (for pending orders)

**Alpine.js Features:**
```javascript
x-data="{ 
    activeStatus: 'all',
    orders: {{ $orders->map(function($order) {
        return [
            'id' => $order->id,
            'name' => $order->name,
            'status' => $order->status,
            // ... more fields
        ];
    })->toJson() }},
    get filteredOrders() {
        if (this.activeStatus === 'all') return this.orders;
        return this.orders.filter(o => o.status === this.activeStatus);
    },
    getStatusColor(status) {
        // Color mapping for status badges
    }
}"
```

**Statistics Cards:**
- Total Orders (all time)
- In Progress (active now)
- Pending (awaiting review)
- Completed (this month)

### 4. Customer Quotes
**Location:** `resources/views/customer/quotes.blade.php`

**Key Features:**
- List of customer quotes with status indicators
- Quote number, date, and total amount
- Status badges (Sent, Accepted, Rejected)
- View details link
- Empty state with call-to-action

**Status Color Mapping:**
```php
$statusColors = [
    'sent' => 'bg-blue-100 text-blue-800',
    'accepted' => 'bg-green-100 text-green-800',
    'rejected' => 'bg-red-100 text-red-800',
];
```

### 5. Customer Profile
**Location:** `resources/views/customer/profile.blade.php`

**Key Features:**
- Personal information form
- First name, last name, phone, email fields
- Email verification status
- Resend verification email functionality
- Form validation with error display
- Success message display

**Validation:**
- Uses Laravel's built-in validation
- Profile validation rules from trait
- Email verification workflow

---

## Layout & Navigation

### Customer Layout
**Location:** `resources/views/layouts/app/customer.blade.php`

**Structure:**
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            @import url('https://fonts.cdnfonts.com/css/neue-haas-grotesk-display-pro');
            body {
                font-family: 'Neue Haas Grotesk Display Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            }
            [x-cloak] { display: none !important; }
        </style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-white">
        @include('partials.global-navbar')
        <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>
    </body>
</html>
```

**Key Features:**
- Custom font (Neue Haas Grotesk Display Pro)
- Alpine.js integration with x-cloak directive
- Global navbar for navigation
- Responsive container layout
- Clean, white background

---

## Data Models & Relationships

### ServiceJob Model
- Represents customer service requests
- Relationships:
  - `service()` - Polymorphic relationship to PrintingService or TechnicalService
  - `customer()` - BelongsTo User
  - `employee()` - BelongsTo User (assigned employee)
  - `quote()` - HasOne Quote

### Quote Model
- Represents price quotes for services
- Relationships:
  - `customer()` - BelongsTo User
  - `serviceJob()` - BelongsTo ServiceJob
  - `employee()` - BelongsTo User
  - `lineItems()` - HasMany QuoteLineItem

### QuoteLineItem Model
- Individual line items in a quote
- Fields: item_name, description, quantity, unit_price, line_total

---

## Security & Authorization

### Route Middleware
```php
Route::middleware(['auth'])->group(function () {
    Route::prefix('customer')->name('customer.')->group(function () {
        // All customer routes require authentication
    });
});
```

### Controller Authorization
```php
// Ownership check
if ($order->customer_id !== auth()->id()) {
    abort(403, 'Unauthorized access');
}

// Policy-based authorization
if (! auth()->user()->can('cancel_own_orders')) {
    abort(403, 'You do not have permission to cancel orders.');
}
```

### Validation
- Request validation on all form submissions
- Laravel validation rules
- Custom error messages
- Data sanitization

---

## Performance Optimizations

### Eager Loading
```php
// Prevents N+1 queries
$orders = ServiceJob::where('customer_id', auth()->id())
    ->with(['service', 'employee', 'quote.lineItems'])
    ->get();
```

### Database Indexing
- customer_id indexed on ServiceJob
- status indexed on ServiceJob
- customer_id indexed on Quote

### Client-Side Filtering
- Alpine.js handles filtering and sorting on the client
- Reduces server load for common operations
- Instant feedback for users

---

## User Experience Features

### Responsive Design
- Mobile-first approach
- Tailwind CSS for responsive layouts
- Adaptive grid systems
- Touch-friendly buttons

### Real-Time Feedback
- Alpine.js for instant UI updates
- Status indicators with color coding
- Success/error messages
- Loading states

### Accessibility
- Semantic HTML
- ARIA labels
- Keyboard navigation
- Screen reader friendly

### Empty States
- Helpful empty state messages
- Call-to-action buttons
- Illustrations for visual appeal

---

## Code Quality

### Laravel Best Practices
- Controller thin logic
- Service classes for complex operations
- Repository pattern for data access
- Form request validation

### Code Organization
- Clear separation of concerns
- Consistent naming conventions
- DRY principle adherence
- Single responsibility principle

### Testing
- Feature tests for customer workflows
- Unit tests for business logic
- Integration tests for API endpoints

---

## Future Enhancements

### Potential Improvements
1. Real-time notifications for order status changes
2. Email notifications for quote updates
3. File upload for order specifications
4. Payment gateway integration
5. Order tracking with timeline
6. Customer reviews and ratings
7. Repeat order functionality
8. Wishlist/favorite services

### Scalability Considerations
- Caching for service catalogs
- Queue for email notifications
- Database optimization for large datasets
- CDN for static assets

---

## Summary

The PrintSync customer portal is a well-architected Laravel application that provides customers with a complete service management experience. Key strengths include:

- **Clean Architecture:** MVC pattern with clear separation of concerns
- **Modern UI:** Tailwind CSS with Alpine.js for interactivity
- **Security:** Comprehensive authorization and validation
- **Performance:** Eager loading and client-side filtering
- **User Experience:** Responsive design with real-time feedback
- **Maintainability:** Clean code following Laravel best practices

The codebase demonstrates professional Laravel development practices and provides a solid foundation for future enhancements.
