@extends('layouts.app.customer')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
                {{ $quote->status === 'accepted' ? 'Receipt' : 'Quote' }} Details
            </h1>
            <p class="mt-2 text-zinc-600">{{ $quote->quote_number }}</p>
        </div>
        <a href="{{ route('customer.orders') }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
        <a href="{{ route('customer.chat.open-for-quote', $quote) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-blue-600 rounded-lg text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Message Owner
        </a>
    </div>

    @if($quote->status === 'sent')
    <div class="bg-white rounded-lg border border-zinc-200 shadow-sm" x-data="{ showNegotiateForm: false }">
        <div class="p-6 border-b border-zinc-200 bg-blue-50">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h2 class="text-base font-semibold text-blue-900">Quote from PrintSync</h2>
                    <p class="text-sm text-blue-700">Total: <span class="font-bold">₱{{ number_format($quote->total, 2) }}</span>
                        @if((float)$quote->adjustment !== 0.0)
                            <span class="text-xs ml-1">(includes ₱{{ number_format($quote->adjustment, 2) }} adjustment)</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($quote->notes)
            <p class="mt-3 text-sm text-blue-800 bg-blue-100 rounded-lg px-4 py-3">{{ $quote->notes }}</p>
            @endif
        </div>
        <div class="p-6">
            @if($quote->negotiation_status !== 'pending')
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <form action="{{ route('customer.quotes.approve', $quote) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-sm">
                        Accept Quote
                    </button>
                </form>
                <button type="button" @click="showNegotiateForm = !showNegotiateForm" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors text-sm">
                    Negotiate Price
                </button>
                <form action="{{ route('customer.quotes.cancel', $quote) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm" onclick="return confirm('Are you sure you want to cancel this order?')">
                        Cancel Order
                    </button>
                </form>
            </div>
            @endif
            <div x-show="showNegotiateForm" x-transition class="border-t border-zinc-200 pt-4">
                <h3 class="text-sm font-semibold text-zinc-800 mb-3">Submit Counter-Offer</h3>
                <form action="{{ route('customer.quotes.negotiate', $quote) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">Your Proposed Adjustment ± (₱)</label>
                        <input type="number" name="negotiation_adjustment" step="0.01" required
                            class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm text-zinc-900 bg-white"
                            placeholder="e.g. -50 to reduce by ₱50">
                        <p class="text-xs text-zinc-500 mt-1">Enter negative to reduce, positive to add.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">Notes (Optional)</label>
                        <textarea name="negotiation_notes" rows="2"
                            class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm text-zinc-900 bg-white resize-none"
                            placeholder="Explain your counter-offer..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors text-sm">
                            Send Counter-Offer
                        </button>
                        <button type="button" @click="showNegotiateForm = false" class="px-4 py-2 border border-zinc-300 text-zinc-700 font-medium rounded-lg hover:bg-zinc-50 transition-colors text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @if($quote->negotiation_status === 'pending')
    <div class="bg-amber-50 rounded-lg border border-amber-300 p-5">
        <div class="flex items-center gap-2 text-amber-800">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">Your counter-offer of <span class="font-bold">₱{{ number_format($quote->negotiation_adjustment, 2) }}</span> is awaiting the owner's review.</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg border border-zinc-200">
        <div class="p-6 border-b border-zinc-200">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $statusColors = [
                            'draft' => 'bg-zinc-100 text-zinc-800',
                            'sent' => 'bg-blue-100 text-blue-800',
                            'accepted' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'draft' => 'Pending Review',
                            'sent' => 'Awaiting Your Approval',
                            'accepted' => 'Approved',
                            'rejected' => 'Rejected',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                        {{ $statusLabels[$quote->status] ?? ucfirst($quote->status) }}
                    </span>
                </div>
                <div class="text-sm text-zinc-500">
                    Date: {{ $quote->date->format('M d, Y') }}
                </div>
            </div>
        </div>

        @if($quote->status === 'draft')
            <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-medium text-yellow-900">Quote Under Review</h3>
                        <p class="text-sm text-yellow-700">Your quote is being reviewed by our team. We'll send it to you for approval once pricing is finalized.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="p-6">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Service Information</h2>
            <div class="bg-zinc-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-zinc-500 text-sm">Service:</span>
                        <p class="text-zinc-900 font-medium">{{ $quote->serviceJob->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-zinc-500 text-sm">Deadline:</span>
                        <p class="text-zinc-900 font-medium">{{ $quote->serviceJob->deadline ? $quote->serviceJob->deadline->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
                @if($quote->serviceJob->notes)
                    <div class="mt-4">
                        <span class="text-zinc-500 text-sm">Notes:</span>
                        <p class="text-zinc-900 mt-1">{{ $quote->serviceJob->notes }}</p>
                    </div>
                @endif
            </div>

            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Line Items</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200">
                            <th class="text-left py-3 px-4 text-sm font-medium text-zinc-500">Item</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-zinc-500">Qty</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-zinc-500">Price</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-zinc-500">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->lineItems as $item)
                            <tr class="border-b border-zinc-100">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-zinc-900">{{ $item->item_name }}</div>
                                    @if($item->description)
                                        <div class="text-sm text-zinc-500">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right text-zinc-900">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 text-right text-zinc-900">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 px-4 text-right text-zinc-900 font-medium">₱{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-500">Subtotal:</span>
                        <span class="text-zinc-900">₱{{ number_format($quote->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-zinc-200">
                        <span class="text-zinc-900">Total:</span>
                        <span class="text-zinc-900">₱{{ number_format($quote->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($quote->rejection_reason)
                <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                    <h3 class="text-sm font-medium text-red-900 mb-2">Rejection Reason</h3>
                    <p class="text-sm text-red-700">{{ $quote->rejection_reason }}</p>
                </div>
            @endif
        </div>


        @if($quote->status === 'accepted')
            <div class="p-6 border-t border-zinc-200 bg-green-50">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-medium text-green-900">Quote Approved</h3>
                        <p class="text-sm text-green-700">This quote has been approved and is now your receipt.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-zinc-200">
                @php
                    $latestPayment = $quote->payments()->latest()->first();
                    $hasPending = $latestPayment && $latestPayment->status === \App\Models\Payment::STATUS_PENDING;
                    $latestVerified = $quote->payments()->where('status', \App\Models\Payment::STATUS_VERIFIED)->latest()->first();
                @endphp

                @if($quote->isFullyPaid() && $latestVerified)
                    <div class="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-medium text-emerald-900">Paid in Full</h3>
                            <p class="text-sm text-emerald-700">Payment of ₱{{ number_format($latestVerified->amount, 2) }} verified on {{ $latestVerified->verified_at->format('M d, Y') }}. Work on your order will begin shortly.</p>
                        </div>
                    </div>
                @elseif($hasPending)
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 class="font-medium text-amber-900">Payment Submitted</h3>
                                <p class="text-sm text-amber-700">Awaiting owner verification.</p>
                            </div>
                        </div>
                        <a href="{{ route('customer.payments.show', $latestPayment) }}" class="px-4 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition">
                            View Payment Status
                        </a>
                    </div>
                @else
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="font-medium text-zinc-900">Ready to Pay</h3>
                            <p class="text-sm text-zinc-600">Pay ₱{{ number_format($quote->total, 2) }} via GCash to confirm your order.</p>
                        </div>
                        <a href="{{ route('customer.quotes.pay', $quote) }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Pay the Quote
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
