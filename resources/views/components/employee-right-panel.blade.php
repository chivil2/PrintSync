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

    // Build jobs by date for calendar indicators
    $jobsByDate = [];
    if (isset($jobsWithDeadlines)) {
        foreach ($jobsWithDeadlines as $date => $jobs) {
            $day = \Carbon\Carbon::parse($date)->day;
            $jobsByDate[$day] = $jobs;
        }
    }
@endphp

<div x-data="calendar()" x-init="initCalendar({{ now()->year }}, {{ now()->month }})">

<aside class="w-[380px] p-4 flex-shrink-0 sticky top-4 self-start">
    <div class="bg-white rounded-lg h-[calc(100vh-2rem)] p-6 shadow-sm border border-slate-200 flex flex-col overflow-hidden">

        <div class="flex items-center gap-3 mb-8">
            @if($user->profile_photo_path)
                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-12 h-12 rounded-lg object-cover">
            @else
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                    {{ $initials }}
                </div>
            @endif
            <div class="flex-1">
                <div class="font-bold text-slate-900 text-lg">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="text-sm text-slate-500">Employee</div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-slate-500"></i>
                    Schedule
                </div>
                <div class="flex items-center gap-2">
                    <button @click="previousMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-chevron-left text-slate-400 text-sm"></i>
                    </button>
                    <div class="text-sm text-slate-500 font-medium w-24 text-center" x-text="currentMonthName + ' ' + currentYear">{{ $currentMonth }} {{ $currentYear }}</div>
                    <button @click="nextMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-chevron-right text-slate-400 text-sm"></i>
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
                                        <a :href="'/employee/jobs/' + job.id"
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
                                <a :href="'/employee/jobs/' + job.id" class="block p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
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

        <!-- Jobs Section -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Jobs
                </div>
            </div>

            <div class="space-y-3">
                @if(isset($jobs) && $jobs->count() > 0)
                    @foreach($jobs as $job)
                        @php
                            $statusColors = [
                                'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-100', 'icon' => 'bg-yellow-500'],
                                'in_progress' => ['bg' => 'bg-sky-50', 'border' => 'border-sky-100', 'icon' => 'bg-sky-400'],
                                'completed' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'icon' => 'bg-emerald-500'],
                            ];
                            $statusColor = $statusColors[$job->status] ?? $statusColors['pending'];
                            $deadlineText = $job->deadline ? $job->deadline->diffForHumans() : 'No deadline';
                        @endphp
                        <a href="{{ route('employee.jobs.show', $job) }}" class="block p-3 rounded-xl {{ $statusColor['bg'] }} border {{ $statusColor['border'] }} cursor-pointer hover:opacity-80 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 {{ $statusColor['icon'] }} rounded-lg flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900">{{ $job->service->name ?? 'Service' }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $job->customer->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-400 mt-1">Due: {{ $deadlineText }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-sm text-slate-500 text-center">No jobs assigned</p>
                    </div>
                @endif
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
        jobsByDate: {},
        hoverDay: null,
        activeDay: null,

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
                const response = await fetch(`/employee/jobs-by-month?year=${this.currentYear}&month=${this.currentMonth}`);
                const data = await response.json();
                // Re-index by day number for calendar lookup
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
