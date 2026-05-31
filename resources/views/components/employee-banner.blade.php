@props([
    'title' => '',
    'subtitle' => '',
    'mode' => 'default', // 'default' or 'edit'
])

<div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 mt-6 rounded-3xl px-6 pt-6 pb-8 mb-8 relative overflow-hidden">
    <div class="welcome-dots"></div>
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">{{ $title }}</h1>
        <p class="text-white/80">{{ $subtitle }}</p>
    </div>
</div>
