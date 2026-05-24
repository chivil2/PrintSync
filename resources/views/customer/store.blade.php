@extends('layouts.app.customer')

@section('content')

        <!-- Services Section -->
        <div class="space-y-6" x-data="{ activeTab: 'printing' }" x-init="$watch('activeTab', () => { $dispatch('modal-close') })">
            <div>
                <h2 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Our Services</h2>
                <p class="mt-2 text-zinc-600">Browse our printing and technical services</p>
            </div>

            <div class="flex gap-4 border-b border-zinc-200">
                <button
                    @click="activeTab = 'printing'"
                    :class="activeTab === 'printing' ? 'text-[#E8743B] border-[#E8743B]' : 'text-zinc-500 border-transparent hover:text-zinc-700'"
                    class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Printing Services
                </button>
                <button
                    @click="activeTab = 'technical'"
                    :class="activeTab === 'technical' ? 'text-[#19A7CE] border-[#19A7CE]' : 'text-zinc-500 border-transparent hover:text-zinc-700'"
                    class="flex items-center gap-2 px-4 py-2 border-b-2 transition-colors font-medium"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Technical Services
                </button>
            </div>

            <div class="mt-8">
                <div x-show="activeTab === 'printing'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($printingServices as $service)
                        <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden hover:shadow-lg transition-shadow cursor-pointer" 
                     @click="$dispatch('open-modal', { service: {{ $service->toJson() }}, type: 'printing' })">
                            <div class="aspect-video bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                                <svg class="w-16 h-16 text-[#E8743B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-zinc-900">{{ $service->name }}</h3>
                                    <p class="mt-2 text-sm text-zinc-600">{{ $service->description }}</p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-[#E8743B]">₱{{ number_format($service->price, 2) }}</span>
                                    <button class="px-4 py-2 bg-[#E8743B] hover:bg-[#d66532] text-white font-medium rounded-lg transition-colors">
                                        Request Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div x-show="activeTab === 'technical'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($technicalServices as $service)
                        <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden hover:shadow-lg transition-shadow cursor-pointer" 
                     @click="$dispatch('open-modal', { service: {{ $service->toJson() }}, type: 'technical' })">
                            <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                                <svg class="w-16 h-16 text-[#19A7CE]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-zinc-900">{{ $service->name }}</h3>
                                    <p class="mt-2 text-sm text-zinc-600">{{ $service->description }}</p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-[#19A7CE]">₱{{ number_format($service->price, 2) }}</span>
                                    <button class="px-4 py-2 bg-[#19A7CE] hover:bg-[#1596b8] text-white font-medium rounded-lg transition-colors">
                                        Request Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Service Modal Component -->
        <x-service-modal />
@endsection
