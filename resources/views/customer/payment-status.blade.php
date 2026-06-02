@extends('layouts.app.customer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
                Payment Status
            </h1>
            <p class="mt-2 text-zinc-600">Reference #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <a href="{{ route('customer.quotes.show', $payment->quote) }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Quote
        </a>
    </div>

    <div class="bg-white rounded-lg border border-zinc-200 shadow-sm p-6">
        @if($payment->status === 'pending_verification')
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900">Awaiting Verification</h2>
                    <p class="text-sm text-zinc-600 mt-1">Your payment has been submitted. The owner will verify it shortly.</p>
                </div>
            </div>
        @elseif($payment->status === 'verified')
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900">Payment Verified</h2>
                    <p class="text-sm text-zinc-600 mt-1">Your payment has been verified. Work on your order will begin shortly.</p>
                    @if($payment->verified_at)
                        <p class="text-xs text-zinc-500 mt-1">Verified on {{ $payment->verified_at->format('M d, Y g:i A') }}</p>
                    @endif
                </div>
            </div>
        @else
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-zinc-900">Payment Rejected</h2>
                    <p class="text-sm text-zinc-600 mt-1">The owner rejected this payment. Please review the reason and submit a new payment.</p>
                    @if($payment->rejection_reason)
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-800"><span class="font-semibold">Reason:</span> {{ $payment->rejection_reason }}</p>
                        </div>
                        <a href="{{ route('customer.quotes.pay', $payment->quote) }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm">
                            Submit New Payment
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg border border-zinc-200 shadow-sm">
        <div class="p-6 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Payment Details</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-zinc-500">Amount</dt>
                    <dd class="font-semibold text-zinc-900 mt-0.5">₱{{ number_format($payment->amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Method</dt>
                    <dd class="font-semibold text-zinc-900 mt-0.5">{{ strtoupper($payment->method) }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Reference No.</dt>
                    <dd class="font-mono text-zinc-900 mt-0.5">{{ $payment->reference_no ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Submitted</dt>
                    <dd class="text-zinc-900 mt-0.5">{{ $payment->created_at->format('M d, Y g:i A') }}</dd>
                </div>
                @if($payment->notes)
                    <div class="col-span-2">
                        <dt class="text-zinc-500">Notes</dt>
                        <dd class="text-zinc-900 mt-0.5">{{ $payment->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>
        @if($payment->proof_path)
            <div class="p-6">
                <h3 class="text-sm font-semibold text-zinc-700 mb-2">Proof of Payment</h3>
                <a href="{{ asset('storage/'.$payment->proof_path) }}" target="_blank" class="inline-block">
                    <img src="{{ asset('storage/'.$payment->proof_path) }}" alt="Proof" class="max-w-xs rounded-lg border border-zinc-200 hover:shadow-md transition">
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
