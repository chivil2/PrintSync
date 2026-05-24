<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
        <div class="fixed top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 via-indigo-500 to-orange-500 z-50"></div>

        <div class="flex min-h-screen">
            <!-- Sidebar Component -->
            <x-owner-sidebar />

            <!-- Mobile Header -->
            <div class="lg:hidden fixed top-0 left-0 right-0 z-30 bg-blue-950 border-b border-blue-800/30 px-4 py-3 flex items-center justify-between">
                <button type="button" class="p-2 text-blue-200 hover:text-white" onclick="document.getElementById('sidebar').classList.remove('-translate-x-full')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-orange-500 flex items-center justify-center text-white text-sm font-semibold shadow-lg">
                        {{ auth()->user()->initials() }}
                    </div>
                </div>
            </div>

            <!-- Overlay -->
            <div class="fixed inset-0 bg-blue-950/80 z-30 hidden lg:hidden backdrop-blur-sm" id="sidebar-overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')"></div>

            <!-- Main Content -->
            <main class="flex-1 lg:ml-0 pt-14 lg:pt-0">
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