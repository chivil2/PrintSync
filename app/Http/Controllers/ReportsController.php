<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Quote;
use App\Models\ServiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = Inventory::query()->get();

        $totals = [
            'skus' => $inventory->count(),
            'quantity' => (int) $inventory->sum('quantity'),
            'value' => (float) $inventory->sum(fn (Inventory $item) => $item->quantity * (float) $item->unit_price),
            'low_stock' => $inventory->where('status', 'low_stock')->count(),
            'out_of_stock' => $inventory->where('status', 'out_of_stock')->count(),
        ];

        $statusBreakdown = [
            'in_stock' => $inventory->where('status', 'in_stock')->count(),
            'low_stock' => $inventory->where('status', 'low_stock')->count(),
            'out_of_stock' => $inventory->where('status', 'out_of_stock')->count(),
        ];

        $topByQuantity = $inventory
            ->sortByDesc('quantity')
            ->take(10)
            ->map(fn (Inventory $item) => [
                'name' => $item->name,
                'quantity' => (int) $item->quantity,
            ])
            ->values();

        $topByValue = $inventory
            ->map(fn (Inventory $item) => [
                'name' => $item->name,
                'value' => round((float) $item->quantity * (float) $item->unit_price, 2),
            ])
            ->sortByDesc('value')
            ->take(10)
            ->values();

        $topByUnitPrice = $inventory
            ->sortByDesc('unit_price')
            ->take(10)
            ->map(fn (Inventory $item) => [
                'name' => $item->name,
                'unit_price' => (float) $item->unit_price,
                'unit' => $item->unit,
            ])
            ->values();

        $stockLevelDistribution = $this->buildStockLevelDistribution($inventory);

        $lowStockItems = $inventory
            ->filter(fn (Inventory $item) => $item->status !== 'in_stock')
            ->sortBy('quantity')
            ->values();

        $supplierBreakdown = $inventory
            ->groupBy(fn (Inventory $item) => $item->supplier ?: 'Unspecified')
            ->map(fn (Collection $group, string $supplier) => [
                'supplier' => $supplier,
                'items' => $group->count(),
                'value' => round((float) $group->sum(fn (Inventory $item) => $item->quantity * (float) $item->unit_price), 2),
            ])
            ->sortByDesc('value')
            ->values();

        // Sales Revenue Data
        $period = $request->query('period', 'all');
        $month = $request->query('month');
        $year = $request->query('year');

        $salesData = $this->getSalesRevenue($period, $month, $year);

        return view('owner.reports', [
            'totals' => $totals,
            'statusBreakdown' => $statusBreakdown,
            'topByQuantity' => $topByQuantity,
            'topByValue' => $topByValue,
            'topByUnitPrice' => $topByUnitPrice,
            'stockLevelDistribution' => $stockLevelDistribution,
            'lowStockItems' => $lowStockItems,
            'supplierBreakdown' => $supplierBreakdown,
            'salesData' => $salesData,
            'period' => $period,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * @param  Collection<int, Inventory>  $inventory
     * @return array<int, array{label: string, count: int, fill: string}>
     */
    private function buildStockLevelDistribution(Collection $inventory): array
    {
        $buckets = [
            ['label' => 'Out (0)', 'min' => 0, 'max' => 0],
            ['label' => 'Critical (1-10)', 'min' => 1, 'max' => 10],
            ['label' => 'Low (11-50)', 'min' => 11, 'max' => 50],
            ['label' => 'Medium (51-200)', 'min' => 51, 'max' => 200],
            ['label' => 'High (201-500)', 'min' => 201, 'max' => 500],
            ['label' => 'Very High (500+)', 'min' => 501, 'max' => PHP_INT_MAX],
        ];

        return array_map(function (array $bucket) use ($inventory) {
            $count = $inventory->filter(fn (Inventory $item) => $item->quantity >= $bucket['min'] && $item->quantity <= $bucket['max'])->count();

            return [
                'label' => $bucket['label'],
                'count' => $count,
            ];
        }, $buckets);
    }

    private function getSalesRevenue(string $period, ?string $month, ?string $year): array
    {
        $query = Quote::whereIn('service_job_id', ServiceJob::where('status', 'completed')->pluck('id'));

        // Apply filters
        if ($period === 'month' && $month && $year) {
            $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        } elseif ($period === 'year' && $year) {
            $query->whereYear('created_at', $year);
        }
        // 'all' period - no filter

        $quotes = $query->with(['serviceJob', 'customer'])->get();

        $totalRevenue = (float) $quotes->sum('total');
        $totalOrders = $quotes->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalCustomers = $quotes->pluck('customer_id')->unique()->count();

        // Monthly trend (last 12 months)
        $monthlyTrend = Quote::whereIn('service_job_id', ServiceJob::where('status', 'completed')->pluck('id'))
            ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, SUM(total) as revenue, COUNT(*) as orders")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($item) => [
                'label' => date('M Y', mktime(0, 0, 0, (int) $item->month, 1, (int) $item->year)),
                'revenue' => (float) $item->revenue,
                'orders' => $item->orders,
            ])
            ->values();

        // Yearly trend
        $yearlyTrend = Quote::whereIn('service_job_id', ServiceJob::where('status', 'completed')->pluck('id'))
            ->selectRaw("strftime('%Y', created_at) as year, SUM(total) as revenue, COUNT(*) as orders")
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(fn ($item) => [
                'label' => (string) $item->year,
                'revenue' => (float) $item->revenue,
                'orders' => $item->orders,
            ])
            ->values();

        // Top services by revenue
        $topServices = $quotes
            ->load('serviceJob.service')
            ->groupBy(fn ($quote) => $quote->serviceJob?->service?->name ?? 'Unknown')
            ->map(fn ($group, $service) => [
                'service' => $service,
                'revenue' => (float) $group->sum('total'),
                'orders' => $group->count(),
            ])
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        // Top customers by revenue
        $topCustomers = $quotes
            ->groupBy('customer_id')
            ->map(fn ($group) => [
                'customer' => $group->first()->customer?->first_name.' '.$group->first()->customer?->last_name ?? 'Unknown',
                'revenue' => (float) $group->sum('total'),
                'orders' => $group->count(),
            ])
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        // Top orders by value
        $topOrders = $quotes
            ->sortByDesc('total')
            ->take(10)
            ->map(fn ($quote) => [
                'id' => $quote->id,
                'customer' => $quote->customer?->first_name.' '.$quote->customer?->last_name ?? 'Unknown',
                'total' => (float) $quote->total,
                'created_at' => $quote->created_at->format('M d, Y'),
            ])
            ->values();

        return [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'totalCustomers' => $totalCustomers,
            'monthlyTrend' => $monthlyTrend,
            'yearlyTrend' => $yearlyTrend,
            'topServices' => $topServices,
            'topCustomers' => $topCustomers,
            'topOrders' => $topOrders,
        ];
    }
}
