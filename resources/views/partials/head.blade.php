<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<base href="{{ url('/') }}">

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="{{ asset('images/logo.png') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
<link rel="preload" href="{{ asset('images/logo.png') }}" as="image">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
