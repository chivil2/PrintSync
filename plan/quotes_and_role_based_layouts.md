# Quotes CSV Import and Role-Based Layout Implementation Plan

## Overview
This document outlines all changes and additions made to implement a quotes CSV import system and role-based layout architecture for the PrintSync application.

---

## Part 1: Quotes CSV Import System

### Database Changes

#### 1. Quotes Table Migration
**File:** `database/migrations/2026_05_08_040111_create_quotes_table.php`

**Purpose:** Create the main quotes table to store quote metadata from CSV imports.

**Schema:**
- `id` - Primary key
- `quote_number` - Unique quote identifier (e.g., "QT-000015")
- `customer_id` - Foreign key to users table (cascade delete)
- `date` - Quote date
- `status` - Quote status (pending, sent, accepted, rejected)
- `currency` - Currency code (default: PHP)
- `subtotal` - Subtotal amount (decimal 10,2)
- `tax` - Tax amount (decimal 10,2)
- `discount` - Discount amount (decimal 10,2)
- `total` - Final total (decimal 10,2)
- `terms` - Terms and conditions text (nullable)
- `notes` - Additional notes (nullable)
- `employee_id` - Assigned employee (nullable, set null on delete)
- `timestamps` - created_at, updated_at

#### 2. Quote Line Items Table Migration
**File:** `database/migrations/2026_05_08_040400_create_quote_line_items_table.php`

**Purpose:** Create the quote_line_items table to store individual line items for each quote.

**Schema:**
- `id` - Primary key
- `quote_id` - Foreign key to quotes table (cascade delete)
- `item_name` - Service/item name
- `description` - Item description (nullable)
- `quantity` - Item quantity (decimal 10,2, default 1)
- `unit_price` - Price per unit (decimal 10,2)
- `line_total` - Line item total (decimal 10,2)
- `timestamps` - created_at, updated_at

### Models

#### 1. Quote Model
**File:** `app/Models/Quote.php`

**Purpose:** Eloquent model for quotes table with relationships.

**Fillable Fields:** All database columns except id and timestamps

**Relationships:**
- `customer()` - BelongsTo User
- `employee()` - BelongsTo User
- `lineItems()` - HasMany QuoteLineItem

#### 2. QuoteLineItem Model
**File:** `app/Models/QuoteLineItem.php`

**Purpose:** Eloquent model for quote_line_items table with relationships.

**Fillable Fields:** All database columns except id and timestamps

**Relationships:**
- `quote()` - BelongsTo Quote

### CSV Import Command

#### ImportQuotesFromCsv Command
**File:** `app/Console/Commands/ImportQuotesFromCsv.php`

**Purpose:** Artisan command to import quotes from CSV files with deduplication logic.

**Usage:**
```bash
# Import with duplicate skipping (default)
php artisan quotes:import path/to/quotes.csv

# Import and update existing quotes
php artisan quotes:import path/to/quotes.csv --update
```

**Features:**
- **Deduplication:** Checks if `quote_number` already exists in database
- **Flexible Column Mapping:** Tries multiple column names/positions for CSV data
- **Customer Handling:** Finds existing customer by ID/email or creates new customer
- **Transaction Safety:** Uses database transaction (commits only if all rows succeed)
- **Error Handling:** Reports import, update, skip, and error counts
- **Date Parsing:** Safe date parsing with fallback to current date

**CSV Column Mapping:**
- Quote number: `quote_number`, `Quote Number`, or column index 2
- Date: `date` or column index 0
- Status: `status` or column index 3
- Currency: `currency` or column index 6
- Subtotal: `subtotal` or column index 9
- Tax: `tax` or column index 12
- Discount: `discount` or column index 13
- Total: `total` or column index 14
- Terms: `terms` or column index 17
- Item name: `item_name` or column index 19
- Quantity: `quantity` or column index 20
- Unit price: `unit_price` or column index 21
- Line total: `line_total` or column index 22

---

## Part 2: Role-Based Layout Architecture

### Middleware

#### 1. EnsureEmployeeRole Middleware
**File:** `app/Http/Middleware/EnsureEmployeeRole.php`

**Purpose:** Middleware to ensure authenticated user has employee role.

**Logic:**
- Checks if user is authenticated (redirects to login if not)
- Checks if user has 'employee' role (aborts with 403 if not)
- Allows request to proceed if both checks pass

