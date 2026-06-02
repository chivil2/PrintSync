@extends('layouts.app.customer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
                Pay for Quote
            </h1>
            <p class="mt-2 text-zinc-600">{{ $quote->quote_number }}</p>
        </div>
        <a href="{{ route('customer.quotes.show', $quote) }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Quote
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-800">{{ $errors->first() }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-zinc-200 shadow-sm">
        <div class="p-6 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">Order Summary</h2>
            <div class="space-y-2">
                @foreach($quote->lineItems as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-zinc-600">{{ $item->item_name }} <span class="text-zinc-400">× {{ $item->quantity }}</span></span>
                        <span class="text-zinc-900">₱{{ number_format($item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-zinc-200 flex justify-between items-center">
                <span class="text-zinc-900 font-semibold">Total to Pay</span>
                <span class="text-2xl font-bold text-zinc-900">₱{{ number_format($quote->total, 2) }}</span>
            </div>
        </div>

        <div class="p-6 border-b border-[#0066CC]" style="background-color: #0073E6; color: #ffffff;">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-white">Payment via {{ $method['name'] }}</h3>
                    <p class="text-sm text-white/90 mt-1">
                        Send <span class="font-bold text-white">₱{{ number_format($quote->total, 2) }}</span> to:
                    </p>
                    <div class="mt-2 bg-white/10 rounded-lg p-3 space-y-1 border border-white/20">
                        <div class="flex justify-between text-sm">
                            <span class="text-white/70">GCash #</span>
                            <span class="font-mono font-semibold text-white">{{ $method['account_number'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-white/70">Account name</span>
                            <span class="font-semibold text-white">{{ $method['account_name'] }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-white/80 mt-3">{{ $method['instructions'] }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('customer.quotes.payments.store', $quote) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="reference_no" class="block text-sm font-medium text-zinc-700 mb-1">
                    GCash Reference No. <span class="text-red-500">*</span>
                </label>
                <input type="text" name="reference_no" id="reference_no" maxlength="100" required
                    value="{{ old('reference_no') }}"
                    class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-zinc-900 bg-white @error('reference_no') border-red-500 @enderror"
                    placeholder="e.g. 1234567890">
                @error('reference_no')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="proof" class="block text-sm font-medium text-zinc-700 mb-1">
                    Screenshot of Payment <span class="text-zinc-400 font-normal">(optional)</span>
                </label>
                <input type="file" name="proof" id="proof" accept="image/*"
                    class="w-full text-sm text-zinc-900 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200">
                <p class="text-xs text-zinc-500 mt-1">JPG, PNG, or GIF. Max 2MB.</p>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-zinc-700 mb-1">
                    Notes <span class="text-zinc-400 font-normal">(optional)</span>
                </label>
                <textarea name="notes" id="notes" rows="3" maxlength="500"
                    class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-zinc-900 bg-white resize-none"
                    placeholder="Anything the owner should know about this payment...">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 border-t border-zinc-200 flex gap-3">
                <a href="{{ route('customer.quotes.show', $quote) }}" class="flex-1 px-4 py-2.5 border border-zinc-300 text-zinc-700 font-medium rounded-lg hover:bg-zinc-50 transition-colors text-sm text-center">
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">
                    Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
