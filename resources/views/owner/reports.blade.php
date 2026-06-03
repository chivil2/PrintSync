<x-layouts::app.owner>
    @php
        $statusData = [
            'in_stock' => $statusBreakdown['in_stock'] ?? 0,
            'low_stock' => $statusBreakdown['low_stock'] ?? 0,
            'out_of_stock' => $statusBreakdown['out_of_stock'] ?? 0,
        ];
    @endphp

    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto"
        x-data="reportsPage(@js($statusData), @js($topByQuantity), @js($topByValue), @js($topByUnitPrice), @js($stockLevelDistribution), @js($supplierBreakdown), @js($salesData), '{{ $period }}', '{{ $month ?? '' }}', '{{ $year ?? '' }}')"
        x-init="initCharts()">

        <!-- Banner -->
        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
            <div class="welcome-dots"></div>
            <div class="relative z-10">
                <h1 class="text-4xl font-bold mb-2">Reports</h1>
                <p class="text-orange-100">Inventory levels, sales revenue, and business insights.</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6">
            <button @click="activeTab = 'inventory'" :class="activeTab === 'inventory' ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'" class="px-6 py-3 rounded-xl font-medium transition-colors border border-slate-200">
                Inventory Reports
            </button>
            <button @click="activeTab = 'sales'" :class="activeTab === 'sales' ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50'" class="px-6 py-3 rounded-xl font-medium transition-colors border border-slate-200">
                Sales Revenue
            </button>
        </div>

        <!-- Inventory Section -->
        <div x-show="activeTab === 'inventory'" x-transition>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" x-text="formatNumber({{ (int) $totals['skus'] }})"></div>
                        <div class="text-xs text-slate-500">Total SKUs</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 7l2 12a2 2 0 002 2h8a2 2 0 002-2l2-12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" x-text="formatNumber({{ (int) $totals['quantity'] }})"></div>
                        <div class="text-xs text-slate-500">Total Units in Stock</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" x-text="formatCurrency({{ (float) $totals['value'] }})"></div>
                        <div class="text-xs text-slate-500">Total Inventory Value</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center gap-3">
                    <div class="bg-rose-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.071 19h13.858c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900" x-text="formatNumber({{ (int) $totals['low_stock'] + (int) $totals['out_of_stock'] }})"></div>
                        <div class="text-xs text-slate-500">Needs Attention</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Stock Status</h3>
                        <p class="text-xs text-slate-500">Current status distribution</p>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas x-ref="statusChart"></canvas>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                    <div class="bg-emerald-50 rounded-xl py-2">
                        <div class="text-lg font-bold text-emerald-700" x-text="status.in_stock"></div>
                        <div class="text-[10px] uppercase tracking-wide text-emerald-600">In Stock</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl py-2">
                        <div class="text-lg font-bold text-amber-700" x-text="status.low_stock"></div>
                        <div class="text-[10px] uppercase tracking-wide text-amber-600">Low</div>
                    </div>
                    <div class="bg-rose-50 rounded-xl py-2">
                        <div class="text-lg font-bold text-rose-700" x-text="status.out_of_stock"></div>
                        <div class="text-[10px] uppercase tracking-wide text-rose-600">Out</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Top 10 Items by Quantity</h3>
                        <p class="text-xs text-slate-500">Items with the highest units in stock</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas x-ref="quantityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Top 10 Items by Value</h3>
                        <p class="text-xs text-slate-500">Highest stock value (quantity x unit price)</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas x-ref="valueChart"></canvas>
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Stock Level Distribution</h3>
                        <p class="text-xs text-slate-500">How units are spread across quantity buckets</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas x-ref="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Top 10 by Unit Price</h3>
                        <p class="text-xs text-slate-500">Most expensive items per unit</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas x-ref="unitPriceChart"></canvas>
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Supplier Breakdown</h3>
                        <p class="text-xs text-slate-500">Inventory value grouped by supplier</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas x-ref="supplierChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Low Stock Table -->
        <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Items Needing Attention</h3>
                    <p class="text-xs text-slate-500">Low stock and out of stock items, ordered by quantity</p>
                </div>
                <a href="{{ route('owner.inventory.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View inventory &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-200">
                            <th class="border border-slate-200 px-3 py-2 text-left font-semibold text-slate-700">SKU</th>
                            <th class="border border-slate-200 px-3 py-2 text-left font-semibold text-slate-700">Name</th>
                            <th class="border border-slate-200 px-3 py-2 text-right font-semibold text-slate-700">Quantity</th>
                            <th class="border border-slate-200 px-3 py-2 text-right font-semibold text-slate-700">Min Level</th>
                            <th class="border border-slate-200 px-3 py-2 text-center font-semibold text-slate-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $item)
                            <tr class="hover:bg-rose-50/50 transition-colors">
                                <td class="border border-slate-200 px-3 py-2 text-slate-700 font-mono">{{ $item->sku }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-slate-900 font-medium">{{ $item->name }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-slate-700 text-right font-mono">{{ number_format($item->quantity) }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-slate-700 text-right font-mono">{{ number_format($item->min_stock_level) }}</td>
                                <td class="border border-slate-200 px-3 py-2 text-center">
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-semibold rounded
                                        @if($item->status === 'out_of_stock') bg-rose-100 text-rose-800 border border-rose-200
                                        @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                        {{ str_replace('_', ' ', ucfirst($item->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border border-slate-200 px-3 py-6 text-center text-slate-500">
                                    All inventory items are well stocked
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>

        <!-- Sales Revenue Section -->
        <div x-show="activeTab === 'sales'" x-transition>
            <!-- Filters -->
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow mb-6">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 block mb-1">Period</label>
                        <select x-model="period" @change="applyFilters()" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Time</option>
                            <option value="month">By Month</option>
                            <option value="year">By Year</option>
                        </select>
                    </div>
                    <div x-show="period === 'month'">
                        <label class="text-xs font-semibold text-slate-500 block mb-1">Month</label>
                        <select x-model="month" @change="applyFilters()" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div x-show="period === 'month' || period === 'year'">
                        <label class="text-xs font-semibold text-slate-500 block mb-1">Year</label>
                        <select x-model="year" @change="applyFilters()" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Year</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-emerald-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900" x-text="formatCurrency(salesData.totalRevenue)"></div>
                            <div class="text-xs text-slate-500">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900" x-text="formatNumber(salesData.totalOrders)"></div>
                            <div class="text-xs text-slate-500">Total Orders</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900" x-text="formatNumber(salesData.totalCustomers)"></div>
                            <div class="text-xs text-slate-500">Total Customers</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900" x-text="formatCurrency(salesData.avgOrderValue)"></div>
                            <div class="text-xs text-slate-500">Avg Order Value</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Monthly Revenue (Last 12 Months)</h3>
                            <p class="text-xs text-slate-500">Revenue and orders over time</p>
                        </div>
                    </div>
                    <div class="relative h-80">
                        <canvas x-ref="monthlyTrendChart"></canvas>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Yearly Revenue</h3>
                            <p class="text-xs text-slate-500">Revenue and orders by year</p>
                        </div>
                    </div>
                    <div class="relative h-80">
                        <canvas x-ref="yearlyTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Top Services by Revenue</h3>
                            <p class="text-xs text-slate-500">Best performing services</p>
                        </div>
                    </div>
                    <div class="relative h-80">
                        <canvas x-ref="topServicesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Top Customers by Revenue</h3>
                            <p class="text-xs text-slate-500">Highest spending customers</p>
                        </div>
                    </div>
                    <div class="relative h-80">
                        <canvas x-ref="topCustomersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Orders Table -->
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Top 10 Orders by Value</h3>
                        <p class="text-xs text-slate-500">Highest value orders</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-200">
                                <th class="border border-slate-200 px-3 py-2 text-left font-semibold text-slate-700">Order ID</th>
                                <th class="border border-slate-200 px-3 py-2 text-left font-semibold text-slate-700">Customer</th>
                                <th class="border border-slate-200 px-3 py-2 text-right font-semibold text-slate-700">Total</th>
                                <th class="border border-slate-200 px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="order in salesData.topOrders" :key="order.id">
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="border border-slate-200 px-3 py-2 text-slate-700 font-mono" x-text="'#' + order.id"></td>
                                    <td class="border border-slate-200 px-3 py-2 text-slate-900 font-medium" x-text="order.customer"></td>
                                    <td class="border border-slate-200 px-3 py-2 text-slate-700 text-right font-mono" x-text="formatCurrency(order.total)"></td>
                                    <td class="border border-slate-200 px-3 py-2 text-slate-700" x-text="order.created_at"></td>
                                </tr>
                            </template>
                            <tr x-show="salesData.topOrders.length === 0">
                                <td colspan="4" class="border border-slate-200 px-3 py-6 text-center text-slate-500">
                                    No orders found for the selected period
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        function reportsPage(status, topByQuantity, topByValue, topByUnitPrice, distribution, suppliers, salesData, initialPeriod, initialMonth, initialYear) {
            return {
                activeTab: 'inventory',
                status: status,
                topByQuantity: topByQuantity,
                topByValue: topByValue,
                topByUnitPrice: topByUnitPrice,
                distribution: distribution,
                suppliers: suppliers,
                salesData: salesData,
                period: initialPeriod || 'all',
                month: initialMonth || '',
                year: initialYear || '',
                charts: {},

                formatNumber(value) {
                    return new Intl.NumberFormat('en-PH').format(value);
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 2 }).format(value);
                },

                destroyCharts() {
                    Object.values(this.charts).forEach(chart => chart && chart.destroy());
                    this.charts = {};
                },

                applyFilters() {
                    const url = new URL(window.location);
                    if (this.period !== 'all') {
                        url.searchParams.set('period', this.period);
                        if (this.period === 'month' && this.month) {
                            url.searchParams.set('month', this.month);
                        }
                        if (this.year) {
                            url.searchParams.set('year', this.year);
                        }
                    } else {
                        url.searchParams.delete('period');
                        url.searchParams.delete('month');
                        url.searchParams.delete('year');
                    }
                    window.location.href = url.toString();
                },

                initCharts() {
                    this.destroyCharts();

                    // Inventory Charts
                    this.charts.status = new Chart(this.$refs.statusChart, {
                        type: 'doughnut',
                        data: {
                            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                            datasets: [{
                                data: [this.status.in_stock, this.status.low_stock, this.status.out_of_stock],
                                backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                                borderWidth: 0,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 10, padding: 12 } },
                            }
                        }
                    });

                    this.charts.quantity = new Chart(this.$refs.quantityChart, {
                        type: 'bar',
                        data: {
                            labels: this.topByQuantity.map(i => i.name),
                            datasets: [{
                                label: 'Quantity',
                                data: this.topByQuantity.map(i => i.quantity),
                                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                                borderRadius: 6,
                                maxBarThickness: 22,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (ctx) => this.formatNumber(ctx.parsed.x) + ' units' } }
                            },
                            scales: {
                                x: { beginAtZero: true, ticks: { callback: (v) => this.formatNumber(v) }, grid: { color: '#f1f5f9' } },
                                y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                            }
                        }
                    });

                    this.charts.value = new Chart(this.$refs.valueChart, {
                        type: 'bar',
                        data: {
                            labels: this.topByValue.map(i => i.name),
                            datasets: [{
                                label: 'Value',
                                data: this.topByValue.map(i => i.value),
                                backgroundColor: 'rgba(99, 102, 241, 0.85)',
                                borderRadius: 6,
                                maxBarThickness: 22,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (ctx) => this.formatCurrency(ctx.parsed.x) } }
                            },
                            scales: {
                                x: { beginAtZero: true, ticks: { callback: (v) => '₱' + this.formatNumber(v) }, grid: { color: '#f1f5f9' } },
                                y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                            }
                        }
                    });

                    this.charts.distribution = new Chart(this.$refs.distributionChart, {
                        type: 'bar',
                        data: {
                            labels: this.distribution.map(d => d.label),
                            datasets: [{
                                label: 'SKUs',
                                data: this.distribution.map(d => d.count),
                                backgroundColor: ['#f43f5e', '#fb7185', '#f59e0b', '#3b82f6', '#10b981', '#6366f1'],
                                borderRadius: 6,
                                maxBarThickness: 50,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (ctx) => ctx.parsed.y + ' SKUs' } }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f1f5f9' } },
                                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                            }
                        }
                    });

                    this.charts.unitPrice = new Chart(this.$refs.unitPriceChart, {
                        type: 'bar',
                        data: {
                            labels: this.topByUnitPrice.map(i => i.name),
                            datasets: [{
                                label: 'Unit Price',
                                data: this.topByUnitPrice.map(i => i.unit_price),
                                backgroundColor: 'rgba(244, 63, 94, 0.85)',
                                borderRadius: 6,
                                maxBarThickness: 22,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (ctx) => this.formatCurrency(ctx.parsed.x) } }
                            },
                            scales: {
                                x: { beginAtZero: true, ticks: { callback: (v) => '₱' + this.formatNumber(v) }, grid: { color: '#f1f5f9' } },
                                y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                            }
                        }
                    });

                    this.charts.supplier = new Chart(this.$refs.supplierChart, {
                        type: 'bar',
                        data: {
                            labels: this.suppliers.map(s => s.supplier),
                            datasets: [
                                {
                                    label: 'Value',
                                    data: this.suppliers.map(s => s.value),
                                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                    borderRadius: 6,
                                    yAxisID: 'y',
                                    maxBarThickness: 30,
                                },
                                {
                                    label: 'Items',
                                    data: this.suppliers.map(s => s.items),
                                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                                    borderRadius: 6,
                                    yAxisID: 'y1',
                                    maxBarThickness: 30,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 10, padding: 12 } },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => {
                                            if (ctx.dataset.yAxisID === 'y') return 'Value: ' + this.formatCurrency(ctx.parsed.y);
                                            return 'Items: ' + ctx.parsed.y;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Value (₱)' }, grid: { color: '#f1f5f9' } },
                                y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Items' }, grid: { display: false } },
                                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                            }
                        }
                    });

                    // Sales Revenue Charts
                    if (this.$refs.monthlyTrendChart) {
                        this.charts.monthlyTrend = new Chart(this.$refs.monthlyTrendChart, {
                            type: 'line',
                            data: {
                                labels: this.salesData.monthlyTrend.map(d => d.label),
                                datasets: [
                                    {
                                        label: 'Revenue',
                                        data: this.salesData.monthlyTrend.map(d => d.revenue),
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        fill: true,
                                        tension: 0.4,
                                        yAxisID: 'y',
                                    },
                                    {
                                        label: 'Orders',
                                        data: this.salesData.monthlyTrend.map(d => d.orders),
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        fill: true,
                                        tension: 0.4,
                                        yAxisID: 'y1',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 10, padding: 12 } },
                                    tooltip: {
                                        callbacks: {
                                            label: (ctx) => {
                                                if (ctx.dataset.yAxisID === 'y') return 'Revenue: ' + this.formatCurrency(ctx.parsed.y);
                                                return 'Orders: ' + ctx.parsed.y;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Revenue (₱)' }, grid: { color: '#f1f5f9' } },
                                    y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Orders' }, grid: { display: false } },
                                    x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                                }
                            }
                        });
                    }

                    if (this.$refs.yearlyTrendChart) {
                        this.charts.yearlyTrend = new Chart(this.$refs.yearlyTrendChart, {
                            type: 'bar',
                            data: {
                                labels: this.salesData.yearlyTrend.map(d => d.label),
                                datasets: [
                                    {
                                        label: 'Revenue',
                                        data: this.salesData.yearlyTrend.map(d => d.revenue),
                                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                        borderRadius: 6,
                                        yAxisID: 'y',
                                    },
                                    {
                                        label: 'Orders',
                                        data: this.salesData.yearlyTrend.map(d => d.orders),
                                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                                        borderRadius: 6,
                                        yAxisID: 'y1',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 10, padding: 12 } },
                                    tooltip: {
                                        callbacks: {
                                            label: (ctx) => {
                                                if (ctx.dataset.yAxisID === 'y') return 'Revenue: ' + this.formatCurrency(ctx.parsed.y);
                                                return 'Orders: ' + ctx.parsed.y;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Revenue (₱)' }, grid: { color: '#f1f5f9' } },
                                    y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Orders' }, grid: { display: false } },
                                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                                }
                            }
                        });
                    }

                    if (this.$refs.topServicesChart) {
                        this.charts.topServices = new Chart(this.$refs.topServicesChart, {
                            type: 'bar',
                            data: {
                                labels: this.salesData.topServices.map(s => s.service),
                                datasets: [{
                                    label: 'Revenue',
                                    data: this.salesData.topServices.map(s => s.revenue),
                                    backgroundColor: 'rgba(99, 102, 241, 0.85)',
                                    borderRadius: 6,
                                    maxBarThickness: 30,
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { callbacks: { label: (ctx) => this.formatCurrency(ctx.parsed.x) } }
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { callback: (v) => '₱' + this.formatNumber(v) }, grid: { color: '#f1f5f9' } },
                                    y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                                }
                            }
                        });
                    }

                    if (this.$refs.topCustomersChart) {
                        this.charts.topCustomers = new Chart(this.$refs.topCustomersChart, {
                            type: 'bar',
                            data: {
                                labels: this.salesData.topCustomers.map(c => c.customer),
                                datasets: [{
                                    label: 'Revenue',
                                    data: this.salesData.topCustomers.map(c => c.revenue),
                                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                                    borderRadius: 6,
                                    maxBarThickness: 30,
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { callbacks: { label: (ctx) => this.formatCurrency(ctx.parsed.x) } }
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { callback: (v) => '₱' + this.formatNumber(v) }, grid: { color: '#f1f5f9' } },
                                    y: { ticks: { font: { size: 11 } }, grid: { display: false } }
                                }
                            }
                        });
                    }
                }
            }
        }
    </script>
</x-layouts::app.owner>
