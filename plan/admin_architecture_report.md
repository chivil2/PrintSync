# PrintSync — Admin Architecture Comprehensive Report

> **Date:** May 24, 2026
> **Scope:** Full admin/staff architecture, database schema, roles & permissions, routes, controllers, views, tests, and gap analysis.

---

## 1. DATABASE SCHEMA (SQLite)

### 1.1 Users Table

| Column | Type | Purpose | Role |
|--------|------|---------|------|
| `id` | integer PK | Unique identifier | All |
| `first_name` | varchar | Given name | All |
| `last_name` | varchar | Family name | All |
| `email` | varchar UNIQUE | Login credential | All |
| `phone` | varchar nullable | Contact number | All |
| `password` | varchar | Hashed password | All |
| `title` | varchar nullable | Employee classification (`printing_staff`/`technical_staff`) | Employee |
| `company_name` | varchar(255) nullable | Business name | Owner |
| `tax_id` | varchar(255) nullable | Tax identification | Owner |
| `business_address` | text nullable | Business address | Owner |
| `employee_id` | varchar(255) UNIQUE nullable | Employee number | Employee |
| `hire_date` | date nullable | Start date | Employee |
| `specialization` | varchar(255) nullable | Job specialization | Employee |
| `hourly_rate` | decimal(10,2) nullable | Pay rate | Employee |
| `employee_status` | varchar(20) nullable default `active` | Employment status | Employee |
| `customer_id` | varchar(255) UNIQUE nullable | Customer number | Customer |
| `billing_address` | text nullable | Billing address | Customer |
| `shipping_address` | text nullable | Shipping address | Customer |
| `credit_limit` | decimal(10,2) nullable | Credit limit | Customer |
| `preferred_payment_method` | varchar(255) nullable | Payment preference | Customer |
| `two_factor_secret` | text nullable | 2FA secret | All |
| `two_factor_recovery_codes` | text nullable | 2FA recovery codes | All |
| `two_factor_confirmed_at` | datetime nullable | 2FA confirmation timestamp | All |
| `email_verified_at` | datetime nullable | Email verification | All |
| `remember_token` | varchar nullable | "Remember me" token | All |
| `created_at` | datetime | Record creation | All |
| `updated_at` | datetime | Record update | All |

**Indexes:** `email` (unique), `customer_id` (unique), `employee_id` (unique), `title`

### 1.2 Spatie Permission Tables

**`roles`** — `id`, `name`, `guard_name`, `created_at`, `updated_at`
- 3 roles: `owner`, `employee`, `customer` (all `web` guard)

**`permissions`** — `id`, `name`, `guard_name`, `created_at`, `updated_at`
- 20 permissions total

**`model_has_roles`** — `role_id` → `roles.id`, `model_id` → `users.id`, `model_type` (App\Models\User)
**`role_has_permissions`** — `permission_id` → `permissions.id`, `role_id` → `roles.id`
**`model_has_permissions`** — direct permission-to-user assignments (unused in current code)

### 1.3 Service Jobs Table

| Column | Type | Purpose |
|--------|------|---------|
| `id` | integer PK | Unique ID |
| `name` | varchar | Job title |
| `description` | text nullable | Job details |
| `type` | varchar | `printing` or `technical` |
| `customer_id` | integer FK → `users.id` (CASCADE DELETE) | Who requested |
| `employee_id` | integer FK → `users.id` (SET NULL) nullable | Who's assigned |
| `service_id` | integer | Polymorphic: `printing_services.id` or `technical_services.id` |
| `service_type` | varchar | `printing_service` / `technical_service` (morph map) |
| `status` | varchar nullable | Workflow state (no enum constraint) |
| `priority` | varchar nullable | Priority level (no enum constraint) |
| `started_at` | datetime nullable | When work began |
| `completed_at` | datetime nullable | When work finished |
| `deadline` | datetime nullable | Due date |
| `notes` | text nullable | Additional info |
| `created_at` | datetime | |
| `updated_at` | datetime | |

