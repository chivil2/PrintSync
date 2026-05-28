@extends('layouts.app.customer')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">My Quotes</h1>
        <p class="mt-2 text-zinc-600">View and manage your service quotes</p>
    </div>

    @if($quotes->isEmpty())
        <div class="bg-white rounded-lg border border-zinc-200 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-zinc-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-zinc-900 mb-2">No quotes yet</h3>
            <p class="text-zinc-600 mb-6">Quotes will appear here after you request a service</p>
            <a href="{{ route('customer.store') }}" class="inline-block px-4 py-2 bg-[#E8743B] hover:bg-[#d66532] text-white font-medium rounded-lg transition-colors">
                Browse Services
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($quotes as $quote)
                <div class="bg-white rounded-lg border border-zinc-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-semibold text-zinc-900">{{ $quote->quote_number }}</h3>
                                    @php
                                        $statusColors = [
                                            'sent' => 'bg-blue-100 text-blue-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ str_replace('_', ' ', ucfirst($quote->status)) }}
                                    </span>
                                </div>
                                <p class="text-zinc-600 mb-3">{{ $quote->serviceJob->name ?? 'Service Quote' }}</p>
                                <div class="flex items-center gap-4 text-sm text-zinc-500">
                                    <span>Date: {{ $quote->date->format('M d, Y') }}</span>
                                    <span>Total: ₱{{ number_format($quote->total, 2) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('customer.quotes.show', $quote) }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
