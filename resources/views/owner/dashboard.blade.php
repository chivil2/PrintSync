<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="clock()" x-init="startClock()">
        <!-- Banner -->
        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
            <div class="welcome-dots"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Welcome back!</h1>
                    <p class="text-orange-100">Manage your printing business from your dashboard.</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold" x-text="currentTime"></div>
                    <div class="text-orange-100 text-sm" x-text="currentDate"></div>
                </div>
            </div>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-4 gap-4">
            <a href="{{ route('owner.quotes') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-900">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Total Revenue</div>
            </a>

            <a href="{{ route('owner.jobs') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                <div class="bg-orange-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-900">{{ $totalOrders }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Total Orders</div>
            </a>

            <a href="{{ route('owner.employees') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                <div class="bg-amber-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-900">{{ $activeCustomers }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Active Customers</div>
            </a>

            <a href="{{ route('owner.jobs') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                <div class="bg-emerald-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-slate-900">{{ $completedJobs }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Completed Jobs</div>
            </a>
        </div>

        <!-- Placeholder Cards -->
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <a href="{{ route('owner.employees') }}" class="text-xs text-purple-600 hover:text-purple-700 font-medium">View All</a>
                </div>
                <div class="text-3xl font-bold text-slate-900">{{ $employees->count() }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Active Employees</div>
                <div class="mt-4 space-y-2">
                    @forelse($employees as $employee)
                        <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                            @if($employee->profile_photo_path)
                                <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" alt="{{ $employee->first_name }} {{ $employee->last_name }}" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold text-sm">
                                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 truncate">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $employee->specialization ?? 'Employee' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500 text-center py-4">No employees yet</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-pink-50 w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-pink-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <a href="{{ route('owner.jobs') }}" class="text-xs text-pink-600 hover:text-pink-700 font-medium">View All</a>
                </div>
                <div class="text-3xl font-bold text-slate-900">{{ $jobs->total() }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Total Jobs</div>
                <div class="mt-4 space-y-2">
                    @forelse($jobs as $job)
                        <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-semibold text-sm">
                                {{ strtoupper(substr($job->customer->first_name ?? 'C', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-900 truncate">{{ $job->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $job->customer->first_name ?? 'Unknown' }} {{ $job->customer->last_name ?? '' }}</div>
                                <div class="text-xs text-slate-400">{{ $job->service->name ?? 'Unknown Service' }} • {{ $job->created_at->format('M j, Y') }}</div>
                            </div>
                            <div class="text-xs px-2 py-1 rounded-full
                                @if($job->status === 'completed') bg-emerald-100 text-emerald-700
                                @elseif($job->status === 'in_progress') bg-blue-100 text-blue-700
                                @elseif($job->status === 'pending') bg-amber-100 text-amber-700
                                @else bg-slate-100 text-slate-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500 text-center py-4">No jobs yet</div>
                    @endforelse
                </div>
                @if($jobs->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $jobs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    function clock() {
        return {
            currentTime: '',
            currentDate: '',

            startClock() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
            },

            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                this.currentDate = now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        }
    }
    </script>
</x-layouts::app.owner>
