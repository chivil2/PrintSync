@props(['role'])

@php
$colors = [
    'owner' => 'bg-indigo-100 text-indigo-800',
    'employee' => 'bg-blue-100 text-blue-800',
    'customer' => 'bg-green-100 text-green-800',
];

$color = $colors[$role] ?? 'bg-gray-100 text-gray-800';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
    {{ ucfirst($role) }}
</span>
