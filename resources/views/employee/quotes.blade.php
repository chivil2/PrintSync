<x-layouts::app.employee-sidebar>
    <flux:main>
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <flux:heading level="1" class="text-white!">My Quotes</flux:heading>
            <flux:text class="text-white/80!">Manage your assigned quotes</flux:text>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4 flex-wrap">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" placeholder="Search quotes..." class="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-shadow" disabled>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 text-sm rounded-lg">All</span>
                    <span class="px-3 py-1.5 text-zinc-400 text-sm rounded-lg">Pending</span>
                    <span class="px-3 py-1.5 text-zinc-400 text-sm rounded-lg">Approved</span>
                    <span class="px-3 py-1.5 text-zinc-400 text-sm rounded-lg">Declined</span>
                </div>
            </div>

            <div class="p-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">Quote Management</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm">This feature is coming soon. You'll be able to review and manage your assigned quotes here.</p>
            </div>
        </div>
    </flux:main>
</x-layouts::app.employee-sidebar>