#### 2. EnsureOwnerRole Middleware
**File:** `app/Http/Middleware/EnsureOwnerRole.php`

**Purpose:** Middleware to ensure authenticated user has owner role.

**Logic:**
- Checks if user is authenticated (redirects to login if not)
- Checks if user has 'owner' role (aborts with 403 if not)
- Allows request to proceed if both checks pass

### Middleware Registration
**File:** `bootstrap/app.php`

**Changes:** Added middleware aliases in the `withMiddleware` closure:
```php
$middleware->alias([
    'employee' => \App\Http\Middleware\EnsureEmployeeRole::class,
    'owner' => \App\Http\Middleware\EnsureOwnerRole::class,
]);
```

### Role-Based Login Redirection

**Note:** Laravel Fortify v1 does not support the `loginResponse()` method. The redirection is handled in the dashboard route instead.

#### Dashboard Route Update
**File:** `routes/web.php`

**Changes:** Modified the dashboard route to handle role-based redirection after login.

**Logic:**
```php
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
    return view('dashboard');
})->name('dashboard');
```

**Flow:**
- Employee → Redirect to `employee.dashboard`
- Owner → Redirect to `owner.dashboard`
- Customer → Redirect to `customer.store`
- Default → Show default dashboard view

#### FortifyServiceProvider Update
**File:** `app/Providers/FortifyServiceProvider.php`

**Changes:** Removed role restriction from `authenticateUsing` (previously only allowed customers) to allow all roles to authenticate.

### Layouts

#### 1. Employee Layout
**File:** `resources/views/layouts/employee.blade.php`

**Purpose:** Employee-specific layout with sidebar navigation.

**Features:**
- Flux UI sidebar component
- Employee-specific navigation items:
  - Dashboard
  - My Quotes
  - Service Jobs
- Mobile-responsive with header user menu
- Desktop user menu in sidebar
- Toast notifications support
- Flux scripts inclusion

#### 2. Owner Layout
**File:** `resources/views/layouts/owner.blade.php`

**Purpose:** Owner-specific layout with sidebar navigation.

**Features:**
- Flux UI sidebar component
- Owner-specific navigation items:
  - Dashboard
  - All Quotes
  - Employees
  - Service Jobs
- Mobile-responsive with header user menu
- Desktop user menu in sidebar
- Toast notifications support
- Flux scripts inclusion

### Routes

#### Employee Routes
**File:** `routes/web.php`

**Route Group:**
- Middleware: `employee`
- Prefix: `employee`
- Name prefix: `employee.`

**Routes:**
- `GET /employee/dashboard` → `employee.dashboard` → Employee dashboard view
- `GET /employee/quotes` → `employee.quotes` → Employee quotes view
- `GET /employee/jobs` → `employee.jobs` → Employee jobs view

#### Owner Routes
**File:** `routes/web.php`

**Route Group:**
- Middleware: `owner`
- Prefix: `owner`
- Name prefix: `owner.`

**Routes:**
- `GET /owner/dashboard` → `owner.dashboard` → Owner dashboard view
- `GET /owner/quotes` → `owner.quotes` → Owner quotes view
- `GET /owner/employees` → `owner.employees` → Owner employees view
- `GET /owner/jobs` → `owner.jobs` → Owner jobs view

### Views

#### Employee Views

**1. Employee Dashboard**
**File:** `resources/views/employee/dashboard.blade.php`

**Purpose:** Employee dashboard with statistics cards.

**Content:**
- Welcome message with user name
- Statistics cards:
  - Assigned Quotes (placeholder)
  - Pending Jobs (placeholder)
  - Completed Jobs (placeholder)

**2. Employee Quotes**
**File:** `resources/views/employee/quotes.blade.php`

**Purpose:** Placeholder view for employee quotes management.

**3. Employee Jobs**
**File:** `resources/views/employee/jobs.blade.php`

**Purpose:** Placeholder view for employee service jobs management.

#### Owner Views

**1. Owner Dashboard**
**File:** `resources/views/owner/dashboard.blade.php`

**Purpose:** Owner dashboard with statistics cards.

**Content:**
- Welcome message with user name
- Statistics cards:
  - Total Quotes (placeholder)
  - Total Employees (placeholder)
  - Active Jobs (placeholder)
  - Revenue in PHP (placeholder)

