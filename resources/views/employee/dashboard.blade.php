@extends('layouts.app.employee')

@section('content')
<div class="flex-1 flex flex-col" x-data="clock()" x-init="startClock()">
    <div class="px-0 sm:px-6 lg:px-8 pt-14 lg:pt-8 pb-4 flex-1 overflow-y-auto">
        {{-- Banner --}}
        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-xl sm:rounded-3xl p-4 sm:p-8 text-white relative overflow-hidden mb-5 sm:mb-8">
            <div class="welcome-dots"></div>
            <div class="relative z-10 flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg sm:text-3xl lg:text-4xl font-bold truncate">Hey, {{ auth()->user()->first_name }}!</h1>
                    <p class="text-orange-100 text-xs sm:text-base mt-0.5 sm:mt-1">Here&rsquo;s your task overview</p>
                </div>
                <div class="text-right hidden sm:block flex-shrink-0">
                    <div class="text-2xl sm:text-3xl font-bold" x-text="currentTime"></div>
                    <div class="text-orange-100 text-xs sm:text-sm" x-text="currentDate"></div>
                </div>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 sm:gap-4 mb-5 sm:mb-8">
            <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-3.5 sm:p-5 rounded-xl sm:rounded-3xl shadow-sm transition-all cursor-pointer active:scale-[0.98] hover:shadow-md">
                <div class="bg-emerald-50 w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-4 text-emerald-600">
                    <svg class="w-[18px] h-[18px] sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="text-xl sm:text-3xl font-bold text-emerald-600">{{ $totalJobs }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500 mt-px">Total Jobs</div>
            </a>

            <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-3.5 sm:p-5 rounded-xl sm:rounded-3xl shadow-sm transition-all cursor-pointer active:scale-[0.98] hover:shadow-md">
                <div class="bg-orange-50 w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-4 text-orange-600">
                    <svg class="w-[18px] h-[18px] sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900">{{ $inProgressJobs }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500 mt-px">In Progress</div>
            </a>

            <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-3.5 sm:p-5 rounded-xl sm:rounded-3xl shadow-sm transition-all cursor-pointer active:scale-[0.98] hover:shadow-md">
                <div class="bg-amber-50 w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-4 text-amber-600">
                    <svg class="w-[18px] h-[18px] sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900">{{ $pendingJobs }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500 mt-px">Pending</div>
            </a>

            <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-3.5 sm:p-5 rounded-xl sm:rounded-3xl shadow-sm transition-all cursor-pointer active:scale-[0.98] hover:shadow-md">
                <div class="bg-emerald-50 w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-4 text-emerald-600">
                    <svg class="w-[18px] h-[18px] sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xl sm:text-3xl font-bold text-slate-900">{{ $completedJobs }}</div>
                <div class="text-[11px] sm:text-xs text-slate-500 mt-px">Completed</div>
            </a>
        </div>

        {{-- Section heading --}}
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <h2 class="font-bold text-base sm:text-xl text-slate-900">Recent Jobs</h2>
            @if ($recentJobs->count() > 0)
                <a href="{{ route('employee.jobs') }}" class="text-xs sm:text-sm font-medium text-orange-500 hover:text-orange-600 transition-colors">View all</a>
            @endif
        </div>

        @if ($recentJobs->count() > 0)
            {{-- Mobile card list --}}
            <div class="sm:hidden space-y-2">
                @foreach ($recentJobs as $job)
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        $accentColors = [
                            'pending' => 'border-l-amber-400',
                            'in_progress' => 'border-l-blue-400',
                            'completed' => 'border-l-emerald-400',
                            'cancelled' => 'border-l-red-400',
                        ];
                    @endphp
                    <a href="{{ route('employee.jobs.show', $job) }}" class="block bg-white border border-slate-100 border-l-4 {{ $accentColors[$job->status] ?? 'border-l-slate-400' }} rounded-r-xl py-3 px-3.5 shadow-sm active:scale-[0.99] transition-all cursor-pointer">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-900 text-sm truncate">{{ $job->customer->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $job->name }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold leading-tight {{ $statusColors[$job->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Desktop table --}}
            <div class="hidden sm:block bg-white border border-slate-100 rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left py-3 sm:py-4 px-4 sm:px-6 text-xs font-semibold text-slate-400 tracking-wider">CUSTOMER</th>
                                <th class="text-left py-3 sm:py-4 px-4 sm:px-6 text-xs font-semibold text-slate-400 tracking-wider">JOB TYPE</th>
                                <th class="text-left py-3 sm:py-4 px-4 sm:px-6 text-xs font-semibold text-slate-400 tracking-wider">STATUS</th>
                                <th class="text-left py-3 sm:py-4 px-4 sm:px-6 text-xs font-semibold text-slate-400 tracking-wider">DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentJobs as $job)
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-emerald-100 text-emerald-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <tr class="border-b border-slate-50 last:border-none hover:bg-slate-50/50 transition-colors group">
                                    <td class="py-3 sm:py-4 px-4 sm:px-6 font-semibold text-slate-900 text-sm sm:text-base">{{ $job->customer->name ?? 'N/A' }}</td>
                                    <td class="py-3 sm:py-4 px-4 sm:px-6 text-slate-600 text-sm">{{ $job->name }}</td>
                                    <td class="py-3 sm:py-4 px-4 sm:px-6">
                                        <span class="inline-block px-3 sm:px-4 py-1 rounded-full text-xs font-bold {{ $statusColors[$job->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 sm:py-4 px-4 sm:px-6 text-slate-500 text-xs sm:text-sm">{{ $job->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white border border-slate-100 rounded-xl sm:rounded-3xl p-8 sm:p-12 text-center shadow-sm">
                <div class="text-slate-300 text-3xl sm:text-4xl mb-3">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <p class="text-slate-500 font-medium text-sm">No jobs assigned yet.</p>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="border-t border-slate-100 px-4 sm:px-8 py-2.5 sm:py-4 flex items-center justify-between text-[11px] sm:text-xs text-slate-400">
        <div>Showing {{ min($recentJobs->count(), 5) }} of {{ $totalJobs }} jobs</div>
        <div>Updated {{ now()->diffForHumans() }}</div>
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
@endsection
