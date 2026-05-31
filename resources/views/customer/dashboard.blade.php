@extends('layouts.app.customer')

@section('content')
<div class="max-w-[1280px] mx-auto px-5 py-6 lg:px-8 lg:py-7 space-y-6" x-data="clock()" x-init="startClock()">
    <!-- Welcome Banner -->
    <div class="welcome-banner-wrap">
        {{-- Clipped background layer (keeps border-radius + overflow:hidden) --}}
        <div class="welcome-banner">
            <div class="welcome-bg"></div>
            <div class="welcome-dots"></div>
            <div class="welcome-glow1"></div>
            <div class="welcome-glow2"></div>
            <div class="welcome-content flex items-center justify-between">
                <div>
                    <h1>Welcome back, {{ auth()->user()->first_name }}!</h1>
                    <p>Manage your printing and technical services from your personal dashboard.</p>
                </div>
                <div class="text-right">
                    <div class="text-white/80 text-sm" x-text="currentDate"></div>
                </div>
            </div>
        </div>
        {{-- Illustration sits outside the clipped banner, above it in z-index --}}
        <div class="welcome-illustration">
            <img src="{{ asset('images/girl_welcome.svg') }}" alt="" aria-hidden="true">
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <div class="quick-actions-header">
            <h2 class="quick-actions-title">Quick Actions</h2>
            <div class="quick-actions-accent"></div>
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('customer.store') }}" class="quick-action-btn">
                <div class="quick-action-icon orange">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <span class="quick-action-label">Browse Store</span>
            </a>

            <a href="{{ route('customer.orders') }}" class="quick-action-btn">
                <div class="quick-action-icon blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="quick-action-label">My Orders</span>
            </a>

            <a href="{{ route('customer.profile') }}" class="quick-action-btn">
                <div class="quick-action-icon purple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="quick-action-label">Profile</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4">
        <a href="{{ route('customer.orders') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
            <div class="bg-blue-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $totalOrders }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Total Orders</div>
        </a>

        <a href="{{ route('customer.orders') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
            <div class="bg-emerald-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $completedOrders }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Completed</div>
        </a>

        <a href="{{ route('customer.orders') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
            <div class="bg-orange-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-slate-900">₱{{ number_format($totalSpent, 2) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Total Spent</div>
        </a>

        <a href="{{ route('customer.orders') }}" class="bg-white border border-slate-100 p-5 rounded-3xl card-shadow transition-all cursor-pointer hover:shadow-lg">
            <div class="bg-purple-50 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-sm text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $pendingOrders }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Pending Orders</div>
        </a>
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
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="order-status-badge {{ $order->status === 'completed' ? 'completed' : ($order->status === 'in_progress' ? 'processing' : 'pending') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    @if($order->quote && $order->quote->payment_status)
                                        @php
                                            $paymentStatusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'paid' => 'bg-emerald-100 text-emerald-700',
                                                'partial' => 'bg-blue-100 text-blue-700',
                                                'overdue' => 'bg-red-100 text-red-700',
                                            ];
                                            $paymentStatusColor = $paymentStatusColors[$order->quote->payment_status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $paymentStatusColor }}">
                                            {{ ucfirst($order->quote->payment_status) }}
                                        </span>
                                    @endif
                                </div>
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
                                <p class="order-amount">
                                    @if($order->quote?->total)
                                        ₱{{ number_format($order->quote->total, 2) }}
                                    @elseif($order->service?->price)
                                        ₱{{ number_format($order->service->price * ($order->quote?->lineItems->first()?->quantity ?? 1), 2) }}
                                    @else
                                        No Price Yet
                                    @endif
                                </p>
                                @if($order->status === 'pending')
                                    <span class="order-payment-badge">Pending payment</span>
                                @endif
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

<script>
function clock() {
    return {
        currentTime: '',
        currentDate: '',

        startClock() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },

        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            this.currentDate = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }
}
</script>
@endsection
