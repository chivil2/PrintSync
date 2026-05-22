# UI/UX Layouts Needed

## Overview
This document outlines the UI/UX layouts that still need to be implemented for the PrintSync application to complete the role-based user experience.

---

## Current State - What Already Exists

### Role-Based Layouts

**Employee Layout:**
- Location: Employee sidebar layout with Flux UI components
- Navigation items: Dashboard, My Quotes, Service Jobs
- Features:
  - Sidebar with role badge showing user's role (blue badge)
  - User avatar and name display in sidebar
  - Logout button in sidebar
  - Mobile header with dropdown menu (includes logout)
  - Repository and Documentation links in sidebar
- Dashboard view: Basic stat cards (Assigned Quotes, Pending Jobs, Completed Jobs) showing placeholder zeros
- Quote view: Placeholder view
- Jobs view: Placeholder view

**Owner Layout:**
- Location: Owner sidebar layout with Flux UI components
- Navigation items: Dashboard, All Quotes, Employees, Jobs
- Features:
  - Sidebar with role badge showing user's role (purple badge)
  - User avatar and name display in sidebar
  - Logout button in sidebar
  - Mobile header with dropdown menu (includes logout)
  - Repository and Documentation links in sidebar
- Dashboard view: Basic stat cards (Total Quotes, Total Employees, Active Jobs, Revenue) showing placeholder zeros
- Quote view: Placeholder view
- Employees view: Placeholder view
- Jobs view: Placeholder view

**Customer Layout:**
- Location: Original customer layout (not Flux UI based)
- Navigation: Basic navbar (not sidebar-based)
- Features:
  - Customer navbar with navigation links
  - Uses original styling (Neue Haas Grotesk Display Pro font)
  - Simple container-based layout
- Dashboard view: Basic stat cards (Recent Quotes, Active Orders, Credit Limit) with quick action buttons (Request Quote, View Orders, Update Profile)
- Store view: Original quote request view
- Orders view: Original orders view
- Profile view: Original profile view

### Components

**Role Badge Component:**
- Location: Components folder
- Purpose: Display user role with color coding
- Colors: Owner (purple/indigo), Employee (blue), Customer (green)
- Usage: Integrated into employee and owner sidebar layouts

### Authentication & Redirection

**Role-Based Login Redirection:**
- After login, users redirect based on role:
  - Employees → `/employee/dashboard`
  - Owners → `/owner/dashboard`
  - Customers → `/customer/dashboard`
- Implemented via Fortify home route configuration pointing to `/dashboard` which checks user role

### Middleware

**Employee Middleware:**
- Checks if user has employee role
- Aborts with 403 if unauthorized
- Applied to employee routes

**Owner Middleware:**
- Checks if user has owner role
- Aborts with 403 if unauthorized
- Applied to owner routes

### Routes

**Employee Routes:**
- `/employee/dashboard` - Employee dashboard
- `/employee/quotes` - Employee quotes (placeholder)
- `/employee/jobs` - Employee jobs (placeholder)

**Owner Routes:**
- `/owner/dashboard` - Owner dashboard
- `/owner/quotes` - Owner quotes (placeholder)
- `/owner/employees` - Owner employees (placeholder)
- `/owner/jobs` - Owner jobs (placeholder)

**Customer Routes:**
- `/customer/dashboard` - Customer dashboard
- `/customer/store` - Customer store/quote request
- `/customer/orders` - Customer orders
- `/customer/profile` - Customer profile

### What's Working

- Users can log in and are redirected to correct role-based dashboard
- Employee and owner sidebars display correctly with role badges
- Logout buttons work in employee and owner sidebars
- Customer dashboard displays with original layout
- Basic stat cards display on all dashboards (placeholder data)
- Quick action buttons work on customer dashboard
- Role-based middleware protection works for employee and owner routes

### What's Placeholder/Incomplete

- Employee dashboard: Shows zeros, no real data
- Owner dashboard: Shows zeros, no real data
- Customer dashboard: Shows zeros, no real data
- All quote views: Placeholder only
- All job views: Placeholder only
- Employee and owner employee views: Placeholder only
- No profile forms implemented yet
- No quote management UI implemented yet
- No job management UI implemented yet

---

## 1. Profile Forms by Role

### 1.1 Owner Profile Form

**Purpose:** Allow owners to manage their personal and business information.

