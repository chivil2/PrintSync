<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="servicesData()" x-init="initServices()">
        @if(session('success'))
            <x-printsync-toast :message="session('success')" />
        @endif

        <!-- Delete Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center" x-transition>
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Delete Service</h3>
                            <p class="text-sm text-slate-500">Are you sure you want to delete this service? This action cannot be undone.</p>
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

        <!-- Services Management - Excel Style -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 border-b border-blue-700 px-4 py-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wide">Services Management</h3>
                    <p class="text-xs text-blue-100">Showing {{ $services->count() }} of {{ $services->total() }} services</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative min-w-0 sm:w-64">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            placeholder="Search..."
                            class="w-full pl-9 pr-4 py-1.5 text-xs border border-slate-300 bg-white rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            x-model="searchQuery"
                            @keyup.enter="search()"
                        >
                    </div>
                    <select x-model="serviceTypeFilter" @change="search()" class="px-3 py-1.5 text-xs border border-slate-300 bg-white rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:w-36">
                        <option value="">All Types</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('owner.services.create') }}" class="px-4 py-1.5 text-xs bg-white text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 font-medium">
                        + Add Service
                    </a>
                </div>
            </div>

            <!-- Excel-style Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-300">
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100">Image</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100">ID</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100">Name</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100">Type</th>
                            <th class="border border-slate-300 px-3 py-2 text-left font-semibold text-slate-700 bg-slate-100">Description</th>
                            <th class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-700 bg-slate-100">Price</th>
                            <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-700 bg-slate-100">Production Time</th>
                            <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-700 bg-slate-100">Status</th>
                            <th class="border border-slate-300 px-3 py-2 text-center font-semibold text-slate-700 bg-slate-100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $index => $service)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-blue-50 transition-colors">
                                <td class="border border-slate-300 px-3 py-2">
                                    @if($service->image)
                                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="w-12 h-12 object-cover rounded border border-slate-200">
                                    @else
                                        <div class="w-12 h-12 bg-slate-100 rounded border border-slate-200 flex items-center justify-center text-slate-400 text-xs">
                                            No Image
                                        </div>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-700 font-mono">{{ $service->id }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-900 font-medium">{{ $service->name }}</td>
                                <td class="border border-slate-300 px-3 py-2">
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-semibold {{ $service->service_type === 'printing' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-purple-100 text-purple-800 border border-purple-200' }}">
                                        {{ ucfirst($service->service_type) }}
                                    </span>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-600 max-w-xs truncate" title="{{ $service->description }}">{{ $service->description }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-700 text-right font-mono">₱{{ number_format($service->price, 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-600 text-center">{{ $service->production_time ?? 'N/A' }}d</td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-semibold {{ $service->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-slate-200 text-slate-700 border border-slate-300' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('owner.services.edit', ['id' => $service->id, 'serviceType' => $service->service_type]) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded hover:bg-blue-50 transition-colors">Edit</a>
                                        <button @click="confirmDelete({{ $service->id }}, '{{ $service->service_type }}')" class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded hover:bg-red-50 transition-colors">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer with Pagination -->
            <div class="bg-slate-50 border-t border-slate-300 px-4 py-2 flex items-center justify-between">
                <div class="text-xs text-slate-600">
                    Page {{ $services->currentPage() }} of {{ $services->lastPage() }}
                </div>
                @if($services->hasPages())
                    <div class="flex items-center gap-1">
                        @if($services->onFirstPage())
                            <span class="px-2 py-1 text-xs text-slate-400 border border-slate-200 bg-slate-100">Previous</span>
                        @else
                            <a href="{{ $services->previousPageUrl() }}" class="px-2 py-1 text-xs text-slate-700 border border-slate-300 bg-white hover:bg-slate-50">Previous</a>
                        @endif
                        
                        @foreach($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                            @if($page == $services->currentPage())
                                <span class="px-2 py-1 text-xs text-white bg-blue-600 border border-blue-600">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-2 py-1 text-xs text-slate-700 border border-slate-300 bg-white hover:bg-slate-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        
                        @if($services->hasMorePages())
                            <a href="{{ $services->nextPageUrl() }}" class="px-2 py-1 text-xs text-slate-700 border border-slate-300 bg-white hover:bg-slate-50">Next</a>
                        @else
                            <span class="px-2 py-1 text-xs text-slate-400 border border-slate-200 bg-slate-100">Next</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function servicesData() {
            return {
                searchQuery: '',
                serviceTypeFilter: '',
                showDeleteModal: false,
                deleteUrl: '',
                
                initServices() {
                    // Get URL parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    this.searchQuery = urlParams.get('search') || '';
                    this.serviceTypeFilter = urlParams.get('service_type') || '';
                },
                
                search() {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.serviceTypeFilter) params.set('service_type', this.serviceTypeFilter);
                    
                    window.location.href = `{{ route('owner.services.index') }}?${params.toString()}`;
                },

                confirmDelete(id, serviceType) {
                    this.deleteUrl = `{{ route('owner.services.destroy', ['id' => ':id', 'serviceType' => ':type']) }}`.replace(':id', id).replace(':type', serviceType);
                    this.showDeleteModal = true;
                }
            };
        }
    </script>
</x-layouts::app.owner>
