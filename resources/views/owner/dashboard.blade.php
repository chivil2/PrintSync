<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="dashboardData()" x-init="initDashboard()">
        <div class="bg-gradient-to-r from-[#14224a] via-[#2f4fae] to-[#ff6a2a] rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 text-white mb-6 shadow-2xl shadow-blue-950/30 ring-1 ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-start">
                <div>
                    <h1 class="text-2xl sm:text-[28px] font-bold tracking-tight">Dashboard</h1>
                    <p class="text-white/85 mt-1">Hello there, {{ ucfirst(auth()->user()->first_name) }} {{ ucfirst(auth()->user()->last_name) }}</p>
                </div>
                <div class="text-right" x-data="{ time: '', date: '' }" x-init="time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); date = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }); setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }, 1000)">
                    <div class="text-2xl sm:text-[32px] font-bold leading-none tracking-tight" x-text="time"></div>
                    <div class="text-white/85 text-sm mt-1.5" x-text="date"></div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            <div class="bg-white/95 rounded-[24px] p-6 shadow-xl shadow-blue-950/10 border border-white/70">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-[28px] font-bold tracking-tight text-slate-900">₱{{ number_format($completedJobRevenue, 0) }}</div>
                        <div class="text-slate-600 text-[15px] mt-1 font-medium">From Completed Orders</div>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-50 to-orange-50 flex items-center justify-center text-[#1f347a]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/95 rounded-[24px] p-6 shadow-xl shadow-blue-950/10 border border-white/70">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-[28px] font-bold tracking-tight text-slate-900">{{ $completedJobs }}</div>
                        <div class="text-slate-600 text-[15px] mt-1 font-medium">Total Orders Fulfilled</div>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-50 to-orange-50 flex items-center justify-center text-[#1f347a]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/95 rounded-[24px] p-6 shadow-xl shadow-blue-950/10 border border-white/70">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-[28px] font-bold tracking-tight text-slate-900">₱{{ number_format($averageOrderValue, 0) }}</div>
                        <div class="text-slate-600 text-[15px] mt-1 font-medium">Per Transaction</div>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-50 to-orange-50 flex items-center justify-center text-[#1f347a]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings Section -->
        <div class="bg-[#f5ede3]/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 shadow-2xl shadow-blue-950/20 border border-white/60 mb-6">
            <h2 class="text-[24px] font-bold text-slate-900 mb-6">Earnings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <div>
                    <div class="relative mx-auto h-[240px] w-[240px] sm:h-[280px] sm:w-[280px]">
                        @php
                            $totalEarnings = $completedJobRevenue;
                            $colors = ['#f4a5c2', '#f5d87a', '#a8c8e8'];
                        @endphp
                        <svg viewBox="0 0 36 36" class="w-full h-full">
                            <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#f4a5c2" stroke-width="3" stroke-dasharray="60 40" stroke-dashoffset="25"></circle>
                            <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#f5d87a" stroke-width="3" stroke-dasharray="25 75" stroke-dashoffset="-35"></circle>
                            <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#a8c8e8" stroke-width="3" stroke-dasharray="15 85" stroke-dashoffset="-60"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <div class="text-[36px] font-bold text-slate-900 leading-none">₱{{ number_format($totalEarnings / 1000, 1) }}k</div>
                        </div>
                        <div class="absolute top-[10%] left-[8%] w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center text-lg">💰</div>
                        <div class="absolute top-[8%] right-[16%] w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center text-lg">💖</div>
                        <div class="absolute bottom-[15%] left-[10%] w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center text-lg">📄</div>
                    </div>

                    <div class="flex justify-center mt-2">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800">
                                <span x-text="earningsPeriod === 'week' ? 'This week' : (earningsPeriod === 'month' ? 'This month' : 'This year')"></span>
                                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="absolute left-1/2 top-full z-20 mt-2 w-40 -translate-x-1/2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg transition-all duration-200" :class="open ? 'pointer-events-auto translate-y-0 opacity-100' : 'pointer-events-none -translate-y-2 opacity-0'">
                                <button @click="setEarningsPeriod('week'); open = false" class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition" :class="earningsPeriod === 'week' ? 'bg-[#f5ede3] font-medium text-slate-900' : 'text-slate-600 hover:bg-slate-50'">
                                    <span>This week</span>
                                    <svg x-show="earningsPeriod === 'week'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button @click="setEarningsPeriod('month'); open = false" class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition" :class="earningsPeriod === 'month' ? 'bg-[#f5ede3] font-medium text-slate-900' : 'text-slate-600 hover:bg-slate-50'">
                                    <span>This month</span>
                                    <svg x-show="earningsPeriod === 'month'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button @click="setEarningsPeriod('year'); open = false" class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition" :class="earningsPeriod === 'year' ? 'bg-[#f5ede3] font-medium text-slate-900' : 'text-slate-600 hover:bg-slate-50'">
                                    <span>This year</span>
                                    <svg x-show="earningsPeriod === 'year'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="bg-[#fce0eb] rounded-2xl p-4 border border-[#f8c8d8]">
                        <div class="flex items-center gap-2 text-[15px] font-medium text-slate-800"><span>💳</span>Payments received</div>
                        <div class="flex items-baseline justify-between mt-2">
                            <div class="text-[26px] font-bold text-slate-900" x-text="`₱${earningsBreakdown.fullyPaid.toLocaleString()}`"></div>
                            <div class="flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 px-2.5 py-1 rounded-full">↑ +13%</div>
                        </div>
                        <div class="text-xs text-slate-600 mt-1">Total receipts value</div>
                    </div>
                    <div class="bg-[#dbeafe] rounded-2xl p-4 border border-[#bfdbfe]">
                        <div class="flex items-center gap-2 text-[15px] font-medium text-slate-800"><span>📤</span>Downpayments</div>
                        <div class="flex items-baseline justify-between mt-2">
                            <div class="text-[26px] font-bold text-slate-900" x-text="`₱${earningsBreakdown.downpayment.toLocaleString()}`"></div>
                            <div class="flex items-center gap-1 text-xs font-medium text-red-700 bg-red-100 px-2.5 py-1 rounded-full">↓ -6%</div>
                        </div>
                        <div class="text-xs text-slate-600 mt-1">Partial payments received</div>
                    </div>
                    <div class="bg-[#fef3c7] rounded-2xl p-4 border border-[#fde68a]">
                        <div class="flex items-center gap-2 text-[15px] font-medium text-slate-800"><span>🧾</span>Non-paid invoices</div>
                        <div class="flex items-baseline justify-between mt-2">
                            <div class="text-[26px] font-bold text-slate-900" x-text="`₱${earningsBreakdown.nonPaid.toLocaleString()}`"></div>
                            <div class="flex items-center gap-1 text-xs font-medium text-red-700 bg-red-100 px-2.5 py-1 rounded-full">↓ -17%</div>
                        </div>
                        <div class="text-xs text-slate-600 mt-1">Confirmed orders awaiting completion</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quote Approval Section -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-900 text-[17px]">Quote Approval</h3>
                <div class="text-xs text-slate-600 font-medium" x-text="`${@js($pendingQuotes).length} pending`"></div>
            </div>
            <div class="grid grid-cols-1 gap-3">
                <template x-for="quote in paginatedQuotes" :key="quote.id">
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl bg-blue-400 flex items-center justify-center text-white font-bold text-sm shrink-0" x-text="quote.customer ? quote.customer.first_name[0] + quote.customer.last_name[0] : 'NA'"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-semibold text-slate-900 truncate" x-text="quote.customer ? `${quote.customer.first_name} ${quote.customer.last_name}` : 'N/A'"></div>
                                    <div class="font-bold text-slate-900 whitespace-nowrap" x-text="`₱${(quote.amount || 0).toLocaleString()}`"></div>
                                </div>
                                <div class="mt-1 grid grid-cols-2 gap-x-3 gap-y-1 text-[11px] text-slate-500">
                                    <span>Order ID: <b class="text-slate-700" x-text="`ORD-Q${quote.id}`"></b></span>
                                    <span class="text-right">Expires: <b class="text-slate-700" x-text="quote.expires_at ? new Date(quote.expires_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'"></b></span>
                                    <span class="col-span-2 truncate" x-text="quote.service_job?.name || 'Service Request'"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end gap-2">
                            <button @click="approveQuote(quote.id)" class="flex items-center gap-1 rounded-lg bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                            <button @click="rejectQuote(quote.id)" class="flex items-center gap-1 rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </div>
                    </div>
                </template>
                <div x-show="@js($pendingQuotes).length === 0" class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No quotes awaiting approval.</div>
            </div>
            
            <!-- Pagination -->
            <div x-show="totalQuotePages > 1" class="flex items-center justify-center gap-2 mt-4">
                <button @click="prevQuotePage()" :disabled="quotePage === 0" class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <template x-for="page in totalQuotePages" :key="page">
                    <button @click="goToQuotePage(page - 1)" :class="quotePage === page - 1 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="w-8 h-8 rounded-lg text-sm font-medium transition" x-text="page"></button>
                </template>
                <button @click="nextQuotePage()" :disabled="quotePage === totalQuotePages - 1" class="p-2 rounded-lg hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <!-- Latest Transactions -->
        <div class="bg-white/95 rounded-[24px] p-5 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900 text-[17px]">Latest Transactions</h3>
                <a href="{{ route('owner.quotes') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="space-y-3">
                @php
                    $recentTransactions = \App\Models\Quote::with(['customer'])
                        ->where('status', 'accepted')
                        ->whereNotNull('payment_status')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                @if($recentTransactions->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No recent transactions.</div>
                @else
                    @foreach($recentTransactions as $transaction)
                        <div class="flex items-center justify-between p-3 bg-[#f5ede3] rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-orange-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ $transaction->customer ? $transaction->customer->first_name[0] . $transaction->customer->last_name[0] : 'NA' }}
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900 text-sm">{{ $transaction->customer ? $transaction->customer->first_name . ' ' . $transaction->customer->last_name : 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ ucfirst(str_replace('_', ' ', $transaction->payment_status ?? 'unpaid')) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-slate-900">₱{{ number_format($transaction->total ?? 0, 2) }}</div>
                                <div class="text-xs text-slate-500">{{ $transaction->created_at ? $transaction->created_at->diffForHumans() : 'N/A' }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white/95 rounded-[24px] p-5 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <div class="flex items-center gap-2 mb-4 flex-wrap">
                <button @click="setOrderFilter('all')" :class="orderFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'" class="px-4 py-1.5 rounded-full text-[13px] font-medium whitespace-nowrap transition-all">All</button>
                <button @click="setOrderFilter('printing')" :class="orderFilter === 'printing' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'" class="px-4 py-1.5 rounded-full text-[13px] font-medium whitespace-nowrap transition-all">Printing</button>
                <button @click="setOrderFilter('technical')" :class="orderFilter === 'technical' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'" class="px-4 py-1.5 rounded-full text-[13px] font-medium whitespace-nowrap transition-all">Technical</button>
                <div class="ml-auto">
                    <input type="text" x-model="searchQuery" placeholder="Search orders..." class="px-3 py-1.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[13px] text-slate-500 border-b border-slate-100">
                            <th class="pb-3 font-medium">Type</th>
                            <th class="pb-3 font-medium">Date Placed</th>
                            <th class="pb-3 font-medium">Customer</th>
                            <th class="pb-3 font-medium">Order</th>
                            <th class="pb-3 font-medium text-right pr-8">Amount</th>
                            <th class="pb-3 font-medium">Due Date</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Assign</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <template x-for="job in filteredOrders.slice(0, 5)" :key="job.id">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-medium" :class="job.service_type === 'printing' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="job.service_type === 'printing' ? 'bg-orange-500' : 'bg-blue-500'"></span>
                                        <span x-text="job.service_type || 'Service'"></span>
                                    </span>
                                </td>
                                <td class="py-3 text-slate-600" x-text="new Date(job.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                                <td class="py-3 font-medium text-slate-900" x-text="job.customer ? `${job.customer.first_name} ${job.customer.last_name}` : 'N/A'"></td>
                                <td class="py-3 text-slate-600 max-w-[180px] truncate" x-text="job.name"></td>
                                <td class="py-3 text-right font-semibold text-slate-900 pr-8" x-text="`₱${(job.quote?.amount || 0).toLocaleString()}`"></td>
                                <td class="py-3 text-slate-600" x-text="job.deadline ? new Date(job.deadline).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'"></td>
                                <td class="py-3">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[12px] font-medium" :class="{
                                        'bg-amber-100 text-amber-700': job.status === 'pending',
                                        'bg-blue-100 text-blue-700': job.status === 'in_progress',
                                        'bg-green-100 text-green-700': job.status === 'completed',
                                        'bg-red-100 text-red-700': job.status === 'cancelled',
                                        'bg-slate-100 text-slate-600': !['pending', 'in_progress', 'completed', 'cancelled'].includes(job.status)
                                    }" x-text="job.status.replace('_', ' ')"></span>
                                </td>
                                <td class="py-3">
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                            Assign
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute right-0 top-full z-20 mt-1 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg transition-all" style="display: none;">
                                            <div class="p-2">
                                                <template x-for="employee in @js($employees)" :key="employee.id">
                                                    <button @click="assignEmployee(job.id, employee.id); open = false" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                                                        <div class="h-6 w-6 rounded-full bg-gradient-to-br from-blue-500 to-orange-500 flex items-center justify-center text-white text-[10px] font-semibold" x-text="employee.first_name[0] + employee.last_name[0]"></div>
                                                        <span x-text="`${employee.first_name} ${employee.last_name}`"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredOrders.length === 0">
                            <td colspan="8" class="py-10 text-center text-slate-500">No orders found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>



        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white/95 rounded-[24px] p-5 shadow-xl shadow-blue-950/10 border border-white/70 flex flex-col">
                <div class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider">Recent Jobs</div>
                <div class="mt-4 flex-1 overflow-y-auto max-h-64 space-y-2">
                    @forelse($recentJobs as $job)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium text-slate-900">{{ $job->name }}</div>
                                <div class="mt-0.5 flex items-center gap-2 text-xs text-slate-500">
                                    <span>{{ $job->customer?->first_name }} {{ $job->customer?->last_name }}</span>
                                    @if($job->employee)
                                        <span class="text-slate-300">|</span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">{{ $job->employee?->first_name }} {{ $job->employee?->last_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0 text-right">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[12px] font-medium {{ match($job->status) {
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-600',
                                } }}">{{ str_replace('_', ' ', $job->status) }}</span>
                                <div class="mt-0.5 text-xs text-slate-400">{{ $job->created_at->format('M d') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-slate-500">No jobs yet</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white/95 rounded-[24px] p-5 shadow-xl shadow-blue-950/10 border border-white/70 flex flex-col">
                <div class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider">Recent Customers</div>
                <div class="mt-4 flex-1 overflow-y-auto max-h-64 space-y-2">
                    @forelse($recentCustomers as $customer)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium text-slate-900">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                                <div class="text-xs text-slate-500">{{ $customer->email }}</div>
                            </div>
                            <div class="ml-2 flex-shrink-0 text-xs text-slate-400">
                                {{ $customer->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-slate-500">No customers yet</div>
                    @endforelse
                </div>
            </div>
        </div>


    </div>

    <script>
        function dashboardData() {
            return {
                // State
                earningsPeriod: 'month',
                earningsBreakdown: @js($earningsBreakdown),
                orderFilter: 'all',
                searchQuery: '',
                quotePage: 0,
                quotesPerPage: 3,
                
                // Computed
                get filteredOrders() {
                    let orders = @js($recentJobs);
                    
                    if (this.orderFilter !== 'all') {
                        orders = orders.filter(job => 
                            job.service_type?.toLowerCase() === this.orderFilter.toLowerCase()
                        );
                    }
                    
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        orders = orders.filter(job =>
                            job.name?.toLowerCase().includes(query) ||
                            job.customer?.first_name?.toLowerCase().includes(query) ||
                            job.customer?.last_name?.toLowerCase().includes(query)
                        );
                    }
                    
                    return orders;
                },
                
                get paginatedQuotes() {
                    const quotes = @js($pendingQuotes);
                    const start = this.quotePage * this.quotesPerPage;
                    return quotes.slice(start, start + this.quotesPerPage);
                },
                
                get totalQuotePages() {
                    const quotes = @js($pendingQuotes);
                    return Math.ceil(quotes.length / this.quotesPerPage);
                },
                
                // Methods
                initDashboard() {
                    // Load saved preferences from localStorage
                    const savedPeriod = localStorage.getItem('dashboard_earnings_period');
                    if (savedPeriod) this.earningsPeriod = savedPeriod;
                    
                    const savedFilter = localStorage.getItem('dashboard_order_filter');
                    if (savedFilter) this.orderFilter = savedFilter;
                },
                
                setEarningsPeriod(period) {
                    this.earningsPeriod = period;
                    localStorage.setItem('dashboard_earnings_period', period);
                    fetch('/owner/dashboard/earnings?period=' + period, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.earningsBreakdown = data;
                    })
                    .catch(error => {
                        console.error('Error fetching earnings:', error);
                    });
                },
                
                setOrderFilter(filter) {
                    this.orderFilter = filter;
                    localStorage.setItem('dashboard_order_filter', filter);
                },
                
                nextQuotePage() {
                    if (this.quotePage < this.totalQuotePages - 1) {
                        this.quotePage++;
                    }
                },
                
                prevQuotePage() {
                    if (this.quotePage > 0) {
                        this.quotePage--;
                    }
                },
                
                goToQuotePage(page) {
                    this.quotePage = page;
                },
                
                assignEmployee(jobId, employeeId) {
                    fetch(`/owner/jobs/${jobId}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ employee_id: employeeId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh the page to show updated data
                            window.location.reload();
                        } else {
                            alert('Failed to assign employee');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to assign employee');
                    });
                },

                approveQuote(quoteId) {
                    if (confirm('Are you sure you want to approve this quote?')) {
                        fetch(`/owner/quotes/${quoteId}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Failed to approve quote');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to approve quote');
                        });
                    }
                },

                rejectQuote(quoteId) {
                    if (confirm('Are you sure you want to reject this quote?')) {
                        fetch(`/owner/quotes/${quoteId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Failed to reject quote');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to reject quote');
                        });
                    }
                }
            };
        }
    </script>
</x-layouts::app.owner>
