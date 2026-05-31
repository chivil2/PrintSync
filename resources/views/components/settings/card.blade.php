@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-[0_1px_4px_rgba(0,0,0,0.06)] ' . $class]) }}>
    {{ $slot }}
</div>
