<x-layouts::app.employee-sidebar>
    <flux:main>
        <flux:heading level="1">Employee Dashboard</flux:heading>
        <flux:text>Welcome, {{ auth()->user()->name }}</flux:text>

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold mb-2">Assigned Quotes</h3>
                <p class="text-4xl font-bold">0</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold mb-2">Pending Jobs</h3>
                <p class="text-4xl font-bold">0</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold mb-2">Completed Jobs</h3>
                <p class="text-4xl font-bold">0</p>
            </div>
        </div>
    </flux:main>
</x-layouts::app.employee-sidebar>
