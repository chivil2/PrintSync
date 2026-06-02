<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="bg-emerald-600 rounded-lg px-6 py-4 mb-6">
            <h1 class="text-xl font-bold text-white">Add Inventory Item</h1>
            <p class="text-emerald-100 text-sm">Add a new item to your inventory</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-300 p-6" x-data="{ isLoading: false, handleSubmit() { this.isLoading = true; this.$refs.form.submit(); } }">
            <!-- Card Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Item Details</h2>
            </div>

            <form x-ref="form" action="{{ route('owner.inventory.store') }}" method="POST" class="space-y-6" @submit.prevent="handleSubmit">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Enter item name">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-slate-700 mb-2">SKU <span class="text-red-500">*</span></label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="e.g. INK-BLK-001">
                        @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Min Stock Level -->
                    <div>
                        <label for="min_stock_level" class="block text-sm font-medium text-slate-700 mb-2">Min Stock Level <span class="text-red-500">*</span></label>
                        <input type="number" id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', 10) }}" min="0" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('min_stock_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <label for="unit_price" class="block text-sm font-medium text-slate-700 mb-2">Unit Price (₱) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">₱</span>
                            <input type="number" id="unit_price" name="unit_price" value="{{ old('unit_price', '0.00') }}" step="0.01" min="0" required
                                class="w-full pl-7 pr-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="0.00">
                        </div>
                        @error('unit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-slate-700 mb-2">Unit <span class="text-red-500">*</span></label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', 'pcs') }}" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="e.g. pcs, kg, liters">
                        @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label for="supplier" class="block text-sm font-medium text-slate-700 mb-2">Supplier</label>
                        <input type="text" id="supplier" name="supplier" value="{{ old('supplier') }}"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Enter supplier name">
                        @error('supplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-slate-700 mb-2">Storage Location</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="e.g. Shelf A-3">
                        @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Enter item description">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                    <button type="submit" :disabled="isLoading"
                        class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Adding...' : 'Add Item'"></span>
                    </button>
                    <a href="{{ route('owner.inventory.index') }}"
                        class="px-6 py-2 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app.owner>
