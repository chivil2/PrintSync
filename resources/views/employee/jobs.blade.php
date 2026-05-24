<x-layouts::app.employee-sidebar>
    <flux:main>
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <flux:heading level="1" class="text-white!">Service Jobs</flux:heading>
            <flux:text class="text-white/80!">View and manage your assigned service jobs</flux:text>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4 flex-wrap">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" placeholder="Search jobs..." class="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-shadow" disabled>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 text-sm rounded-lg">All</span>
                    <span class="px-3 py-1.5 text-zinc-400 text-sm rounded-lg">Pending</span>
                    <span class="px-3 py-1.5 text-zinc-400 text-sm rounded-lg">In Progress</span>
                    <span class="px-3 py-1.5 text-zinc-400 text-sm rounded-lg">Completed</span>
                </div>
            </div>

            <div class="p-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Job Management</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm">This feature is coming soon. You'll be able to view and manage your assigned service jobs here.</p>
            </div>
        </div>
    </flux:main>
</x-layouts::app.employee-sidebar>
