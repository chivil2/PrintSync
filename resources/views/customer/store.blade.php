@extends('layouts.app.customer')

@section('content')
<div class="max-w-[1280px] mx-auto px-6 py-8 lg:px-8 lg:py-8 space-y-8">
    <!-- Store Hero Banner -->
    <div class="store-hero">
        <div class="store-hero-bg"></div>
        <div class="store-hero-glow1"></div>
        <div class="store-hero-glow2"></div>
        <div class="store-hero-content">
            <span class="store-hero-chip">✨ Welcome back, {{ auth()->user()->first_name }}</span>
            <h1 class="store-hero-title">Print, repair, and support for your next project.</h1>
            <p class="store-hero-subtitle">Browse our printing and technical services</p>
            <div class="store-hero-stats">
                <div class="store-hero-stat">
                    <div class="store-hero-stat-icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Active Orders
                    </div>
                    <div class="store-hero-stat-value">{{ $activeOrders ?? 0 }}</div>
                </div>
                <div class="store-hero-stat">
                    <div class="store-hero-stat-icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Wishlist
                    </div>
                    <div class="store-hero-stat-value">{{ $wishlistCount ?? 0 }}</div>
                </div>
                <div class="store-hero-stat">
                    <div class="store-hero-stat-icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Gold Status
                    </div>
                    <div class="store-hero-stat-value">Gold</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="products-section">
        <!-- Products Main -->
        <div class="products-main" x-data="{ 
                activeTab: new URLSearchParams(window.location.search).get('tab') || 'printing', 
                search: '',
                allPrintingServices: {{ $printingServices->toJson() }},
                allTechnicalServices: {{ $technicalServices->toJson() }},
                printingPage: 1,
                technicalPage: 1,
                allPage: 1,
                itemsPerPage: 3,
                get filteredPrinting() {
                    if (!this.search) return this.allPrintingServices;
                    const searchLower = this.search.toLowerCase();
                    return this.allPrintingServices.filter(s => 
                        s.name.toLowerCase().includes(searchLower)
                    );
                },
                get filteredTechnical() {
                    if (!this.search) return this.allTechnicalServices;
                    const searchLower = this.search.toLowerCase();
                    return this.allTechnicalServices.filter(s => 
                        s.name.toLowerCase().includes(searchLower)
                    );
                },
                get filteredAll() {
                    const all = [...this.allPrintingServices.map(s => ({...s, type: 'printing'})), ...this.allTechnicalServices.map(s => ({...s, type: 'technical'}))];
                    if (!this.search) return all;
                    const searchLower = this.search.toLowerCase();
                    return all.filter(s => 
                        s.name.toLowerCase().includes(searchLower)
                    );
                },
                get paginatedPrinting() {
                    const start = (this.printingPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredPrinting.slice(start, end);
                },
                get paginatedTechnical() {
                    const start = (this.technicalPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredTechnical.slice(start, end);
                },
                get paginatedAll() {
                    const start = (this.allPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredAll.slice(start, end);
                },
                get printingTotalPages() {
                    return Math.ceil(this.filteredPrinting.length / this.itemsPerPage);
                },
                get technicalTotalPages() {
                    return Math.ceil(this.filteredTechnical.length / this.itemsPerPage);
                },
                get allTotalPages() {
                    return Math.ceil(this.filteredAll.length / this.itemsPerPage);
                },
                get printingPageNumbers() {
                    const pages = [];
                    for (let i = 1; i <= this.printingTotalPages; i++) {
                        pages.push(i);
                    }
                    return pages;
                },
                get technicalPageNumbers() {
                    const pages = [];
                    for (let i = 1; i <= this.technicalTotalPages; i++) {
                        pages.push(i);
                    }
                    return pages;
                },
                get allPageNumbers() {
                    const pages = [];
                    for (let i = 1; i <= this.allTotalPages; i++) {
                        pages.push(i);
                    }
                    return pages;
                }
            }" x-init="$watch('activeTab', (val) => { $dispatch('modal-close'); const url = new URL(window.location.href); url.searchParams.set('tab', val); window.history.replaceState({}, '', url) }); $watch('search', () => { printingPage = 1; technicalPage = 1; allPage = 1 })">
            <div class="products-header">
                <h2 class="products-title">Browse Products & Services</h2>
                <div class="products-search">
                    <input 
                        type="text" 
                        placeholder="Search services..."
                        x-model="search"
                    >
                    <button>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Services Section with Tabs -->
                <!-- Tab Navigation -->
                <div class="flex gap-4 border-b border-gray-200 mb-8">
                    <button
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'text-[#F47C3C] border-[#F47C3C]' : 'text-gray-500 border-transparent hover:text-gray-700'"
                        class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        All Services
                    </button>
                    <button
                        @click="activeTab = 'printing'"
                        :class="activeTab === 'printing' ? 'text-[#F47C3C] border-[#F47C3C]' : 'text-gray-500 border-transparent hover:text-gray-700'"
                        class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Printing Services
                    </button>
                    <button
                        @click="activeTab = 'technical'"
                        :class="activeTab === 'technical' ? 'text-[#4A6CF7] border-[#4A6CF7]' : 'text-gray-500 border-transparent hover:text-gray-700'"
                        class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Technical Services
                    </button>
                </div>
                <div class="products-grid">
                    <div x-show="activeTab === 'all'" x-transition class="contents">
                        <template x-for="service in paginatedAll" :key="service.id">
                            <div class="product-card" @click="$dispatch('open-modal', { service: service, type: service.type })">
                                <div class="product-img-wrap" :class="service.type === 'technical' ? 'service' : ''">
                                    <span class="product-cat-tag" :class="service.type === 'printing' ? 'product-tag' : 'service-tag'" x-text="service.type === 'printing' ? 'Printing' : 'Service'"></span>
                                    <div class="aspect-square flex items-center justify-center">
                                        <svg class="w-16 h-16" :class="service.type === 'printing' ? 'text-[#F47C3C]' : 'text-[#4A6CF7]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path x-show="service.type === 'printing'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            <path x-show="service.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path x-show="service.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name" x-text="service.name"></h3>
                                    <p class="product-price" :class="service.type === 'technical' ? 'service' : ''" x-text="'₱' + parseFloat(service.price).toFixed(2)"></p>
                                    <div class="product-actions">
                                        <button class="product-btn-primary" x-text="service.type === 'printing' ? 'Order Now' : 'Book Now'"></button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="activeTab === 'printing'" x-transition class="contents">
                        <template x-for="service in paginatedPrinting" :key="service.id">
                            <div class="product-card" @click="$dispatch('open-modal', { service: service, type: 'printing' })">
                                <div class="product-img-wrap">
                                    <span class="product-cat-tag product-tag">Printing</span>
                                    <div class="aspect-square flex items-center justify-center">
                                        <svg class="w-16 h-16 text-[#F47C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name" x-text="service.name"></h3>
                                    <p class="product-price" x-text="'₱' + parseFloat(service.price).toFixed(2)"></p>
                                    <div class="product-actions">
                                        <button class="product-btn-primary">Order Now</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="activeTab === 'technical'" x-transition class="contents">
                        <template x-for="service in paginatedTechnical" :key="service.id">
                            <div class="product-card" @click="$dispatch('open-modal', { service: service, type: 'technical' })">
                                <div class="product-img-wrap service">
                                    <span class="product-cat-tag service-tag">Service</span>
                                    <div class="aspect-square flex items-center justify-center">
                                        <svg class="w-16 h-16 text-[#4A6CF7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name" x-text="service.name"></h3>
                                    <p class="product-price service" x-text="'₱' + parseFloat(service.price).toFixed(2)"></p>
                                    <div class="product-actions">
                                        <button class="product-btn-primary">Book Now</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="activeTab === 'all'" class="pagination">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            Showing 
                            <span class="font-semibold text-gray-900" x-text="(allPage - 1) * itemsPerPage + 1"></span> 
                            to 
                            <span class="font-semibold text-gray-900" x-text="Math.min(allPage * itemsPerPage, filteredAll.length)"></span> 
                            of 
                            <span class="font-semibold text-gray-900" x-text="filteredAll.length"></span> 
                            results
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                @click="allPage > 1 && allPage--"
                                :disabled="allPage === 1"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Previous page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <template x-for="page in allPageNumbers" :key="page">
                                <button 
                                    @click="allPage = page"
                                    :class="allPage === page ? 'bg-[#F47C3C] text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 font-medium'"
                                    class="flex items-center justify-center w-10 h-10 rounded-lg transition-colors"
                                    x-text="page">
                                </button>
                            </template>
                            <button 
                                @click="allPage < allTotalPages && allPage++"
                                :disabled="allPage === allTotalPages"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Next page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </nav>
                </div>
                <div x-show="activeTab === 'printing'" class="pagination">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            Showing 
                            <span class="font-semibold text-gray-900" x-text="(printingPage - 1) * itemsPerPage + 1"></span> 
                            to 
                            <span class="font-semibold text-gray-900" x-text="Math.min(printingPage * itemsPerPage, filteredPrinting.length)"></span> 
                            of 
                            <span class="font-semibold text-gray-900" x-text="filteredPrinting.length"></span> 
                            results
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                @click="printingPage > 1 && printingPage--"
                                :disabled="printingPage === 1"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Previous page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <template x-for="page in printingPageNumbers" :key="page">
                                <button 
                                    @click="printingPage = page"
                                    :class="printingPage === page ? 'bg-[#F47C3C] text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 font-medium'"
                                    class="flex items-center justify-center w-10 h-10 rounded-lg transition-colors"
                                    x-text="page">
                                </button>
                            </template>
                            <button 
                                @click="printingPage < printingTotalPages && printingPage++"
                                :disabled="printingPage === printingTotalPages"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Next page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </nav>
                </div>
                <div x-show="activeTab === 'technical'" class="pagination">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            Showing 
                            <span class="font-semibold text-gray-900" x-text="(technicalPage - 1) * itemsPerPage + 1"></span> 
                            to 
                            <span class="font-semibold text-gray-900" x-text="Math.min(technicalPage * itemsPerPage, filteredTechnical.length)"></span> 
                            of 
                            <span class="font-semibold text-gray-900" x-text="filteredTechnical.length"></span> 
                            results
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                @click="technicalPage > 1 && technicalPage--"
                                :disabled="technicalPage === 1"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Previous page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <template x-for="page in technicalPageNumbers" :key="page">
                                <button 
                                    @click="technicalPage = page"
                                    :class="technicalPage === page ? 'bg-[#F47C3C] text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 font-medium'"
                                    class="flex items-center justify-center w-10 h-10 rounded-lg transition-colors"
                                    x-text="page">
                                </button>
                            </template>
                            <button 
                                @click="technicalPage < technicalTotalPages && technicalPage++"
                                :disabled="technicalPage === technicalTotalPages"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-600 hover:border-[#F47C3C] hover:text-[#F47C3C] hover:bg-orange-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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

<!-- Service Modal Component -->
<x-service-modal />
@endsection
