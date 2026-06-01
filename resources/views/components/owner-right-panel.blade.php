@php
    $user = $user ?? auth()->user();
    $recentJobs = $recentJobs ?? collect();
    $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));

    // Accepted quotes needing employee assignment
    $acceptedQuotesNeedingAssignment = \App\Models\Quote::where('status', 'accepted')
        ->whereHas('serviceJob', function($q) {
            $q->whereNull('employee_id');
        })
        ->with(['serviceJob', 'customer'])
        ->latest()
        ->take(5)
        ->get();

    // Unread DB notifications: negotiation requests & job completions
    $unreadDbNotifications = $user->unreadNotifications
        ->whereIn('data.event_type', ['quote_negotiation', 'job_completed'])
        ->take(5);

    $notificationCount = $acceptedQuotesNeedingAssignment->count() + $unreadDbNotifications->count();

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

    // Build jobs by date for calendar indicators
    $jobsByDate = [];
    if (isset($jobsWithDeadlines)) {
        foreach ($jobsWithDeadlines as $date => $jobs) {
            $day = \Carbon\Carbon::parse($date)->day;
            $jobsByDate[$day] = $jobs;
        }
    }

    $statusColors = [
        'pending' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-100', 'icon' => 'bg-amber-500'],
        'in_progress' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'icon' => 'bg-blue-500'],
        'completed' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'icon' => 'bg-emerald-500'],
        'cancelled' => ['bg' => 'bg-red-50', 'border' => 'border-red-100', 'icon' => 'bg-red-500'],
    ];
@endphp

<div x-data="{ printbuddyExpanded: false, jobsByDate: @js(collect($jobsByDate)->map(fn($jobs) => $jobs->map(fn($j) => ['id' => $j->id, 'name' => $j->name, 'priority' => $j->priority, 'status' => $j->status, 'customer_name' => $j->customer->name ?? 'Unknown', 'deadline_formatted' => $j->deadline->format('M d, Y')])->values())->toArray()), hoverDay: null, activeDay: null, ...calendar() }" x-init="initCalendar({{ now()->year }}, {{ now()->month }})">

