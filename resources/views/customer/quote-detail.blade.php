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
    </div>

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
                    @if($quote->tax > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Tax:</span>
                            <span class="text-zinc-900">₱{{ number_format($quote->tax, 2) }}</span>
                        </div>
                    @endif
                    @if($quote->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-500">Discount:</span>
                            <span class="text-green-600">-₱{{ number_format($quote->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-zinc-200">
                        <span class="text-zinc-900">Total:</span>
                        <span class="text-zinc-900">₱{{ number_format($quote->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($quote->terms)
                <div class="mt-6 p-4 bg-zinc-50 rounded-lg">
                    <h3 class="text-sm font-medium text-zinc-900 mb-2">Terms & Conditions</h3>
                    <p class="text-sm text-zinc-600">{{ $quote->terms }}</p>
                </div>
            @endif

            @if($quote->rejection_reason)
                <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                    <h3 class="text-sm font-medium text-red-900 mb-2">Rejection Reason</h3>
                    <p class="text-sm text-red-700">{{ $quote->rejection_reason }}</p>
                </div>
            @endif
        </div>

        @if($quote->status === 'sent')
            <div class="p-6 border-t border-zinc-200 bg-zinc-50">
                <form action="{{ route('customer.quotes.reject', $quote) }}" method="POST" x-data="{ showRejectForm: false }">
                    @csrf
                    <div class="flex items-center gap-3">
                        <button type="submit" formaction="{{ route('customer.quotes.approve', $quote) }}" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            Approve Quote
                        </button>
                        <button type="button" @click="showRejectForm = !showRejectForm" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Reject Quote
                        </button>
                    </div>
                    <div x-show="showRejectForm" x-transition class="mt-4">
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Reason for rejection</label>
                        <textarea name="rejection_reason" rows="3" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Please explain why you're rejecting this quote..." required></textarea>
                        <div class="mt-3 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                Submit Rejection
                            </button>
                            <button type="button" @click="showRejectForm = false" class="px-4 py-2 border border-zinc-300 text-zinc-700 font-medium rounded-lg hover:bg-zinc-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        @if($quote->status === 'accepted')
            <div class="p-6 border-t border-zinc-200 bg-green-50">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-medium text-green-900">Quote Approved</h3>
                        <p class="text-sm text-green-700">This quote has been approved and is now your receipt. Your order is being processed.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
