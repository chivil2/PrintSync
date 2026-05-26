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
            <div class="store-hero-search">
                <input type="text" placeholder="Search services, products...">
                <button>Search</button>
            </div>
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
        <!-- Category Sidebar -->
        <div class="category-sidebar">
            <div class="category-card">
                <h3 class="category-heading">Categories</h3>
                <div class="category-item active">
                    <span class="category-name">All</span>
                    <span class="category-count">34</span>
                </div>
                <div class="category-item">
                    <span class="category-name">Paper</span>
                    <span class="category-count">8</span>
                </div>
                <div class="category-item">
                    <span class="category-name">Marketing</span>
                    <span class="category-count">12</span>
                </div>
                <div class="category-item">
                    <span class="category-name">Signage</span>
                    <span class="category-count">6</span>
                </div>
                <div class="category-item">
                    <span class="category-name">Merchandise</span>
                    <span class="category-count">8</span>
                </div>
            </div>
        </div>

        <!-- Products Main -->
        <div class="products-main">
            <div class="products-header">
                <h2 class="products-title">Browse Products & Services</h2>
            </div>

            <!-- Services Section with Tabs -->
            <div x-data="{ activeTab: new URLSearchParams(window.location.search).get('tab') || 'printing' }" x-init="$watch('activeTab', (val) => { $dispatch('modal-close'); const url = new URL(window.location.href); url.searchParams.set('tab', val); window.history.replaceState({}, '', url) })">
                <!-- Tab Navigation -->
                <div class="flex gap-4 border-b border-gray-200 mb-8">
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
                    <div x-show="activeTab === 'printing'" x-transition class="contents">
                        @foreach($printingServices as $service)
                            <div class="product-card" @click="$dispatch('open-modal', { service: {{ $service->toJson() }}, type: 'printing' })">
                                <div class="product-img-wrap">
                                    <span class="product-cat-tag product-tag">Printing</span>
                                    <div class="aspect-square flex items-center justify-center">
                                        <svg class="w-16 h-16 text-[#F47C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">{{ $service->name }}</h3>
                                    <p class="product-price">₱{{ number_format($service->price, 2) }}</p>
                                    <div class="product-actions">
                                        <button class="product-btn-primary">Order Now</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div x-show="activeTab === 'technical'" x-transition class="contents">
                        @foreach($technicalServices as $service)
                            <div class="product-card" @click="$dispatch('open-modal', { service: {{ $service->toJson() }}, type: 'technical' })">
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
                                    <h3 class="product-name">{{ $service->name }}</h3>
                                    <span class="product-from-label">from</span>
                                    <p class="product-price service">₱{{ number_format($service->price, 2) }}</p>
                                    <p class="product-detail">{{ $service->description }}</p>
                                    <div class="product-actions">
                                        <button class="product-btn-primary">Book Now</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="activeTab === 'printing'" class="pagination">
                    {{ $printingServices->appends(['tab' => 'printing'])->links('vendor.pagination.store') }}
                </div>
                <div x-show="activeTab === 'technical'" class="pagination">
                    {{ $technicalServices->appends(['tab' => 'technical'])->links('vendor.pagination.store') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Modal Component -->
<x-service-modal />
@endsection
