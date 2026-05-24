<x-layouts::app.owner>
    <flux:main>
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <flux:heading level="1" class="text-white!">Owner Dashboard</flux:heading>
            <flux:text class="text-white/80!">Welcome, {{ auth()->user()->name }}</flux:text>
        </div>

        <div class="p-12 flex flex-col items-center justify-center text-center text-zinc-500 dark:text-zinc-400">
            <p>Dashboard content coming soon...</p>
        </div>
    </flux:main>
</x-layouts::app.owner>