**Fields to Include:**
- Personal Information
  - First Name
  - Last Name
  - Email
  - Phone

- Business Information
  - Company Name
  - Tax ID
  - Business Address

**Layout Location:** New profile view file for owner
**Route:** Owner profile route (may need to be created)
**Layout:** Use owner sidebar layout

**Validation Rules:**
- Company name: required, max 255 characters
- Tax ID: optional, max 50 characters
- Business address: optional, text field

---

### 1.2 Employee Profile Form

**Purpose:** Allow employees to manage their personal and employment information.

**Fields to Include:**
- Personal Information
  - First Name
  - Last Name
  - Email
  - Phone

- Employment Information
  - Employee ID (read-only, auto-generated)
  - Hire Date
  - Specialization (dropdown: printing_staff, technical_staff, etc.)
  - Hourly Rate
  - Employment Status (dropdown: active, on_leave, terminated)

**Layout Location:** New profile view file for employee
**Route:** Employee profile route (may need to be created)
**Layout:** Use employee sidebar layout

**Validation Rules:**
- Hire date: required, date format
- Specialization: required
- Hourly rate: required, decimal, minimum 0
- Employment status: required

**Permissions:**
- Employees can view their own profile
- Owners can edit any employee's profile
- Employees cannot change their own employment status or hourly rate (owner only)

---

### 1.3 Customer Profile Form

**Purpose:** Allow customers to manage their personal and account information.

**Fields to Include:**
- Personal Information
  - First Name
  - Last Name
  - Email
  - Phone

- Account Information
  - Customer ID (read-only, auto-generated)
  - Billing Address
  - Shipping Address
  - Credit Limit
  - Preferred Payment Method (dropdown: cash, card, credit)

**Layout Location:** Customer profile view (already exists, needs enhancement)
**Route:** Customer profile route (already exists)
**Layout:** Use customer layout (already exists)

**Validation Rules:**
- Billing address: optional, text field
- Shipping address: optional, text field
- Credit limit: optional, decimal, minimum 0
- Preferred payment method: optional

**Permissions:**
- Customers can view and edit their own profile
- Customers cannot change their credit limit (owner only)
- Owners can view and edit any customer's profile

---

## 2. Enhanced Dashboards

### 2.1 Owner Dashboard Enhancements

**Current State:** Basic placeholder with stat cards showing zeros

**Needed Enhancements:**
- Real-time statistics
  - Total quotes by status (pending, approved, rejected, completed)
  - Total revenue (daily, weekly, monthly)
  - Total employees count
  - Active jobs count
  - New customers count

- Charts/Visualizations
  - Revenue trend chart (line chart)
  - Quote status distribution (pie chart)
  - Employee performance metrics (bar chart)

- Quick Actions
  - Add new employee button
  - View all quotes button
  - View analytics button
  - Update pricing button

- Recent Activity Feed
  - Recent quote submissions
  - Recent job completions
  - Recent employee assignments

**Data Sources:**
- Quotes table for quote statistics
- Users table for employee/customer counts
- Jobs table (when implemented) for job statistics
- Quote line items for revenue calculations

---

### 2.2 Employee Dashboard Enhancements

**Current State:** Basic placeholder with stat cards showing zeros

**Needed Enhancements:**
- Personal statistics
  - Assigned quotes count
  - Assigned jobs count
  - Completed jobs today
  - Pending tasks count
  - Performance score

- Task Lists
  - Today's assigned quotes (with priority indicators)
  - Today's assigned jobs (with deadline indicators)
  - Overdue tasks (highlighted)

- Quick Actions
  - Update quote status button
  - Add job notes button
  - View calendar button
  - View performance button

- Calendar View
  - Mini calendar showing upcoming deadlines
  - Click to view full calendar

**Data Sources:**
- Quotes table (filtered by assigned employee)
- Jobs table (filtered by assigned employee)
- User assignments (when implemented)

---

### 2.3 Customer Dashboard Enhancements

**Current State:** Basic dashboard with stat cards and quick action buttons

**Needed Enhancements:**
- Personal statistics
  - Total quotes submitted
  - Active orders count
  - Completed orders count
  - Current credit usage vs limit

- Recent Activity
  - Recent quote requests with status
  - Recent order updates
  - Payment due dates

- Quick Actions
  - Request new quote button
  - View order history button
  - Update profile button
  - Contact support button

