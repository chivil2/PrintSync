<aside class="fixed inset-y-0 left-0 z-[60] w-[160px] bg-[#0d1730]/95 border-r border-[#263862] transform transition-all duration-300 lg:translate-x-0 -translate-x-full lg:static hidden md:flex flex-col h-screen shadow-2xl shadow-black/20 backdrop-blur" id="sidebar" x-data="{ sidebarOpen: true }" :class="sidebarOpen ? 'w-[160px]' : 'w-[88px]'">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center p-4" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <button
                type="button"
                class="h-10 w-10 flex items-center justify-center cursor-pointer shrink-0 rounded-xl bg-slate-400/15 transition-colors hover:bg-slate-300/25"
                @click="sidebarOpen = !sidebarOpen"
                aria-label="Toggle sidebar"
            >
                <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="h-8 w-8 object-contain">
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-3" :class="sidebarOpen ? 'px-2 mt-2' : 'px-0 mt-4'">
            <a href="{{ route('owner.dashboard') }}" class="flex items-center transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" :class="sidebarOpen ? 'w-full justify-start gap-2 px-3 py-2.5 rounded-xl' : 'mx-auto h-11 w-16 justify-center rounded-2xl'">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
                <span class="font-medium text-[11px] whitespace-nowrap" x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('owner.quotes') }}" class="flex items-center transition-all {{ request()->routeIs('owner.quotes') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" :class="sidebarOpen ? 'w-full justify-start gap-2 px-3 py-2.5 rounded-xl' : 'mx-auto h-11 w-16 justify-center rounded-2xl'">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                <span class="font-medium text-[11px] whitespace-nowrap" x-show="sidebarOpen">Reports</span>
            </a>

            <a href="{{ route('owner.inventory.index') }}" class="flex items-center transition-all {{ request()->routeIs('owner.inventory.*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" :class="sidebarOpen ? 'w-full justify-start gap-2 px-3 py-2.5 rounded-xl' : 'mx-auto h-11 w-16 justify-center rounded-2xl'">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="font-medium text-[11px] whitespace-nowrap" x-show="sidebarOpen">Inventory</span>
            </a>

            <a href="{{ route('owner.products.index') }}" class="flex items-center transition-all {{ request()->routeIs('owner.products.*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" :class="sidebarOpen ? 'w-full justify-start gap-2 px-3 py-2.5 rounded-xl' : 'mx-auto h-11 w-16 justify-center rounded-2xl'">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <span class="font-medium text-[11px] whitespace-nowrap" x-show="sidebarOpen">Products</span>
            </a>

            <a href="{{ route('owner.jobs') }}" class="flex items-center transition-all {{ request()->routeIs('owner.jobs*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white shadow-lg shadow-blue-950/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}" :class="sidebarOpen ? 'w-full justify-start gap-2 px-3 py-2.5 rounded-xl' : 'mx-auto h-11 w-16 justify-center rounded-2xl'">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="font-medium text-[11px] whitespace-nowrap" x-show="sidebarOpen">Orders</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="border-t border-white/10" :class="sidebarOpen ? 'p-2' : 'p-0 py-4'">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center text-orange-400 hover:bg-orange-500/10 hover:text-orange-300 transition-all" :class="sidebarOpen ? 'w-full justify-start gap-2 px-3 py-2.5 rounded-xl' : 'mx-auto h-11 w-16 justify-center rounded-2xl'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span class="font-medium text-[11px]" x-show="sidebarOpen">Log out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