**2. Owner Quotes**
**File:** `resources/views/owner/quotes.blade.php`

**Purpose:** Placeholder view for owner quotes management.

**3. Owner Employees**
**File:** `resources/views/owner/employees.blade.php`

**Purpose:** Placeholder view for owner employee management.

**4. Owner Jobs**
**File:** `resources/views/owner/jobs.blade.php`

**Purpose:** Placeholder view for owner service jobs management.

---

## Architecture Flow

### Login Flow
```
User submits login form
    ↓
Fortify authentication
    ↓
RoleBasedLoginResponse checks user role
    ↓
Redirect based on role:
    - Employee → /employee/dashboard
    - Owner → /owner/dashboard
    - Customer → /customer/store
    ↓
Middleware checks role for protected routes
    ↓
Role-specific layout is applied
    ↓
View is rendered
```

### CSV Import Flow
```
Run: php artisan quotes:import file.csv
    ↓
Command reads CSV header
    ↓
For each row:
    ↓
Extract quote_number
    ↓
Check if quote exists in database
    ↓
If exists:
    - Skip (default) OR
    - Update (if --update flag)
    ↓
If new:
    - Find/create customer
    - Create quote record
    ↓
Import line items
    ↓
Report statistics
```

---

## Security Considerations

1. **Middleware Protection:** All employee and owner routes are protected by role-checking middleware
2. **Authentication Check:** Middleware redirects unauthenticated users to login
3. **Authorization Check:** Middleware aborts with 403 if user lacks required role
4. **Transaction Safety:** CSV import uses database transactions to ensure data integrity
5. **Deduplication:** CSV import prevents duplicate quotes based on quote_number
6. **Foreign Key Constraints:** Database enforces referential integrity

---

## Next Steps

### High Priority
1. Add quote management permissions to RoleAndPermissionSeeder
2. Create QuoteController for CRUD operations
3. Implement employee quotes list view with data
4. Implement owner quotes list view with data

### Medium Priority
1. Create quote details views
2. Implement quote status update functionality
3. Add quote-to-job conversion logic
4. Implement employee assignment for quotes

### Low Priority
1. Test role-based layout switching
2. Test CSV import functionality
3. Add email notifications for quote status changes
4. Create quote PDF generation

---

## Testing Checklist

- [ ] Test employee login redirects to employee dashboard
- [ ] Test owner login redirects to owner dashboard
- [ ] Test customer login redirects to customer store
- [ ] Test employee cannot access owner routes
- [ ] Test owner cannot access employee routes
- [ ] Test CSV import with new quotes
- [ ] Test CSV import with duplicate quotes (skip mode)
- [ ] Test CSV import with duplicate quotes (update mode)
- [ ] Test customer creation during CSV import
- [ ] Test quote_number uniqueness constraint
- [ ] Test foreign key constraints

---

## File Summary

### Database Files Created
- `database/migrations/2026_05_08_040111_create_quotes_table.php`
- `database/migrations/2026_05_08_040400_create_quote_line_items_table.php`

### Model Files Created
- `app/Models/Quote.php`
- `app/Models/QuoteLineItem.php`

### Command Files Created
- `app/Console/Commands/ImportQuotesFromCsv.php`

### Middleware Files Created
- `app/Http/Middleware/EnsureEmployeeRole.php`
- `app/Http/Middleware/EnsureOwnerRole.php`

### Fortify Files Created/Modified
- `app/Actions/Fortify/RoleBasedLoginResponse.php` (created)
- `app/Providers/FortifyServiceProvider.php` (modified)

### Configuration Files Modified
- `bootstrap/app.php` (added middleware aliases)

### Layout Files Created
- `resources/views/layouts/employee.blade.php`
- `resources/views/layouts/owner.blade.php`

### View Files Created
- `resources/views/employee/dashboard.blade.php`
- `resources/views/employee/quotes.blade.php`
- `resources/views/employee/jobs.blade.php`
- `resources/views/owner/dashboard.blade.php`
- `resources/views/owner/quotes.blade.php`
- `resources/views/owner/employees.blade.php`
- `resources/views/owner/jobs.blade.php`

### Route Files Modified
- `routes/web.php` (added employee and owner route groups)
