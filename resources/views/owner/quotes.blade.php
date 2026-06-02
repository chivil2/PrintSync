<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto">
        <!-- Quotes List -->
        <div class="bg-white/95 rounded-[24px] p-5 shadow-xl shadow-blue-950/10 border border-white/70">
            <h3 class="text-[13px] font-semibold text-slate-500 uppercase tracking-wider mb-4">Recent Quotes</h3>
            @if($quotes->isEmpty())
                <div class="text-center py-12 text-slate-500">No quotes yet</div>
            @else
                <div class="space-y-3">
                    @foreach($quotes->take(10) as $quote)
                        <div class="bg-slate-50 rounded-xl p-4 hover:bg-slate-100 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-semibold text-slate-900">{{ $quote->quote_number }}</h3>
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-slate-200 text-slate-700',
                                                'sent' => 'bg-blue-100 text-blue-700',
                                                'accepted' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$quote->status] ?? 'bg-slate-200 text-slate-700' }}">
                                            {{ str_replace('_', ' ', ucfirst($quote->status)) }}
                                        </span>
                                        @if(in_array($quote->status, ['accepted', 'sent']))
                                            @php
                                                $paymentBadge = match (true) {
                                                    $quote->payment_status === 'paid' => ['bg-emerald-100 text-emerald-700', 'Paid'],
                                                    $quote->hasPendingPayment() => ['bg-amber-100 text-amber-700', 'Pending Verification'],
                                                    default => ['bg-rose-100 text-rose-700', 'Unpaid'],
                                                };
                                            @endphp
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentBadge[0] }}">
                                                {{ $paymentBadge[1] }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-600">
                                        Customer: {{ $quote->customer->first_name ?? 'N/A' }} {{ $quote->customer->last_name ?? '' }}
                                        @if($quote->serviceJob)
                                            | Service: {{ $quote->serviceJob->name }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-4 text-xs text-slate-500 mt-1">
                                        <span>Date: {{ $quote->date->format('M d, Y') }}</span>
                                        <span>Total: ₱{{ number_format($quote->total, 0) }}</span>
                                    </div>
                                </div>
                                @if($quote->status === 'draft')
                                    <a href="{{ route('owner.quotes.view', $quote) }}" class="ml-4 px-3 py-1.5 bg-orange-500 text-white rounded-lg text-xs font-medium hover:bg-orange-600 transition-colors">
                                        Review & Send
                                    </a>
                                @elseif($quote->status === 'sent')
                                    <div class="flex items-center gap-2 ml-4">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-medium">
                                            Awaiting Customer
                                        </span>
                                        <a href="{{ route('owner.quotes.view', $quote) }}" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition-colors">
                                            View
                                        </a>
                                    </div>
                                @elseif($quote->status === 'accepted')
                                    <div class="flex items-center gap-2 ml-4">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <a href="{{ route('owner.quotes.view', $quote) }}" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition-colors">
                                            View
                                        </a>
                                    </div>
                                @elseif($quote->status === 'rejected')
                                    <div class="flex items-center gap-2 ml-4">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <a href="{{ route('owner.quotes.view', $quote) }}" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition-colors">
                                            View
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('owner.quotes.view', $quote) }}" class="ml-4 px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition-colors">
                                        View
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app.owner>
