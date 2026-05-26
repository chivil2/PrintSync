@props([
    'size' => 'h-9 w-9',
    'textSize' => 'text-[18px]',
    'showText' => true,
    'variant' => 'default', // default, gradient, minimal
])

@php
    $colors = config('colors');
    $primaryColor = $colors['primary'] ?? '#F47C3C';
    $secondaryColor = $colors['secondary'] ?? '#4A6CF7';
@endphp

@if ($variant === 'gradient')
    <div class="flex items-center gap-2">
        <div class="{{ $size }} bg-gradient-to-br from-blue-500 to-orange-500 rounded-lg flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
        </div>
        @if ($showText)
            <span class="{{ $textSize }} font-bold text-white">PrintSync</span>
        @endif
    </div>
@elseif ($variant === 'minimal')
    <div class="flex items-center gap-2">
        <svg class="{{ $size }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="{{ $primaryColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 7H15M9 11H15M9 15H12" stroke="{{ $secondaryColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @if ($showText)
            <span class="{{ $textSize }} font-bold text-zinc-900">PrintSync</span>
        @endif
    </div>
@else
    <div class="flex items-center gap-2">
        <svg class="{{ $size }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="8" fill="{{ $primaryColor }}"/>
            <path d="M9 7H15M9 11H15M9 15H12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @if ($showText)
            <span class="{{ $textSize }} font-bold text-[#111827]">PrintSync</span>
        @endif
    </div>
@endif
