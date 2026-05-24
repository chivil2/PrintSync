@extends('layouts.app.employee')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Service Jobs</h1>
        <p class="mt-2 text-zinc-600">View and manage your assigned service jobs</p>
    </div>

    <div class="bg-white rounded-lg border border-zinc-200 p-12 text-center">
        <div class="text-zinc-400">No jobs assigned yet</div>
    </div>
</div>
@endsection
