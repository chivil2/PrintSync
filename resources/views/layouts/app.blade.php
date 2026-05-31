<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white font-sans antialiased">
        @include('partials.global-navbar')

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
