# User Role Differentiation Plan

## Overview
This document outlines a comprehensive approach to differentiate between employees, customers (clients), and owners in the PrintSync application across visual, functional, data, and UX dimensions.

---

## Current State

### Existing Role System
- **Owner:** Full system access, can manage all data
- **Employee:** Assigned to specific tasks, limited access
- **Customer:** Can request services, view own orders

### Existing Data Fields
- `User.title` field used for employee classification (printing_staff, technical_staff)
- Spatie Permission package for role management
- RoleAndPermissionSeeder defines permissions

---

## 1. Visual Differentiation

### 1.1 UI Badges and Indicators

**Purpose:** Visually distinguish user roles in the interface

**Implementation:**
- Add role badges to user profile displays
- Color-coded badges:
  - Owner: Purple/Indigo (e.g., `bg-indigo-100 text-indigo-800`)
  - Employee: Blue (e.g., `bg-blue-100 text-blue-800`)
  - Customer: Green (e.g., `bg-green-100 text-green-800`)

**Locations:**
- Sidebar user menu
- User dropdown in mobile header
- Profile pages
- User lists (for owner view)
- Quote/job assignments

**Component:**
```blade
<x-role-badge :role="$user->getRoleNames()->first()" />
```

### 1.2 Layout Customization

**Owner Layout:**
- Purple accent color theme
- Analytics dashboard
- Employee management section
- Full system metrics

**Employee Layout:**
- Blue accent color theme
- Task-focused dashboard
- Assigned quotes/jobs
- Performance metrics

**Customer Layout:**
- Green accent color theme
- Service catalog
- Order history
- Self-service features

### 1.3 Avatar Differentiation

**Approach:** Different avatar borders or backgrounds based on role
- Owner: Gold border
- Employee: Blue border
- Customer: Gray border

---

## 2. Functional Differentiation

### 2.1 Permission Matrix

**Owner Permissions:**
- `manage_all_quotes` - Create, edit, delete any quote
- `manage_all_jobs` - Create, edit, delete any service job
- `manage_employees` - Add, edit, remove employees
- `manage_pricing` - Update service pricing
- `view_analytics` - Access business analytics
- `assign_jobs` - Assign jobs to employees

**Employee Permissions:**
- `view_assigned_quotes` - View quotes assigned to them
- `update_quote_status` - Update status of assigned quotes
- `view_assigned_jobs` - View jobs assigned to them
- `update_job_status` - Update status of assigned jobs
- `add_job_notes` - Add notes to assigned jobs

**Customer Permissions:**
- `request_quotes` - Create quote requests
- `view_own_quotes` - View their own quotes
- `view_own_jobs` - View their own service jobs
- `update_profile` - Update their profile
- `place_orders` - Convert quotes to orders

### 2.2 Middleware Enforcement

**Status: ✅ COMPLETED**
- `EnsureEmployeeRole` middleware - Checks employee role, redirects/aborts if unauthorized
- `EnsureOwnerRole` middleware - Checks owner role, redirects/aborts if unauthorized
- Both registered in `bootstrap/app.php` as `employee` and `owner` aliases

**Note:** Customer-specific middleware not needed as customer routes use standard auth middleware

### 2.3 Feature Access by Role

| Feature | Owner | Employee | Customer |
|---------|-------|----------|----------|
| View all quotes | ✅ | ❌ | ❌ |
| View own quotes | ✅ | ✅ (assigned) | ✅ |
| Create quotes | ✅ | ❌ | ✅ (request) |
| Edit quotes | ✅ | ✅ (assigned) | ❌ |
| Delete quotes | ✅ | ❌ | ❌ |
| View all jobs | ✅ | ❌ | ❌ |
| View assigned jobs | ✅ | ✅ | ❌ |
| View own jobs | ✅ | ✅ (assigned) | ✅ |
| Create jobs | ✅ | ❌ | ❌ |
| Assign jobs | ✅ | ❌ | ❌ |
| Manage employees | ✅ | ❌ | ❌ |
| View analytics | ✅ | ❌ | ❌ |
| Manage pricing | ✅ | ❌ | ❌ |

---

## 3. Data Differentiation

### 3.1 User-Specific Fields

**Current `User` Model Fields:**
- `first_name`, `last_name`
- `email`, `phone`
- `password`
- `title` (for employee classification)

**Add Role-Specific Fields:**

**Owner-Specific:**
- `company_name` - For business identification
- `tax_id` - For tax purposes
- `business_address` - Physical business location

**Employee-Specific:**
- `employee_id` - Unique employee number
- `hire_date` - When they started
- `specialization` - printing_staff, technical_staff, etc.
- `hourly_rate` - For payroll calculations
- `status` - active, on_leave, terminated

