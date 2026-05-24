@extends('layouts.app.employee')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-600 to-orange-500 rounded-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-lg opacity-90">Manage your assigned quotes and service jobs.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">Assigned Jobs</div>
            <div class="mt-2 text-3xl font-bold text-blue-600">0</div>
        </div>
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">Pending Quotes</div>
            <div class="mt-2 text-3xl font-bold text-orange-600">0</div>
        </div>
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="text-zinc-500 text-sm font-medium">Completed</div>
            <div class="mt-2 text-3xl font-bold text-green-600">0</div>
        </div>
    </div>
</div>
@endsection
