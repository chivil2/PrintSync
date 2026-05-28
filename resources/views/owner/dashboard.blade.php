<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Dashboard</h1>
                    <p class="text-white/80">Hello there, {{ ucfirst(auth()->user()->first_name) }} {{ ucfirst(auth()->user()->last_name) }}</p>
                </div>
                <div class="text-right" x-data="{ time: '', date: '' }" x-init="time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); date = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }); setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }, 1000)">
                    <div class="text-white text-4xl font-mono font-bold" x-text="time"></div>
                    <div class="text-white/70 text-xs font-mono" x-text="date"></div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-sm font-medium mb-1">From Completed Orders</p>
                        <p class="text-3xl font-bold">₱{{ number_format($completedJobRevenue, 0) }}</p>
                        <p class="text-green-100 text-xs mt-2">Total Revenue</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 ml-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Orders Fulfilled</p>
                        <p class="text-3xl font-bold">{{ $completedJobs }}</p>
                        <p class="text-blue-100 text-xs mt-2">Completed Jobs</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 ml-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-purple-100 text-sm font-medium mb-1">Per Transaction</p>
                        <p class="text-3xl font-bold">₱{{ number_format($averageOrderValue, 0) }}</p>
                        <p class="text-purple-100 text-xs mt-2">Average Order Value</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 ml-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-orange-100 text-sm font-medium mb-1">Single Order Record</p>
                        <p class="text-3xl font-bold">₱{{ number_format($highestOrderValue, 0) }}</p>
                        <p class="text-orange-100 text-xs mt-2">Highest Order</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 ml-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Overview -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Operational Overview</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Job Status Pie Chart -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-4">Order Status</div>
                    <div class="flex items-center justify-center">
                        <div class="relative w-40 h-40">
                            @php
                                $totalJobs = array_sum($jobsByStatus->toArray());
                                $colors = ['pending' => '#fbbf24', 'in_progress' => '#3b82f6', 'completed' => '#22c55e', 'cancelled' => '#ef4444'];
                                $offset = 0;
                            @endphp
                            <svg viewBox="0 0 36 36" class="w-full h-full">
                                @foreach($jobsByStatus as $status => $count)
                                    @if($totalJobs > 0)
                                        @php
                                            $percentage = ($count / $totalJobs) * 100;
                                            $dashArray = $percentage * 0.359;
                                        @endphp
                                        <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="{{ $colors[$status] ?? '#94a3b8' }}" stroke-width="3" stroke-dasharray="{{ $dashArray }} 100" stroke-dashoffset="{{ $offset * -1 }}"></circle>
                                        @php
                                            $offset += $percentage;
                                        @endphp
                                    @endif
                                @endforeach
                            </svg>
                            @if($totalJobs > 0)
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-zinc-900">{{ $totalJobs }}</span>
                                </div>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-sm text-zinc-400">No jobs</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $status => $label)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $colors[$status] ?? '#94a3b8' }}"></div>
                                    <span class="text-zinc-600">{{ $label }}</span>
                                </div>
                                <span class="font-medium text-zinc-900">{{ $jobsByStatus[$status] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Employee Workload -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-4">Employee Workload</div>
                    <div class="space-y-3">
                        @forelse($employees as $employee)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-teal-400 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ $employee->initials() }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                        <p class="text-xs text-zinc-500">{{ $employee->specializationLabel() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-blue-600">{{ $employee->assigned_jobs_count }}</span>
                                    <p class="text-xs text-zinc-500">Active</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-zinc-500 py-4">No active employees</div>
                        @endforelse
                    </div>
                </div>

                <!-- Service Type Breakdown -->
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-4">Service Types</div>
                    <div class="space-y-3">
                        @foreach($serviceTypes as $type => $count)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm text-zinc-600">{{ ucfirst($type) }}</span>
                                    <span class="text-sm font-medium text-zinc-900">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-zinc-200 rounded-full h-2">
                                    @php
                                        $totalServiceJobs = $serviceTypes->sum();
                                        $percentage = $totalServiceJobs > 0 ? ($count / $totalServiceJobs) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        @if($serviceTypes->isEmpty())
                            <div class="text-center text-sm text-zinc-500 py-4">No service data</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm flex flex-col">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Recent Jobs</div>
                <div class="mt-4 flex-1 overflow-y-auto max-h-64 space-y-2">
                    @forelse($recentJobs as $job)
                        <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium text-zinc-800">{{ $job->name }}</div>
                                <div class="mt-0.5 flex items-center gap-2 text-xs text-zinc-400">
                                    <span>{{ $job->customer?->first_name }} {{ $job->customer?->last_name }}</span>
                                    @if($job->employee)
                                        <span class="text-zinc-300">|</span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700">{{ $job->employee?->first_name }} {{ $job->employee?->last_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0 text-right">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ match($job->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-zinc-100 text-zinc-600',
                                } }}">{{ str_replace('_', ' ', $job->status) }}</span>
                                <div class="mt-0.5 text-xs text-zinc-400">{{ $job->created_at->format('M d') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-zinc-400">No jobs yet</div>
                    @endforelse
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm flex flex-col">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Recent Customers</div>
                <div class="mt-4 flex-1 overflow-y-auto max-h-64 space-y-2">
                    @forelse($recentCustomers as $customer)
                        <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium text-zinc-800">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                                <div class="text-xs text-zinc-400">{{ $customer->email }}</div>
                            </div>
                            <div class="ml-2 flex-shrink-0 text-xs text-zinc-400">
                                {{ $customer->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-zinc-400">No customers yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-4">Quick Actions</div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('owner.jobs') }}" class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        View All Jobs
                    </a>
                    <a href="{{ route('owner.quotes') }}" class="inline-flex items-center px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View All Quotes
                    </a>
                    <a href="{{ route('owner.employees') }}" class="inline-flex items-center px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Manage Employees
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.owner>