- Order Tracking
  - Active orders with progress indicators
  - Estimated completion dates
  - Status updates

**Data Sources:**
- Quotes table (filtered by customer)
- Orders table (when implemented)
- User credit_limit field

---

## 3. Navigation Enhancements

### 3.1 Owner Navigation

**Current State:** Basic sidebar with Dashboard, All Quotes, Employees, Jobs

**Needed Enhancements:**
- Add Analytics section
- Add Pricing section
- Add Reports section
- Add Settings section
- Add Notifications indicator
- Add search functionality

---

### 3.2 Employee Navigation

**Current State:** Basic sidebar with Dashboard, My Quotes, Service Jobs

**Needed Enhancements:**
- Add Calendar section
- Add Performance section
- Add Notifications indicator
- Add search functionality
- Add task filters (today, this week, overdue)

---

### 3.3 Customer Navigation

**Current State:** Basic navbar (not sidebar-based like employee/owner)

**Needed Enhancements:**
- Consider adding sidebar layout for consistency
- Add Quote History section
- Add Support section
- Add Notifications indicator
- Add order tracking section

---

## 4. Quote Management UI

### 4.1 Quote List View (Owner)

**Purpose:** Allow owners to view, filter, and manage all quotes.

**Features Needed:**
- Table view of all quotes
- Columns: Quote Number, Customer, Status, Amount, Date, Assigned Employee
- Filter by status
- Search by customer name or quote number
- Sort by date or amount
- Action buttons: View, Edit, Delete, Assign
- Pagination
- Bulk actions (approve, reject, assign)

**Layout:** Owner layout with sidebar
**Route:** Owner quotes index route (already exists, needs controller)

---

### 4.2 Quote Detail View (Owner)

**Purpose:** Allow owners to view full quote details and manage.

**Features Needed:**
- Quote header information
- Line items table
- Customer information sidebar
- Status change buttons
- Assign employee dropdown
- Add notes section
- Convert to job button
- Print/export button

**Layout:** Owner layout with sidebar
**Route:** Owner quotes show route (needs to be created)

---

### 4.3 Quote List View (Employee)

**Purpose:** Allow employees to view their assigned quotes.

**Features Needed:**
- Table view of assigned quotes only
- Columns: Quote Number, Customer, Status, Amount, Due Date, Priority
- Filter by status
- Search by customer name or quote number
- Sort by due date or priority
- Action buttons: View, Update Status, Add Notes
- Pagination
- Priority indicators (high, medium, low)

**Layout:** Employee layout with sidebar
**Route:** Employee quotes index route (already exists, needs controller)

---

### 4.4 Quote Request Form (Customer)

**Purpose:** Allow customers to request new quotes.

**Features Needed:**
- Product/service selection
- Quantity input
- Specifications text area
- Upload file option (for designs)
- Delivery date picker
- Special instructions text area
- Submit button
- Quote preview before submit

**Layout:** Customer layout
**Route:** Customer store route (already exists)

---

## 5. Job Management UI

### 5.1 Job List View (Owner)

**Purpose:** Allow owners to view and manage all jobs.

**Features Needed:**
- Table view of all jobs
- Columns: Job ID, Quote, Assigned Employee, Status, Progress, Due Date
- Filter by status
- Search by job ID or employee name
- Sort by due date
- Action buttons: View, Reassign, Update Status
- Progress bars for each job
- Pagination

**Layout:** Owner layout with sidebar
**Route:** Owner jobs index route (already exists, needs controller)

---

### 5.2 Job Detail View (Owner)

**Purpose:** Allow owners to view full job details and manage.

**Features Needed:**
- Job header information
- Related quote information
- Assigned employee information
- Status change buttons
- Progress tracking (percentage)
- Notes/updates timeline
- Upload photos/documents section
- Mark complete button
- Generate invoice button

**Layout:** Owner layout with sidebar
**Route:** Owner jobs show route (needs to be created)

---

### 5.3 Job List View (Employee)

**Purpose:** Allow employees to view their assigned jobs.

**Features Needed:**
- Table view of assigned jobs only
- Columns: Job ID, Quote, Status, Progress, Due Date, Priority
- Filter by status
- Search by job ID
- Sort by due date
- Action buttons: View, Update Progress, Add Notes, Upload Photos
- Progress bars
- Priority indicators
- Overdue highlighting

