<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="reportsData()" x-init="initReports()">
        <!-- Total Sales Revenue Report -->
        <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Total Sales Revenue Report</h3>
            <div class="flex items-baseline gap-3">
                <div class="text-[32px] font-bold text-slate-900">₱{{ number_format($totalRevenue ?? 0, 0) }}</div>
                <span class="text-red-500">↓ 23.4% vs last month</span>
            </div>
            <div class="flex gap-5 mt-2 text-sm text-slate-600">
                <div>Orders: <span class="font-semibold text-slate-900">{{ $totalOrders ?? 0 }}</span></div>
                <div>Avg. Order: <span class="font-semibold text-slate-900">₱{{ number_format($averageOrderValue ?? 0, 0) }}</span></div>
                <div>Period: <span class="font-semibold text-slate-900">Jul 2025 – Dec 2025</span></div>
            </div>
            <div class="h-[280px] mt-6">
                <svg viewBox="0 0 800 280" class="w-full h-full">
                    <defs>
                        <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="5%" stop-color="#4a6cf7" stop-opacity="0.2"/>
                            <stop offset="95%" stop-color="#4a6cf7" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <!-- Grid lines -->
                    <line x1="50" y1="240" x2="750" y2="240" stroke="#e2e8f0" stroke-width="1"/>
                    <line x1="50" y1="180" x2="750" y2="180" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    <line x1="50" y1="120" x2="750" y2="120" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    <line x1="50" y1="60" x2="750" y2="60" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    
                    <!-- Area chart -->
                    <path d="M50,240 L50,200 L170,180 L290,150 L410,120 L530,80 L650,100 L750,140 L750,240 Z" fill="url(#revGrad)"/>
                    <path d="M50,200 L170,180 L290,150 L410,120 L530,80 L650,100 L750,140" fill="none" stroke="#4a6cf7" stroke-width="2.5"/>
                    
                    <!-- X-axis labels -->
                    <text x="50" y="260" font-size="12" fill="#94a3b8" text-anchor="middle">Jul</text>
                    <text x="170" y="260" font-size="12" fill="#94a3b8" text-anchor="middle">Aug</text>
                    <text x="290" y="260" font-size="12" fill="#94a3b8" text-anchor="middle">Sep</text>
                    <text x="410" y="260" font-size="12" fill="#94a3b8" text-anchor="middle">Oct</text>
                    <text x="530" y="260" font-size="12" fill="#94a3b8" text-anchor="middle">Nov</text>
                    <text x="650" y="260" font-size="12" fill="#94a3b8" text-anchor="middle">Dec</text>
                </svg>
            </div>
        </div>

        <!-- Sales Report -->
        <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <div class="flex flex-col gap-5 mb-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider">Sales Report</h3>
                    <div class="text-xs text-slate-500 mt-1">
                        Jul 2025 – Dec 2025
                    </div>
                    <div class="flex flex-wrap gap-6 mt-3">
                        <div><span class="text-[22px] font-bold text-slate-900">₱{{ number_format($totalRevenue ?? 0, 0) }}</span><div class="text-xs text-slate-500">Selected Revenue</div></div>
                        <div><span class="text-[22px] font-bold text-slate-900">{{ $totalOrders ?? 0 }}</span><div class="text-xs text-slate-500">Selected Orders</div></div>
                        <div class="rounded-2xl px-4 py-2 bg-red-50 text-red-700">
                            <div class="text-[18px] font-bold">Loss 23.4%</div>
                            <div class="text-xs opacity-80">This month vs prev month · Dec vs Nov</div>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-end gap-0 bg-slate-100 p-1 rounded-xl overflow-hidden">
                        <button @click="reportType = 'monthly'" :class="reportType === 'monthly' ? 'bg-[#356dff] text-white shadow-sm' : 'text-slate-600 hover:bg-white/50'" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors">MONTHLY</button>
                        <button @click="reportType = 'yearly'" :class="reportType === 'yearly' ? 'bg-[#356dff] text-white shadow-sm' : 'text-slate-600 hover:bg-white/50'" class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors">YEARLY</button>
                    </div>
                </div>
            </div>

            <div class="text-[13px] text-slate-600 mb-2 font-medium">Revenue Trend</div>
            <div class="h-[220px]">
                <svg viewBox="0 0 800 220" class="w-full h-full">
                    <!-- Grid lines -->
                    <line x1="50" y1="180" x2="750" y2="180" stroke="#e2e8f0" stroke-width="1"/>
                    <line x1="50" y1="140" x2="750" y2="140" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    <line x1="50" y1="100" x2="750" y2="100" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    <line x1="50" y1="60" x2="750" y2="60" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    
                    <!-- Line chart -->
                    <path d="M50,160 L170,140 L290,110 L410,80 L530,50 L650,70 L750,90" fill="none" stroke="#a855f7" stroke-width="2.5"/>
                    
                    <!-- Dots -->
                    <circle cx="50" cy="160" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    <circle cx="170" cy="140" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    <circle cx="290" cy="110" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    <circle cx="410" cy="80" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    <circle cx="530" cy="50" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    <circle cx="650" cy="70" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    <circle cx="750" cy="90" r="4" fill="#a855f7" stroke="white" stroke-width="2"/>
                    
                    <!-- X-axis labels -->
                    <text x="50" y="200" font-size="12" fill="#94a3b8" text-anchor="middle">Jul</text>
                    <text x="170" y="200" font-size="12" fill="#94a3b8" text-anchor="middle">Aug</text>
                    <text x="290" y="200" font-size="12" fill="#94a3b8" text-anchor="middle">Sep</text>
                    <text x="410" y="200" font-size="12" fill="#94a3b8" text-anchor="middle">Oct</text>
                    <text x="530" y="200" font-size="12" fill="#94a3b8" text-anchor="middle">Nov</text>
                    <text x="650" y="200" font-size="12" fill="#94a3b8" text-anchor="middle">Dec</text>
                </svg>
            </div>
            
            <div class="text-[13px] text-slate-600 mb-2 font-medium mt-6">Orders per Month</div>
            <div class="h-[140px]">
                <svg viewBox="0 0 800 140" class="w-full h-full">
                    <!-- Grid lines -->
                    <line x1="50" y1="100" x2="750" y2="100" stroke="#e2e8f0" stroke-width="1"/>
                    <line x1="50" y1="70" x2="750" y2="70" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    <line x1="50" y1="40" x2="750" y2="40" stroke="#e2e8f0" stroke-width="1" stroke-dasharray="3 3"/>
                    
                    <!-- Bar chart -->
                    <rect x="70" y="60" width="40" height="40" fill="#f97316" rx="6"/>
                    <rect x="190" y="50" width="40" height="50" fill="#f97316" rx="6"/>
                    <rect x="310" y="40" width="40" height="60" fill="#f97316" rx="6"/>
                    <rect x="430" y="30" width="40" height="70" fill="#f97316" rx="6"/>
                    <rect x="550" y="20" width="40" height="80" fill="#f97316" rx="6"/>
                    <rect x="670" y="35" width="40" height="65" fill="#f97316" rx="6"/>
                    
                    <!-- X-axis labels -->
                    <text x="90" y="120" font-size="12" fill="#94a3b8" text-anchor="middle">Jul</text>
                    <text x="210" y="120" font-size="12" fill="#94a3b8" text-anchor="middle">Aug</text>
                    <text x="330" y="120" font-size="12" fill="#94a3b8" text-anchor="middle">Sep</text>
                    <text x="450" y="120" font-size="12" fill="#94a3b8" text-anchor="middle">Oct</text>
                    <text x="570" y="120" font-size="12" fill="#94a3b8" text-anchor="middle">Nov</text>
                    <text x="690" y="120" font-size="12" fill="#94a3b8" text-anchor="middle">Dec</text>
                </svg>
            </div>
        </div>

        <!-- Top Products Report -->
        <div class="bg-white/95 rounded-[24px] sm:rounded-[28px] p-5 sm:p-7 shadow-xl shadow-blue-950/10 border border-white/70 mb-6">
            <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-4">Top Products Report <span class="text-slate-400 font-normal normal-case">(Top 10)</span></h3>
            <div class="grid lg:grid-cols-2 gap-8">
                <div class="flex justify-center">
                    <div class="relative w-[280px] h-[280px] rounded-[32px] bg-[#f5ede3] p-4 shadow-inner">
                        <svg viewBox="0 0 280 280" class="w-full h-full">
                            <!-- Pie chart segments -->
                            <circle cx="140" cy="140" r="118" fill="none" stroke="#3b82f6" stroke-width="40" stroke-dasharray="26 740" stroke-dashoffset="0" transform="rotate(90 140 140)"/>
                            <circle cx="140" cy="140" r="118" fill="none" stroke="#a855f7" stroke-width="40" stroke-dasharray="23 743" stroke-dashoffset="-26" transform="rotate(90 140 140)"/>
                            <circle cx="140" cy="140" r="118" fill="none" stroke="#f97316" stroke-width="40" stroke-dasharray="17 749" stroke-dashoffset="-49" transform="rotate(90 140 140)"/>
                            <circle cx="140" cy="140" r="118" fill="none" stroke="#10b981" stroke-width="40" stroke-dasharray="17 749" stroke-dashoffset="-66" transform="rotate(90 140 140)"/>
                            <circle cx="140" cy="140" r="118" fill="none" stroke="#ec4899" stroke-width="40" stroke-dasharray="13 753" stroke-dashoffset="-83" transform="rotate(90 140 140)"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <div class="text-3xl font-bold text-slate-900">10</div>
                            <div class="text-xs text-slate-500">Top Products</div>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="w-32 text-xs text-slate-700 truncate text-right">Custom T-Shirt (M)</div>
                        <div class="flex-1 h-5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-blue-500" style="width: 26%"/>
                        </div>
                        <div class="text-xs w-12 text-right font-medium">26%</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-32 text-xs text-slate-700 truncate text-right">Custom T-Shirt (S)</div>
                        <div class="flex-1 h-5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-purple-500" style="width: 23%"/>
                        </div>
                        <div class="text-xs w-12 text-right font-medium">23%</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-32 text-xs text-slate-700 truncate text-right">Custom T-Shirt (L)</div>
                        <div class="flex-1 h-5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-orange-500" style="width: 17%"/>
                        </div>
                        <div class="text-xs w-12 text-right font-medium">17%</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-32 text-xs text-slate-700 truncate text-right">Tarpaulin Print</div>
                        <div class="flex-1 h-5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-green-500" style="width: 17%"/>
                        </div>
                        <div class="text-xs w-12 text-right font-medium">17%</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-32 text-xs text-slate-700 truncate text-right">Custom Lanyard</div>
                        <div class="flex-1 h-5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-pink-500" style="width: 13%"/>
                        </div>
                        <div class="text-xs w-12 text-right font-medium">13%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotes List -->
        <div class="bg-white/95 rounded-[24px] p-5 shadow-xl shadow-blue-950/10 border border-white/70">
            <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-4">Recent Quotes</h3>
            @if($quotes->isEmpty())
                <div class="text-center py-12 text-slate-500">No quotes yet</div>
            @else
                <div class="space-y-3">
                    @foreach($quotes->take(10) as $quote)
                        <div class="bg-slate-50 rounded-xl p-4 hover:bg-slate-100 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-semibold text-slate-900">{{ $quote->quote_number }}</h3>
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-slate-200 text-slate-700',
                                                'sent' => 'bg-blue-100 text-blue-700',
                                                'accepted' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$quote->status] ?? 'bg-slate-200 text-slate-700' }}">
                                            {{ str_replace('_', ' ', ucfirst($quote->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600">
                                        Customer: {{ $quote->customer->first_name ?? 'N/A' }} {{ $quote->customer->last_name ?? '' }}
                                        @if($quote->serviceJob)
                                            | Service: {{ $quote->serviceJob->name }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-4 text-xs text-slate-500 mt-1">
                                        <span>Date: {{ $quote->date->format('M d, Y') }}</span>
                                        <span>Total: ₱{{ number_format($quote->total, 0) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('owner.quotes.edit', $quote) }}" class="ml-4 px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function reportsData() {
            return {
                reportType: 'monthly',
                
                initReports() {
                    // Initialize any report-specific logic
                }
            };
        }
    </script>
</x-layouts::app.owner>
