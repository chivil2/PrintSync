<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
        <style>
            .hero-bg {
                background-color: #fafafa;
                background-image:
                    radial-gradient(ellipse 80% 50% at 20% 0%, rgba(232, 116, 59, 0.10), transparent 60%),
                    radial-gradient(ellipse 70% 50% at 85% 30%, rgba(25, 167, 206, 0.10), transparent 60%),
                    radial-gradient(circle, rgba(15, 23, 42, 0.06) 1px, transparent 1px);
                background-size: auto, auto, 22px 22px;
            }
            @media (min-width: 768px) {
                .hero-bg { background-size: auto, auto, 26px 26px; }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-zinc-900 bg-zinc-50">
        <x-navbar />

        <div class="relative min-h-svh flex items-center justify-center -mt-20 pt-28 pb-12 px-4 sm:px-6 overflow-hidden">
            <div class="absolute inset-0 hero-bg pointer-events-none"></div>
            <div class="absolute -top-10 -left-16 w-72 h-72 bg-orange-300/25 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-32 -right-16 w-72 h-72 bg-blue-300/25 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative w-full max-w-sm">
                <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