**Indexes:** `customer_id`, `employee_id`, `status`, `type`, `deadline`
**Polymorphic relation:** `service()` → morphMap: `printing_service`→`PrintingService`, `technical_service`→`TechnicalService`

### 1.4 Quotes Table

| Column | Type | Purpose |
|--------|------|---------|
| `id` | integer PK | |
| `quote_number` | varchar UNIQUE | Human-readable ID |
| `customer_id` | integer FK → `users.id` (CASCADE) | Requestor |
| `date` | date | Quote date |
| `status` | varchar default `pending` | Workflow state |
| `currency` | varchar default `PHP` | Currency |
| `subtotal` | numeric default `0` | Before tax |
| `tax` | numeric default `0` | Tax amount |
| `discount` | numeric default `0` | Discount |
| `total` | numeric default `0` | Grand total |
| `terms` | text nullable | Payment terms |
| `notes` | text nullable | Internal notes |
| `employee_id` | integer FK → `users.id` (SET NULL) nullable | Assigned staff |
| `created_at` | datetime | |
| `updated_at` | datetime | |

### 1.5 Quote Line Items Table

| Column | Type |
|--------|------|
| `id` | integer PK |
| `quote_id` | integer FK → `quotes.id` (CASCADE DELETE) |
| `item_name` | varchar |
| `description` | text nullable |
| `quantity` | numeric default `1` |
| `unit_price` | numeric default `0` |
| `line_total` | numeric default `0` |
| `created_at` | datetime |
| `updated_at` | datetime |

### 1.6 Printing Services & Technical Services

Identical structure for both:

| Column | Type | Notes |
|--------|------|-------|
| `id` | integer PK | |
| `name` | varchar | Unique index |
| `description` | text nullable | |
| `price` | numeric | |
| `image` | varchar nullable | File path |
| `is_active` | tinyint(1) default `1` | Soft toggle |
| `created_at` | datetime | |
| `updated_at` | datetime | |

**Seed data:**
- Printing: Signage (₱500), Tarpaulin (₱800), Invitations (₱150), Brochure (₱200), T-Shirts (₱300), Mugs (₱250)
- Technical: Installation (₱1,500), Repair (₱1,000)

### 1.7 System Tables

`cache`, `cache_locks`, `failed_jobs`, `job_batches`, `jobs`, `migrations`, `password_reset_tokens`, `sessions`

---

## 2. FULL PERMISSIONS MATRIX

### 2.1 All 20 Permissions by Role

| Permission | Owner | Employee | Customer |
|------------|:-----:|:--------:|:--------:|
| `view_assigned_service_jobs` | ✗ | ✓ | ✗ |
| `update_service_job_status` | ✗ | ✓ | ✗ |
| `add_service_job_notes` | ✗ | ✓ | ✗ |
| `create_service_requests` | ✗ | ✗ | ✓ |
| `view_own_orders` | ✗ | ✗ | ✓ |
| `cancel_own_orders` | ✗ | ✗ | ✓ |
| `upload_service_files` | ✗ | ✗ | ✓ |
| `manage_all_service_jobs` | ✓ | ✗ | ✗ |
| `assign_service_jobs` | ✓ | ✗ | ✗ |
| `manage_pricing` | ✓ | ✗ | ✗ |
| `manage_users` | ✓ | ✗ | ✗ |
| `manage_all_quotes` | ✓ | ✗ | ✗ |
| `view_assigned_quotes` | ✗ | ✓ | ✗ |
| `update_quote_status` | ✗ | ✓ | ✗ |
| `request_quotes` | ✗ | ✗ | ✓ |
| `view_own_quotes` | ✗ | ✗ | ✓ |
| `place_orders` | ✗ | ✗ | ✓ |
| `view_analytics` | ✓ | ✗ | ✗ |
| `view_own_profile` | ✓ | ✓ | ✗ |
| `update_own_profile` | ✓ | ✓ | ✓ |

