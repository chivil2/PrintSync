<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\ServiceJob;
use App\Models\User;

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

    private function getJobsByStatus(): \Illuminate\Support\Collection
    {
        return ServiceJob::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    private function getServiceTypes(): \Illuminate\Support\Collection
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
        
        return match($period) {
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
