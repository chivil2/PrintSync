<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Edit Product</h1>
            <p class="text-white/80">Update product details</p>
        </div>

        <div>
            <form action="{{ route('owner.products.update', $product) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm p-6 space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-zinc-800">Product Details</h2>

                            <div>
                                <label for="name" class="block text-sm font-medium text-zinc-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-zinc-700 mb-1">SKU <span class="text-red-500">*</span></label>
                                <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-zinc-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('description', $product->description) }}</textarea>
                                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-zinc-700 mb-1">Category <span class="text-red-500">*</span></label>
                                <select id="category" name="category"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ old('category', $product->category) === $category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                                    @endforeach
                                </select>
                                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h2 class="text-lg font-semibold text-zinc-800">Pricing & Status</h2>

                            <div>
                                <label for="price" class="block text-sm font-medium text-zinc-700 mb-1">Price <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm">₱</span>
                                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                                        class="w-full rounded-lg border border-zinc-300 pl-7 pr-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-zinc-700 mb-1">Image URL</label>
                                <input type="url" id="image" name="image" value="{{ old('image', $product->image) }}"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                    placeholder="https://example.com/image.jpg">
                                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="flex items-center gap-3 mt-4">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                            class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-zinc-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-colors flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-zinc-900">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                        Update Product
                    </button>
                    <a href="{{ route('owner.products.index') }}"
                        class="px-5 py-2.5 text-zinc-700 font-medium rounded-lg hover:bg-zinc-100 transition-colors text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app.owner>
