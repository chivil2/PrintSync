<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="inventoryData()" x-init="initInventory()">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <!-- Inventory Alert -->
        @if($criticalCount > 0 || $lowCount > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-5 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-500 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900">Inventory Alert</h3>
                            <p class="text-sm text-slate-600">{{ $criticalCount }} critical items and {{ $lowCount }} low stock items need attention</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Inventory Management -->
        <div class="bg-white rounded-lg p-5 sm:p-6 shadow-sm border border-slate-200 mb-6">
            <div class="flex flex-col gap-4 mb-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Inventory Management</h3>
                    <p class="text-sm text-slate-500">Showing {{ $inventory->count() }} of {{ $inventory->total() }} items</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative min-w-0 sm:w-72">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            placeholder="Search inventory, supplier, or unit..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            x-model="searchQuery"
                            @keyup.enter="search()"
                        >
                    </div>
                    <select x-model="sortBy" @change="search()" class="px-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-40">
                        <option value="name">Alphabetically</option>
                        <option value="status">Status</option>
                        <option value="stock">Stock</option>
                    </select>
                    <select x-model="order" @change="search()" class="px-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-32">
                        <option value="asc">Asc (stocked)</option>
                        <option value="desc">Desc (critical)</option>
                    </select>
                    <a href="{{ route('owner.inventory.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-sm font-medium flex items-center justify-center gap-2 hover:bg-slate-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Item
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-500 border-b border-slate-200">
                            <th class="pb-3 font-medium">SKU</th>
                            <th class="pb-3 font-medium">Name</th>
                            <th class="pb-3 font-medium">Quantity</th>
                            <th class="pb-3 font-medium">Unit</th>
                            <th class="pb-3 font-medium">Unit Price</th>
                            <th class="pb-3 font-medium">Supplier</th>
                            <th class="pb-3 font-medium">Location</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($inventory as $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 text-slate-700 font-mono text-xs">{{ $item->sku }}</td>
                                <td class="py-3 font-medium text-slate-900">{{ $item->name }}</td>
                                <td class="py-3">
                                    <span class="font-semibold {{ $item->isOutOfStock() ? 'text-red-600' : ($item->isLowStock() ? 'text-orange-600' : 'text-slate-900') }}">
                                        {{ $item->quantity }}
                                    </span>
                                    <span class="text-xs text-slate-400">/ {{ $item->min_stock_level }}</span>
                                </td>
                                <td class="py-3 text-slate-600">{{ $item->unit }}</td>
                                <td class="py-3 text-slate-600">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 text-slate-600">{{ $item->supplier ?? '—' }}</td>
                                <td class="py-3 text-slate-600">{{ $item->location ?? '—' }}</td>
                                <td class="py-3">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[12px] font-medium {{ $item->status === 'in_stock' ? 'bg-green-100 text-green-700' : ($item->status === 'low_stock' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                        {{ str_replace('_', ' ', ucfirst($item->status)) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('owner.inventory.edit', $item) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                        <form action="{{ route('owner.inventory.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
            @if($inventory->hasPages())
                <div class="mt-4">
                    {{ $inventory->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function inventoryData() {
            return {
                searchQuery: '',
                sortBy: 'name',
                order: 'asc',
                
                initInventory() {
                    // Get URL parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    this.searchQuery = urlParams.get('search') || '';
                    this.sortBy = urlParams.get('sort_by') || 'name';
                    this.order = urlParams.get('order') || 'asc';
                },
                
                search() {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.sortBy) params.set('sort_by', this.sortBy);
                    if (this.order) params.set('order', this.order);
                    
                    window.location.href = `{{ route('owner.inventory.index') }}?${params.toString()}`;
                }
            };
        }
    </script>
</x-layouts::app.owner>