### 2.2 Permission Groupings by Domain

**Service Jobs (8 permissions):**
- Owner: `manage_all_service_jobs`, `assign_service_jobs`
- Employee: `view_assigned_service_jobs`, `update_service_job_status`, `add_service_job_notes`
- Customer: `create_service_requests`, `view_own_orders`, `cancel_own_orders`, `upload_service_files`

**Quotes (6 permissions):**
- Owner: `manage_all_quotes`
- Employee: `view_assigned_quotes`, `update_quote_status`
- Customer: `request_quotes`, `view_own_quotes`, `place_orders`

**Admin/System (3 permissions):**
- Owner: `manage_pricing`, `manage_users`, `view_analytics`

**Profile (2 permissions):**
- Owner: `view_own_profile`, `update_own_profile`
- Employee: `view_own_profile`, `update_own_profile`
- Customer: (none for view), `update_own_profile`

---

## 3. ROUTES & MIDDLEWARE ARCHITECTURE

### 3.1 Complete Route Map

```
Method  | URI                              | Action                                    | Middleware
--------|----------------------------------|-------------------------------------------|-----------------
GET     | /                                | landing page                              | guest
GET     | /database                        | DatabaseController@index                  | —
POST    | /login                           | AuthController@login                      | guest
GET     | /login                           | AuthController@showLoginForm              | guest
POST    | /register                        | AuthController@register                   | guest
GET     | /register                        | AuthController@showRegistrationForm       | guest
POST    | /admin/register                  | AuthController@registerAdmin              | guest
GET     | /admin/register                  | AuthController@showAdminRegistrationForm  | guest
GET     | /staff/dashboard                 | StaffController@dashboard                 | — (PUBLIC!)
POST    | /logout                          | AuthController@logout                     | auth
GET     | /dashboard                       | Role-check redirect                       | auth
GET     | /customer/dashboard              | CustomerController@dashboard              | auth
GET     | /customer/store                  | CustomerController@store                  | auth
POST    | /customer/request-service        | CustomerController@requestService         | auth
GET     | /customer/orders                 | CustomerController@orders                 | auth
DELETE  | /customer/orders/{order}         | CustomerController@destroyOrder           | auth
GET     | /customer/profile                | CustomerController@profile                | auth
POST    | /customer/profile                | CustomerController@updateProfile          | auth
POST    | /customer/profile/resend-verification | CustomerController@resendVerification | auth
GET     | /employee/dashboard              | View: employee.dashboard                  | auth, employee
GET     | /employee/quotes                 | View: employee.quotes                     | auth, employee
GET     | /employee/jobs                   | View: employee.jobs                       | auth, employee
GET     | /owner/dashboard                 | View: owner.dashboard                     | auth, owner
GET     | /owner/quotes                    | View: owner.quotes                        | auth, owner
GET     | /owner/employees                 | AdminController@employees                 | auth, owner
GET     | /owner/jobs                      | View: owner.jobs                          | auth, owner
—       | /settings                        | Redirect to /settings/profile             | auth
GET     | /settings/profile                | Livewire: pages::settings.profile         | auth
```

### 3.2 Middleware Aliases (`bootstrap/app.php`)

```php
'middleware' => [
    'aliases' => [
        'employee' => EnsureEmployeeRole::class,
        'owner'    => EnsureOwnerRole::class,
    ],
]
```

**`EnsureEmployeeRole`:**
- Unauthenticated → redirect to `login`
- User lacks `employee` role → abort 403 "Unauthorized access. Employee role required."
- Passes → next request

**`EnsureOwnerRole`:**
- Unauthenticated → redirect to `login`
- User lacks `owner` role → abort 403 "Unauthorized access. Owner role required."
- Passes → next request

**Notable gap:** No `auth` middleware on `StaffController@dashboard` — publicly accessible.

### 3.3 Auth Flow

