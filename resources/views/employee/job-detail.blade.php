@extends('layouts.app.employee')

@section('content')
<div class="p-4 flex-1 flex flex-col">
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/70 border border-slate-100 flex-1 flex flex-col overflow-hidden">
        <div class="px-8 pt-8 pb-4 flex-1 overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Job Details</h1>
                    <p class="mt-1 text-slate-500 text-sm">{{ $job->name }}</p>
                </div>
                <a href="{{ route('employee.jobs') }}" class="flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-2xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Jobs
                </a>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold {{ $statusColors[$job->status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ str_replace('_', ' ', ucfirst($job->status)) }}
                        </span>
                        <span class="text-sm text-slate-400 font-medium">Job #{{ $job->id }}</span>
                    </div>
                </div>

                <div class="px-8 py-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Service Information</h2>
                    <div class="bg-slate-50 rounded-3xl p-6 mb-6 border border-slate-100">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <span class="text-slate-400 text-xs font-medium">Service</span>
                                <p class="text-slate-900 font-bold mt-1">{{ $job->name }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs font-medium">Type</span>
                                <p class="text-slate-900 font-bold mt-1">{{ ucfirst($job->type) }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-xs font-medium">Requested</span>
                                <p class="text-slate-900 font-bold mt-1">{{ $job->created_at->format('M d, Y') }}</p>
                            </div>
                            @if ($job->deadline)
                                <div>
                                    <span class="text-slate-400 text-xs font-medium">Deadline</span>
                                    <p class="text-slate-900 font-bold mt-1">{{ $job->deadline->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                        @if ($job->description)
                            <div class="mt-5 pt-5 border-t border-slate-200">
                                <span class="text-slate-400 text-xs font-medium">Description</span>
                                <p class="text-slate-700 mt-1">{{ $job->description }}</p>
                            </div>
                        @endif
                        @if ($job->notes)
                            <div class="mt-5 pt-5 border-t border-slate-200">
                                <span class="text-slate-400 text-xs font-medium">Notes</span>
                                <p class="text-slate-700 mt-1">{{ $job->notes }}</p>
                            </div>
                        @endif
                        <div class="mt-5 pt-5 border-t border-slate-200">
                            <span class="text-slate-400 text-xs font-medium">Customer</span>
                            <p class="text-slate-900 font-bold mt-1">{{ $job->customer->name ?? 'N/A' }}</p>
                            @if ($job->customer->email)
                                <p class="text-sm text-slate-500 mt-0.5">{{ $job->customer->email }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4">Update Status</h2>
                        <form method="POST" action="{{ route('employee.jobs.update', $job) }}">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-4">
                                <select name="status" class="block w-64 text-sm font-medium rounded-2xl p-3 border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 text-slate-900">
                                    <option value="pending" {{ $job->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $job->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $job->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-2xl transition-colors cursor-pointer shadow-sm">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>

                    @if ($job->quote)
                        <div class="mt-6">
                            <h2 class="text-lg font-bold text-slate-900 mb-4">Quote / Receipt</h2>
                            <div class="bg-blue-50 rounded-3xl p-6 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            @php
                                                $quoteStatusColors = [
                                                    'draft' => 'bg-slate-100 text-slate-700',
                                                    'sent' => 'bg-blue-100 text-blue-700',
                                                    'accepted' => 'bg-emerald-100 text-emerald-700',
                                                    'rejected' => 'bg-red-100 text-red-700',
                                                ];
                                                $quoteStatusLabels = [
                                                    'draft' => 'Pending Review',
                                                    'sent' => 'Awaiting Approval',
                                                    'accepted' => 'Approved',
                                                    'rejected' => 'Rejected',
                                                ];
                                            @endphp
                                            <span class="inline-block px-4 py-1 rounded-full text-xs font-bold {{ $quoteStatusColors[$job->quote->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $quoteStatusLabels[$job->quote->status] ?? ucfirst($job->quote->status) }}
                                            </span>
                                            <span class="text-sm text-slate-500 font-medium">{{ $job->quote->quote_number }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700">
                                            Total: <span class="font-bold text-slate-900">₱{{ number_format($job->quote->total, 2) }}</span>
                                        </p>
                                        @php
                                            $statusMessage = match($job->quote->status) {
                                                'draft' => 'Quote is being reviewed by owner',
                                                'sent' => 'Quote sent to customer for approval',
                                                'accepted' => 'Quote approved by customer',
                                                'rejected' => 'Quote was rejected by customer',
                                                default => ''
                                            };
                                        @endphp
                                        @if ($statusMessage)
                                            <p class="text-xs mt-1 {{ $job->quote->status === 'accepted' ? 'text-emerald-600' : ($job->quote->status === 'rejected' ? 'text-red-600' : 'text-slate-500') }}">
                                                {{ $statusMessage }}
                                            </p>
                                        @endif
                                    </div>
                                    <a href="{{ route('owner.quotes.edit', $job->quote) }}" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-2xl transition-colors cursor-pointer shadow-sm">
                                        <i class="fa-solid fa-file-lines text-xs"></i>
                                        View Quote
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-6">
                            <h2 class="text-lg font-bold text-slate-900 mb-4">Quote / Receipt</h2>
                            <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-200 rounded-2xl flex items-center justify-center text-slate-500">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                    <p class="text-sm text-slate-500 font-medium">Quote is being generated by owner.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