<aside class="w-[380px] p-4 flex-shrink-0 hidden xl:block sticky top-4 self-start">
    <div class="bg-white rounded-lg h-[calc(100vh-2rem)] p-6 shadow-sm border border-slate-200 flex flex-col overflow-hidden">

        <div class="flex items-center gap-3 mb-8" x-show="!printbuddyExpanded" x-transition>
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
                @if($notificationCount > 0)
                    <a href="{{ route('owner.jobs') }}" class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full hover:bg-orange-600 transition-colors cursor-pointer">
                        {{ $notificationCount }}
                    </a>
                @else
                    <span class="bg-slate-300 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
                @endif
            </div>

            <div class="space-y-3">
                @if($notificationCount === 0)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <p class="text-sm text-slate-500">No pending notifications</p>
                    </div>
                @else
                    {{-- Accepted quotes needing assignment --}}
                    @if($acceptedQuotesNeedingAssignment->count() > 0)
                        <div class="text-xs font-medium text-slate-500 mb-1">Ready to assign</div>
                        @foreach($acceptedQuotesNeedingAssignment as $quote)
                            <a href="{{ route('owner.jobs') }}" class="block p-3 rounded-xl bg-emerald-50 border border-emerald-100 cursor-pointer hover:opacity-80 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900">{{ $quote->serviceJob->name ?? 'Service Job' }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $quote->customer->first_name ?? 'Unknown' }} {{ $quote->customer->last_name ?? '' }}</p>
                                        <p class="text-xs text-emerald-600 mt-1 font-medium">Quote accepted — ready to assign</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif

                    {{-- Negotiation requests from customers --}}
                    @php
                        $negotiationNotifs = $unreadDbNotifications->where('data.event_type', 'quote_negotiation');
                        $jobCompletedNotifs = $unreadDbNotifications->where('data.event_type', 'job_completed');
                    @endphp

                    @if($negotiationNotifs->count() > 0)
                        <div class="text-xs font-medium text-slate-500 mb-1 {{ $acceptedQuotesNeedingAssignment->count() > 0 ? 'mt-3' : '' }}">Counter-offers</div>
                        @foreach($negotiationNotifs as $notif)
                            <form method="POST" action="{{ route('owner.notifications.read', $notif->id) }}"
                                  x-data="{ seen: false }"
                                  @submit.prevent="seen = true; $el.submit()">
                                @csrf
                                <button type="submit" class="w-full text-left p-3 rounded-xl border transition-all"
                                        :class="seen ? 'bg-slate-50 border-slate-100 opacity-60' : 'bg-amber-50 border-amber-100 hover:opacity-80'">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white flex-shrink-0 transition-colors"
                                             :class="seen ? 'bg-slate-400' : 'bg-amber-500'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-slate-900">{{ $notif->data['title'] ?? 'Counter-offer' }}</p>
                                                <span x-show="seen" class="text-xs text-slate-400 font-medium">Seen</span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1">{{ $notif->data['message'] ?? '' }}</p>
                                            <p class="text-xs mt-1 font-medium transition-colors" :class="seen ? 'text-slate-400' : 'text-amber-600'">Customer negotiated price</p>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    @endif

                    {{-- Job completed by employee --}}
                    @if($jobCompletedNotifs->count() > 0)
                        <div class="text-xs font-medium text-slate-500 mb-1 {{ ($acceptedQuotesNeedingAssignment->count() > 0 || $negotiationNotifs->count() > 0) ? 'mt-3' : '' }}">Completed jobs</div>
                        @foreach($jobCompletedNotifs as $notif)
                            <form method="POST" action="{{ route('owner.notifications.read', $notif->id) }}"
                                  x-data="{ seen: false }"
                                  @submit.prevent="seen = true; $el.submit()">
                                @csrf
                                <button type="submit" class="w-full text-left p-3 rounded-xl border transition-all"
                                        :class="seen ? 'bg-slate-50 border-slate-100 opacity-60' : 'bg-blue-50 border-blue-100 hover:opacity-80'">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white flex-shrink-0 transition-colors"
                                             :class="seen ? 'bg-slate-400' : 'bg-blue-500'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-slate-900">{{ $notif->data['title'] ?? 'Job Completed' }}</p>
                                                <span x-show="seen" class="text-xs text-slate-400 font-medium">Seen</span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1">{{ $notif->data['message'] ?? '' }}</p>
                                            <p class="text-xs mt-1 font-medium transition-colors" :class="seen ? 'text-slate-400' : 'text-blue-600'">Employee marked as complete</p>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    @endif
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
            <div class="grid grid-cols-7 gap-1 text-center text-base" @click.outside="activeDay = null">
                <template x-for="day in calendarDays" :key="day">
                    <div x-show="day !== null"
                         class="py-1.5 rounded-xl transition-colors cursor-pointer relative select-none"
                         :class="{
                             'bg-orange-500 text-white font-bold': day === today && currentMonth === {{ now()->month }} && currentYear === {{ now()->year }},
                             'ring-2 ring-orange-400 ring-offset-1': activeDay === day && jobsByDate[day],
                             'text-slate-600 hover:bg-slate-100': !(day === today && currentMonth === {{ now()->month }} && currentYear === {{ now()->year }}),
                         }"
                         @mouseenter="jobsByDate[day] ? hoverDay = day : null"
                         @mouseleave="hoverDay = null"
                         @click="jobsByDate[day] ? (activeDay = activeDay === day ? null : day) : null">
                        <span x-text="day"></span>
                        <template x-if="jobsByDate[day]">
                            <div class="absolute bottom-0.5 left-1/2 transform -translate-x-1/2 flex gap-0.5">
                                <template x-for="job in jobsByDate[day].slice(0, 3)" :key="job.id">
                                    <div class="w-1.5 h-1.5 rounded-full"
                                         :class="job.priority === 'urgent' ? 'bg-red-500' : (job.priority === 'high' ? 'bg-orange-400' : 'bg-blue-400')"></div>
                                </template>
                            </div>
                        </template>

                        <!-- Hover/Click popup -->
                        <template x-if="jobsByDate[day] && (hoverDay === day || activeDay === day)">
                            <div class="absolute z-50 bg-white border border-slate-200 rounded-2xl shadow-xl p-3 text-left w-56"
                                 style="top: calc(100% + 6px); left: 50%; transform: translateX(-50%);">
                                <div class="text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider"
                                     x-text="currentMonthName + ' ' + day"></div>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="job in jobsByDate[day]" :key="job.id">
                                        <a :href="'/owner/jobs/' + job.id"
                                           class="flex items-start gap-2 p-2 rounded-xl hover:bg-slate-50 transition-colors block">
                                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"
                                                 :class="job.priority === 'urgent' ? 'bg-red-500' : (job.priority === 'high' ? 'bg-orange-400' : 'bg-blue-400')"></div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-semibold text-slate-800 truncate" x-text="job.name"></p>
                                                <p class="text-xs text-slate-500 truncate" x-text="job.customer_name"></p>
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                                      :class="job.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : (job.status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')"
                                                      x-text="job.status === 'in_progress' ? 'In Progress' : (job.status === 'completed' ? 'Completed' : 'Pending')"></span>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <template x-if="Object.keys(jobsByDate).length > 0">
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="text-sm font-medium text-slate-400 px-1 mb-3">UPCOMING DEADLINES</div>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        <template x-for="[day, jobs] in Object.entries(jobsByDate)" :key="day">
                            <template x-for="job in jobs.slice(0, 3)" :key="job.id">
                                <a :href="'/owner/jobs/' + job.id" class="block p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full"
                                             :class="job.priority === 'urgent' ? 'bg-red-500' : (job.priority === 'high' ? 'bg-orange-500' : 'bg-blue-500')"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-900 truncate" x-text="job.name"></p>
                                            <p class="text-sm text-slate-500" x-text="job.deadline_formatted"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Separator -->
        <div class="border-t border-slate-200 my-6" x-show="!printbuddyExpanded" x-transition></div>

        <!-- PrintBuddy Chatbot Section -->
        <div class="flex flex-col flex-1 min-h-0" @toggle-printbuddy="printbuddyExpanded = !printbuddyExpanded">
            <x-printbuddy-chat />
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
            this.fetchJobsForMonth();
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
            this.fetchJobsForMonth();
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 12) {
                this.currentMonth = 1;
                this.currentYear++;
            }
            this.updateCalendar();
            this.fetchJobsForMonth();
        },

        async fetchJobsForMonth() {
            try {
                const response = await fetch(`/owner/jobs-by-month?year=${this.currentYear}&month=${this.currentMonth}`);
                const data = await response.json();
                const byDay = {};
                for (const [dateStr, jobs] of Object.entries(data.jobs_by_date)) {
                    const day = parseInt(dateStr.split('-')[2], 10);
                    byDay[day] = jobs;
                }
                this.jobsByDate = byDay;
                this.activeDay = null;
                this.hoverDay = null;
            } catch (error) {
                console.error('Error fetching jobs:', error);
            }
        }
    }
}
</script>
</div>