**Customer registration** (`/register`):
1. Validate profile rules + password rules
2. Create User with `first_name`, `last_name`, `email`, `password`
3. `$user->assignRole('customer')`
4. `Auth::login($user)`
5. Redirect to `/dashboard` → role-check → `/customer/dashboard`

**Admin registration** (`/admin/register`):
1. Same validation
2. Create User
3. `$user->assignRole('owner')`
4. `Auth::login($user)`
5. Redirect to `/dashboard` → role-check → `/owner/dashboard`

**Login** (`/login`):
1. Validate email + password
2. `Auth::attempt()`
3. Session regenerate
4. `redirect()->intended(route('dashboard'))`

**Role-based dashboard routing** (`GET /dashboard`):
```php
if (hasRole('employee')) -> redirect('/employee/dashboard')
if (hasRole('owner'))    -> redirect('/owner/dashboard')
if (hasRole('customer')) -> redirect('/customer/dashboard')
else                     -> view('dashboard')
```

### 3.4 Laravel Fortify Integration

- `laravel/fortify` is installed
- No `config/fortify.php` found (likely using defaults)
- 2FA columns exist on users table
- Fortify's `home` route configured to `/dashboard`

---

## 4. MODELS

| Model | File | Traits | Key Relationships |
|-------|------|--------|-------------------|
| `User` | `app/Models/User.php` | `HasFactory, HasRoles, Notifiable` | `initials()` helper |
| `ServiceJob` | `app/Models/ServiceJob.php` | `HasFactory` | `customer()`, `employee()` (BelongsTo), `service()` (MorphTo) |
| `Quote` | `app/Models/Quote.php` | none | `customer()`, `employee()` (BelongsTo), `lineItems()` (HasMany) |
| `QuoteLineItem` | `app/Models/QuoteLineItem.php` | none | `quote()` (BelongsTo) |
| `PrintingService` | `app/Models/PrintingService.php` | `HasFactory` | none |
| `TechnicalService` | `app/Models/TechnicalService.php` | `HasFactory` | none |

**Policies:** NONE — `app/Policies/` directory doesn't exist.
**Form Requests:** NONE — validation happens in controllers.

### 4.1 User Model Fillable Fields

```php
'first_name', 'last_name', 'email', 'phone', 'password', 'title',
'company_name', 'tax_id', 'business_address',
'employee_id', 'hire_date', 'specialization', 'hourly_rate', 'employee_status',
'customer_id', 'billing_address', 'shipping_address', 'credit_limit', 'preferred_payment_method'
```

### 4.2 Morph Map (AppServiceProvider)

```php
Relation::morphMap([
    'printing_service' => PrintingService::class,
    'technical_service' => TechnicalService::class,
]);
```

---

## 5. CONTROLLERS

| Controller | Methods | Backend Logic |
|-----------|---------|---------------|
| `AuthController` | login, register, registerAdmin, logout, form methods | ✅ Full auth logic |
| `CustomerController` | dashboard, store, requestService, orders, destroyOrder, profile, updateProfile, resendVerification | ✅ Full CRUD for customer domain |
| `StaffController` | dashboard | ⚠️ Returns view only — no logic |
| `AdminController` | employees | ⚠️ Returns view only — placeholder |

### 5.1 AuthController

- Uses `PasswordValidationRules` and `ProfileValidationRules` traits
- `register()` → assigns `customer` role
- `registerAdmin()` → assigns `owner` role
- `login()` → standard Laravel Auth attempt
- `logout()` → session invalidation

### 5.2 CustomerController

- `dashboard()` → queries `ServiceJob` for authenticated customer, computes `totalOrders`, `completedOrders`, `totalSpent`
- `store()` → fetches active `PrintingService` and `TechnicalService`
- `requestService()` → validates service_id + service_type, creates `ServiceJob` with polymorphic relationship
- `orders()` → lists customer's service jobs with eager-loaded `service` + `employee` relations
- `destroyOrder()` → authorization check (customer_id match), then delete
- `profile()` → shows user profile with email verification status
- `updateProfile()` → validates, fills, handles email changes (resets verification)
- `resendVerification()` → sends verification notification

