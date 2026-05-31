<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-800 font-sans">
        <div class="flex min-h-screen">
            <!-- Sidebar Component -->
            <x-owner-sidebar />

            <!-- Mobile Header -->
            <div class="lg:hidden fixed top-0 left-0 right-0 z-30 bg-slate-800 border-b border-slate-700 px-4 py-3 shadow-lg">
                <div class="mb-3 flex items-center justify-between">
                    <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="h-12 w-48 object-contain object-left">
                    <span class="rounded-full bg-orange-500/20 px-3 py-1 text-xs font-semibold text-orange-300">Owner</span>
                </div>
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <a href="{{ route('owner.dashboard') }}" class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.dashboard') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                            <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                            <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('owner.quotes') }}" class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.quotes*') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        Quotes
                    </a>
                    <a href="{{ route('owner.inventory.index') }}" class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.inventory.*') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Inventory
                    </a>
                    <a href="{{ route('owner.services.index') }}" class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.services.*') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                        </svg>
                        Services
                    </a>
                    <a href="{{ route('owner.employees') }}" class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold transition {{ request()->routeIs('owner.employees*') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-slate-300' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Employees
                    </a>
                </div>
            </div>

            <!-- Overlay -->
            <div class="fixed inset-0 bg-slate-900/80 z-30 hidden lg:hidden backdrop-blur-sm" id="sidebar-overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

            <!-- Main Content -->
            <main class="flex-1 md:ml-[240px] lg:ml-0 pt-14 lg:pt-0">
                <div class="flex">
                    <div class="flex-1 px-6">
                        {{ $slot }}
                    </div>
                    @if(isset($user))
                        <x-owner-right-panel :user="$user" />
                    @else
                        <x-owner-right-panel />
                    @endif
                </div>
            </main>
        </div>

        @fluxScripts

        <script>
            // Mobile sidebar toggle
            document.querySelector('[onclick*="sidebar"]').addEventListener('click', function() {
                document.getElementById('sidebar-overlay').classList.remove('hidden');
            });

            // Cache logo in localStorage
            document.addEventListener('DOMContentLoaded', function() {
                const logoUrl = '{{ asset("images/logo.png") }}';
                const cacheKey = 'printsync_logo_cache';
                const logoElement = document.getElementById('owner-sidebar-logo');

                // Check if logo is cached in localStorage
                const cachedLogo = localStorage.getItem(cacheKey);
                if (cachedLogo) {
                    // Use cached version
                    if (logoElement) {
                        logoElement.style.backgroundImage = 'url(\'' + cachedLogo + '\')';
                    }
                } else {
                    // Fetch and cache the logo
                    fetch(logoUrl)
                        .then(response => response.blob())
                        .then(blob => {
                            const reader = new FileReader();
                            reader.onloadend = function() {
                                const base64data = reader.result;
                                localStorage.setItem(cacheKey, base64data);
                                // Update the logo with cached version
                                if (logoElement) {
                                    logoElement.style.backgroundImage = 'url(\'' + base64data + '\')';
                                }
                            };
                            reader.readAsDataURL(blob);
                        })
                        .catch(error => console.error('Error caching logo:', error));
                }
            });
        </script>
    </body>
</html>