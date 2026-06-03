<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\QuoteLineItem;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardDataService
{
    public function getDashboardData(): array
    {
        return [
            // Stats
            'completedJobRevenue' => $this->getCompletedJobRevenue(),
            'completedJobs' => $this->getCompletedJobsCount(),
            'averageOrderValue' => $this->getAverageOrderValue(),
            'highestOrderValue' => $this->getHighestOrderValue(),

            // Jobs
            'recentJobs' => $this->getRecentJobs(),
            'jobsByStatus' => $this->getJobsByStatus(),
            'serviceTypes' => $this->getServiceTypes(),

            // Quotes
            'pendingQuotes' => $this->getPendingQuotes(),
            'quotesCount' => Quote::count(),
            'sentQuotes' => Quote::where('status', 'sent')->count(),
            'acceptedQuotes' => Quote::where('status', 'accepted')->count(),

            // Customers
            'customersCount' => User::role('customer')->count(),
            'recentCustomers' => $this->getRecentCustomers(),

            // Employees
            'employees' => $this->getActiveEmployees(),

            // Earnings breakdown
            'earningsBreakdown' => $this->getCurrentEarningsBreakdown(),
        ];
    }

    private function getCompletedJobRevenue(): float
    {
        return Quote::whereHas('serviceJob', function ($query) {
            $query->where('status', 'completed');
        })->where('status', 'accepted')->sum('total') ?? 0;
    }

    private function getCompletedJobsCount(): int
    {
        return ServiceJob::where('status', 'completed')->count();
    }

    private function getAverageOrderValue(): float
    {
        $completedJobs = $this->getCompletedJobsCount();
        $revenue = $this->getCompletedJobRevenue();

        return $completedJobs > 0 ? $revenue / $completedJobs : 0;
    }

    private function getHighestOrderValue(): float
    {
        return Quote::whereHas('serviceJob', function ($query) {
            $query->where('status', 'completed');
        })->where('status', 'accepted')->max('total') ?? 0;
    }

    private function getRecentJobs(): \Illuminate\Database\Eloquent\Collection
    {
        return ServiceJob::with(['customer', 'employee', 'quote'])
            ->latest()
            ->take(10)
            ->get();
    }

    private function getJobsByStatus(): Collection
    {
        return ServiceJob::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    private function getServiceTypes(): Collection
    {
        return ServiceJob::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
    }

    private function getPendingQuotes(): \Illuminate\Database\Eloquent\Collection
    {
        return Quote::where('status', 'draft')
            ->with(['customer', 'serviceJob'])
            ->latest()
            ->get();
    }

    private function getRecentCustomers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::role('customer')
            ->latest()
            ->take(10)
            ->get();
    }

    private function getActiveEmployees(): \Illuminate\Database\Eloquent\Collection
    {
        return User::role('employee')
            ->where('employee_status', 'active')
            ->withCount(['serviceJobs as assigned_jobs_count' => function ($query) {
                $query->where('status', 'in_progress');
            }])
            ->get();
    }

    public function getEarningsByPeriod(string $period): array
    {
        $now = now();

        return match ($period) {
            'week' => [
                'fullyPaid' => $this->getRevenueByDateRange($now->startOfWeek(), $now->endOfWeek(), 'paid'),
                'downpayment' => $this->getRevenueByDateRange($now->startOfWeek(), $now->endOfWeek(), 'partially_paid'),
                'nonPaid' => $this->getRevenueByDateRange($now->startOfWeek(), $now->endOfWeek(), 'unpaid'),
            ],
            'month' => [
                'fullyPaid' => $this->getRevenueByDateRange($now->startOfMonth(), $now->endOfMonth(), 'paid'),
                'downpayment' => $this->getRevenueByDateRange($now->startOfMonth(), $now->endOfMonth(), 'partially_paid'),
                'nonPaid' => $this->getRevenueByDateRange($now->startOfMonth(), $now->endOfMonth(), 'unpaid'),
            ],
            'year' => [
                'fullyPaid' => $this->getRevenueByDateRange($now->startOfYear(), $now->endOfYear(), 'paid'),
                'downpayment' => $this->getRevenueByDateRange($now->startOfYear(), $now->endOfYear(), 'partially_paid'),
                'nonPaid' => $this->getRevenueByDateRange($now->startOfYear(), $now->endOfYear(), 'unpaid'),
            ],
            default => [
                'fullyPaid' => 0,
                'downpayment' => 0,
                'nonPaid' => 0,
            ],
        };
    }

    private function getRevenueByDateRange($startDate, $endDate, string $paymentStatus): float
    {
        return Quote::whereHas('serviceJob', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->where('status', 'accepted')
            ->where('payment_status', $paymentStatus)
            ->sum('total') ?? 0;
    }

    public function getMonthlyReport(int $year): array
    {
        $config = config('reports');
        $model = $config['source']['model'];
        $statuses = $config['status_filter'];
        $dateCol = $config['date_column'];

        $baseQuery = $model::whereIn('status', $statuses)
            ->whereYear($dateCol, $year);

        $revenue = (clone $baseQuery)
            ->selectRaw('cast(strftime("%m", '.$dateCol.') as integer) as period, SUM(total) as value')
            ->groupBy('period')
            ->pluck('value', 'period');

        $orders = (clone $baseQuery)
            ->selectRaw('cast(strftime("%m", '.$dateCol.') as integer) as period, COUNT(id) as value')
            ->groupBy('period')
            ->pluck('value', 'period');

        $paid = (clone $baseQuery)->where('payment_status', 'paid')
            ->selectRaw('cast(strftime("%m", '.$dateCol.') as integer) as period, SUM(total) as value')
            ->groupBy('period')
            ->pluck('value', 'period');

        $downpayment = (clone $baseQuery)->where('payment_status', 'partially_paid')
            ->selectRaw('cast(strftime("%m", '.$dateCol.') as integer) as period, SUM(total) as value')
            ->groupBy('period')
            ->pluck('value', 'period');

        $unpaid = (clone $baseQuery)->where('payment_status', 'unpaid')
            ->selectRaw('cast(strftime("%m", '.$dateCol.') as integer) as period, SUM(total) as value')
            ->groupBy('period')
            ->pluck('value', 'period');

        return collect(range(1, 12))->map(fn ($m) => [
            'month' => $m,
            'revenue' => (float) ($revenue[$m] ?? 0),
            'orders' => (int) ($orders[$m] ?? 0),
            'paid' => (float) ($paid[$m] ?? 0),
            'downpayment' => (float) ($downpayment[$m] ?? 0),
            'unpaid' => (float) ($unpaid[$m] ?? 0),
        ])->toArray();
    }

    public function getYearlyReport(): array
    {
        $config = config('reports');
        $model = $config['source']['model'];
        $statuses = $config['status_filter'];
        $dateCol = $config['date_column'];

        $years = $model::whereIn('status', $statuses)
            ->selectRaw('cast(strftime("%Y", '.$dateCol.') as integer) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return $years->map(function ($year) use ($model, $statuses, $dateCol) {
            $query = $model::whereYear($dateCol, $year)->whereIn('status', $statuses);

            return [
                'year' => $year,
                'revenue' => (float) (clone $query)->sum('total'),
                'orders' => (clone $query)->count(),
                'paid' => (float) $model::whereYear($dateCol, $year)->whereIn('status', $statuses)->where('payment_status', 'paid')->sum('total'),
                'downpayment' => (float) $model::whereYear($dateCol, $year)->whereIn('status', $statuses)->where('payment_status', 'partially_paid')->sum('total'),
                'unpaid' => (float) $model::whereYear($dateCol, $year)->whereIn('status', $statuses)->where('payment_status', 'unpaid')->sum('total'),
            ];
        })->toArray();
    }

    public function getMonthlyOrderDetails(int $year, int $month): array
    {
        $config = config('reports');
        $model = $config['source']['model'];
        $statuses = $config['status_filter'];
        $dateCol = $config['date_column'];

        $orders = $model::whereIn('status', $statuses)
            ->whereYear($dateCol, $year)
            ->whereMonth($dateCol, $month)
            ->with(['customer', 'serviceJob.service', 'lineItems'])
            ->get();

        $paid = $orders->where('payment_status', 'paid');
        $downpayment = $orders->where('payment_status', 'partially_paid');
        $unpaid = $orders->where('payment_status', 'unpaid');

        $revenueTotal = (float) $orders->sum('total');

        $metrics = [
            [
                'key' => 'revenue',
                'label' => 'Revenue',
                'formula' => "SUM(total) of {$orders->count()} accepted order".($orders->count() !== 1 ? 's' : ''),
                'value' => $revenueTotal,
                'count' => $orders->count(),
            ],
            [
                'key' => 'orders',
                'label' => 'Orders',
                'formula' => 'COUNT(accepted quotes in this month)',
                'value' => $orders->count(),
                'count' => $orders->count(),
            ],
            [
                'key' => 'paid',
                'label' => 'Paid',
                'formula' => "SUM(total) WHERE payment_status = 'paid' (".$paid->count().' order'.($paid->count() !== 1 ? 's' : '').')',
                'value' => (float) $paid->sum('total'),
                'count' => $paid->count(),
            ],
            [
                'key' => 'downpayment',
                'label' => 'Down Payment',
                'formula' => "SUM(total) WHERE payment_status = 'partially_paid' (".$downpayment->count().' order'.($downpayment->count() !== 1 ? 's' : '').')',
                'value' => (float) $downpayment->sum('total'),
                'count' => $downpayment->count(),
            ],
            [
                'key' => 'unpaid',
                'label' => 'Unpaid',
                'formula' => "SUM(total) WHERE payment_status = 'unpaid' (".$unpaid->count().' order'.($unpaid->count() !== 1 ? 's' : '').')',
                'value' => (float) $unpaid->sum('total'),
                'count' => $unpaid->count(),
            ],
        ];

        $paymentSplit = [
            'paid_total' => (float) $paid->sum('total'),
            'downpayment_total' => (float) $downpayment->sum('total'),
            'unpaid_total' => (float) $unpaid->sum('total'),
            'paid_count' => $paid->count(),
            'downpayment_count' => $downpayment->count(),
            'unpaid_count' => $unpaid->count(),
        ];

        $orderList = $orders->map(fn (Quote $quote) => [
            'quote_number' => $quote->quote_number,
            'customer_name' => trim($quote->customer->first_name.' '.$quote->customer->last_name),
            'date' => $quote->created_at->format('M d, Y'),
            'total' => (float) $quote->total,
            'payment_status' => $quote->payment_status,
            'service_type_label' => $quote->serviceJob
                ? ($quote->serviceJob->type === 'printing' ? 'Printing' : 'Technical')
                : 'N/A',
            'service_name' => $quote->serviceJob?->service->name ?? 'N/A',
            'line_items' => $quote->lineItems->map(fn (QuoteLineItem $item) => [
                'item_name' => $item->item_name,
                'description' => $item->description,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
            ])->values(),
        ]);

        return [
            'meta' => [
                'month' => $month,
                'month_name' => now()->year($year)->month($month)->format('F'),
                'year' => $year,
                'total_revenue' => $revenueTotal,
                'total_orders' => $orders->count(),
            ],
            'metrics' => $metrics,
            'payment_split' => $paymentSplit,
            'orders' => $orderList,
        ];
    }

    public function getCurrentEarningsBreakdown(): array
    {
        $totalRevenue = $this->getCompletedJobRevenue();

        // Calculate actual breakdown based on payment status
        $fullyPaid = Quote::whereHas('serviceJob', function ($query) {
            $query->where('status', 'completed');
        })->where('status', 'accepted')
            ->where('payment_status', 'paid')
            ->sum('total') ?? 0;

        $downpayment = Quote::whereHas('serviceJob', function ($query) {
            $query->where('status', 'completed');
        })->where('status', 'accepted')
            ->where('payment_status', 'partially_paid')
            ->sum('total') ?? 0;

        $nonPaid = Quote::whereHas('serviceJob', function ($query) {
            $query->where('status', 'completed');
        })->where('status', 'accepted')
            ->where('payment_status', 'unpaid')
            ->sum('total') ?? 0;

        return [
            'fullyPaid' => $fullyPaid,
            'downpayment' => $downpayment,
            'nonPaid' => $nonPaid,
        ];
    }
}
