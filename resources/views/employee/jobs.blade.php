@extends('layouts.app.employee')

@section('content')
<div class="p-4 flex-1 flex flex-col">
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/70 border border-slate-100 flex-1 flex flex-col overflow-hidden">
        <div class="px-8 pt-8 pb-4 flex-1 overflow-y-auto">
            <div class="flex items-center justify-between mb-6 px-1">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Service Jobs</h1>
                    <p class="mt-1 text-slate-500 text-sm">View and manage your assigned service jobs</p>
                </div>
            </div>

            @if ($jobs->isEmpty())
                <div class="bg-white border border-slate-100 rounded-3xl p-16 text-center">
                    <div class="text-slate-400 text-5xl mb-4">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No jobs assigned yet</h3>
                    <p class="text-slate-500">Jobs assigned to you will appear here</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($jobs as $job)
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <a href="{{ route('employee.jobs.show', $job) }}" class="block bg-white border border-slate-100 rounded-3xl p-6 hover:shadow-md hover:border-slate-200 transition-all cursor-pointer group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-xl font-bold text-slate-900">{{ $job->name }}</h3>
                                        <span class="inline-block px-4 py-1 rounded-full text-xs font-bold {{ $statusColors[$job->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                        </span>
                                        @if ($job->quote && $job->quote->status === 'accepted')
                                            <span class="inline-block px-4 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                                Quote Approved
                                            </span>
                                        @endif
                                    </div>

                                    @if ($job->description)
                                        <p class="text-slate-600 text-sm mb-3 line-clamp-2">{{ $job->description }}</p>
                                    @endif

                                    <div class="grid grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-slate-400 text-xs">Service Type</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">{{ ucfirst($job->type) }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 text-xs">Customer</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">{{ $job->customer->name ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 text-xs">Requested</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">{{ $job->created_at->format('M d, Y') }}</p>
                                        </div>
                                        @if ($job->deadline)
                                            <div>
                                                <span class="text-slate-400 text-xs">Deadline</span>
                                                <p class="font-semibold text-slate-900 mt-0.5">{{ $job->deadline->format('M d, Y') }}</p>
                                            </div>
                                        @else
                                            <div></div>
                                        @endif
                                    </div>

                                    @if ($job->notes)
                                        <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                            <span class="text-xs text-slate-400 font-medium">Notes</span>
                                            <p class="text-sm text-slate-700 mt-1">{{ $job->notes }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="ml-6 flex flex-col items-center gap-3 flex-shrink-0">
                                    <div class="w-14 h-14 {{ $job->type === 'printing' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }} rounded-2xl flex items-center justify-center text-2xl shadow-sm">
                                        @if ($job->type === 'printing')
                                            <i class="fa-solid fa-print"></i>
                                        @else
                                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                                        @endif
                                    </div>
                                    <div class="text-orange-600 opacity-0 group-hover:opacity-100 text-sm font-medium transition-opacity flex items-center gap-1">
                                        View Details <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </div>
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