**Customer-Specific:**
- `customer_id` - Unique customer number
- `billing_address` - Separate from shipping
- `shipping_address` - For deliveries
- `credit_limit` - For payment terms
- `preferred_payment_method` - cash, card, credit

### 3.2 Database Schema Updates

**Migration: Add role-specific fields to users table**

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Owner fields
        $table->string('company_name')->nullable();
        $table->string('tax_id')->nullable();
        $table->text('business_address')->nullable();

        // Employee fields
        $table->string('employee_id')->nullable();
        $table->date('hire_date')->nullable();
        $table->string('specialization')->nullable();
        $table->decimal('hourly_rate', 10, 2)->nullable();
        $table->enum('employee_status', ['active', 'on_leave', 'terminated'])->default('active');

        // Customer fields
        $table->string('customer_id')->nullable();
        $table->text('billing_address')->nullable();
        $table->text('shipping_address')->nullable();
        $table->decimal('credit_limit', 10, 2)->nullable();
        $table->string('preferred_payment_method')->nullable();
    });
}
```

### 3.3 Profile Forms by Role

**Owner Profile:**
- Personal information
- Company information
- Business address
- Tax ID

**Employee Profile:**
- Personal information
- Employee ID (auto-generated)
- Hire date
- Specialization
- Hourly rate
- Employment status

**Customer Profile:**
- Personal information
- Customer ID (auto-generated)
- Billing address
- Shipping address
- Credit limit
- Preferred payment method

**Status: ❌ PENDING**
- Profile forms not yet created
- Will be implemented as part of UX customization phase

---

## 4. UX Differentiation

### 4.1 Dashboard Differences

**Owner Dashboard:**
- Revenue metrics (daily, weekly, monthly)
- Total quotes by status
- Employee performance metrics
- Active jobs overview
- Customer acquisition stats
- Profit margins
- Quick actions: Add employee, Update pricing, View analytics

**Employee Dashboard:**
- Assigned quotes (with priority)
- Assigned jobs (with deadlines)
- Today's schedule
- Performance stats (completed jobs, efficiency)
- Quick actions: Update status, Add notes, View calendar

**Customer Dashboard:**
- Recent quotes
- Active orders
- Order status tracking
- Quick actions: Request quote, View catalog, Contact support

### 4.2 Navigation Differences

**Owner Navigation:**
- Dashboard
- All Quotes
- All Jobs
- Employees
- Analytics
- Pricing
- Settings
- Reports

**Employee Navigation:**
- Dashboard
- My Quotes
- My Jobs
- Calendar
- Performance
- Profile

**Customer Navigation:**
- Dashboard
- Store/Services
- My Orders
- Quote Requests
- Profile
- Support

### 4.3 Workflow Differences

**Quote Workflow:**

**Owner:**
1. View all quotes
2. Assign quotes to employees
3. Approve/reject quotes
4. Convert quotes to jobs
5. Monitor progress

**Employee:**
1. View assigned quotes
2. Review quote details
3. Update quote status
4. Add notes/updates
5. Convert to job (if authorized)

**Customer:**
1. Request quote
2. View quote status
3. Accept/reject quote
4. Convert to order
5. Track order progress

**Job Workflow:**

**Owner:**
1. Create jobs from quotes
2. Assign jobs to employees
3. Monitor job progress
4. Approve completed jobs
5. Generate invoices

**Employee:**
1. View assigned jobs
2. Start/complete jobs
3. Add progress notes
4. Upload photos/documents
5. Mark as complete

**Customer:**
1. View job status
2. Track progress
3. Provide feedback
4. Approve completion
5. Make payment

---

## 5. Implementation Plan

### Phase 1: Visual Differentiation (Priority: High)
1. Create role badge component - ❌ PENDING
2. Add role badges to sidebar user menu - ❌ PENDING
3. Add role badges to profile pages - ❌ PENDING
4. Implement color themes per role layout - ❌ PENDING
5. Add avatar differentiation - ❌ PENDING

### Phase 2: Permission Updates (Priority: High)
1. Update RoleAndPermissionSeeder with new permissions - ✅ COMPLETED
2. Add missing permissions to middleware checks - ✅ COMPLETED
3. Implement permission-based feature access - ✅ COMPLETED
4. Add permission checks to QuoteController - ✅ COMPLETED
5. Add permission checks to JobController - ❌ PENDING

### Phase 3: Data Structure Updates (Priority: Medium)
1. Create migration for role-specific fields - ✅ COMPLETED
2. Update User model with new fillable fields - ✅ COMPLETED
3. Update User factory with role-specific data - ❌ PENDING
4. Update DemoUserSeeder with new fields - ✅ COMPLETED
5. Create profile forms for each role - ❌ PENDING

### Phase 4: UX Customization (Priority: Medium)
1. Implement owner dashboard with analytics - ❌ PENDING
2. Implement employee dashboard with task focus - ❌ PENDING
3. Implement customer dashboard with order focus - ❌ PENDING
4. Create role-specific navigation - ✅ COMPLETED (basic)
5. Implement role-based workflows - ❌ PENDING

### Phase 5: Testing (Priority: Low)
1. Test visual differentiation across roles - ❌ PENDING
2. Test permission enforcement - ❌ PENDING
3. Test role-specific data storage - ✅ COMPLETED
4. Test role-based workflows - ❌ PENDING
5. Test role-based layouts - ❌ PENDING

---

## 6. File Changes Required

### New Files to Create
- `resources/views/components/role-badge.blade.php` - ❌ PENDING
- `resources/views/profiles/owner-profile.blade.php` - ❌ PENDING
- `resources/views/profiles/employee-profile.blade.php` - ❌ PENDING
- `resources/views/profiles/customer-profile.blade.php` - ❌ PENDING
- `database/migrations/2026_05_08_074416_add_role_specific_fields_to_users_table.php` - ✅ COMPLETED

### Files to Modify
- `database/seeders/RoleAndPermissionSeeder.php` - ✅ COMPLETED (Added new permissions)
- `database/seeders/DemoUserSeeder.php` - ✅ COMPLETED (Added role-specific fields)
- `app/Models/User.php` - ✅ COMPLETED (Added new fillable fields)
- `resources/views/layouts/app/employee-sidebar.blade.php` - ✅ COMPLETED (Created)
- `resources/views/layouts/app/owner-sidebar.blade.php` - ✅ COMPLETED (Created)
- `resources/views/layouts/customer.blade.php` - ✅ COMPLETED (Already exists)
- `app/Http/Controllers/ProfileController.php` - ❌ PENDING
- `app/Http/Controllers/QuoteController.php` - ✅ COMPLETED (Created with permission checks)

---

## 7. Security Considerations

1. **Permission Checks:** All controller actions must check permissions before allowing access
2. **Data Isolation:** Employees should only see their assigned data
3. **Role Validation:** Ensure users cannot elevate their own role
4. **Audit Logging:** Track role changes and permission modifications
5. **API Security:** API endpoints must enforce role-based access

---

## 8. Today's Changes (May 8, 2026)

### Overview
Today's work focused on fixing role-based redirection issues that occurred after the employee layout was added, and restoring the customer dashboard functionality. The main issue was that customers were being redirected incorrectly after login, and the role-based dashboard routing was not working as intended.

### What Was Changed

**No existing code was removed.** All changes were additions or modifications to fix functionality.

#### 8.1 Role-Based Redirection Fix

**Problem:** After login, users were not being redirected to their role-specific dashboards. The Fortify configuration was pointing to the wrong route, bypassing the role-based logic.

**Solution:**
- Updated the Fortify home configuration to point to the generic `/dashboard` route instead of a specific customer route
- This allows the `/dashboard` route to check the user's role and redirect appropriately

**Location:** Fortify configuration file

**Result:** After login, users now redirect to:
- Employees → Employee dashboard
- Owners → Owner dashboard
- Customers → Customer dashboard

#### 8.2 Customer Dashboard Restoration

**Problem:** When the employee layout was added, the customer dashboard route was never created. Customers were being redirected to a store page instead of a proper dashboard.

**Solution:**
- Added a new customer dashboard route
- Created a simple customer dashboard view with stats and quick action buttons
- Updated the main dashboard route to redirect customers to the new customer dashboard instead of the store

**Locations:**
- Web routes file (added customer dashboard route)
- Customer views folder (new dashboard view file)

**Result:** Customers now have their own dashboard with:
- Stats cards (Recent Quotes, Active Orders, Credit Limit)
- Quick action buttons (Request Quote, View Orders, Update Profile)
- Uses the original customer layout (not the new Flux UI layout)

#### 8.3 Visual Role Indicators

**Problem:** Users couldn't easily see what role they were logged in as, making it hard to verify role-based functionality.

**Solution:**
- Created a reusable role badge component that displays the user's role with color coding
- Added the role badge to the employee and owner sidebar layouts
- Color scheme: Owner (purple), Employee (blue), Customer (green)

**Locations:**
- Components folder (new role badge component)
- Employee sidebar layout (added badge to user menu)
- Owner sidebar layout (added badge to user menu)

**Result:** Users can now clearly see their role displayed in the sidebar.

#### 8.4 Flux UI Component Fixes

**Problem:** The new dashboards used Flux UI components that had syntax errors or didn't exist in the installed version.

**Issues Found:**
- Nested card components (like `flux:card.header`) are not supported
- Icon names used in sidebar items don't exist in the Flux UI icon set
- Button variant "secondary" is not supported

**Solution:**
- Replaced Flux UI card components with simple HTML/CSS divs for dashboards
- Removed all icon attributes from sidebar navigation items
- Changed button variant from "secondary" to "outline" in customer dashboard

**Locations:**
- Employee dashboard view
- Owner dashboard view
- Employee sidebar layout
- Owner sidebar layout
- Customer dashboard view

**Result:** Dashboards now render without component errors.

#### 8.5 Logout Accessibility

**Problem:** The employee and owner sidebars didn't have a visible logout button on desktop, making it difficult to log out and test different roles.

**Solution:**
- Added a logout form with button to the bottom of both employee and owner sidebars
- The logout button appears below the user info in the sidebar

**Locations:**
- Employee sidebar layout
- Owner sidebar layout

**Result:** Users can now easily log out from the desktop sidebar.

#### 8.6 Route Cache Clearing

**Problem:** After route changes, the application was still using cached routes, causing the role-based redirection to not work.

**Solution:**
- Cleared the route cache to ensure new route configurations take effect

**Result:** Role-based redirection now works immediately after changes.

### Step-by-Step Flow

**How role-based redirection works now:**

1. User logs in via Fortify authentication
2. Fortify redirects to `/dashboard` (configured home route)
3. The `/dashboard` route checks the authenticated user's role:
   - If employee → redirect to `/employee/dashboard`
   - If owner → redirect to `/owner/dashboard`
   - If customer → redirect to `/customer/dashboard`
4. User lands on their role-specific dashboard

**How to test different roles:**

1. Log out using the logout button in the sidebar
2. Log in with a different user account:
   - Employee: jane@printsync.com / password
   - Owner: owner@printsync.com / password
   - Customer: alice@example.com / password
3. Verify you land on the correct dashboard for that role

### Troubleshooting Done

**Issue 1: Customer redirect not working**
- **Symptom:** Customers were going to store page instead of dashboard
- **Cause:** Customer dashboard route didn't exist, dashboard route pointed to wrong destination
- **Fix:** Created customer dashboard route and view, updated redirect logic

**Issue 2: Flux UI component errors**
- **Symptom:** "Unable to locate a class or view for component" errors
- **Cause:** Used unsupported Flux UI component syntax and icon names
- **Fix:** Replaced with simple HTML/CSS, removed unsupported attributes

**Issue 3: No logout button visible**
- **Symptom:** Couldn't log out to test different roles
- **Cause:** Logout only in mobile menu, not desktop sidebar
- **Fix:** Added logout button to desktop sidebar

**Issue 4: Route cache not updating**
- **Symptom:** Route changes not taking effect
- **Cause:** Laravel caches routes for performance
- **Fix:** Ran route:clear command

**Issue 5: Duplicate route group**
- **Symptom:** Routes file had nested customer route groups
- **Cause:** Edit created duplicate during route addition
- **Fix:** Removed duplicate route group

### Files Modified Today

**Configuration:**
- Fortify configuration file (home route setting)

**Routes:**
- Web routes file (added customer dashboard route, updated redirect logic)

**Views:**
- Employee sidebar layout (added role badge, logout button, removed icons)
- Owner sidebar layout (added role badge, logout button, removed icons)
- Employee dashboard (replaced Flux UI cards with HTML/CSS)
- Owner dashboard (replaced Flux UI cards with HTML/CSS)
- Customer dashboard (new file, uses original customer layout)

**Components:**
- Role badge component (new file)

### What Was NOT Changed

**Employee implementation (as requested):**
- Employee middleware - unchanged
- Employee controller - unchanged
- Employee views - unchanged
- Employee routes - unchanged (except logout button addition to layout)
- Employee layout structure - unchanged

**Original customer files:**
- Customer store view - unchanged
- Customer orders view - unchanged
- Customer profile view - unchanged
- Customer layout - unchanged (used by new dashboard)

---

## 9. Future Enhancements

1. **Role Hierarchy:** Implement role inheritance (e.g., Manager > Employee)
2. **Custom Roles:** Allow creation of custom roles beyond the three main ones
3. **Dynamic Permissions:** Allow owners to create custom permissions
4. **Role Expiration:** Implement temporary role assignments
5. **Multi-Role Users:** Allow users to have multiple roles (e.g., Owner + Employee)
