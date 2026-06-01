@extends('layouts.app.employee')

@section('content')
<div class="p-4 flex-1 flex flex-col">
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="px-6 lg:px-8 py-8 flex-1 overflow-y-auto">
            <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
                <div class="welcome-dots"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-2">Service Jobs</h1>
                        <p class="text-orange-100 text-base">View and manage your assigned service jobs</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ $jobs->count() }}</div>
                        <div class="text-orange-100 text-sm">Total Jobs</div>
                    </div>
                </div>
            </div>

            @php
                $totalJobs = $jobs->count();
                $inProgressJobs = $jobs->where('status', 'in_progress')->count();
                $pendingJobs = $jobs->where('status', 'pending')->count();
                $completedJobs = $jobs->where('status', 'completed')->count();
            @endphp

            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-slate-100 p-4 rounded-2xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-sky-50 w-10 h-10 rounded-xl flex items-center justify-center text-sky-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $totalJobs }}</div>
                            <div class="text-xs text-slate-500">Total Jobs</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 p-4 rounded-2xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-orange-50 w-10 h-10 rounded-xl flex items-center justify-center text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $inProgressJobs }}</div>
                            <div class="text-xs text-slate-500">In Progress</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 p-4 rounded-2xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-amber-50 w-10 h-10 rounded-xl flex items-center justify-center text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $pendingJobs }}</div>
                            <div class="text-xs text-slate-500">Pending</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-slate-100 p-4 rounded-2xl card-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-emerald-50 w-10 h-10 rounded-xl flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900">{{ $completedJobs }}</div>
                            <div class="text-xs text-slate-500">Completed</div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($jobs->isEmpty())
                <div class="bg-white border border-slate-100 rounded-3xl p-16 text-center card-shadow">
                    <div class="text-slate-400 text-5xl mb-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No jobs assigned yet</h3>
                    <p class="text-base text-slate-500">Jobs assigned to you will appear here</p>
                </div>
            @else
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden card-shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50">
                                    <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">Job</th>
                                    <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer</th>
                                    <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">Type</th>
                                    <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">Priority</th>
                                    <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                                    <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">Deadline</th>
                                    <th class="text-right py-4 px-6 text-xs font-semibold text-slate-400 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jobs as $job)
                                    @php
                                        $priorityColors = [
                                            'low' => 'bg-slate-100 text-slate-600',
                                            'medium' => 'bg-blue-100 text-blue-700',
                                            'high' => 'bg-orange-100 text-orange-700',
                                            'urgent' => 'bg-red-100 text-red-700',
                                        ];
                                        $statusBadgeColors = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'in_progress' => 'bg-blue-100 text-blue-700',
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                        ];
                                        $statusDotColors = [
                                            'pending' => 'bg-amber-400',
                                            'in_progress' => 'bg-blue-400',
                                            'completed' => 'bg-emerald-400',
                                            'cancelled' => 'bg-red-400',
                                        ];
                                    @endphp
                                    <tr class="border-b border-slate-50 last:border-none hover:bg-slate-50/50 transition-colors group">
                                        <td class="py-4 px-6">
                                            <a href="{{ route('employee.jobs.show', $job) }}" class="flex items-center gap-3 group/link">
                                                <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $statusDotColors[$job->status] ?? 'bg-slate-400' }}"></div>
                                                <div>
                                                    <div class="text-base font-semibold text-slate-900 group-hover/link:text-orange-600 transition-colors">{{ $job->name }}</div>
                                                    <div class="text-xs text-slate-400">{{ $job->created_at->format('M d, Y') }}</div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-sm font-medium text-slate-900">{{ $job->customer->name ?? 'N/A' }}</div>
                                            @if ($job->customer->email)
                                                <div class="text-xs text-slate-400">{{ $job->customer->email }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-sm text-slate-700 capitalize">{{ $job->type }}</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$job->priority ?? 'medium'] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ ucfirst($job->priority ?? 'medium') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $statusBadgeColors[$job->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            @if ($job->deadline)
                                                <div class="text-sm text-slate-700">{{ $job->deadline->format('M d, Y') }}</div>
                                                @php
                                                    $daysLeft = now()->diffInDays($job->deadline, false);
                                                @endphp
                                                @if ($daysLeft >= 0 && $daysLeft <= 3 && $job->status !== 'completed')
                                                    <div class="text-xs {{ $daysLeft <= 1 ? 'text-red-500 font-semibold' : 'text-orange-500' }}">
                                                        @if ($daysLeft == 0)
                                                            Due today
                                                        @elseif ($daysLeft == 1)
                                                            1 day left
                                                        @else
                                                            {{ $daysLeft }} days left
                                                        @endif
                                                    </div>
                                                @elseif ($daysLeft < 0 && $job->status !== 'completed')
                                                    <div class="text-xs text-red-500 font-semibold">{{ abs($daysLeft) }} days overdue</div>
                                                @endif
                                            @else
                                                <span class="text-sm text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="{{ route('employee.jobs.show', $job) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 text-xs text-slate-400 px-1">
                    Showing all {{ $totalJobs }} job{{ $totalJobs !== 1 ? 's' : '' }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
