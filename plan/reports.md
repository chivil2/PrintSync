# Reports Page — Documentation

## Overview

The Reports page lets the owner view sales data broken down by month and year. It uses a config-driven approach so metrics can be added or changed without touching the service or view logic.

---

## Files Involved

| File | Purpose |
|------|---------|
| `config/reports.php` | Defines data source, metrics, and currency formatting |
| `app/Services/DashboardDataService.php` | Contains the query logic for monthly and yearly reports |
| `app/Http/Controllers/OwnerController.php` | `reports()` method — passes data to the view |
| `resources/views/owner/reports.blade.php` | The page UI (banner, tabs, stat cards, tables) |
| `routes/web.php` | Route `owner.reports` mapped to `OwnerController@reports` |
| `resources/views/components/owner-sidebar.blade.php` | Sidebar nav link to the reports page |
| `resources/views/layouts/app/owner.blade.php` | Mobile nav link to the reports page |

---

## Data Flow

```
User visits /owner/reports
        │
        ▼
   routes/web.php
   ┌─────────────────────────────────────────┐
   │ Route: GET owner/reports                │
   │ Name:  owner.reports                    │
   │ Maps to: OwnerController@reports        │
   └─────────────────────────────────────────┘
        │
        ▼
   OwnerController::reports()
   ┌─────────────────────────────────────────┐
   │ 1. Reads year from query string (?year=)│
   │    Defaults to current year if missing  │
   │ 2. Creates DashboardDataService         │
   │ 3. Calls getMonthlyReport($year)        │
   │ 4. Calls getYearlyReport()              │
   │ 5. Passes all data to Blade view        │
   └─────────────────────────────────────────┘
        │
        ▼
   DashboardDataService::getMonthlyReport()
   ┌─────────────────────────────────────────┐
   │ Reads config/reports.php for:           │
   │   - Source model (Quote)                │
   │   - Status filter (accepted)            │
   │   - Date column (created_at)            │
   │                                         │
   │ Queries the quotes table:               │
   │   - Filters by status = accepted        │
   │   - Filters by year                     │
   │   - Groups by month                     │
   │                                         │
   │ Returns 12 rows (Jan–Dec), each with:   │
   │   month, revenue, orders, paid,         │
   │   downpayment, unpaid                   │
   └─────────────────────────────────────────┘
        │
        ▼
   DashboardDataService::getYearlyReport()
   ┌─────────────────────────────────────────┐
   │ Reads config/reports.php for same config│
   │                                         │
   │ Queries the quotes table:               │
   │   - Gets distinct years that have data  │
   │   - For each year, sums revenue, orders │
   │   - Splits by payment status            │
   │                                         │
   │ Returns N rows (one per year), each:    │
   │   year, revenue, orders, paid,          │
   │   downpayment, unpaid                   │
   └─────────────────────────────────────────┘
        │
        ▼
   Blade View (reports.blade.php)
   ┌─────────────────────────────────────────┐
   │ 1. Banner with gradient + welcome-dots  │
   │ 2. Tab switcher (Alpine.js)             │
   │    - Monthly | Yearly                   │
   │ 3. Year selector dropdown (monthly)     │
   │ 4. Stat cards showing totals            │
   │ 5. Data table:                          │
   │    - Monthly tab → 12 rows + footer     │
   │    - Yearly tab  → N rows + footer      │
   └─────────────────────────────────────────┘
```

---

## Config File Flow (`config/reports.php`)

The config defines everything the service needs without hardcoding:

- **source.model** → Which Eloquent model to query (`Quote`)
- **source.table** → Database table name (`quotes`)
- **status_filter** → Which quote statuses count as sales (`accepted`)
- **date_column** → Which column to group by (`created_at`)
- **metrics** → What to calculate and how to display it
  - `revenue` — Sum of `total` column
  - `orders` — Count of records
  - `paid` — Sum where `payment_status = paid`
  - `downpayment` — Sum where `payment_status = partially_paid`
  - `unpaid` — Sum where `payment_status = unpaid`
- **currency** → Symbol and formatting (`₱`, 2 decimal places)

---

## Monthly Report Flow

1. User lands on `/owner/reports` → defaults to current year
2. User selects a different year from the dropdown
3. Page reloads with `?year=2024` query parameter
4. Service queries `quotes` table for that year
5. Groups results by month (1–12)
6. For each month, calculates:
   - Total revenue (sum of quote totals)
   - Order count (number of accepted quotes)
   - Payment breakdown (paid / downpayment / unpaid)
7. View renders a 12-row table with a totals footer

---

## Yearly Report Flow

1. User clicks the "Yearly" tab (Alpine.js toggle, no page reload)
2. Service queries `quotes` table for all distinct years
3. For each year, calculates the same metrics as monthly
4. View renders a table with one row per year + totals footer

---

## UI Components

| Component | Description |
|-----------|-------------|
| **Banner** | Orange-to-blue gradient with welcome-dots pattern |
| **Tab Switcher** | Two pill buttons (Monthly / Yearly) using Alpine.js |
| **Year Selector** | Dropdown that reloads the page with the selected year |
| **Stat Cards** | Two cards showing total revenue and total orders |
| **Monthly Table** | 6 columns: Month, Revenue, Orders, Paid, Down Payment, Unpaid |
| **Yearly Table** | Same 6 columns but grouped by year |
| **Table Footer** | Totals row at the bottom of each table |

---

## Navigation

- **Sidebar**: Reports link is between Messages and the Employees section
- **Mobile nav**: Reports link appears after Employees in the top bar

---

## How to Add a New Metric

1. Add an entry to `config/reports.php` under the `metrics` array
2. Add a corresponding query in `DashboardDataService` (clone the base query, apply conditions)
3. Add a column to the table in `reports.blade.php`
4. Add the metric to the footer totals

No controller or route changes needed — the config drives the data flow.
