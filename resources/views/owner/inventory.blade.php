<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="{
        inventory: {{ $inventory ?? '[]' }},
        search: '',
        statusFilter: '',
        showDeleteModal: false,
        deleteUrl: '',
        get filteredInventory() {
            return this.inventory.filter(item => {
                const matchesSearch = this.search === '' || 
                    item.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    item.sku.toLowerCase().includes(this.search.toLowerCase()) ||
                    (item.supplier && item.supplier.toLowerCase().includes(this.search.toLowerCase()));
                const matchesStatus = this.statusFilter === '' || item.status === this.statusFilter;
                return matchesSearch && matchesStatus;
            });
        }
    }">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <!-- Delete Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" x-transition>
            <div class="absolute inset-0 bg-black/70" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Delete Inventory Item</h3>
                            <p class="text-sm text-slate-500">Are you sure you want to delete this inventory item? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                            Cancel
                        </button>
                        <form :action="deleteUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Management - Excel Style -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
            <!-- Header -->
            <div class="bg-emerald-600 border-b border-emerald-700 px-4 py-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wide">Inventory Management</h3>
                    <p class="text-xs text-emerald-100">Showing <span x-text="filteredInventory.length"></span> of <span x-text="inventory.length"></span> items</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative min-w-0 sm:w-64">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            placeholder="Search..."
                            class="w-full pl-9 pr-4 py-1.5 text-xs border border-slate-300 bg-white rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                            x-model="search"
                        >
                    </div>
                    <select x-model="statusFilter" class="px-3 py-1.5 text-xs border border-slate-300 bg-white rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 sm:w-36">
                        <option value="">All Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                    <a href="{{ route('owner.inventory.create') }}" class="px-4 py-1.5 text-xs bg-white text-emerald-600 border border-emerald-600 rounded-lg hover:bg-emerald-50 font-medium">
                        + Add Item
                    </a>
                </div>
            </div>

            <!-- Excel-style Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-300">
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100 w-24">SKU</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100 w-32">Name</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100 w-48">Description</th>
                            <th class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-700 bg-slate-100 w-20">Quantity</th>
                            <th class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-700 bg-slate-100 w-20">Min Level</th>
                            <th class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-700 bg-slate-100 w-24">Unit Price</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100 w-16">Unit</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100 w-32">Supplier</th>
                            <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-700 bg-slate-100 w-28">Status</th>
                            <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-700 bg-slate-100 w-20">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in filteredInventory" :key="item.id">
                            <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-slate-50'" class="hover:bg-emerald-50 transition-colors">
                                <td class="border border-slate-300 px-3 py-2 text-slate-700 font-mono" x-text="item.sku"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-900 font-medium" x-text="item.name"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-600 max-w-xs truncate" :title="item.description" x-text="item.description || '-'"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-700 text-right font-mono" x-text="item.quantity"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-700 text-right font-mono" x-text="item.min_stock_level"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-700 text-right font-mono" x-text="'₱' + parseFloat(item.unit_price).toFixed(2)"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-600" x-text="item.unit"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-600" x-text="item.supplier || '-'"></td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-semibold" 
                                        :class="{
                                            'bg-green-100 text-green-800 border border-green-200': item.status === 'in_stock',
                                            'bg-yellow-100 text-yellow-800 border border-yellow-200': item.status === 'low_stock',
                                            'bg-red-100 text-red-800 border border-red-200': item.status === 'out_of_stock'
                                        }"
                                        x-text="item.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())">
                                    </span>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a :href="'{{ route('owner.inventory.edit', ':id') }}'.replace(':id', item.id)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button @click="deleteUrl = '{{ route('owner.inventory.destroy', ':id') }}'.replace(':id', item.id); showDeleteModal = true" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredInventory.length === 0">
                            <td colspan="10" class="border border-slate-300 px-3 py-8 text-center text-slate-500">
                                No inventory items found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app.owner>
