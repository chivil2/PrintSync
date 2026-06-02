@extends('layouts.app.customer')

@section('content')
<div class="max-w-[1280px] mx-auto px-6 py-8 lg:px-8 lg:py-8 space-y-8">
    <!-- Store Hero Banner -->
    <div class="relative p-8 sm:p-10 mb-8 rounded-2xl overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center blur-[3px]" style="background-image: url('{{ asset('images/store-background-banner.jpg') }}');"></div>
        <div class="absolute inset-0 animate-gradient opacity-70"></div>
        <div class="absolute inset-0 animate-pulse-slow" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px; opacity: 0.4;"></div>
        <div class="relative z-10">
            <h1 class="text-4xl sm:text-5xl font-bold mb-2 text-white">Print, repair, and support<br class="hidden sm:block" /> for your next project.</h1>
            <p class="text-lg max-w-md text-white">Browse our printing and technical services. Quality guaranteed.</p>
        </div>
    </div>

    <style>
        @keyframes pulse-slow {
            0%, 100% {
                opacity: 0.4;
            }
            50% {
                opacity: 0.6;
            }
        }
        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        @keyframes gradient-animation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .animate-gradient {
            background: linear-gradient(270deg, #f97316, #3b82f6, #f97316);
            background-size: 200% 200%;
            animation: gradient-animation 6s ease infinite;
        }
    </style>

    <!-- Products Section -->
    <div class="flex flex-col lg:flex-row gap-8" x-data="{ 
        activeCategory: 'all',
        sortBy: 'price-low',
        search: '',
        allPrintingServices: {{ $printingServices->toJson() }},
        allTechnicalServices: {{ $technicalServices->toJson() }},
        page: 1,
        itemsPerPage: 6,
        get filteredServices() {
            let services = [];
            if (this.activeCategory === 'all') {
                services = [...this.allPrintingServices.map(s => ({...s, type: 'printing'})), ...this.allTechnicalServices.map(s => ({...s, type: 'technical'}))];
            } else if (this.activeCategory === 'printing') {
                services = this.allPrintingServices.map(s => ({...s, type: 'printing'}));
            } else {
                services = this.allTechnicalServices.map(s => ({...s, type: 'technical'}));
            }

            if (this.search) {
                const searchLower = this.search.toLowerCase();
                services = services.filter(s =>
                    s.name.toLowerCase().includes(searchLower) || s.description.toLowerCase().includes(searchLower)
                );
            }

            if (this.sortBy === 'price-low') {
                services.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            } else if (this.sortBy === 'price-high') {
                services.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            }

            return services;
        },
        get paginatedServices() {
            const start = (this.page - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredServices.slice(start, end);
        },
        get totalPages() {
            return Math.ceil(this.filteredServices.length / this.itemsPerPage);
        },
        get pageNumbers() {
            const pages = [];
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
            return pages;
        }
    }" x-init="$watch('page', () => { window.scrollTo({ top: 0, behavior: 'smooth' }) }); $watch('activeCategory', () => { page = 1 }); $watch('sortBy', () => { page = 1 })">
        <!-- Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Categories</h3>
                <div class="space-y-1">
                    <button
                        @click="activeCategory = 'all'"
                        :class="activeCategory === 'all' ? 'bg-[#E8743B]/10 text-[#E8743B]' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
                    >
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            All Services
                        </span>
                        <span :class="activeCategory === 'all' ? 'bg-[#E8743B]/20 text-[#E8743B]' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-0.5 rounded-md">
                            {{ $printingServices->count() + $technicalServices->count() }}
                        </span>
                    </button>
                    <button
                        @click="activeCategory = 'printing'"
                        :class="activeCategory === 'printing' ? 'bg-[#E8743B]/10 text-[#E8743B]' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
                    >
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Printing Services
                        </span>
                        <span :class="activeCategory === 'printing' ? 'bg-[#E8743B]/20 text-[#E8743B]' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-0.5 rounded-md">
                            {{ $printingServices->count() }}
                        </span>
                    </button>
                    <button
                        @click="activeCategory = 'technical'"
                        :class="activeCategory === 'technical' ? 'bg-[#E8743B]/10 text-[#E8743B]' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
                    >
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Technical Services
                        </span>
                        <span :class="activeCategory === 'technical' ? 'bg-[#E8743B]/20 text-[#E8743B]' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-0.5 rounded-md">
                            {{ $technicalServices->count() }}
                        </span>
                    </button>
                </div>

                <div class="border-t border-gray-100 my-4"></div>

                <h3 class="text-sm font-semibold text-gray-900 mb-3">Sort By</h3>
                <div class="space-y-1">
                    <button
                        @click="sortBy = 'price-low'"
                        :class="sortBy === 'price-low' ? 'text-[#E8743B] font-medium bg-[#E8743B]/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="w-full text-left px-3 py-2 rounded-xl text-sm transition-all"
                    >
                        Price: Low to High
                    </button>
                    <button
                        @click="sortBy = 'price-high'"
                        :class="sortBy === 'price-high' ? 'text-[#E8743B] font-medium bg-[#E8743B]/5' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="w-full text-left px-3 py-2 rounded-xl text-sm transition-all"
                    >
                        Price: High to Low
                    </button>

                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
                <!-- Search & Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900" x-text="activeCategory === 'all' ? 'All Services' : activeCategory === 'printing' ? 'Printing Services' : 'Technical Services'"></h2>
                        <p class="text-sm text-gray-400 mt-0.5" x-text="filteredServices.length + ' services available'"></p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text"
                            placeholder="Search services..."
                            x-model="search"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#E8743B]/20 focus:border-[#E8743B]/30 transition-all"
                        >
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    <template x-for="service in paginatedServices" :key="service.id">
                        <div
                            class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300 cursor-pointer"
                            @click="service.type === 'technical' ? $dispatch('open-tech-modal', { service: service, type: service.type }) : $dispatch('open-modal', { service: service, type: service.type })"
                        >
                            <!-- Image Area -->
                            <div class="relative h-52 flex items-center justify-center bg-gray-100">
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-900 text-white" x-text="service.type === 'printing' ? 'Printing' : 'Technical'">
                                </span>

                                <template x-if="service.image">
                                    <img :src="'/storage/' + service.image" :alt="service.name" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                </template>
                                <template x-if="!service.image">
                                    <div class="flex items-center justify-center w-full h-full" :class="service.type === 'printing' ? 'bg-gradient-to-br from-orange-50 to-amber-50' : 'bg-gradient-to-br from-blue-50 to-indigo-50'">
                                        <svg class="w-14 h-14" :class="service.type === 'printing' ? 'text-orange-300' : 'text-blue-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="service.type === 'printing'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            <path x-show="service.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path x-show="service.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                
                                <h3 class="font-semibold text-gray-900 mb-1" x-text="service.name"></h3>
                                <p class="text-xs text-gray-400 mb-4 line-clamp-1" x-text="service.description"></p>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900" x-text="'₱' + parseFloat(service.price).toFixed(2)"></p>
                                        <p class="text-[10px] text-gray-400">Starting price</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-4">

                                    <button
                                        @click.stop="service.type === 'technical' ? $dispatch('open-tech-modal', { service: service, type: service.type }) : $dispatch('open-modal', { service: service, type: service.type })"
                                        class="px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-200 text-gray-700 hover:border-[#E8743B] hover:text-[#E8743B] transition-all"
                                        x-text="service.type === 'printing' ? 'Order' : 'Book'"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="filteredServices.length === 0" class="text-center py-20">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">No services found</p>
                    <p class="text-sm text-gray-400 mt-1">Try adjusting your search or filters</p>
                </div>

                <!-- Pagination -->
                <div x-show="filteredServices.length > 0" class="mt-8">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            Showing 
                            <span class="font-semibold text-gray-900" x-text="(page - 1) * itemsPerPage + 1"></span> 
                            to 
                            <span class="font-semibold text-gray-900" x-text="Math.min(page * itemsPerPage, filteredServices.length)"></span> 
                            of 
                            <span class="font-semibold text-gray-900" x-text="filteredServices.length"></span> 
                            results
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                @click="page > 1 && page--"
                                :disabled="page === 1"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#E8743B] hover:text-[#E8743B] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Previous page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <template x-for="p in pageNumbers" :key="p">
                                <button 
                                    @click="page = p"
                                    :class="p === page ? 'bg-[#E8743B] text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:border-[#E8743B] hover:text-[#E8743B] hover:bg-orange-50 font-medium'"
                                    class="flex items-center justify-center w-10 h-10 rounded-lg transition-colors"
                                    x-text="p">
                                </button>
                            </template>
                            <button 
                                @click="page < totalPages && page++"
                                :disabled="page === totalPages"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#E8743B] hover:text-[#E8743B] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Next page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Modal Components -->
<x-service-modal />
<x-technical-service-modal />
@endsection
