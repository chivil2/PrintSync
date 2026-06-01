<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            @import url('https://fonts.cdnfonts.com/css/neue-haas-grotesk-display-pro');

            body {
                font-family: 'Neue Haas Grotesk Display Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="min-h-screen bg-slate-50" x-data="{ sidebarOpen: false, rightPanelOpen: false }">
        <div class="flex w-full min-h-screen">
            <x-employee-sidebar />

            <!-- Mobile overlays -->
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 lg:hidden" x-transition.opacity></div>
            <div x-show="rightPanelOpen" x-cloak @click="rightPanelOpen = false" class="fixed inset-0 z-30 bg-black/50 xl:hidden" x-transition.opacity></div>

            <!-- Mobile top bar -->
            <div class="fixed top-0 left-0 right-0 z-20 flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3 lg:hidden">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('employee.dashboard') }}" class="font-bold text-slate-900 text-lg">PrintSync</a>
                <button @click="rightPanelOpen = !rightPanelOpen" class="p-2 -mr-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 min-w-0 flex flex-col bg-white lg:pt-0 pt-14">
                <main class="flex-1 overflow-y-auto">
                    <div class="flex">
                        <div class="flex-1 min-w-0">
                            @yield('content')
                        </div>
                        <x-employee-right-panel />
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
