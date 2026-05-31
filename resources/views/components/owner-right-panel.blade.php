@php
    $user = $user ?? auth()->user();
    $recentJobs = $recentJobs ?? collect();
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

    $statusColors = [
        'pending' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-100', 'icon' => 'bg-amber-500'],
        'in_progress' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'icon' => 'bg-blue-500'],
        'completed' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'icon' => 'bg-emerald-500'],
        'cancelled' => ['bg' => 'bg-red-50', 'border' => 'border-red-100', 'icon' => 'bg-red-500'],
    ];
@endphp

<div x-data="{ printbuddyExpanded: false, ...calendar() }" x-init="initCalendar({{ now()->year }}, {{ now()->month }})">

<aside class="w-[380px] p-4 flex-shrink-0 hidden xl:block sticky top-4 self-start">
    <div class="bg-white rounded-lg h-[calc(100vh-2rem)] p-6 shadow-sm border border-slate-200 flex flex-col overflow-hidden">

        <div class="flex items-center gap-3 mb-8" x-show="!printbuddyExpanded">
            @if($user->profile_photo_path)
                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-12 h-12 rounded-lg object-cover">
            @else
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                    {{ $initials }}
                </div>
            @endif
            <div>
                <div class="font-bold text-slate-900 text-lg">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="text-sm text-slate-500">Owner</div>
            </div>
        </div>

        <!-- Notifications Section -->
        <div class="mb-8" x-show="!printbuddyExpanded" x-transition>
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Notifications
                </div>
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $recentJobs->count() }}</span>
            </div>

            <div class="space-y-3">
                @if($recentJobs->count() > 0)
                    @foreach($recentJobs as $job)
                        @php
                            $statusColor = $statusColors[$job->status] ?? $statusColors['pending'];
                        @endphp
                        <a href="{{ route('owner.jobs.show', $job->id) }}" class="block p-3 rounded-xl {{ $statusColor['bg'] }} border {{ $statusColor['border'] }} cursor-pointer hover:opacity-80 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 {{ $statusColor['icon'] }} rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900">{{ $job->service->name ?? 'Service' }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $job->customer->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <p class="text-sm text-slate-500">No recent jobs</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="mb-0" x-show="!printbuddyExpanded" x-transition>
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Calendar
                </div>
                <div class="flex items-center gap-2">
                    <button @click="previousMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="text-sm text-slate-500 font-medium w-24 text-center" x-text="currentMonthName + ' ' + currentYear">{{ $currentMonth }} {{ $currentYear }}</div>
                    <button @click="nextMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center mb-2 text-sm text-slate-400 font-medium px-1">
                @foreach ($weekDays as $d)
                    <div>{{ $d }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-base">
                <template x-for="day in calendarDays" :key="day">
                    <div x-show="day !== null" class="py-1.5 rounded-xl transition-colors cursor-pointer relative"
                         :class="day === today && currentMonth === {{ now()->month }} && currentYear === {{ now()->year }} ? 'bg-orange-500 text-white font-bold' : 'text-slate-600 hover:bg-slate-100'">
                        <span x-text="day"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Separator -->
        <div class="border-t border-slate-200 my-6" x-show="!printbuddyExpanded" x-transition></div>

        <!-- PrintBuddy Chatbot Section -->
        <div class="flex flex-col flex-1">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4 px-1 cursor-pointer" @click="printbuddyExpanded = !printbuddyExpanded">
                <div class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    PrintBuddy
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-purple-100 text-purple-600 text-xs font-bold px-2 py-1 rounded-full">AI</span>
                    <svg x-show="!printbuddyExpanded" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                    <svg x-show="printbuddyExpanded" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-blue-50 border border-purple-100 rounded-2xl overflow-hidden flex flex-col flex-1">
                <!-- Chat Messages -->
                <div class="p-4 overflow-y-auto flex-1 min-h-0">
                    <div class="flex gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">PB</div>
                        <div class="bg-white border border-purple-200 rounded-2xl rounded-tl-none p-3 max-w-[85%] shadow-sm">
                            <p class="text-sm text-slate-700">Hello! I'm PrintBuddy, your AI assistant. How can I help you manage your printing business today?</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="p-3 border-t border-purple-100 bg-white/50 flex-shrink-0">
                    <div class="flex gap-2">
                        <input type="text" placeholder="Ask PrintBuddy..." class="flex-1 px-4 py-2 bg-white border border-purple-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" disabled>
                        <button class="w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center hover:bg-purple-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
function calendar() {
    return {
        currentYear: null,
        currentMonth: null,
        currentMonthName: '',
        today: {{ now()->day }},
        calendarDays: [],

        initCalendar(year, month) {
            this.currentYear = year;
            this.currentMonth = month;
            this.updateCalendar();
        },

        updateCalendar() {
            const date = new Date(this.currentYear, this.currentMonth - 1, 1);
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            this.currentMonthName = monthNames[this.currentMonth - 1];

            const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
            const firstDayOfWeek = date.getDay();

            this.calendarDays = [];
            for (let i = 0; i < firstDayOfWeek; i++) {
                this.calendarDays.push(null);
            }
            for (let i = 1; i <= daysInMonth; i++) {
                this.calendarDays.push(i);
            }
        },

        previousMonth() {
            this.currentMonth--;
            if (this.currentMonth < 1) {
                this.currentMonth = 12;
                this.currentYear--;
            }
            this.updateCalendar();
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 12) {
                this.currentMonth = 1;
                this.currentYear++;
            }
            this.updateCalendar();
        }
    }
}
</script>
</div>