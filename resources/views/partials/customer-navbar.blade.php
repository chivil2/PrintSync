<header class="border-b border-zinc-200 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ route('customer.store') }}" class="flex items-center gap-2">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="#E8743B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 7H15M9 11H15M9 15H12" stroke="#19A7CE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-xl font-semibold text-black" style="font-family: 'Neue Haas Grotesk Display Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">PrintSync</span>
                </a>

                <div class="flex items-center gap-1">
                    <a 
                        href="{{ route('customer.dashboard') }}" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.dashboard') ? 'text-[#E8743B] bg-orange-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a 
                        href="{{ route('customer.store') }}" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.store') ? 'text-[#E8743B] bg-orange-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="font-medium">Store</span>
                    </a>

                    <a 
                        href="{{ route('customer.orders') }}" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors {{ request()->routeIs('customer.orders') ? 'text-[#E8743B] bg-orange-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="font-medium">Orders</span>
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button 
                            @click="open = !open"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-zinc-50 transition-colors"
                        >
                            <div class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center">
                                <span class="text-sm font-medium text-black">{{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-black">{{ auth()->user()->first_name }}</span>
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div 
                            x-show="open" 
                            x-cloak
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-zinc-200 py-1 z-50"
                        >
                            <a href="{{ route('customer.profile') }}" @click="open = false" class="block px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                Profile Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" @click="open = false" class="w-full text-left px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>
    </div>
</header>
