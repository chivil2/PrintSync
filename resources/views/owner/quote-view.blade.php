<x-layouts::app.owner>
    <div class="min-h-screen bg-slate-50">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-orange-500 to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden mb-8 mx-4 mt-4">
            <div class="welcome-dots"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Review & Send Quote</h1>
                    <p class="text-orange-100 text-lg font-medium">{{ $quote->quote_number }}</p>
                </div>
                <a href="{{ route('owner.quotes') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-colors duration-200 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Quotes
                </a>
            </div>
        </div>

        <!-- Quote Info Banner -->
        <div class="max-w-7xl mx-auto px-6 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex flex-wrap items-center gap-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Customer</p>
                            <p class="text-base font-semibold text-slate-900">{{ $quote->customer->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($quote->serviceJob)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Service</p>
                            <p class="text-base font-semibold text-slate-900">{{ $quote->serviceJob->name }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Status</p>
                            @php
                                $statusColors = [
                                    'draft' => 'bg-slate-100 text-slate-700',
                                    'sent' => 'bg-blue-100 text-blue-700',
                                    'accepted' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$quote->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ str_replace('_', ' ', ucfirst($quote->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="quoteEditor()">
            <div class="max-w-7xl mx-auto px-6 pb-12">
                <!-- Order Details -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Order Details</h2>
                            <p class="text-base text-slate-500 mt-1">Review and adjust pricing for this quote</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="divide-y divide-slate-100" id="line-items-container">
                            <template x-for="(item, index) in lineItems" :key="index">
                                <div class="p-6">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 space-y-5">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Service Name</label>
                                                <p class="text-sm text-slate-900 font-medium" x-text="item.item_name"></p>
                                                <input type="hidden" :name="`line_items[${index}][id]`" :value="item.id">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                                                <p class="text-sm text-slate-600" x-text="item.description || '—'"></p>
                                            </div>
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Quantity</label>
                                                    <p class="text-sm text-slate-900 font-semibold" x-text="item.quantity"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Unit Price (₱)</label>
                                                    <p class="text-sm text-slate-900 font-semibold" x-text="'₱' + parseFloat(item.unit_price).toFixed(2)"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Line Total (₱)</label>
                                                    <p class="text-sm text-slate-900 font-semibold" x-text="'₱' + parseFloat(item.line_total).toFixed(2)"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        @if($quote->serviceJob && $quote->serviceJob->notes)
                        <div class="px-6 py-4 border-t border-slate-100">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Customer Notes</label>
                            <div class="w-full px-4 py-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-slate-50 leading-relaxed whitespace-pre-wrap">{{ $quote->serviceJob->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Negotiation Response -->
                @if($quote->negotiation_status === 'pending')
                <div class="mb-8">
                    <div class="bg-amber-50 rounded-xl border border-amber-300 p-6">
                        <div class="flex items-start gap-3 mb-4">
                            <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div>
                                <h3 class="text-base font-semibold text-amber-900">Customer Counter-Offer Received</h3>
                                <p class="text-sm text-amber-700">{{ $quote->customer->name }} has proposed a price adjustment.</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs font-medium text-amber-700 uppercase tracking-wide mb-1">Proposed Adjustment</p>
                                <p class="text-lg font-bold text-amber-900">₱{{ number_format($quote->negotiation_adjustment, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-amber-700 uppercase tracking-wide mb-1">Proposed Total</p>
                                <p class="text-lg font-bold text-amber-900">₱{{ number_format((float)$quote->subtotal + (float)$quote->negotiation_adjustment, 2) }}</p>
                            </div>
                        </div>
                        @if($quote->negotiation_notes)
                        <div class="mb-4">
                            <p class="text-xs font-medium text-amber-700 uppercase tracking-wide mb-1">Customer Notes</p>
                            <p class="text-sm text-amber-900 bg-amber-100 rounded-lg px-4 py-3">{{ $quote->negotiation_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Adjustment -->
                <div class="mb-8">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-slate-900">Adjustment</h2>
                        <p class="text-base text-slate-500 mt-1">Add an adjustment to the total</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Adjustment ± (₱)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₱</span>
                                    <input type="number" name="adjustment" x-model="adjustment" step="0.01" placeholder="Enter positive or negative amount" class="w-full pl-8 pr-4 py-2.5 text-sm border border-slate-300 rounded-lg bg-white text-slate-700 font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <p class="text-xs text-slate-500 mt-1">Enter positive to add, negative to subtract</p>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Adjustment Total (₱)</label>
                                    <div class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-900 font-semibold" x-text="'₱' + parseFloat(adjustment || 0).toFixed(2)"></div>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">New SubTotal (₱)</label>
                                    <div class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-900 font-semibold" x-text="'₱' + parseFloat(subtotal).toFixed(2)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Negotiation/Notes -->
                <div class="mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Negotiation/Notes (Optional)</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-lg bg-white text-slate-700 resize-none" placeholder="Enter negotiation details or internal notes for this quote...">{{ $quote->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    @if($quote->status === 'draft')
                        <a href="{{ route('owner.quotes') }}" class="px-6 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-colors duration-200 cursor-pointer">
                            Cancel
                        </a>
                        <form action="{{ route('owner.quotes.approve', $quote) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Accept
                            </button>
                        </form>
                        <form action="{{ route('owner.quotes.send', $quote) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="adjustment" :value="adjustment">
                            <input type="hidden" name="notes" x-bind:value="document.querySelector('textarea[name=notes]')?.value ?? ''">
                            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Send Quote to Customer
                            </button>
                        </form>
                    @elseif($quote->status === 'sent')
                        <a href="{{ route('owner.quotes') }}" class="px-6 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-colors duration-200 cursor-pointer">
                            Back to Quotes
                        </a>
                        @if($quote->negotiation_status === 'pending')
                            <form action="{{ route('owner.quotes.send', $quote) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="adjustment" value="{{ $quote->negotiation_adjustment }}">
                                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition-colors duration-200 cursor-pointer flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Accept Counter-Offer & Resend
                                </button>
                            </form>
                            <form action="{{ route('owner.quotes.approve', $quote) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Accept Quote as-is
                                </button>
                            </form>
                        @else
                            <button class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg cursor-not-allowed opacity-75" disabled>
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Awaiting Customer Approval
                                </span>
                            </button>
                        @endif
                    @elseif($quote->status === 'accepted')
                        <a href="{{ route('owner.jobs') }}" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Quote Accepted - Go to Jobs to Assign
                        </a>
                    @elseif($quote->status === 'rejected')
                        <div class="flex items-center gap-2 text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Quote was rejected by customer</span>
                        </div>
                        <a href="{{ route('owner.quotes') }}" class="px-6 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors duration-200 cursor-pointer">
                            Back to Quotes
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function quoteEditor() {
            return {
                lineItems: {!! json_encode($quote->lineItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => $item->item_name,
                        'description' => $item->description,
                        'quantity' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'line_total' => (float) $item->line_total,
                    ];
                })) !!},
                adjustment: {{ (float) ($quote->adjustment ?? 0) }},
                get subtotal() {
                    const lineTotal = this.lineItems.reduce((sum, item) => sum + parseFloat(item.line_total || 0), 0);
                    return lineTotal + parseFloat(this.adjustment || 0);
                },
            }
        }
    </script>
</x-layouts::app.owner>
