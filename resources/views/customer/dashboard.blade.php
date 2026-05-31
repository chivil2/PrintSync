@extends('layouts.app.customer')

@section('content')
<div class="max-w-[1280px] mx-auto px-6 py-8 lg:px-8 lg:py-8 space-y-8">
    <!-- Welcome Banner -->
    <div class="welcome-banner-wrap">
        {{-- Clipped background layer (keeps border-radius + overflow:hidden) --}}
        <div class="welcome-banner">
            <div class="welcome-bg"></div>
            <div class="welcome-dots"></div>
            <div class="welcome-glow1"></div>
            <div class="welcome-glow2"></div>
            <div class="welcome-content">
                <h1>Welcome back, {{ auth()->user()->first_name }}!</h1>
                <p>Manage your printing and technical services from your personal dashboard.</p>
            </div>
        </div>
        {{-- Illustration sits outside the clipped banner, above it in z-index --}}
        <div class="welcome-illustration">
            <img src="{{ asset('images/girl_welcome.svg') }}" alt="" aria-hidden="true">
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-inner">
                <div class="stat-icon orange">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="stat-label">Total Orders</p>
                <p class="stat-value">{{ $totalOrders }}</p>
                <p class="stat-trend">All time</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-inner">
                <div class="stat-icon green">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="stat-label">Completed</p>
                <p class="stat-value">{{ $completedOrders }}</p>
                <p class="stat-trend">Successfully delivered</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-inner">
                <div class="stat-icon blue">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="stat-label">Total Spent</p>
                <p class="stat-value">₱{{ number_format($totalSpent, 2) }}</p>
                <p class="stat-trend">Lifetime purchases</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-card">
        <div class="section-header">
            <h2 class="section-title">Quick Actions</h2>
            <div class="section-accent"></div>
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('customer.store') }}" class="quick-action-btn">
                <div class="quick-action-icon orange">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="quick-action-label">Browse Store</p>
                <p class="quick-action-desc">View services</p>
                <svg class="w-4 h-4 quick-action-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('customer.orders') }}" class="quick-action-btn">
                <div class="quick-action-icon blue">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="quick-action-label">My Orders</p>
                <p class="quick-action-desc">Track orders</p>
                <svg class="w-4 h-4 quick-action-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('customer.profile') }}" class="quick-action-btn">
                <div class="quick-action-icon purple">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <p class="quick-action-label">Profile</p>
                <p class="quick-action-desc">Update info</p>
                <svg class="w-4 h-4 quick-action-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>


        </div>
    </div>

    <!-- Recent Orders -->
    <div class="recent-orders-card">
        <div class="recent-orders-header">
            <h2 class="recent-orders-title">Recent Orders</h2>
            <a href="{{ route('customer.orders') }}" class="view-all-link">
                View All →
            </a>
        </div>
        <div class="recent-orders-body">
            @if($recentOrders->count() > 0)
                @foreach($recentOrders as $order)
                    <div class="order-card">
                        <div class="order-card-inner">
                            <div class="order-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span class="order-status-dot"></span>
                            </div>
                            <div class="order-info">
                                <p class="order-name">{{ $order->service->name ?? 'Service' }}</p>
                                <span class="order-status-badge {{ $order->status === 'completed' ? 'completed' : ($order->status === 'in_progress' ? 'processing' : 'pending') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <div class="order-meta">
                                    <span>Order #{{ $order->id }}</span>
                                    <span>•</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="order-right">
                                <p class="order-amount">₱{{ number_format($order->price, 2) }}</p>
                                <p class="order-pending-label">{{ $order->status === 'pending' ? 'Pending payment' : '' }}</p>
                                <a href="{{ route('customer.orders.show', $order) }}" class="order-details-btn">Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="orders-divider">
                    <span class="orders-divider-text">You have {{ $recentOrders->count() }} active order{{ $recentOrders->count() > 1 ? 's' : '' }}</span>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-zinc-500">No orders yet. <a href="{{ route('customer.store') }}" class="text-[#F47C3C] hover:text-[#E8654A] font-medium">Browse our services</a> to get started!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
