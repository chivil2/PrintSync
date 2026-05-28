<x-layouts::app.owner>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Edit Quote</h1>
                <p class="text-zinc-600">{{ $quote->quote_number }}</p>
            </div>
            <a href="{{ route('owner.quotes') }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Quotes
            </a>
        </div>

        <div class="bg-white rounded-lg border border-zinc-200">
            <div class="p-6 border-b border-zinc-200">
                <div class="flex items-center gap-4 mb-4">
                    <div>
                        <span class="text-zinc-500 text-sm">Customer:</span>
                        <p class="text-zinc-900 font-medium">{{ $quote->customer->name ?? 'N/A' }}</p>
                    </div>
                    @if($quote->serviceJob)
                        <div>
                            <span class="text-zinc-500 text-sm">Service:</span>
                            <p class="text-zinc-900 font-medium">{{ $quote->serviceJob->name }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-zinc-500 text-sm">Status:</span>
                        @php
                            $statusColors = [
                                'draft' => 'bg-zinc-100 text-zinc-800',
                                'sent' => 'bg-blue-100 text-blue-800',
                                'accepted' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$quote->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                            {{ str_replace('_', ' ', ucfirst($quote->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <form action="{{ route('owner.quotes.update', $quote) }}" method="POST" x-data="{ lineItems: {{ json_encode($quote->lineItems) }} }">
                @csrf
                @method('PUT')

                <div class="p-6">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Line Items</h2>
                    <div class="space-y-3" id="line-items-container">
                        <template x-for="(item, index) in lineItems" :key="index">
                            <div class="flex items-start gap-3 p-4 bg-zinc-50 rounded-lg">
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 mb-1">Item Name</label>
                                        <input type="text" name="line_items[{{ '{{' }}index{{ '}}' }}][item_name]" x-model="item.item_name" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <input type="hidden" name="line_items[{{ '{{' }}index{{ '}}' }}][id]" :value="item.id">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 mb-1">Description</label>
                                        <textarea name="line_items[{{ '{{' }}index{{ '}}' }}][description]" x-model="item.description" rows="2" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-zinc-700 mb-1">Quantity</label>
                                            <input type="number" name="line_items[{{ '{{' }}index{{ '}}' }}][quantity]" x-model="item.quantity" @input="item.line_total = item.quantity * item.unit_price" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-zinc-700 mb-1">Unit Price</label>
                                            <input type="number" name="line_items[{{ '{{' }}index{{ '}}' }}][unit_price]" x-model="item.unit_price" @input="item.line_total = item.quantity * item.unit_price" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-zinc-700 mb-1">Line Total</label>
                                            <input type="number" name="line_items[{{ '{{' }}index{{ '}}' }}][line_total]" x-model="item.line_total" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg bg-zinc-100" readonly>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="lineItems.splice(index, 1)" class="text-red-600 hover:text-red-700 mt-6">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="lineItems.push({ id: null, item_name: '', description: '', quantity: 1, unit_price: 0, line_total: 0 })" class="mt-3 inline-flex items-center px-4 py-2 border border-dashed border-zinc-300 rounded-lg text-sm font-medium text-zinc-600 hover:border-zinc-400 hover:text-zinc-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Line Item
                    </button>
                </div>

                <div class="p-6 border-t border-zinc-200">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Pricing</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Subtotal</label>
                            <input type="number" name="subtotal" value="{{ $quote->subtotal }}" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Tax</label>
                            <input type="number" name="tax" value="{{ $quote->tax }}" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Discount</label>
                            <input type="number" name="discount" value="{{ $quote->discount }}" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Total</label>
                            <input type="number" name="total" value="{{ $quote->total }}" step="0.01" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-zinc-200">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Additional Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Terms & Conditions</label>
                            <textarea name="terms" rows="3" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $quote->terms }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $quote->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        Save Quote
                    </button>
                    @if($quote->status === 'draft')
                        <form action="{{ route('owner.quotes.send', $quote) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                Send to Customer
                            </button>
                        </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-layouts::app.owner>
