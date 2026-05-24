<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Owner Dashboard</h1>
            <p class="text-white/80">Welcome, {{ auth()->user()->name }}</p>
        </div>

        <div class="p-12 flex flex-col items-center justify-center text-center text-zinc-500 dark:text-zinc-400">
            <p>Dashboard content coming soon...</p>
        </div>
    </div>
</x-layouts::app.owner>