---

## 6. VIEWS & UI STATUS

### 6.1 Owner-Specific Views

| View | Path | Status | Notes |
|------|------|--------|-------|
| Owner Dashboard | `resources/views/owner/dashboard.blade.php` | ✅ Built | 4 stat cards (Total Quotes, Employees, Active Jobs, Revenue — all hardcoded to 0) |
| All Quotes | `resources/views/owner/quotes.blade.php` | 🟡 Placeholder | Search bar + filter tabs + "coming soon" message |
| Service Jobs | `resources/views/owner/jobs.blade.php` | 🟡 Placeholder | Search bar + filter tabs + "coming soon" message |
| Employees | `resources/views/admin/employees.blade.php` | 🟡 Placeholder | Search bar + filter tabs + "coming soon" message |
| Owner Sidebar | `layouts/app/owner-sidebar.blade.php` | ✅ Full | Flux sidebar with nav, role badge, logout button, mobile menu |

**Owner sidebar nav items:** Dashboard, All Quotes, Employees, Service Jobs, Settings

### 6.2 Employee-Specific Views

| View | Path | Status | Notes |
|------|------|--------|-------|
| Employee Dashboard | `resources/views/employee/dashboard.blade.php` | ✅ Built | 3 stat cards (Assigned Quotes, Pending Jobs, Completed — all hardcoded to 0) |
| My Quotes | `resources/views/employee/quotes.blade.php` | 🟡 Placeholder | Search + filter + "coming soon" |
| Service Jobs | `resources/views/employee/jobs.blade.php` | 🟡 Placeholder | Search + filter + "coming soon" |
| Employee Sidebar | `layouts/app/employee-sidebar.blade.php` | ✅ Full | Flux sidebar with nav, role badge, logout |

**Employee sidebar nav items:** Dashboard, My Quotes, Service Jobs, Settings
(Note: No "Employees" link — employees cannot manage users)

### 6.3 Hybrid Staff Dashboard

- `staff/dashboard.blade.php` extends `layouts.customer` (uses the horizontal top navbar, NOT Flux sidebar)
- Shows 3 cards for Active Jobs, Completed, Employees — all hardcoded `—`
- Route has **no auth/role middleware** — publicly accessible

### 6.4 Customer Navbar (Shared Navigation)

`partials/customer-navbar.blade.php` — a permission-gated top navbar:

- **Logo** → `staff.dashboard` if `can('view_assigned_service_jobs')` (staff), else `customer.store`
- **Customer links** (`@can('place_orders')`): Dashboard, Store, View Orders
- **Staff links** (`@can('view_assigned_service_jobs')`): Dashboard, Quotes (role-routed), Jobs (role-routed), Employees (`@can('manage_users')`)
- **User dropdown:** Profile Settings (if `can('view_own_profile')`), Logout
- **Guest:** Sign In, Get Started

This means the same navbar serves customers, employees, and owners — it just shows/hides links based on permissions.

### 6.5 Layout Summary

| Layout | Used By | Nav Style |
|--------|---------|-----------|
| `layouts.customer` | Customer views, staff dashboard | Horizontal top navbar |
| `layouts.app.owner-sidebar` | Owner: dashboard, quotes, employees, jobs | Flux sidebar (left) |
| `layouts.app.employee-sidebar` | Employee: dashboard, quotes, jobs | Flux sidebar (left) |
| `layouts.app.sidebar` | Default (starter kit) | Flux sidebar |
| `layouts.app.header` | Alternative | Top navbar + mobile sidebar |
| `layouts.auth.simple` | Auth pages | Brand logo background |
| `layouts.auth.split` | Auth pages | Split screen |
| `layouts.auth.card` | Auth pages | Card style |

---

## 7. DATABASE SEEDERS

