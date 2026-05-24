<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Owner Dashboard</h1>
                    <p class="text-white/80">Hello, {{ ucfirst(auth()->user()->first_name) }} {{ ucfirst(auth()->user()->last_name) }}</p>
                </div>
                <div class="text-right" x-data="{ time: '', date: '' }" x-init="time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); date = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }); setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }, 1000)">
                    <div class="text-white text-4xl font-mono font-bold" x-text="time"></div>
                    <div class="text-white/70 text-xs font-mono" x-text="date"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 1</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 2</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 3</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 4</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 5</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="text-zinc-400 text-xs font-semibold uppercase tracking-wider">Card 6</div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 w-3/4 rounded bg-zinc-100"></div>
                    <div class="h-4 w-1/2 rounded bg-zinc-100"></div>
                    <div class="h-4 w-5/6 rounded bg-zinc-100"></div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app.owner>
