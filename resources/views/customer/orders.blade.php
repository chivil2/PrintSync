@extends('layouts.app.customer')

@section('content')
<div class="max-w-[1280px] mx-auto px-8 py-7 space-y-6" x-data="{ 
    activeStatus: 'all',
    preferredPaymentMethod: '{{ $customer->preferred_payment_method ?? '' }}',
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
            'payment_status' => $order->quote ? $order->quote->payment_status : null,
            'invoice_path' => $order->invoice_path,
            'line_items_count' => $order->quote ? $order->quote->lineItems->count() : 0,
            'quote_total' => $order->quote?->total,
            'tech_priority' => $order->technical_details['priority'] ?? null,
            'tech_preferred_at' => isset($order->technical_details['preferred_at']) ? \Carbon\Carbon::parse($order->technical_details['preferred_at'])->format('M d, Y g:i A') : null,
            'tech_contact' => $order->technical_details['contact_preference'] ?? null,
        ];
    })->toJson() }},
    get filteredOrders() {
        if (this.activeStatus === 'all') return this.orders;
        return this.orders.filter(o => o.status === this.activeStatus);
    },
    getStatusColor(status) {
        const colors = {
            'pending': 'bg-amber-100 text-amber-700',
            'in_progress': 'bg-blue-100 text-blue-700',
            'completed': 'bg-emerald-100 text-emerald-700',
            'cancelled': 'bg-red-100 text-red-700',
        };
        return colors[status] || 'bg-gray-100 text-gray-700';
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
    getPaymentStatusColor(status) {
        const colors = {
            'pending': 'bg-amber-100 text-amber-700',
            'paid': 'bg-emerald-100 text-emerald-700',
            'partial': 'bg-blue-100 text-blue-700',
            'overdue': 'bg-red-100 text-red-700',
        };
        return colors[status] || 'bg-gray-100 text-gray-700';
    },
    getPaymentStatusLabel(status) {
        const labels = {
            'pending': 'Pending',
            'paid': 'Paid',
            'partial': 'Partial',
            'overdue': 'Overdue',
        };
        return labels[status] || status ? status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A';
    },
    getOrderId(id) {
        const year = new Date().getFullYear();
        const paddedId = String(id).padStart(3, '0');
        return `ORD-${year}-${paddedId}`;
    },
    getPriorityLabel(priority) {
        const labels = {
            'standard': 'Standard',
            'urgent': 'Urgent',
            'emergency': 'Emergency',
        };
        return labels[priority] || priority;
    },
    getPriorityColor(priority) {
        const colors = {
            'standard': 'bg-sky-100 text-sky-700',
            'urgent': 'bg-amber-100 text-amber-700',
            'emergency': 'bg-rose-100 text-rose-700',
        };
        return colors[priority] || 'bg-gray-100 text-gray-700';
    },
    getContactLabel(contact) {
        const labels = {
            'phone': 'Phone',
            'email': 'Email',
            'sms': 'SMS',
        };
        return labels[contact] || contact;
    }
}">
    <!-- Header -->
    <div class="flex items-start justify-between mb-2">
        <div>
            <h1 class="text-[32px] font-extrabold leading-tight text-zinc-900">Order History</h1>
            <p class="text-zinc-500 text-base mt-1">Track your service requests and orders</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-[14px] border border-gray-200 px-5 py-[18px]">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 font-semibold text-base">Total Orders</span>
                <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18v16H3z M16 2v4 M8 2v4 M3 10h18"/>
                </svg>
            </div>
            <div class="text-4xl font-extrabold mt-1 text-zinc-900" x-text="orders.length"></div>
            <div class="text-sm text-emerald-600 font-semibold mt-2">All time</div>
        </div>

        <div class="bg-white rounded-[14px] border border-blue-100 px-5 py-[18px]">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 font-semibold text-base">In Progress</span>
                <svg class="w-[18px] h-[18px] text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-4xl font-extrabold mt-1 text-zinc-900" x-text="orders.filter(o => o.status === 'in_progress').length"></div>
            <div class="text-sm text-gray-500 mt-2">Active now</div>
        </div>

        <div class="bg-white rounded-[14px] border border-orange-100 px-5 py-[18px]">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 font-semibold text-base">Pending</span>
                <svg class="w-[18px] h-[18px] text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-4xl font-extrabold mt-1 text-zinc-900" x-text="orders.filter(o => o.status === 'pending').length"></div>
            <div class="text-sm text-gray-500 mt-2">Awaiting review</div>
        </div>

        <div class="bg-white rounded-[14px] border border-green-100 px-5 py-[18px]">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 font-semibold text-base">Completed</span>
                <svg class="w-[18px] h-[18px] text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-4xl font-extrabold mt-1 text-zinc-900" x-text="orders.filter(o => o.status === 'completed').length"></div>
            <div class="text-sm text-gray-500 mt-2">This month</div>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex items-center gap-2">
        <button
            @click="activeStatus = 'all'"
            :class="activeStatus === 'all' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300 hover:text-orange-500'"
            class="px-5 py-2.5 rounded-full text-sm font-semibold border transition-colors"
        >
            All Orders
        </button>
        <button
            @click="activeStatus = 'in_progress'"
            :class="activeStatus === 'in_progress' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-500'"
            class="px-5 py-2.5 rounded-full text-sm font-semibold border transition-colors"
        >
            In Progress
        </button>
        <button
            @click="activeStatus = 'pending'"
            :class="activeStatus === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-200 hover:border-amber-300 hover:text-amber-500'"
            class="px-5 py-2.5 rounded-full text-sm font-semibold border transition-colors"
        >
            Pending
        </button>
        <button
            @click="activeStatus = 'completed'"
            :class="activeStatus === 'completed' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-300 hover:text-emerald-500'"
            class="px-5 py-2.5 rounded-full text-sm font-semibold border transition-colors"
        >
            Completed
        </button>
        <button
            @click="activeStatus = 'cancelled'"
            :class="activeStatus === 'cancelled' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-600 border-gray-200 hover:border-red-300 hover:text-red-500'"
            class="px-5 py-2.5 rounded-full text-sm font-semibold border transition-colors"
        >
            Cancelled
        </button>
    </div>

    <!-- Empty State -->
    <div x-show="orders.length === 0" class="bg-white rounded-[14px] border border-gray-200 text-center py-20">
        <div class="w-20 h-20 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-3">No orders yet</h3>
        <p class="text-gray-500 text-lg mb-8 max-w-md mx-auto">Start by browsing our services and requesting one for your next project.</p>
        <a href="{{ route('customer.store') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Browse Services
        </a>
    </div>

    <!-- Filtered Empty State -->
    <div x-show="orders.length > 0 && filteredOrders.length === 0" class="bg-white rounded-[14px] border border-gray-200 text-center py-20">
        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <h3 class="text-2xl font-semibold text-gray-900 mb-3">No orders in this category</h3>
        <p class="text-gray-500 text-lg">Try selecting a different status filter</p>
    </div>

    <!-- Orders List -->
    <div x-show="filteredOrders.length > 0" class="flex flex-col gap-3">
        <template x-for="order in filteredOrders" :key="order.id">
            <div class="bg-white rounded-[14px] border p-4 flex items-center gap-4 hover:shadow-md transition-all"
                 :class="order.status === 'cancelled' ? 'border-red-200 bg-red-50/30' : 'border-gray-200 hover:border-gray-300'">
                <a :href="'/customer/orders/' + order.id" class="flex items-center gap-4 flex-1 min-w-0">
                    <!-- Thumbnail -->
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100">
                        <svg x-show="order.type === 'printing'" class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.6"/>
                            <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.6"/>
                        </svg>
                        <svg x-show="order.type === 'technical'" class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="2" stroke-width="1.6"/>
                            <circle cx="9" cy="9" r="2" stroke-width="1.6"/>
                            <path d="m21 15-5-5L5 21" stroke-width="1.6"/>
                        </svg>
                    </div>

                    <!-- Order Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-extrabold text-base text-zinc-900" x-text="order.name"></span>
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold"
                                :class="getStatusColor(order.status)"
                                x-text="getStatusLabel(order.status)"></span>
                            <span x-show="order.type === 'technical' && order.tech_priority" class="px-2.5 py-1 rounded-md text-xs font-bold"
                                :class="getPriorityColor(order.tech_priority)"
                                x-text="'Priority: ' + getPriorityLabel(order.tech_priority)"></span>
                            <span class="text-gray-300 text-sm">•</span>
                            <span class="text-gray-500 text-sm font-semibold" x-text="getOrderId(order.id)"></span>
                            <span x-show="order.payment_status" class="px-2.5 py-1 rounded-md text-xs font-bold"
                                :class="getPaymentStatusColor(order.payment_status)"
                                x-text="'Payment: ' + getPaymentStatusLabel(order.payment_status)"></span>
                        </div>
                        <p x-show="order.description" class="text-gray-500 text-base mt-1 line-clamp-1" x-text="order.description"></p>
                        <div class="flex items-center gap-3 mt-2 text-sm text-gray-500 flex-wrap">
                            <span>Service: <span class="font-bold text-gray-700 text-base" x-text="order.type"></span></span>
                            <span>Requested: <span class="font-semibold text-gray-600" x-text="order.created_at"></span></span>
                            <span x-show="order.deadline">Deadline: <span class="font-semibold text-gray-600" x-text="order.deadline"></span></span>
                            <span x-show="order.type === 'technical' && order.tech_preferred_at">Preferred: <span class="font-semibold text-gray-600" x-text="order.tech_preferred_at"></span></span>
                            <span x-show="order.type === 'technical' && order.tech_contact">Contact: <span class="font-semibold text-gray-600" x-text="getContactLabel(order.tech_contact)"></span></span>
                            <span x-show="order.employee">Assigned to: <span class="font-semibold text-gray-600" x-text="order.employee"></span></span>
                        </div>
                    </div>

                    <!-- Price & Items -->
                    <div class="text-right text-base flex-shrink-0">
                        <div class="text-gray-500"><span class="font-semibold" x-text="order.line_items_count || 1"></span> item<span x-show="order.line_items_count !== 1">s</span></div>
                        <div class="font-extrabold text-base text-zinc-900 mt-0.5" x-text="order.quote_total ? '₱' + parseFloat(order.quote_total).toFixed(2) : '₱500'"></div>
                        <div x-show="preferredPaymentMethod" class="text-xs text-gray-400 mt-1" x-text="preferredPaymentMethod.charAt(0).toUpperCase() + preferredPaymentMethod.slice(1)"></div>
                    </div>
                </a>

                <!-- Cancel Button -->
                <form x-show="order.status === 'pending'" method="POST" :action="'/customer/orders/' + order.id + '/cancel'" @click.stop>
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                </form>
            </div>
        </template>
    </div>
</div>
@endsection
