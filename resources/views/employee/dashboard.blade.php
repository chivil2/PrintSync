@extends('layouts.app.employee')

@section('rightPanel')
    <x-employee-right-panel />
@endsection

@section('content')
<div class="p-4 flex-1 flex flex-col" x-data="clock()" x-init="startClock()">
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/70 border border-slate-100 flex-1 flex flex-col overflow-hidden">
        <div class="px-8 pt-8 pb-4 flex-1 overflow-y-auto">
            <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8">
                <div class="welcome-dots"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold mb-2">Good {{ now()->format('A') === 'AM' ? 'Morning' : 'Afternoon' }}, {{ auth()->user()->first_name }}!</h1>
                        <p class="text-orange-100">Manage your tasks from your dashboard.</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold" x-text="currentTime"></div>
                        <div class="text-orange-100 text-sm" x-text="currentDate"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-8">
                <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                    <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-slate-900">-</div>
                    <div class="text-xs text-slate-500 mt-0.5">Total Jobs</div>
                </a>

                <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                    <div class="bg-orange-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-slate-900">-</div>
                    <div class="text-xs text-slate-500 mt-0.5">In Progress</div>
                </a>

                <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                    <div class="bg-amber-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-slate-900">-</div>
                    <div class="text-xs text-slate-500 mt-0.5">Pending</div>
                </a>

                <a href="{{ route('employee.jobs') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
                    <div class="bg-emerald-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-slate-900">-</div>
                    <div class="text-xs text-slate-500 mt-0.5">Completed</div>
                </a>
            </div>

            <h2 class="font-bold text-xl text-slate-900 mb-4 px-1">Recent Jobs</h2>

            @if ($recentJobs->count() > 0)
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 tracking-wider">CUSTOMER</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 tracking-wider">JOB TYPE</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 tracking-wider">STATUS</th>
                                <th class="text-left py-4 px-6 text-xs font-semibold text-slate-400 tracking-wider">DATE</th>
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
                                    <td class="py-4 px-6 font-semibold text-slate-900">{{ $job->customer->name ?? 'N/A' }}</td>
                                    <td class="py-4 px-6 text-slate-600">{{ $job->name }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block px-4 py-1 rounded-full text-xs font-bold {{ $statusColors[$job->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 text-sm">{{ $job->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center">
                    <div class="text-slate-400 text-4xl mb-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <p class="text-slate-500 font-medium">No jobs assigned yet.</p>
                </div>
            @endif
        </div>

        <div class="mt-auto border-t border-slate-100 px-8 py-4 flex items-center justify-between text-xs text-slate-400">
            <div>Showing {{ min($recentJobs->count(), 5) }} of {{ $totalJobs }} jobs</div>
            <div>Last updated: {{ now()->diffForHumans() }}</div>
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
@endsection
