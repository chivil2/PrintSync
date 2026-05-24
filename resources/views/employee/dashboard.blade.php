<x-layouts::app.employee-sidebar>
    <flux:main>
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <flux:heading level="1" class="text-white!">Employee Dashboard</flux:heading>
            <flux:text class="text-white/80!">Welcome, {{ auth()->user()->name }}</flux:text>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="group bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-default">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Assigned Quotes</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-0.5">0</p>
                    </div>
                </div>
            </div>

            <div class="group bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-default">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pending Jobs</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-0.5">0</p>
                    </div>
                </div>
            </div>

            <div class="group bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-default">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Completed Jobs</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-0.5">0</p>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts::app.employee-sidebar>
