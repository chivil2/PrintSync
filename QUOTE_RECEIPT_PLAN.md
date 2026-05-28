# Quote/Receipt System Plan

## Overview
A complete quote and receipt system for PrintSync that allows owners to send quotes to customers, customers to approve/review, and automatic receipt generation upon completion.

## 1. Database Schema

### Quotes Table
```php
Schema::create('quotes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_job_id')->constrained()->onDelete('cascade');
    $table->foreignId('created_by')->constrained('users'); // Owner who created quote
    $table->decimal('subtotal', 10, 2);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2);
    $table->enum('status', ['draft', 'sent', 'pending_approval', 'approved', 'rejected', 'finalized'])->default('draft');
    $table->text('notes')->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('rejected_at')->nullable();
    $table->timestamps();
});
```

### Quote Items Table (line items)
```php
Schema::create('quote_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained()->onDelete('cascade');
    $table->string('description');
    $table->integer('quantity')->default(1);
    $table->decimal('unit_price', 10, 2);
    $table->decimal('line_total', 10, 2);
    $table->string('item_type')->default('service'); // service, material, labor, fee
    $table->timestamps();
});
```

### Receipts Table
```php
Schema::create('receipts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained()->onDelete('cascade');
    $table->foreignId('service_job_id')->constrained()->onDelete('cascade');
    $table->string('receipt_number')->unique();
    $table->decimal('amount_paid', 10, 2);
    $table->string('payment_method')->nullable();
    $table->timestamp('payment_date');
    $table->string('pdf_path')->nullable();
    $table->timestamps();
});
```

## 2. Customer Workflow

### Customer Quote View
- **Location**: `/customer/quotes` or within `/customer/orders`
- **Features**:
  - List of all quotes with status indicators
  - View quote details (items, pricing, notes)
  - Approve or reject quote with optional feedback
  - Download PDF quote
  - See revision history if quote was updated

### Customer Receipt View
- **Location**: `/customer/receipts` or within order details
- **Features**:
  - List of completed orders with receipts
  - View receipt details
  - Download PDF receipt
  - Print receipt option

## 3. Owner Workflow

### Quote Creation
- **Location**: `/owner/quotes/create` or from job details
- **Features**:
  - Select service job to quote
  - Add line items (services, materials, labor, fees)
  - Set quantity and unit price per item
  - Apply discount (percentage or fixed amount)
  - Set tax rate
  - Add notes to customer
  - Preview quote
  - Save as draft or send to customer

### Quote Management
- **Location**: `/owner/quotes`
- **Features**:
  - List all quotes with filters (status, date, customer)
  - Edit draft quotes
  - Resend quotes
  - View customer responses (approve/reject with feedback)
  - Revise rejected quotes
  - Finalize approved quotes → generate receipt

### Receipt Generation
- **Trigger**: When job is marked "completed"
- **Features**:
  - Auto-generate receipt from finalized quote
  - Generate unique receipt number
  - Create PDF
  - Email receipt to customer
  - Owner can regenerate if needed

## 4. Pricing Calculation Logic

### Base Price Components
1. **Service base price** (from PrintingService/TechnicalService)
2. **Quantity multiplier** (if customer ordered multiple)
3. **Rush fee** (based on delivery option):
   - Standard: 0%
   - Express: +15%
   - Rush: +30%
4. **Materials** (if applicable)
5. **Labor** (if additional work needed)
6. **Tax** (configurable rate, default 12%)
7. **Discount** (owner can apply)

### Calculation Formula
```
subtotal = (service_price * quantity) + rush_fee + materials + labor
tax = subtotal * tax_rate
discount = subtotal * discount_rate OR fixed_amount
total = subtotal + tax - discount
```

## 5. Quote Status Flow

```
draft → sent → pending_approval → approved → finalized → receipt
               ↓
             rejected → (revision) → sent
```

