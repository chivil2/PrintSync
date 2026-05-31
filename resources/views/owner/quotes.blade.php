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
                                <a href="{{ route('owner.quotes.view', $quote) }}" class="ml-4 px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-medium hover:bg-slate-800">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app.owner>
