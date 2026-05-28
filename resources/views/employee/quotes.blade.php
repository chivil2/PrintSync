@extends('layouts.app.employee')

@section('content')
<div class="p-4 flex-1 flex flex-col">
    <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/70 border border-slate-100 flex-1 flex flex-col overflow-hidden">
        <div class="px-8 pt-8 pb-4 flex-1 overflow-y-auto">
            <div class="flex items-center justify-between mb-6 px-1">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Quotes</h1>
                    <p class="mt-1 text-slate-500 text-sm">View quotes for your assigned service jobs</p>
                </div>
            </div>

            @if ($quotes->isEmpty())
                <div class="bg-white border border-slate-100 rounded-3xl p-16 text-center">
                    <div class="text-slate-400 text-5xl mb-4">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No quotes yet</h3>
                    <p class="text-slate-500">Quotes for your assigned jobs will appear here</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($quotes as $quote)
                        @php
                            $statusColors = [
                                'draft' => 'bg-slate-100 text-slate-700',
                                'sent' => 'bg-blue-100 text-blue-700',
                                'accepted' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'draft' => 'Pending Review',
                                'sent' => 'Awaiting Approval',
                                'accepted' => 'Approved',
                                'rejected' => 'Rejected',
                            ];
                        @endphp
                        <a href="{{ route('owner.quotes.edit', $quote) }}" class="block bg-white border border-slate-100 rounded-3xl p-6 hover:shadow-md hover:border-slate-200 transition-all cursor-pointer group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-xl font-bold text-slate-900">{{ $quote->quote_number }}</h3>
                                        <span class="inline-block px-4 py-1 rounded-full text-xs font-bold {{ $statusColors[$quote->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $statusLabels[$quote->status] ?? ucfirst($quote->status) }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-slate-400 text-xs">Service</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">{{ $quote->serviceJob->name ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 text-xs">Customer</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">{{ $quote->serviceJob->customer->name ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 text-xs">Date</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">{{ $quote->date->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 text-xs">Total</span>
                                            <p class="font-semibold text-slate-900 mt-0.5">₱{{ number_format($quote->total, 2) }}</p>
                                        </div>
                                    </div>

                                    @if ($quote->rejection_reason)
                                        <div class="mt-4 p-4 bg-red-50 rounded-2xl border border-red-100">
                                            <span class="text-xs text-red-500 font-medium">Rejection Reason</span>
                                            <p class="text-sm text-red-700 mt-1">{{ $quote->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="ml-6 flex flex-col items-center gap-3 flex-shrink-0">
                                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <div class="text-orange-600 opacity-0 group-hover:opacity-100 text-sm font-medium transition-opacity flex items-center gap-1">
                                        View Quote <i class="fa-solid fa-arrow-right text-xs"></i>
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
