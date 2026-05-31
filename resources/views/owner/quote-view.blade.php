<x-layouts::app.owner>
    <div class="min-h-screen bg-white">
        <!-- Header -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">Approve Quote</h1>
                        <p class="text-lg text-slate-600 mt-1 font-medium">{{ $quote->quote_number }}</p>
                    </div>
                    <a href="{{ route('owner.quotes') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-colors duration-200 cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Quotes
                    </a>
                </div>
            </div>
        </div>

        <!-- Quote Info Banner -->
        <div class="max-w-7xl mx-auto px-6 py-6">
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

        <!-- Form -->
        <form action="{{ route('owner.quotes.approve', $quote) }}" method="POST" x-data="{ lineItems: {{ json_encode($quote->lineItems) }} }">
            @csrf

            <div class="max-w-7xl mx-auto px-6 pb-12">
                <!-- Line Items Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Line Items</h2>
                            <p class="text-base text-slate-500 mt-1">Add or edit items for this quote</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="divide-y divide-slate-100" id="line-items-container">
                            <template x-for="(item, index) in lineItems" :key="index">
                                <div class="p-6 hover:bg-slate-50 transition-colors duration-150">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 space-y-5">
                                            <!-- Item Name -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Item Name</label>
                                                <input type="text" name="line_items[@{{ index }}][item_name]" x-model="item.item_name" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-medium" readonly>
                                                <input type="hidden" name="line_items[@{{ index }}][id]" :value="item.id">
                                            </div>
                                            <!-- Description -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                                                <textarea name="line_items[@{{ index }}][description]" x-model="item.description" rows="2" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600 resize-none" readonly></textarea>
                                            </div>
                                            <!-- Pricing Grid -->
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Quantity</label>
                                                    <input type="number" name="line_items[@{{ index }}][quantity]" x-model="item.quantity" step="0.01" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600 font-medium" readonly>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Unit Price</label>
                                                    <input type="number" name="line_items[@{{ index }}][unit_price]" x-model="item.unit_price" step="0.01" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600 font-medium" readonly>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 mb-2">Line Total</label>
                                                    <input type="number" name="line_items[@{{ index }}][line_total]" x-model="item.line_total" step="0.01" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600 font-semibold" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="mb-8">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-slate-900">Pricing Summary</h2>
                        <p class="text-base text-slate-500 mt-1">Quote totals</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Subtotal</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                                    <input type="text" value="{{ number_format($quote->subtotal, 2) }}" class="w-full pl-8 pr-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-medium" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Total</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                                    <input type="text" value="{{ number_format($quote->total, 2) }}" class="w-full pl-8 pr-4 py-2.5 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-900 font-semibold" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    @if($quote->status !== 'accepted')
                        <a href="{{ route('owner.quotes') }}" class="px-6 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-colors duration-200 cursor-pointer">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer">
                            Approve Quote
                        </button>
                    @else
                        <a href="{{ route('owner.quotes') }}" class="px-6 py-2.5 text-sm font-medium text-white bg-slate-600 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors duration-200 cursor-pointer">
                            OK
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</x-layouts::app.owner>
