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
                <div class="flex items-center gap-2">
                    <button @click="previousMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-chevron-left text-slate-400 text-xs"></i>
                    </button>
                    <div class="text-xs text-slate-500 font-medium w-24 text-center" x-text="currentMonthName + ' ' + currentYear">{{ $currentMonth }} {{ $currentYear }}</div>
                    <button @click="nextMonth()" class="p-1 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center mb-2 text-xs text-slate-400 font-medium px-1">
                @foreach ($weekDays as $d)
                    <div>{{ $d }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-sm">
                <template x-for="day in calendarDays" :key="day">
                    <div x-show="day !== null" class="py-1.5 rounded-xl transition-colors cursor-pointer relative"
                         :class="day === today && currentMonth === {{ now()->month }} && currentYear === {{ now()->year }} ? 'bg-orange-500 text-white font-bold' : 'text-slate-600 hover:bg-slate-100'">
                        <span x-text="day"></span>
                        <template x-if="jobsByDate[day]">
                            <div class="absolute bottom-0.5 left-1/2 transform -translate-x-1/2 flex gap-0.5">
                                <template x-for="job in jobsByDate[day].slice(0, 2)" :key="job.id">
                                    <div class="w-1.5 h-1.5 rounded-full"
                                         :class="job.priority === 'urgent' ? 'bg-red-500' : (job.priority === 'high' ? 'bg-orange-500' : 'bg-blue-500')"></div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <template x-if="Object.keys(jobsByDate).length > 0">
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="text-xs font-medium text-slate-400 px-1 mb-3">UPCOMING DEADLINES</div>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        <template x-for="[day, jobs] in Object.entries(jobsByDate)" :key="day">
                            <template x-for="job in jobs.slice(0, 3)" :key="job.id">
                                <a :href="'/employee/jobs/' + job.id" class="block p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full"
                                             :class="job.priority === 'urgent' ? 'bg-red-500' : (job.priority === 'high' ? 'bg-orange-500' : 'bg-blue-500')"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-slate-900 truncate" x-text="job.name"></p>
                                            <p class="text-xs text-slate-500" x-text="job.deadline_formatted"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </template>
                    </div>
                </div>
            </template>
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

<script>
function calendar() {
    return {
        currentYear: null,
        currentMonth: null,
        currentMonthName: '',
        today: {{ now()->day }},
        calendarDays: [],
        jobsByDate: {},

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
                const response = await fetch(`/api/employee/jobs-by-month?year=${this.currentYear}&month=${this.currentMonth}`);
                const data = await response.json();
                this.jobsByDate = data.jobs_by_date;
            } catch (error) {
                console.error('Error fetching jobs:', error);
            }
        }
    }
}
</script>
