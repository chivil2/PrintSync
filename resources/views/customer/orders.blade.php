@extends('layouts.app.customer')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Order History</h1>
        <p class="mt-2 text-zinc-600">Track your service requests and orders</p>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-lg border border-zinc-200 text-center py-12">
            <svg class="w-16 h-16 mx-auto text-zinc-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h3 class="text-lg font-semibold text-zinc-900 mb-2">No orders yet</h3>
            <p class="text-zinc-600 mb-6">Start by browsing our services and requesting one</p>
            <a href="{{ route('customer.store') }}" class="inline-block px-4 py-2 bg-[#E8743B] hover:bg-[#d66532] text-white font-medium rounded-lg transition-colors">
                Browse Services
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="block bg-white rounded-lg border border-zinc-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-semibold text-zinc-900">{{ $order->name }}</h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                    </span>
                                </div>

                                @if($order->description)
                                    <p class="text-zinc-600 mb-3">{{ $order->description }}</p>
                                @endif

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-zinc-500">Service Type:</span>
                                        <p class="font-medium text-zinc-900">{{ ucfirst($order->type) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-zinc-500">Requested:</span>
                                        <p class="font-medium text-zinc-900">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    @if($order->deadline)
                                        <div>
                                            <span class="text-zinc-500">Deadline:</span>
                                            <p class="font-medium text-zinc-900">{{ $order->deadline->format('M d, Y') }}</p>
                                        </div>
                                    @endif
                                    @if($order->employee)
                                        <div>
                                            <span class="text-zinc-500">Assigned to:</span>
                                            <p class="font-medium text-zinc-900">{{ $order->employee->first_name }} {{ $order->employee->last_name }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($order->notes)
                                    <div class="mt-4 p-3 bg-zinc-50 rounded-lg">
                                        <span class="text-sm text-zinc-500">Notes:</span>
                                        <p class="text-sm text-zinc-700 mt-1">{{ $order->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="ml-6 flex flex-col items-center gap-3">
                                @if($order->type === 'printing')
                                    <svg class="w-12 h-12 text-[#E8743B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                @else
                                    <svg class="w-12 h-12 text-[#19A7CE]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                @endif

                                @if($order->quote && in_array($order->quote->status, ['sent', 'accepted', 'rejected']))
                                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $order->quote->status === 'accepted' ? 'Receipt' : 'Quote' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
