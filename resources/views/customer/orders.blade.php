@extends('layouts.app.customer')

@section('content')
<div class="max-w-[1280px] mx-auto px-6 py-8 lg:px-8 lg:py-8 space-y-8" x-data="{ 
    activeStatus: 'all',
    orders: {{ $orders->map(function($order) {
        return [
            'id' => $order->id,
            'name' => $order->name,
            'type' => $order->type,
            'status' => $order->status,
            'description' => $order->description,
            'deadline' => $order->deadline ? $order->deadline->format('M d, Y') : null,
            'created_at' => $order->created_at->format('M d, Y'),
            'notes' => $order->notes,
            'employee' => $order->employee ? $order->employee->first_name . ' ' . $order->employee->last_name : null,
            'quote_status' => $order->quote ? $order->quote->status : null,
            'invoice_path' => $order->invoice_path,
        ];
    })->toJson() }},
    get filteredOrders() {
        if (this.activeStatus === 'all') return this.orders;
        return this.orders.filter(o => o.status === this.activeStatus);
    },
    getStatusColor(status) {
        const colors = {
            'pending': 'bg-orange-50 text-orange-700',
            'in_progress': 'bg-blue-50 text-blue-700',
            'completed': 'bg-green-50 text-green-700',
            'cancelled': 'bg-red-50 text-red-700',
        };
        return colors[status] || 'bg-gray-50 text-gray-700';
    },
    getStatusLabel(status) {
        const labels = {
            'pending': 'Pending Review',
            'in_progress': 'In Progress',
            'completed': 'Completed',
            'cancelled': 'Cancelled',
        };
        return labels[status] || status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    },
    getOrderId(id) {
        const year = new Date().getFullYear();
        const paddedId = String(id).padStart(3, '0');
        return `ORD-${year}-${paddedId}`;
    }
}">
    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Order History</h1>
            <p class="mt-2 text-zinc-600">Track your service requests and orders</p>
        </div>
        <a href="{{ route('customer.store') }}" class="bg-gray-900 hover:bg-black text-white font-bold text-sm rounded-lg px-5 py-2.5 flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 5v14M5 12h14"/>
            </svg>
            New Order
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 font-semibold text-sm">Total Orders</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18v16H3z M16 2v4 M8 2v4 M3 10h18"/>
                </svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900" x-text="orders.length"></div>
            <div class="text-xs text-emerald-600 font-semibold mt-1">All time</div>
        </div>

        <div class="bg-white rounded-xl border border-blue-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 font-semibold text-sm">In Progress</span>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900" x-text="orders.filter(o => o.status === 'in_progress').length"></div>
            <div class="text-xs text-gray-500 mt-1">Active now</div>
        </div>

        <div class="bg-white rounded-xl border border-orange-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 font-semibold text-sm">Pending</span>
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900" x-text="orders.filter(o => o.status === 'pending').length"></div>
            <div class="text-xs text-gray-500 mt-1">Awaiting review</div>
        </div>

        <div class="bg-white rounded-xl border border-green-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 font-semibold text-sm">Completed</span>
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900" x-text="orders.filter(o => o.status === 'completed').length"></div>
            <div class="text-xs text-gray-500 mt-1">This month</div>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex gap-2 border-b border-gray-200">
        <button
            @click="activeStatus = 'all'"
            :class="activeStatus === 'all' ? 'text-[#E8743B] border-[#E8743B]' : 'text-gray-500 border-transparent hover:text-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
        >
            All
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full" x-text="orders.length"></span>
        </button>
        <button
            @click="activeStatus = 'pending'"
            :class="activeStatus === 'pending' ? 'text-[#E8743B] border-[#E8743B]' : 'text-gray-500 border-transparent hover:text-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
        >
            Pending
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full" x-text="orders.filter(o => o.status === 'pending').length"></span>
        </button>
        <button
            @click="activeStatus = 'in_progress'"
            :class="activeStatus === 'in_progress' ? 'text-[#E8743B] border-[#E8743B]' : 'text-gray-500 border-transparent hover:text-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
        >
            In Progress
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full" x-text="orders.filter(o => o.status === 'in_progress').length"></span>
        </button>
        <button
            @click="activeStatus = 'completed'"
            :class="activeStatus === 'completed' ? 'text-[#E8743B] border-[#E8743B]' : 'text-gray-500 border-transparent hover:text-gray-700'"
            class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
        >
            Completed
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full" x-text="orders.filter(o => o.status === 'completed').length"></span>
        </button>
    </div>

    <!-- Empty State -->
    <div x-show="orders.length === 0" class="bg-white rounded-2xl border border-gray-100 text-center py-20">
        <div class="w-20 h-20 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-[#E8743B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders yet</h3>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">Start by browsing our services and requesting one for your next project.</p>
        <a href="{{ route('customer.store') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#E8743B] hover:bg-[#d66532] text-white font-medium rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Browse Services
        </a>
    </div>

    <!-- Filtered Empty State -->
    <div x-show="orders.length > 0 && filteredOrders.length === 0" class="bg-white rounded-2xl border border-gray-100 text-center py-20">
        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders in this category</h3>
        <p class="text-gray-500">Try selecting a different status filter</p>
    </div>

    <!-- Orders List -->
    <div x-show="filteredOrders.length > 0" class="flex flex-col gap-3">
        <template x-for="order in filteredOrders" :key="order.id">
            <a :href="'/customer/orders/' + order.id" class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-4 hover:shadow-md hover:border-gray-200 transition-all">
                <!-- Thumbnail -->
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0" :class="order.type === 'printing' ? 'bg-orange-50' : 'bg-blue-50'">
                    <svg class="w-8 h-8" :class="order.type === 'printing' ? 'text-orange-300' : 'text-blue-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="order.type === 'printing'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        <path x-show="order.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path x-show="order.type === 'technical'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>

                <!-- Order Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="font-extrabold text-[15px] text-gray-900" x-text="order.name"></span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" :class="getStatusColor(order.status)" x-text="getStatusLabel(order.status)"></span>
                        <span class="text-gray-400 text-xs">•</span>
                        <span class="text-gray-500 text-xs font-semibold" x-text="getOrderId(order.id)"></span>
                    </div>
                    <p x-show="order.description" class="text-gray-500 text-sm mb-1 line-clamp-1" x-text="order.description"></p>
                    <div class="flex items-center gap-3 text-xs text-gray-500 flex-wrap">
                        <span>Service: <span class="font-bold text-gray-700" x-text="order.type"></span></span>
                        <span>Requested: <span class="font-semibold text-gray-600" x-text="order.created_at"></span></span>
                        <span x-show="order.deadline">Deadline: <span class="font-semibold text-gray-600" x-text="order.deadline"></span></span>
                        <span x-show="order.employee">Assigned to: <span class="font-semibold text-gray-600" x-text="order.employee"></span></span>
                    </div>
                </div>

                <!-- Price & Items -->
                <div class="text-right text-sm flex-shrink-0">
                    <div class="text-gray-500"><span class="font-semibold">1</span> item</div>
                    <div class="font-extrabold text-[15px] text-gray-900">₱500</div>
                </div>
            </a>
        </template>
    </div>
</div>
@endsection
