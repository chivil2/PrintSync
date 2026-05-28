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
                @php
                    $user = auth()->user();
                    $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
                @endphp
                <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100">
                    <div class="relative w-80">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input
                            type="text"
                            placeholder="Search something..."
                            class="w-full bg-white pl-12 pr-4 py-3 text-sm border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 placeholder:text-slate-400 shadow-sm"
                        />
                    </div>

                    <div class="flex items-center gap-4">
                        @if(isset($otherEmployees) && $otherEmployees->count() > 0)
                            @foreach($otherEmployees as $employee)
                                @php
                                    $empInitials = strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1));
                                @endphp
                                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-slate-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                        <p class="text-xs text-slate-500">Employee</p>
                                    </div>
                                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white font-bold text-sm ring-2 ring-white shadow-sm">
                                        {{ $empInitials }}
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                <p class="text-xs text-slate-500">Employee</p>
                            </div>
                            <div class="w-9 h-9 bg-gradient-to-br from-orange-500 to-blue-600 rounded-2xl flex items-center justify-center text-white font-bold text-sm ring-2 ring-white shadow-sm">
                                {{ $initials }}
                            </div>
                        </div>
                    </div>
                </div>

                <main class="flex-1 overflow-y-auto">
                    @yield('content')
                </main>
            </div>

            @hasSection('rightPanel')
                @yield('rightPanel')
            @endif
        </div>
    </body>
</html>
