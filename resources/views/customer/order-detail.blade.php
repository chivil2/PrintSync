@extends('layouts.app.customer')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        .receipt-container { 
            box-shadow: none !important; 
            border: 1px solid #ddd !important;
        }
        body { background: white !important; }
    }
    .receipt-container {
        background: white;
        max-width: 800px;
        margin: 0 auto;
    }
    .receipt-header {
        background: #ececec86;
        padding: 28px 32px;
        border-radius: 16px 16px 0 0;
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<div class="space-y-4 max-w-[900px] mx-auto">
    <!-- Header Actions -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('customer.orders') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Receipt
            </button>
            @if($order->status === null || $order->status === 'pending')
                <form action="{{ route('customer.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @method('PATCH')
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg text-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel Order
                    </button>
                </form>
            @endif
            @if($order->invoice_path && $order->status === 'completed')
                <a href="{{ route('customer.orders.invoice', $order) }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg text-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Invoice
                </a>
            @endif
        </div>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-container rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="flex items-start justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="w-20 h-auto" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold tracking-[0.2em] uppercase text-gray-400">Receipt</p>
                        <h1 class="text-3xl font-extrabold text-gray-900">Order Receipt</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
                <div class="text-right space-y-2">
                    @php
                        $statusConfig = [
                            null => ['bg-amber-500', 'Pending Review'],
                            'pending' => ['bg-amber-500', 'Pending Review'],
                            'in_progress' => ['bg-blue-500', 'In Progress'],
                            'completed' => ['bg-emerald-500', 'Completed'],
                            'cancelled' => ['bg-red-500', 'Cancelled'],
                        ];
                        $statusInfo = $statusConfig[$order->status] ?? ['bg-gray-500', ucfirst($order->status ?? 'pending')];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold {{ $statusInfo[0] }} text-white">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        {{ $statusInfo[1] }}
                    </span>
                    <div class="text-sm text-gray-500 font-mono">
                        <span class="font-semibold text-gray-700">Order #{{ $order->id }}</span>
                        <span class="block">ORD-{{ date('Y') }}-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Body -->
        <div class="p-8">

            <!-- Service Details Grid -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Service</span>
                        <p class="text-gray-900 font-semibold text-lg mt-1">{{ $order->name }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</span>
                        <p class="text-gray-700 font-medium mt-1">{{ ucfirst($order->type) }}</p>
                    </div>
                    @if($order->deadline)
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Deadline</span>
                            <p class="text-gray-700 font-medium mt-1">{{ $order->deadline->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Order Date</span>
                        <p class="text-gray-700 font-medium mt-1">{{ $order->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Quantity</span>
                        <p class="text-gray-700 font-medium mt-1">{{ $order->quote?->lineItems->first()?->quantity ?? 1 }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Cost</span>
                        <p class="text-gray-700 font-bold mt-1 text-lg">₱{{ number_format($order->quote?->total ?? $order->service->price ?? 0, 2) }}</p>
                    </div>
                    @if($order->employee)
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assigned To</span>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr($order->employee->first_name, 0, 1) }}{{ substr($order->employee->last_name, 0, 1) }}
                                </div>
                                <p class="text-gray-700 font-medium">{{ $order->employee->first_name }} {{ $order->employee->last_name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($order->description)
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</span>
                    <p class="text-gray-700 mt-2 leading-relaxed">{{ $order->description }}</p>
                </div>
            @endif

            @if($order->notes)
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Notes</span>
                    <p class="text-gray-700 mt-2 leading-relaxed">{{ $order->notes }}</p>
                </div>
            @endif

            <!-- Quote / Receipt Section -->
            @if($order->quote)
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Quote & Receipt</h2>
                        @if($order->quote->status === 'accepted')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approved
                            </span>
                        @elseif($order->quote->status === 'sent')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Awaiting Approval
                            </span>
                        @elseif($order->quote->status === 'rejected')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pending Review
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-600">Quote Number</span>
                            <span class="font-mono text-gray-900 font-medium">{{ $order->quote->quote_number ?? 'N/A' }}</span>
                        </div>

                        @if($order->quote->lineItems && $order->quote->lineItems->count() > 0)
                            <div class="py-2">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Line Items</span>
                                <div class="mt-3 space-y-2">
                                    @foreach($order->quote->lineItems as $item)
                                        <div class="flex items-center justify-between py-2 px-3 bg-white rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $item->description ?? 'Item' }}</p>
                                                <p class="text-sm text-gray-500">{{ $item->quantity }} x ₱{{ number_format($item->unit_price, 2) }}</p>
                                            </div>
                                            <span class="font-semibold text-gray-900">₱{{ number_format($item->line_total, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between py-3 bg-gray-900 text-white px-4 rounded-lg mt-4">
                            <span class="font-semibold">Total Amount</span>
                            <span class="text-2xl font-bold">₱{{ number_format($order->quote->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3 no-print">
                        @if($order->quote->status === 'sent')
                            <form method="POST" action="{{ route('customer.quotes.accept', $order->quote) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Accept Quote
                                </button>
                            </form>
                            <form method="POST" action="{{ route('customer.quotes.reject', $order->quote) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Decline
                                </button>
                            </form>
                        @else
                            <a href="{{ route('customer.quotes.show', $order->quote) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Full Quote
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Pending Quote -->
                <div class="bg-amber-50 rounded-xl p-6 border border-amber-200">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Quote Being Prepared</h3>
                            <p class="text-sm text-gray-600 mt-1">We're reviewing your order and preparing a detailed quote. You'll be notified once it's ready for approval.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($order->invoice_path && $order->status === 'completed')
                <!-- Invoice Section -->
                <div class="mt-6 bg-emerald-50 rounded-xl p-6 border border-emerald-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Final Invoice Ready</h3>
                                <p class="text-sm text-gray-600 mt-1">Your order has been completed and the final invoice is ready.</p>
                            </div>
                        </div>
                        <a href="{{ route('customer.orders.invoice', $order) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Invoice
                        </a>
                    </div>
                </div>
            @endif


        </div>

        <!-- Receipt Footer -->
        <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-500 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Secured by PrintSync</span>
                </div>
                <p class="text-gray-400 text-sm">Thank you for your business!</p>
            </div>
        </div>
    </div>
</div>
@endsection
