@extends('layouts.app.employee')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Job Details</h1>
            <p class="mt-2 text-zinc-600">{{ $job->name }}</p>
        </div>
        <a href="{{ route('employee.jobs') }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Jobs
        </a>
    </div>

    <div class="bg-white rounded-lg border border-zinc-200">
        <div class="p-6 border-b border-zinc-200">
            <div class="flex items-center gap-3 mb-4">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$job->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                    {{ str_replace('_', ' ', ucfirst($job->status)) }}
                </span>
                <span class="text-sm text-zinc-500">
                    Job #{{ $job->id }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Service Information</h2>
            <div class="bg-zinc-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-zinc-500 text-sm">Service:</span>
                        <p class="text-zinc-900 font-medium">{{ $job->name }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-500 text-sm">Type:</span>
                        <p class="text-zinc-900 font-medium">{{ ucfirst($job->type) }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-500 text-sm">Requested:</span>
                        <p class="text-zinc-900 font-medium">{{ $job->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($job->deadline)
                        <div>
                            <span class="text-zinc-500 text-sm">Deadline:</span>
                            <p class="text-zinc-900 font-medium">{{ $job->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
                @if($job->description)
                    <div class="mt-4">
                        <span class="text-zinc-500 text-sm">Description:</span>
                        <p class="text-zinc-900 mt-1">{{ $job->description }}</p>
                    </div>
                @endif
                @if($job->notes)
                    <div class="mt-4">
                        <span class="text-zinc-500 text-sm">Notes:</span>
                        <p class="text-zinc-900 mt-1">{{ $job->notes }}</p>
                    </div>
                @endif
                <div class="mt-4">
                    <span class="text-zinc-500 text-sm">Customer:</span>
                    <p class="text-zinc-900 font-medium">{{ $job->customer->name ?? 'N/A' }}</p>
                    @if($job->customer->email)
                        <p class="text-sm text-zinc-600">{{ $job->customer->email }}</p>
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold text-zinc-900 mb-4">Update Status</h2>
                <form method="POST" action="{{ route('employee.jobs.update', $job) }}">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center gap-4">
                        <select name="status" class="block w-64 text-sm font-medium rounded-lg p-2.5 border border-zinc-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending" {{ $job->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $job->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $job->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>

            @if($job->quote)
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Quote / Receipt</h2>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    @php
                                        $quoteStatusColors = [
                                            'draft' => 'bg-zinc-100 text-zinc-800',
                                            'sent' => 'bg-blue-100 text-blue-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $quoteStatusLabels = [
                                            'draft' => 'Pending Review',
                                            'sent' => 'Awaiting Approval',
                                            'accepted' => 'Approved',
                                            'rejected' => 'Rejected',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quoteStatusColors[$job->quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ $quoteStatusLabels[$job->quote->status] ?? ucfirst($job->quote->status) }}
                                    </span>
                                    <span class="text-sm text-zinc-600">{{ $job->quote->quote_number }}</span>
                                </div>
                                <p class="text-sm text-zinc-700 mb-2">
                                    Total: <span class="font-semibold text-zinc-900">₱{{ number_format($job->quote->total, 2) }}</span>
                                </p>
                                @if($job->quote->status === 'draft')
                                    <p class="text-xs text-zinc-600">Quote is being reviewed by owner</p>
                                @elseif($job->quote->status === 'sent')
                                    <p class="text-xs text-blue-600">Quote sent to customer for approval</p>
                                @elseif($job->quote->status === 'accepted')
                                    <p class="text-xs text-green-600">Quote approved by customer</p>
                                @elseif($job->quote->status === 'rejected')
                                    <p class="text-xs text-red-600">Quote was rejected by customer</p>
                                @endif
                            </div>
                            <a href="{{ route('owner.quotes.edit', $job->quote) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View Quote
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Quote / Receipt</h2>
                    <div class="bg-zinc-50 rounded-lg p-4 border border-zinc-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-zinc-600">Quote is being generated by owner.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
