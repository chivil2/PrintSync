<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
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

        return view('owner.reports', [
            'totals' => $totals,
            'statusBreakdown' => $statusBreakdown,
            'topByQuantity' => $topByQuantity,
            'topByValue' => $topByValue,
            'topByUnitPrice' => $topByUnitPrice,
            'stockLevelDistribution' => $stockLevelDistribution,
            'lowStockItems' => $lowStockItems,
            'supplierBreakdown' => $supplierBreakdown,
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
}
