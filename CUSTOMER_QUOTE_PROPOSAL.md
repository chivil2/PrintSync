# Customer Quote Feature - Proposed Changes

## Current State Analysis

**What Already Exists:**
- ✅ `quotes` table (quote_number, customer_id, date, status, currency, subtotal, tax, discount, total, terms, notes, employee_id)
- ✅ `quote_line_items` table (quote_id, item_name, description, quantity, unit_price, line_total)
- ✅ `Quote` model with customer, employee, lineItems relationships
- ✅ `QuoteLineItem` model
- ✅ `QuoteController` with API methods (index, store, show, update, destroy)
- ✅ Permission-based access control

**What's Missing for Customer Quotes:**
- ❌ No link between quotes and service_jobs (quotes are standalone)
- ❌ No customer quote routes in web.php
- ❌ No blade views for customer quote interface
- ❌ No approve/reject functionality
- ❌ No status timestamps (sent_at, approved_at, rejected_at)
- ❌ No customer feedback/rejection reason storage
- ❌ QuoteController is API-only (returns JSON, not views)

## Proposed Changes

### 1. Database Migration
**File**: `database/migrations/YYYY_MM_DD_HHMMSS_add_service_job_id_to_quotes_table.php`

```php
Schema::table('quotes', function (Blueprint $table) {
    $table->foreignId('service_job_id')->nullable()->constrained('service_jobs')->onDelete('cascade');
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('rejected_at')->nullable();
    $table->text('rejection_reason')->nullable();
});
```

**Why**: Links quotes to actual service requests, adds status tracking timestamps, stores customer feedback.

### 2. Update Quote Model
**File**: `app/Models/Quote.php`

Add relationship:
```php
public function serviceJob(): BelongsTo
{
    return $this->belongsTo(ServiceJob::class);
}
```

Update fillable to include new fields.

### 3. Add Customer Routes
**File**: `routes/web.php`

Add to customer group:
```php
Route::get('quotes', [QuoteController::class, 'customerIndex'])->name('quotes');
Route::get('quotes/{quote}', [QuoteController::class, 'customerShow'])->name('quotes.show');
Route::post('quotes/{quote}/approve', [QuoteController::class, 'approve'])->name('quotes.approve');
Route::post('quotes/{quote}/reject', [QuoteController::class, 'reject'])->name('quotes.reject');
```

### 4. Add Controller Methods
**File**: `app/Http/Controllers/QuoteController.php`

Add blade view methods:
```php
public function customerIndex()
{
    $quotes = Quote::where('customer_id', auth()->id())
        ->with(['serviceJob', 'lineItems'])
        ->latest()
        ->get();
    
    return view('customer.quotes', compact('quotes'));
}

public function customerShow(Quote $quote)
{
    if ($quote->customer_id !== auth()->id()) {
        abort(403);
    }
    
    $quote->load(['serviceJob', 'lineItems']);
    
    return view('customer.quote-detail', compact('quote'));
}

public function approve(Request $request, Quote $quote)
{
    if ($quote->customer_id !== auth()->id()) {
        abort(403);
    }
    
    $quote->update([
        'status' => 'accepted',
        'approved_at' => now(),
    ]);
    
    return redirect()->route('customer.quotes.show', $quote)
        ->with('success', 'Quote approved successfully');
}

public function reject(Request $request, Quote $quote)
{
    if ($quote->customer_id !== auth()->id()) {
        abort(403);
    }
    
    $validated = $request->validate([
        'rejection_reason' => 'required|string|max:500',
    ]);
    
    $quote->update([
        'status' => 'rejected',
        'rejected_at' => now(),
        'rejection_reason' => $validated['rejection_reason'],
    ]);
    
    return redirect()->route('customer.quotes')
        ->with('success', 'Quote rejected. Owner will be notified.');
}
```

### 5. Create Customer Views
**Files to create**:
- `resources/views/customer/quotes.blade.php` - Quote list with status badges
- `resources/views/customer/quote-detail.blade.php` - Quote detail with approve/reject buttons

**Quote list features**:
- Table showing quote number, date, status, total
- Status badges (pending, sent, accepted, rejected)
- Click to view details
- Filter by status

**Quote detail features**:
- Quote header (number, date, status)
- Service job reference
- Line items table
- Subtotal, tax, discount, total breakdown
- Approve button (if status is sent/pending)
- Reject button with reason textarea (if status is sent/pending)
- Download PDF button (future)

### 6. Update ServiceJob Model
**File**: `app/Models/ServiceJob.php`

Add relationship:
```php
public function quote(): HasOne
{
    return $this->hasOne(Quote::class);
}
```

## Summary of Changes

**Files to modify:**
1. Create migration for quotes table updates
2. Update `app/Models/Quote.php` (add serviceJob relationship, fillable fields)
3. Update `app/Models/ServiceJob.php` (add quote relationship)
4. Update `routes/web.php` (add customer quote routes)
5. Update `app/Http/Controllers/QuoteController.php` (add customer methods)

**Files to create:**
1. `resources/views/customer/quotes.blade.php`
2. `resources/views/customer/quote-detail.blade.php`

**No changes to:**
- Existing quote_line_items table
- Existing QuoteLineItem model
- Existing API methods in QuoteController (they remain for owner/employee)

## Implementation Order

1. Create migration and run it
2. Update models (Quote, ServiceJob)
3. Add customer routes
4. Add controller methods
5. Create quote list view
6. Create quote detail view
7. Test customer quote viewing
8. Test approve/reject functionality

## Questions Before Implementation

1. Should quotes be auto-generated when a service request is submitted, or only created manually by owner?
2. What status should a new quote have when created? (pending, draft, sent?)
3. Should customers be able to see quotes that are still in "draft" status?
4. Do you want email notifications when quotes are sent/approved/rejected?