**Layout:** Employee layout with sidebar
**Route:** Employee jobs index route (already exists, needs controller)

---

### 5.4 Job Detail View (Employee)

**Purpose:** Allow employees to manage their assigned jobs.

**Features Needed:**
- Job header information
- Related quote information
- Progress slider/input (0-100%)
- Status change buttons (in progress, on hold, completed)
- Notes/updates timeline
- Upload photos/documents section
- Mark complete button
- View customer requirements

**Layout:** Employee layout with sidebar
**Route:** Employee jobs show route (needs to be created)

---

## 6. Additional UI Components

### 6.1 Role Badge Component

**Status:** ✅ COMPLETED

**Location:** `resources/views/components/role-badge.blade.php`

**Purpose:** Display user role with color coding

---

### 6.2 Notification Component

**Status:** ❌ NEEDED

**Purpose:** Display notifications to users (new quotes, job assignments, status updates)

**Features:**
- Bell icon with badge count
- Dropdown showing recent notifications
- Mark as read functionality
- Different notification types (info, warning, success)

---

### 6.3 Search Component

**Status:** ❌ NEEDED

**Purpose:** Allow users to search within their dashboard

**Features:**
- Search input field
- Search suggestions
- Filter by type (quotes, jobs, customers)
- Recent searches

---

### 6.4 Calendar Component

**Status:** ❌ NEEDED

**Purpose:** Display calendar for deadline tracking

**Features:**
- Monthly view
- Event indicators (quotes, jobs, deadlines)
- Click to view details
- Navigate between months
- Today button

---

## 7. Implementation Priority

### High Priority (Core Functionality)
1. Profile forms for all three roles
2. Quote list views (owner and employee)
3. Quote detail views (owner and employee)
4. Customer quote request form enhancement

### Medium Priority (Enhanced Experience)
1. Enhanced dashboards with real data
2. Job list views (owner and employee)
3. Job detail views (owner and employee)
4. Navigation enhancements

### Low Priority (Nice to Have)
1. Calendar component
2. Search component
3. Notification component
4. Advanced analytics and charts

---

## 8. Design Guidelines

### Color Scheme
- **Owner:** Purple/Indigo accents (`bg-indigo-600`, `text-indigo-800`)
- **Employee:** Blue accents (`bg-blue-600`, `text-blue-800`)
- **Customer:** Green accents (`bg-green-600`, `text-green-800`)

### Typography
- Headings: Neue Haas Grotesk Display Pro (already in customer layout)
- Body: System fonts or Neue Haas Grotesk Display Pro

### Spacing
- Consistent padding: 4px, 8px, 16px, 24px, 32px
- Gap between elements: 8px, 16px, 24px

### Components
- Use existing Flux UI components where compatible
- Use simple HTML/CSS where Flux UI has limitations
- Maintain consistency with existing layouts

### Responsive Design
- Mobile-first approach
- Sidebar collapses on mobile
- Tables should be scrollable on small screens
- Cards stack vertically on mobile

---

## 9. Accessibility

### Requirements
- All forms should have proper labels
- Buttons should have descriptive text
- Color should not be the only indicator of status
- Keyboard navigation support
- Screen reader compatibility
- Sufficient color contrast

---

## 10. Testing Checklist

### Profile Forms
- [ ] Owner can view and edit profile
- [ ] Employee can view profile (read-only for certain fields)
- [ ] Customer can view and edit profile
- [ ] Validation rules work correctly
- [ ] Data saves correctly to database

### Dashboards
- [ ] Owner dashboard displays correct statistics
- [ ] Employee dashboard displays assigned tasks
- [ ] Customer dashboard displays personal data
- [ ] Quick action buttons work correctly

### Quote Management
- [ ] Owner can view all quotes
- [ ] Employee can view assigned quotes only
- [ ] Customer can submit quote requests
- [ ] Quote status updates work correctly

### Job Management
- [ ] Owner can view all jobs
- [ ] Employee can view assigned jobs only
- [ ] Job progress tracking works
- [ ] File uploads work correctly

---

## Notes

- All new layouts should follow the existing pattern of using role-specific layouts
- Employee implementation should remain unchanged as requested
- Customer layout should continue using the original customer layout
- Use the existing role badge component where appropriate
- Maintain consistency with existing design patterns
