<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            @import url('https://fonts.cdnfonts.com/css/neue-haas-grotesk-display-pro');
            
            body {
                font-family: 'Neue Haas Grotesk Display Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            }
        </style>
    </head>
    <body class="min-h-screen bg-white antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10" style="background: linear-gradient(rgba(30, 58, 138, 0.7), rgba(30, 58, 138, 0.7)), url('https://images.unsplash.com/photo-1562564055-71e051d33c19?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; background-attachment: fixed;">
            <div class="flex w-full max-w-sm flex-col gap-2 text-black">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium mb-4 text-black" wire:navigate>
                    <x-printsync-icon size="size-12" textSize="text-2xl" variant="minimal" />
                </a>
                <div class="flex flex-col gap-6 bg-white/50 backdrop-blur-xl rounded-2xl shadow-2xl p-8 text-black" style="color: black !important;">
                    <style>
                        .text-black * {
                            color: black !important;
                        }
                    </style>
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