| Seeder | Purpose |
|--------|---------|
| `RoleAndPermissionSeeder` | Creates 20 permissions, 3 roles, assigns permissions to roles |
| `PrintingServiceSeeder` | Creates 6 printing services |
| `TechnicalServiceSeeder` | Creates 2 technical services |
| `DemoUserSeeder` | Creates 4 demo users with role-specific fields |

### 7.1 Demo Users

| Email | Role | Name | Details |
|-------|------|------|---------|
| `owner@printsync.com` | Owner | John Owner | company: PrintSync Solutions, tax_id: TAX-123456789 |
| `jane@printsync.com` | Employee | Jane Printer | printing_staff, EMP-001, ₱150/hr |
| `bob@printsync.com` | Employee | Bob Technician | technical_staff, EMP-002, ₱200/hr |
| `alice@example.com` | Customer | Alice Customer | CUST-001, ₱50,000 credit limit |

All passwords: `password`

### 7.2 Factory Status

| Factory | Status | Notes |
|---------|--------|-------|
| `UserFactory` | ✅ Complete | Has `unverified()`, `withTwoFactor()` states |
| `ServiceJobFactory` | ❌ Empty | `definition()` returns `[]` |
| `PrintingServiceFactory` | ✅ Complete | Random names, prices |
| `TechnicalServiceFactory` | ✅ Complete | Random names, prices |

---

## 8. EXISTING TESTS (PHPUnit)

| Test File | What It Tests |
|-----------|---------------|
| `Feature/Auth/AuthenticationTest.php` | Login validation, success, throttling |
| `Feature/Auth/RegistrationTest.php` | Customer registration flow |
| `Feature/Auth/AdminRegistrationTest.php` | Owner registration flow, role assignment |
| `Feature/Auth/PasswordResetTest.php` | Password reset request |
| `Feature/Auth/PasswordConfirmationTest.php` | Password confirmation |
| `Feature/Auth/EmailVerificationTest.php` | Email verification |
| `Feature/Auth/TwoFactorChallengeTest.php` | 2FA challenge |
| `Feature/Settings/ProfileUpdateTest.php` | Profile update via settings |
| `Feature/Settings/SecurityTest.php` | Security settings |
| `Feature/DashboardTest.php` | Dashboard access |
| `Feature/ExampleTest.php` | Welcome page |
| `Unit/ExampleTest.php` | Basic assertion |

**Total: 12 test files**

### 8.1 Test Coverage Gaps

| Missing Coverage | Severity |
|-----------------|----------|
| ServiceJob CRUD (create, read, update, delete) | High |
| Quote CRUD (create, read, update, delete, line items) | High |
| Employee role access — cannot access owner routes | High |
| Owner role access — cannot access employee routes | Medium |
| Customer data isolation — cannot see other customers' orders | Medium |
| Permission enforcement via `@can` directives | Medium |
| Authorization — `destroyOrder` ownership check | Medium |
| CustomerController: store, orders, profile, updateProfile | Medium |
| Role-based redirection (employee vs owner vs customer) | Medium |
| Middleware — unauthenticated redirect for employee/owner routes | Medium |
| Middleware — 403 for wrong role | Medium |
| 2FA setup flow | Low |
| Factory tests | Low |

---

## 9. ARCHITECTURE STATE — COMPLETED VS PLACEHOLDER

### 9.1 Fully Functional

- User authentication (login, logout, registration for customer and owner)
- Role-based routing after login
- Role assignment during registration
- Spatie permission/role infrastructure
- Customer dashboard with real DB-backed stats
- Customer store (service catalog browsing)
- Customer order creation with polymorphic service relationship
- Customer order listing and deletion (own orders only)
- Customer profile update with email verification
- 2FA (Fortify-driven)
- Owner and Employee sidebar layouts with role badge and logout
- Permission-gated dynamic navbar
- Morph map configured for service polymorphism

### 9.2 Placeholder (Stub UI, No Backend)

