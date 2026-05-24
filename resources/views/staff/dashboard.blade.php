@extends('layouts.customer')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-orange-500 to-blue-500 rounded-xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">
            Staff Dashboard
        </h1>
        <p class="text-lg opacity-90">Overview of employee operations and performance.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#E8743B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600">Active Jobs</p>
                    <p class="text-2xl font-bold text-zinc-900">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#19A7CE]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600">Completed</p>
                    <p class="text-2xl font-bold text-zinc-900">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-zinc-600">Employees</p>
                    <p class="text-2xl font-bold text-zinc-900">—</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
