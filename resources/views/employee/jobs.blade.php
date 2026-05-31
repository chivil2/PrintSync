@extends('layouts.app.employee')

@section('content')
<div class="p-4 flex-1 flex flex-col">
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="px-8 pt-8 pb-4 flex-1 overflow-y-auto">
            <div class="flex items-center justify-between mb-6 px-1">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Service Jobs</h1>
                    <p class="mt-1 text-slate-500 text-sm">View and manage your assigned service jobs</p>
                </div>
            </div>

            @if ($jobs->isEmpty())
                <div class="bg-slate-100 p-16 text-center">
                    <div class="text-slate-400 text-5xl mb-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No jobs assigned yet</h3>
                    <p class="text-slate-500">Jobs assigned to you will appear here</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($jobs as $job)
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-400',
                                'in_progress' => 'bg-sky-400',
                                'completed' => 'bg-emerald-400',
                                'cancelled' => 'bg-red-400',
                            ];
                        @endphp
                        <a href="{{ route('employee.jobs.show', $job) }}" class="block {{ $statusColors[$job->status] ?? 'bg-slate-400' }} p-4 hover:opacity-90 transition-opacity cursor-pointer">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-bold text-white">{{ $job->name }}</h3>
                                        <span class="inline-block px-3 py-1 rounded text-xs font-bold bg-white/20 text-white">
                                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-6 mt-2 text-sm text-white/90">
                                        <span>{{ ucfirst($job->type) }}</span>
                                        <span>{{ $job->customer->name ?? 'N/A' }}</span>
                                        <span>{{ $job->created_at->format('M d, Y') }}</span>
                                        @if ($job->deadline)
                                            <span>Due: {{ $job->deadline->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-white text-sm font-medium">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