**Status definitions:**
- **draft**: Owner is creating/editing, not visible to customer
- **sent**: Quote sent to customer, awaiting response
- **pending_approval**: Customer viewed, hasn't decided
- **approved**: Customer approved, ready to proceed
- **rejected**: Customer rejected, needs revision
- **finalized**: Work completed, receipt generated

## 6. PDF Generation

### Quote PDF
- Company logo/header
- Quote number, date, valid until
- Customer information
- Service details
- Line items table
- Subtotal, tax, discount, total
- Terms and conditions
- Owner signature placeholder

### Receipt PDF
- Company logo/header
- Receipt number, date
- Customer information
- Payment details
- Line items (final)
- Amount paid
- Payment method
- "Thank you" message

**Package**: Use `barryvdh/laravel-dompdf` or `snappy/pdf`

## 7. UI Components Needed

### Customer Side
- Quote list card with status badges
- Quote detail modal/page
- Approve/Reject buttons with feedback form
- Receipt download button
- PDF viewer (optional)

### Owner Side
- Quote creation form with dynamic line items
- Line item add/remove functionality
- Price calculator (real-time preview)
- Quote list with filters
- Quote detail view with action buttons
- Receipt generation trigger

## 8. Routes

```php
// Customer
Route::get('/customer/quotes', [QuoteController::class, 'customerIndex'])->name('customer.quotes');
Route::get('/customer/quotes/{quote}', [QuoteController::class, 'customerShow'])->name('customer.quotes.show');
Route::post('/customer/quotes/{quote}/approve', [QuoteController::class, 'approve'])->name('customer.quotes.approve');
Route::post('/customer/quotes/{quote}/reject', [QuoteController::class, 'reject'])->name('customer.quotes.reject');
Route::get('/customer/quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('customer.quotes.pdf');

Route::get('/customer/receipts', [ReceiptController::class, 'customerIndex'])->name('customer.receipts');
Route::get('/customer/receipts/{receipt}', [ReceiptController::class, 'customerShow'])->name('customer.receipts.show');
Route::get('/customer/receipts/{receipt}/pdf', [ReceiptController::class, 'downloadPdf'])->name('customer.receipts.pdf');

// Owner
Route::get('/owner/quotes', [QuoteController::class, 'ownerIndex'])->name('owner.quotes');
Route::get('/owner/quotes/create', [QuoteController::class, 'create'])->name('owner.quotes.create');
Route::post('/owner/quotes', [QuoteController::class, 'store'])->name('owner.quotes.store');
Route::get('/owner/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('owner.quotes.edit');
Route::put('/owner/quotes/{quote}', [QuoteController::class, 'update'])->name('owner.quotes.update');
Route::post('/owner/quotes/{quote}/send', [QuoteController::class, 'send'])->name('owner.quotes.send');
Route::post('/owner/quotes/{quote}/finalize', [QuoteController::class, 'finalize'])->name('owner.quotes.finalize');
Route::get('/owner/quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('owner.quotes.pdf');
```

## 9. Implementation Priority

### Phase 1: Core Database & Basic CRUD
- Create migrations
- Create Quote and QuoteItem models
- Basic owner quote creation (single item)
- Quote list view

### Phase 2: Customer Interaction
- Customer quote view
- Approve/reject functionality
- Quote status updates
- Email notifications

### Phase 3: Advanced Features
- Multiple line items
- Discount/tax calculation
- Quote revisions
- PDF generation

### Phase 4: Receipt System
- Receipt model & migration
- Auto-generation on job completion
- Receipt PDF
- Customer receipt view

## 10. Considerations

- **Currency**: All amounts in PHP (₱)
- **Tax rate**: Make configurable in settings
- **Quote validity**: Set expiration date (e.g., 30 days)
- **Versioning**: Track quote revisions
- **Notifications**: Email/SMS when quote sent/approved/rejected
- **Permissions**: Only owner can create/edit quotes
- **Audit trail**: Log all quote status changes
