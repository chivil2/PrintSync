@extends('layouts.app.employee')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Quotes</h1>
        <p class="mt-2 text-zinc-600">View quotes for your assigned service jobs</p>
    </div>

    @if($quotes->isEmpty())
        <div class="bg-white rounded-lg border border-zinc-200 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-zinc-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-zinc-900 mb-2">No quotes yet</h3>
            <p class="text-zinc-600">Quotes for your assigned jobs will appear here</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($quotes as $quote)
                <a href="{{ route('owner.quotes.edit', $quote) }}" class="block bg-white rounded-lg border border-zinc-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-semibold text-zinc-900">{{ $quote->quote_number }}</h3>
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-zinc-100 text-zinc-800',
                                            'sent' => 'bg-blue-100 text-blue-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Pending Review',
                                            'sent' => 'Awaiting Approval',
                                            'accepted' => 'Approved',
                                            'rejected' => 'Rejected',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ $statusLabels[$quote->status] ?? ucfirst($quote->status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-zinc-500">Service:</span>
                                        <p class="font-medium text-zinc-900">{{ $quote->serviceJob->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-zinc-500">Customer:</span>
                                        <p class="font-medium text-zinc-900">{{ $quote->serviceJob->customer->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-zinc-500">Date:</span>
                                        <p class="font-medium text-zinc-900">{{ $quote->date->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-zinc-500">Total:</span>
                                        <p class="font-medium text-zinc-900">₱{{ number_format($quote->total, 2) }}</p>
                                    </div>
                                </div>

                                @if($quote->rejection_reason)
                                    <div class="mt-4 p-3 bg-red-50 rounded-lg">
                                        <span class="text-sm text-red-500">Rejection Reason:</span>
                                        <p class="text-sm text-red-700 mt-1">{{ $quote->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="ml-6 flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
