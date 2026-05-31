<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="productsData()" x-init="initProducts()">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <!-- Products Management -->
        <div class="bg-white rounded-lg p-5 sm:p-6 shadow-sm border border-slate-200 mb-6">
            <div class="flex flex-col gap-4 mb-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Products Management</h3>
                    <p class="text-sm text-slate-500">Showing {{ $products->count() }} of {{ $products->total() }} products</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative min-w-0 sm:w-72">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            placeholder="Search products, SKU, or description..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            x-model="searchQuery"
                            @keyup.enter="search()"
                        >
                    </div>
                    <select x-model="categoryFilter" @change="search()" class="px-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-40">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('owner.products.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-medium flex items-center justify-center gap-2 hover:bg-slate-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Product
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                            <th class="pb-3 font-medium">SKU</th>
                            <th class="pb-3 font-medium">Name</th>
                            <th class="pb-3 font-medium">Category</th>
                            <th class="pb-3 font-medium">Price</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Completed Orders</th>
                            <th class="pb-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 text-slate-700 font-mono text-xs">{{ $product->sku }}</td>
                                <td class="py-3 font-medium text-slate-900">{{ $product->name }}</td>
                                <td class="py-3 text-slate-600">{{ ucfirst($product->category) }}</td>
                                <td class="py-3 text-slate-600">₱{{ number_format($product->price, 2) }}</td>
                                <td class="py-3">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[12px] font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-500 text-xs">0</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('owner.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                        <form action="{{ route('owner.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function productsData() {
            return {
                searchQuery: '',
                categoryFilter: '',
                
                initProducts() {
                    // Get URL parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    this.searchQuery = urlParams.get('search') || '';
                    this.categoryFilter = urlParams.get('category') || '';
                },
                
                search() {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.categoryFilter) params.set('category', this.categoryFilter);
                    
                    window.location.href = `{{ route('owner.products.index') }}?${params.toString()}`;
                }
            };
        }
    </script>
</x-layouts::app.owner>
