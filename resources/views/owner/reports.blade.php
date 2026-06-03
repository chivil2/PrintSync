<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="{ tab: 'monthly', year: {{ $selectedYear }}, selectedMonth: null, modalData: null, loading: false, showModal: false, expandedOrders: {}, toggleOrder(quoteNumber) { this.expandedOrders[quoteNumber] = !this.expandedOrders[quoteNumber]; }, async openModal(month, monthName) { this.selectedMonth = month; this.modalData = null; this.loading = true; this.showModal = true; try { const res = await fetch(`/owner/reports/monthly-orders?year=${this.year}&month=${month}`); this.modalData = await res.json(); } catch (e) { this.modalData = null; } finally { this.loading = false; } } }" @keydown.escape.window="showModal = false">
        <!-- Banner -->
        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
            <div class="welcome-dots"></div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold">Reports</h1>
                <p class="text-orange-100 mt-1">View and analyze your business performance.</p>
            </div>
        </div>

        <!-- Tabs + Year Selector Row -->
        <div class="flex items-center justify-between mb-6">
            <!-- Tab Switcher -->
            <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
                <button @click="tab = 'monthly'" :class="tab === 'monthly' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Monthly
                </button>
                <button @click="tab = 'yearly'" :class="tab === 'yearly' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/>
                        <path d="M9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z"/>
                        <path d="M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    Yearly
                </button>
            </div>

            <!-- Year Selector (Monthly only) -->
            <div x-show="tab === 'monthly'" x-transition class="relative">
                <select @change="window.location.href = '?year=' + year" x-model="year" class="appearance-none bg-white border border-slate-200 rounded-xl px-4 py-2 pr-10 text-sm font-medium text-slate-700 cursor-pointer hover:border-slate-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            @php
                $totalRevenue = collect($monthlyReport)->sum('revenue');
                $totalOrders = collect($monthlyReport)->sum('orders');
            @endphp
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Revenue</div>
                <div class="text-3xl font-bold text-slate-900">₱{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Orders</div>
                <div class="text-3xl font-bold text-slate-900">{{ number_format($totalOrders) }}</div>
            </div>
        </div>

        <!-- Monthly Table -->
        <div x-show="tab === 'monthly'" x-transition class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-blue-950/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">Monthly Breakdown — {{ $selectedYear }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-6 py-3 font-semibold">Month</th>
                            <th class="text-right px-6 py-3 font-semibold">Revenue</th>
                            <th class="text-right px-6 py-3 font-semibold">Orders</th>
                            <th class="text-right px-6 py-3 font-semibold">Paid</th>
                            <th class="text-right px-6 py-3 font-semibold">Down Payment</th>
                            <th class="text-right px-6 py-3 font-semibold">Unpaid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyReport as $row)
                            <tr
                                class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors duration-150 cursor-pointer"
                                @click="openModal({{ $row['month'] }}, '{{ DateTime::createFromFormat('!m', $row['month'])->format('F') }}')"
                            >
                                <td class="px-6 py-3 font-medium text-slate-900">
                                    {{ DateTime::createFromFormat('!m', $row['month'])->format('F') }}
                                </td>
                                <td class="px-6 py-3 text-right text-slate-900 font-medium">
                                    ₱{{ number_format($row['revenue'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right text-slate-600">
                                    {{ $row['orders'] }}
                                </td>
                                <td class="px-6 py-3 text-right text-emerald-600">
                                    ₱{{ number_format($row['paid'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right text-amber-600">
                                    ₱{{ number_format($row['downpayment'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right text-red-500">
                                    ₱{{ number_format($row['unpaid'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-slate-200 bg-slate-50/80">
                            <td class="px-6 py-3 font-semibold text-slate-900">Total</td>
                            <td class="px-6 py-3 text-right font-semibold text-slate-900">
                                ₱{{ number_format(collect($monthlyReport)->sum('revenue'), 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-slate-600">
                                {{ collect($monthlyReport)->sum('orders') }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-emerald-600">
                                ₱{{ number_format(collect($monthlyReport)->sum('paid'), 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-amber-600">
                                ₱{{ number_format(collect($monthlyReport)->sum('downpayment'), 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-red-500">
                                ₱{{ number_format(collect($monthlyReport)->sum('unpaid'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Yearly Table -->
        <div x-show="tab === 'yearly'" x-cloak x-transition class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-blue-950/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">Yearly Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-6 py-3 font-semibold">Year</th>
                            <th class="text-right px-6 py-3 font-semibold">Revenue</th>
                            <th class="text-right px-6 py-3 font-semibold">Orders</th>
                            <th class="text-right px-6 py-3 font-semibold">Paid</th>
                            <th class="text-right px-6 py-3 font-semibold">Down Payment</th>
                            <th class="text-right px-6 py-3 font-semibold">Unpaid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($yearlyReport as $row)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-3 font-medium text-slate-900">{{ $row['year'] }}</td>
                                <td class="px-6 py-3 text-right text-slate-900 font-medium">
                                    ₱{{ number_format($row['revenue'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right text-slate-600">
                                    {{ $row['orders'] }}
                                </td>
                                <td class="px-6 py-3 text-right text-emerald-600">
                                    ₱{{ number_format($row['paid'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right text-amber-600">
                                    ₱{{ number_format($row['downpayment'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right text-red-500">
                                    ₱{{ number_format($row['unpaid'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">No yearly data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($yearlyReport)
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50/80">
                                <td class="px-6 py-3 font-semibold text-slate-900">Total</td>
                                <td class="px-6 py-3 text-right font-semibold text-slate-900">
                                    ₱{{ number_format(collect($yearlyReport)->sum('revenue'), 2) }}
                                </td>
                                <td class="px-6 py-3 text-right font-semibold text-slate-600">
                                    {{ collect($yearlyReport)->sum('orders') }}
                                </td>
                                <td class="px-6 py-3 text-right font-semibold text-emerald-600">
                                    ₱{{ number_format(collect($yearlyReport)->sum('paid'), 2) }}
                                </td>
                                <td class="px-6 py-3 text-right font-semibold text-amber-600">
                                    ₱{{ number_format(collect($yearlyReport)->sum('downpayment'), 2) }}
                                </td>
                                <td class="px-6 py-3 text-right font-semibold text-red-500">
                                    ₱{{ number_format(collect($yearlyReport)->sum('unpaid'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    <!-- Monthly Detail Modal -->
    <div
        x-show="showModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="showModal = false"></div>

        <!-- Modal Panel -->
        <div
            class="relative w-full max-w-3xl max-h-[85vh] bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden"
            @click.outside="showModal = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Header (fixed) -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900" x-text="modalData ? modalData.meta.month_name + ' ' + modalData.meta.year : (selectedMonth ? 'Loading...' : '')"></h3>
                        <p class="text-xs text-slate-500" x-show="modalData" x-text="modalData ? modalData.meta.total_orders + ' accepted orders' : ''"></p>
                    </div>
                </div>
                <button @click="showModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors duration-150 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content (scrollable) -->
            <div class="overflow-y-auto" style="max-height: calc(85vh - 140px);">
                <!-- Loading State -->
                <div x-show="loading" class="p-6 space-y-4">
                    <div class="h-8 bg-slate-100 rounded-lg animate-pulse"></div>
                    <div class="h-8 bg-slate-100 rounded-lg animate-pulse w-5/6"></div>
                    <div class="h-8 bg-slate-100 rounded-lg animate-pulse w-4/6"></div>
                    <div class="h-32 bg-slate-100 rounded-lg animate-pulse mt-6"></div>
                </div>

                <!-- Content loaded -->
                <div x-show="!loading && modalData" class="p-6 space-y-6">
                    <!-- Payment Split Mini Chart -->
                    <div x-show="modalData && modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total > 0">
                        <div class="flex items-center gap-4 mb-2 text-xs font-medium">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="text-slate-600">Paid</span>
                                <span class="text-slate-900 font-semibold" x-text="modalData ? '₱' + Number(modalData.payment_split.paid_total).toLocaleString('en-PH', {minimumFractionDigits: 2}) : ''"></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="text-slate-600">Down Payment</span>
                                <span class="text-slate-900 font-semibold" x-text="modalData ? '₱' + Number(modalData.payment_split.downpayment_total).toLocaleString('en-PH', {minimumFractionDigits: 2}) : ''"></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                <span class="text-slate-600">Unpaid</span>
                                <span class="text-slate-900 font-semibold" x-text="modalData ? '₱' + Number(modalData.payment_split.unpaid_total).toLocaleString('en-PH', {minimumFractionDigits: 2}) : ''"></span>
                            </div>
                        </div>
                        <div class="h-2 rounded-full overflow-hidden flex bg-slate-100">
                            <div
                                class="bg-emerald-400 h-full transition-all duration-500"
                                :style="modalData && (modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total) > 0
                                    ? `width: ${(modalData.payment_split.paid_total / (modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total)) * 100}%`
                                    : 'width: 0%'"
                            ></div>
                            <div
                                class="bg-amber-400 h-full transition-all duration-500"
                                :style="modalData && (modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total) > 0
                                    ? `width: ${(modalData.payment_split.downpayment_total / (modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total)) * 100}%`
                                    : 'width: 0%'"
                            ></div>
                            <div
                                class="bg-red-400 h-full transition-all duration-500"
                                :style="modalData && (modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total) > 0
                                    ? `width: ${(modalData.payment_split.unpaid_total / (modalData.payment_split.paid_total + modalData.payment_split.downpayment_total + modalData.payment_split.unpaid_total)) * 100}%`
                                    : 'width: 0%'"
                            ></div>
                        </div>
                    </div>

                    <!-- Formula Breakdown -->
                    <div>
                        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Formula Breakdown</h4>
                        <div class="grid grid-cols-1 gap-2">
                            <template x-for="metric in (modalData ? modalData.metrics : [])" :key="metric.key">
                                <div class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors duration-150"
                                    :class="{
                                        'bg-slate-50 border border-slate-100': metric.key === 'revenue' || metric.key === 'orders',
                                        'bg-emerald-50/70 border border-emerald-100': metric.key === 'paid',
                                        'bg-amber-50/70 border border-amber-100': metric.key === 'downpayment',
                                        'bg-red-50/70 border border-red-100': metric.key === 'unpaid',
                                    }"
                                >
                                    <div class="min-w-0">
                                        <div class="text-xs text-slate-500 mb-0.5" x-text="metric.formula"></div>
                                        <div class="font-semibold text-slate-900 text-sm" x-text="metric.key === 'orders' ? metric.value : '₱' + Number(metric.value).toLocaleString('en-PH', {minimumFractionDigits: 2})"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div x-show="modalData && modalData.orders.length > 0">
                        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3" x-text="'Orders (' + (modalData ? modalData.orders.length : 0) + ')'"></h4>
                        <div class="border border-slate-100 rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        <th class="w-8 px-2 py-2.5"></th>
                                        <th class="text-left px-4 py-2.5 font-semibold">Quote</th>
                                        <th class="text-left px-4 py-2.5 font-semibold">Customer</th>
                                        <th class="text-left px-4 py-2.5 font-semibold">Date</th>
                                        <th class="text-right px-4 py-2.5 font-semibold">Total</th>
                                        <th class="text-center px-4 py-2.5 font-semibold">Status</th>
                                        <th class="text-left px-4 py-2.5 font-semibold">Service</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(order, orderIdx) in (modalData ? modalData.orders : [])" :key="order.quote_number">
                                        <tr>
                                            <td colspan="7" class="p-0">
                                                <table class="w-full">
                                                    <tbody>
                                                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors duration-150 cursor-pointer" @click="toggleOrder(order.quote_number)">
                                                            <td class="w-8 px-2 py-2.5 text-center">
                                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 mx-auto" :class="{ 'rotate-90': expandedOrders[order.quote_number] }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                    <path d="M9 5l7 7-7 7"/>
                                                                </svg>
                                                            </td>
                                                            <td class="px-4 py-2.5 font-medium text-slate-900" x-text="order.quote_number"></td>
                                                            <td class="px-4 py-2.5 text-slate-600" x-text="order.customer_name"></td>
                                                            <td class="px-4 py-2.5 text-slate-500 text-xs" x-text="order.date"></td>
                                                            <td class="px-4 py-2.5 text-right font-medium text-slate-900" x-text="'₱' + Number(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                                            <td class="px-4 py-2.5 text-center">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                                    :class="{
                                                                        'bg-emerald-50 text-emerald-700': order.payment_status === 'paid',
                                                                        'bg-amber-50 text-amber-700': order.payment_status === 'partially_paid',
                                                                        'bg-red-50 text-red-600': order.payment_status === 'unpaid',
                                                                    }"
                                                                    x-text="order.payment_status === 'paid' ? 'Paid' : order.payment_status === 'partially_paid' ? 'Down Payment' : 'Unpaid'"
                                                                ></span>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-slate-500 text-xs">
                                                                <span x-text="order.service_type_label"></span>
                                                                <span class="text-slate-400 mx-0.5">&mdash;</span>
                                                                <span x-text="order.service_name"></span>
                                                            </td>
                                                        </tr>
                                                        <tr x-show="expandedOrders[order.quote_number]" x-cloak>
                                                            <td colspan="7" class="bg-slate-50/50 px-4 py-3">
                                                                <template x-if="order.line_items && order.line_items.length > 0">
                                                                    <div>
                                                                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Line Items</div>
                                                                        <table class="w-full text-xs">
                                                                            <thead>
                                                                                <tr class="text-slate-400 border-b border-slate-200">
                                                                                    <th class="text-left py-1.5 font-medium">Item</th>
                                                                                    <th class="text-right py-1.5 font-medium">Qty</th>
                                                                                    <th class="text-right py-1.5 font-medium">Price</th>
                                                                                    <th class="text-right py-1.5 font-medium">Subtotal</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <template x-for="item in order.line_items" :key="item.item_name + (item.description || '')">
                                                                                    <tr class="border-b border-slate-100 last:border-0">
                                                                                        <td class="py-1.5 text-slate-700">
                                                                                            <span x-text="item.item_name"></span>
                                                                                            <span x-show="item.description" class="text-slate-400 block" x-text="item.description"></span>
                                                                                        </td>
                                                                                        <td class="py-1.5 text-right text-slate-600" x-text="item.quantity"></td>
                                                                                        <td class="py-1.5 text-right text-slate-600" x-text="'₱' + Number(item.unit_price).toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                                                                        <td class="py-1.5 text-right font-medium text-slate-900" x-text="'₱' + Number(item.line_total).toLocaleString('en-PH', {minimumFractionDigits: 2})"></td>
                                                                                    </tr>
                                                                                </template>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </template>
                                                                <template x-if="!order.line_items || order.line_items.length === 0">
                                                                    <div class="text-xs text-slate-400 italic">No line items</div>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Empty orders state -->
                    <div x-show="modalData && modalData.orders.length === 0" class="text-center py-8">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-sm text-slate-400">No orders for this month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-layouts::app.owner>
