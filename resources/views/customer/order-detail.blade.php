@extends('layouts.app.customer')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Order Details</h1>
            <p class="mt-2 text-zinc-600">{{ $order->name }}</p>
        </div>
        <a href="{{ route('customer.orders') }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
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
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                </span>
                <span class="text-sm text-zinc-500">
                    Order #{{ $order->id }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Service Information</h2>
            <div class="bg-zinc-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-zinc-500 text-sm">Service:</span>
                        <p class="text-zinc-900 font-medium">{{ $order->name }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-500 text-sm">Type:</span>
                        <p class="text-zinc-900 font-medium">{{ ucfirst($order->type) }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-500 text-sm">Requested:</span>
                        <p class="text-zinc-900 font-medium">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($order->deadline)
                        <div>
                            <span class="text-zinc-500 text-sm">Deadline:</span>
                            <p class="text-zinc-900 font-medium">{{ $order->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
                @if($order->description)
                    <div class="mt-4">
                        <span class="text-zinc-500 text-sm">Description:</span>
                        <p class="text-zinc-900 mt-1">{{ $order->description }}</p>
                    </div>
                @endif
                @if($order->notes)
                    <div class="mt-4">
                        <span class="text-zinc-500 text-sm">Notes:</span>
                        <p class="text-zinc-900 mt-1">{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->employee)
                    <div class="mt-4">
                        <span class="text-zinc-500 text-sm">Assigned to:</span>
                        <p class="text-zinc-900 font-medium">{{ $order->employee->first_name }} {{ $order->employee->last_name }}</p>
                    </div>
                @endif
            </div>

            @if($order->quote)
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
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quoteStatusColors[$order->quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ $quoteStatusLabels[$order->quote->status] ?? ucfirst($order->quote->status) }}
                                    </span>
                                    <span class="text-sm text-zinc-600">{{ $order->quote->quote_number }}</span>
                                </div>
                                <p class="text-sm text-zinc-700 mb-2">
                                    Total: <span class="font-semibold text-zinc-900">₱{{ number_format($order->quote->total, 2) }}</span>
                                </p>
                                @if($order->quote->status === 'sent')
                                    <p class="text-xs text-zinc-600">Review and approve or reject this quote</p>
                                @elseif($order->quote->status === 'accepted')
                                    <p class="text-xs text-green-600">Quote approved - this is your receipt</p>
                                @elseif($order->quote->status === 'rejected')
                                    <p class="text-xs text-red-600">Quote was rejected</p>
                                @else
                                    <p class="text-xs text-zinc-600">Quote is being reviewed</p>
                                @endif
                            </div>
                            <a href="{{ route('customer.quotes.show', $order->quote) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ $order->quote->status === 'accepted' ? 'View Receipt' : 'View Quote' }}
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
                            <p class="text-sm text-zinc-600">Quote is being generated. Check back soon.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($order->invoice_path && $order->status === 'completed')
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Invoice</h2>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-green-900">Invoice Available</span>
                                </div>
                                <p class="text-xs text-green-700">Your order has been completed and the invoice is ready for download.</p>
                            </div>
                            <a href="{{ route('customer.orders.invoice', $order) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
