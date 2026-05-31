@props(['name', 'size' => 'lg', 'src' => null])

@php
    $initials = str($name)
        ->split('/\s+/')
        ->take(2)
        ->map(fn ($part) => str($part)->substr(0, 1)->upper())
        ->join('');
    $dimensions = $size === 'lg' ? 'h-20 w-20 text-2xl' : 'h-7 w-7 text-xs';
@endphp

<div
    class="relative flex shrink-0 items-center justify-center rounded-full font-bold text-white select-none overflow-hidden {{ $dimensions }}"
    style="background: linear-gradient(135deg, #e8701a 0%, #f5a623 100%)"
>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="h-full w-full object-cover">
    @else
        {{ $initials }}
    @endif

    @if ($size === 'lg')
        <span class="absolute bottom-0.5 right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-400 shadow-[0_0_6px_rgba(16,185,129,0.6)]"></span>
    @endif
</div>
