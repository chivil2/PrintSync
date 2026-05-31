@php
    $colors = config('colors');
    $navItems = [
        [
            'label' => 'Dashboard',
            'route' => 'employee.dashboard',
            'icon' => 'fa-solid fa-house',
        ],
        [
            'label' => 'Jobs',
            'route' => 'employee.jobs',
            'icon' => 'fa-solid fa-briefcase',
        ],
        [
            'label' => 'Quotes',
            'route' => 'employee.quotes',
            'icon' => 'fa-solid fa-file-lines',
        ],
    ];
@endphp

<aside class="w-80 p-4 flex-shrink-0 hidden lg:block">
    <div class="bg-white rounded-lg h-full p-5 flex flex-col shadow-sm border border-slate-200 overflow-y-auto">
        <div class="flex items-center gap-3 px-3 py-2 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="PrintSync" class="w-48 h-24 object-contain">
        </div>

        <div class="px-3 mb-2">
            <div class="text-sm font-semibold text-slate-500 tracking-widest px-3 mb-2">MENU</div>
        </div>

        <nav class="space-y-1 px-1 flex-1">
            @foreach ($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-base font-medium transition-all duration-200 cursor-pointer
                        {{ $isActive
                            ? 'bg-blue-600 text-white'
                            : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    <i class="{{ $item['icon'] }} w-[20px] text-center"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-auto pt-4 px-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-base font-medium text-red-600 hover:bg-red-50 transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-right-from-bracket w-[20px] text-center"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
