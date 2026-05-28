@php
    $user = auth()->user();
    $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));

    $currentMonth = now()->format('F');
    $currentYear = now()->format('Y');
    $today = now()->day;
    $daysInMonth = now()->daysInMonth;
    $firstDayOfWeek = now()->startOfMonth()->dayOfWeek;
    $calendarDays = [];

    for ($i = 0; $i < $firstDayOfWeek; $i++) {
        $calendarDays[] = null;
    }
    for ($i = 1; $i <= $daysInMonth; $i++) {
        $calendarDays[] = $i;
    }

    $weekDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
@endphp

<aside class="w-80 p-4 flex-shrink-0 hidden xl:block">
    <div class="bg-white rounded-3xl h-full p-6 shadow-sm border border-slate-200/60 flex flex-col">

        <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-blue-600 rounded-2xl flex items-center justify-center text-white font-bold shadow-sm">
                {{ $initials }}
            </div>
            <div>
                <div class="font-bold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="text-xs text-slate-500">Employee</div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-slate-500"></i>
                    Schedule
                </div>
                <div class="text-xs text-orange-600 font-medium cursor-pointer hover:underline">{{ $currentMonth }} →</div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center mb-2 text-xs text-slate-400 font-medium px-1">
                @foreach ($weekDays as $d)
                    <div>{{ $d }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-sm">
                @foreach ($calendarDays as $day)
                    @if ($day === null)
                        <div></div>
                    @else
                        <div class="py-1.5 rounded-xl transition-colors cursor-pointer
                            {{ $day === $today ? 'bg-orange-500 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                            {{ $day }}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="text-xs font-medium text-slate-400 px-1 mb-3">THIS WEEK</div>
            <div class="flex gap-4">
                <div class="flex-1 bg-emerald-50 p-3 rounded-2xl">
                    <div class="text-emerald-600 font-bold text-2xl">{{ $completedJobs ?? 0 }}</div>
                    <div class="text-xs text-emerald-600 font-medium">Jobs Done</div>
                </div>
                <div class="flex-1 bg-blue-50 p-3 rounded-2xl">
                    <div class="text-blue-600 font-bold text-2xl">{{ $totalJobs ?? 0 }}</div>
                    <div class="text-xs text-blue-600 font-medium">Total Jobs</div>
                </div>
            </div>
        </div>
    </div>
</aside>
