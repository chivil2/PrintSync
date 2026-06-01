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
    <body class="min-h-screen bg-slate-50">
        <div class="flex w-full min-h-screen">
            <x-employee-sidebar />

            <div class="flex-1 min-w-0 flex flex-col bg-white">
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
