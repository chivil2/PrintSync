<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_30%_72%,#294ba8_0%,#1f347a_34%,#142241_68%,#0b1224_100%)] text-slate-800 font-sans">
        <div class="flex min-h-screen">
            <!-- Sidebar Component -->
            <x-owner-sidebar />

            <!-- Mobile Header -->
            <div class="lg:hidden fixed top-0 left-0 right-0 z-30 bg-[#0d1730]/95 border-b border-[#263862] px-4 py-3 shadow-xl ring-1 ring-white/10">
                <div class="mb-3 flex items-center justify-between">
                    <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="h-10 w-36 object-contain object-left rounded-lg bg-white/95 px-2">
                    <span class="rounded-full bg-orange-500/15 px-3 py-1 text-xs font-semibold text-orange-200">Owner</span>
                </div>
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <a href="{{ route('owner.dashboard') }}" class="flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.dashboard') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white' : 'bg-white/10 text-slate-200' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                            <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                            <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('owner.quotes') }}" class="flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.quotes*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white' : 'bg-white/10 text-slate-200' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        Reports
                    </a>
                    <a href="{{ route('owner.inventory.index') }}" class="flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.inventory.*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white' : 'bg-white/10 text-slate-200' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Inventory
                    </a>
                    <a href="{{ route('owner.products.index') }}" class="flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.products.*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white' : 'bg-white/10 text-slate-200' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        Products
                    </a>
                    <a href="{{ route('owner.employees') }}" class="flex shrink-0 items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.employees*') ? 'bg-gradient-to-r from-[#356dff] to-[#ff6a2a] text-white' : 'bg-white/10 text-slate-200' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Employees
                    </a>
                </div>
            </div>

            <!-- Overlay -->
            <div class="fixed inset-0 bg-blue-950/80 z-30 hidden lg:hidden backdrop-blur-sm" id="sidebar-overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

            <!-- Main Content -->
            <main class="flex-1 md:ml-[160px] lg:ml-0 pt-14 lg:pt-0">
                {{ $slot }}
            </main>
        </div>

        @fluxScripts

        <script>
            // Mobile sidebar toggle
            document.querySelector('[onclick*="sidebar"]').addEventListener('click', function() {
                document.getElementById('sidebar-overlay').classList.remove('hidden');
            });
        </script>
    </body>
</html>