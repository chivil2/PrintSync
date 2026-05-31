<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Edit Inventory Item</h1>
            <p class="text-white/80">Update inventory item details</p>
        </div>

        <div>
            <form action="{{ route('owner.inventory.update', $inventory) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm p-6 space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-zinc-800">Item Details</h2>

                            <div>
                                <label for="name" class="block text-sm font-medium text-zinc-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $inventory->name) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-zinc-700 mb-1">SKU <span class="text-red-500">*</span></label>
                                <input type="text" id="sku" name="sku" value="{{ old('sku', $inventory->sku) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-zinc-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('description', $inventory->description) }}</textarea>
                                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-zinc-800">Stock & Pricing</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-zinc-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" min="0" required
                                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="min_stock_level" class="block text-sm font-medium text-zinc-700 mb-1">Min Stock Level <span class="text-red-500">*</span></label>
                                    <input type="number" id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', $inventory->min_stock_level) }}" min="0" required
                                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    @error('min_stock_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="unit_price" class="block text-sm font-medium text-zinc-700 mb-1">Unit Price <span class="text-red-500">*</span></label>
                                    <input type="number" id="unit_price" name="unit_price" value="{{ old('unit_price', $inventory->unit_price) }}" step="0.01" min="0" required
                                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    @error('unit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="unit" class="block text-sm font-medium text-zinc-700 mb-1">Unit <span class="text-red-500">*</span></label>
                                    <input type="text" id="unit" name="unit" value="{{ old('unit', $inventory->unit) }}" required
                                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="supplier" class="block text-sm font-medium text-zinc-700 mb-1">Supplier</label>
                                <input type="text" id="supplier" name="supplier" value="{{ old('supplier', $inventory->supplier) }}"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('supplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-zinc-700 mb-1">Location</label>
                                <input type="text" id="location" name="location" value="{{ old('location', $inventory->location) }}"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                        Update Item
                    </button>
                    <a href="{{ route('owner.inventory.index') }}"
                        class="px-5 py-2.5 text-zinc-700 font-medium rounded-lg hover:bg-zinc-100 transition-colors text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app.owner>
