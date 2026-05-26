@extends('layouts.app.employee')

@section('content')
<div class="space-y-6">
    <div class="welcome-banner">
        <div class="welcome-bg"></div>
        <div class="welcome-dots"></div>
        <div class="welcome-glow1"></div>
        <div class="welcome-glow2"></div>
        <div class="welcome-content">
            <div class="flex items-center justify-between">
                <h1 style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
                    Welcome back, {{ auth()->user()->first_name }}!
                </h1>
                <div class="text-right">
                    <span class="text-2xl font-semibold">{{ now()->format('g:i A') }}</span>
                </div>
            </div>
            <p>Manage your assigned quotes and service jobs.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">Total Jobs</div>
            <div class="mt-2 text-3xl font-bold text-blue-600">{{ $totalJobs }}</div>
        </div>
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">In Progress</div>
            <div class="mt-2 text-3xl font-bold text-orange-600">{{ $inProgressJobs }}</div>
        </div>
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">Pending</div>
            <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $pendingJobs }}</div>
        </div>
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">Completed</div>
            <div class="mt-2 text-3xl font-bold text-green-600">{{ $completedJobs }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-zinc-200">
        <div class="p-6 border-b border-zinc-200">
            <h2 class="text-xl font-semibold text-zinc-900">Recent Jobs</h2>
        </div>
        @if($recentJobs->count() > 0)
            <div class="divide-y divide-zinc-200">
                @foreach($recentJobs as $job)
                    <div class="p-6 hover:bg-zinc-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-zinc-900">{{ $job->name }}</h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$job->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                    </span>
                                </div>
                                <p class="text-sm text-zinc-600 mb-2">{{ $job->description }}</p>
                                <div class="flex items-center gap-4 text-sm text-zinc-500">
                                    <span>Customer: {{ $job->customer->name ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <a href="{{ route('employee.jobs') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-zinc-500">No jobs assigned yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