| Page | UI | Backend |
|------|:--:|:-------:|
| Owner Quotes | Search + filter UI, "coming soon" | ❌ |
| Owner Employees | Search + filter UI, "coming soon" | ❌ |
| Owner Service Jobs | Search + filter UI, "coming soon" | ❌ |
| Employee Quotes | Search + filter UI, "coming soon" | ❌ |
| Employee Service Jobs | Search + filter UI, "coming soon" | ❌ |

### 9.3 Completely Missing

| Missing Feature | Impact | Files Needed |
|----------------|--------|--------------|
| **QuoteController** — CRUD for quotes | High | Controller, views, form requests |
| **ServiceJobController** — CRUD for staff | High | Controller, views, form requests |
| **Employee management** — create/edit/delete employees | High | CRUD UI, owner-only |
| **Job assignment flow** — owner assigns employee | High | Update service_jobs.employee_id |
| **Quote status workflow** — request → review → approve/reject | High | Status transitions |
| **Policies** — model authorization classes | Medium | 5 policy files |
| **Form Requests** — dedicated validation | Low | 5+ form request classes |

---

## 10. KEY ARCHITECTURAL OBSERVATIONS

1. **Polymorphic Service Jobs** — The `service_jobs` table uses `service_id` + `service_type` + morphMap to reference either `PrintingService` or `TechnicalService`. This avoids needing separate job tables per service type.

2. **Dual Navigation Systems** — Customers use a traditional horizontal top navbar (`customer-navbar`). Employees and owners use Flux sidebar layouts. The `staff.dashboard` bridges them by extending `layouts.customer` but showing staff links via permission gates.

3. **Owner Is Not "Admin"** — The `owner` role has `manage_*` permissions but the app has no concept of a full admin panel. The `/admin/register` route creates an owner, not a system admin.

4. **Quotes Module Is Wired Up But Dead** — Full database schema exists (quotes + quote_line_items with foreign keys), models exist with proper relationships, but zero business logic — no controller, no CRUD, no UI beyond "coming soon."

5. **Backend Gap in Staff Features** — Owner and Employee dashboards show hardcoded zeros. Their Quotes and Jobs pages are empty stubs. The database has the tables; the views have the search UI; the business logic between them is entirely missing.

6. **The `StaffController@dashboard` Route Is Public** — No auth middleware, no role check. Anyone can hit `/staff/dashboard`.

7. **No ServiceJobFactory Implementation** — The factory exists but `definition()` returns an empty array, making it impossible to generate test data for service jobs.

8. **No QuoteFactory** — Doesn't exist at all, making quote testing impossible without manual DB inserts.

---

## 11. GAP CLOSURE PRIORITY MATRIX

| Priority | Feature | Effort | Dependencies |
|----------|---------|--------|-------------|
| 🔴 P0 | QuoteController CRUD | Large | None — DB + models exist |
| 🔴 P0 | ServiceJobController CRUD | Large | None — DB + models exist |
| 🔴 P0 | Auth middleware on /staff/dashboard | Small | None |
| 🟡 P1 | Employee management (owner) | Medium | AdminController stub exists |
| 🟡 P1 | Job assignment flow | Medium | ServiceJobController needed |
| 🟡 P1 | Quote status workflow | Medium | QuoteController needed |
| 🟢 P2 | Model Policies | Medium | Requires controller refactors |
| 🟢 P2 | ServiceJobFactory + QuoteFactory | Small | None |
| 🟢 P2 | Test coverage for staff features | Large | Requires controllers first |
| 🔵 P3 | Form Request classes | Small | Controllers exist |
| 🔵 P3 | Customer role middleware | Small | None |

---

## 12. DEMO CREDENTIALS REFERENCE

| Role | Email | Password |
|------|-------|----------|
| Owner | `owner@printsync.com` | `password` |
| Employee (Printing) | `jane@printsync.com` | `password` |
| Employee (Technical) | `bob@printsync.com` | `password` |
| Customer | `alice@example.com` | `password` |

---

*End of report.*
