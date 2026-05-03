@extends('layouts.customer')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-zinc-900" style="font-family: 'Neue Haas Grotesk Display Pro', sans-serif;">Profile</h1>
        <p class="mt-2 text-zinc-600">Update your personal information</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($user)
        <div class="bg-white rounded-lg border border-zinc-200 p-6">
            <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-zinc-900 mb-2">First Name</label>
                        <input 
                            type="text" 
                            name="first_name" 
                            id="first_name" 
                            value="{{ old('first_name', $user->first_name ?? '') }}"
                            required
                            autofocus
                            autocomplete="given-name"
                            class="w-full px-4 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-[#E8743B] focus:border-[#E8743B] outline-none transition-colors text-black"
                        >
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-zinc-900 mb-2">Last Name</label>
                        <input 
                            type="text" 
                            name="last_name" 
                            id="last_name" 
                            value="{{ old('last_name', $user->last_name ?? '') }}"
                            required
                            autocomplete="family-name"
                            class="w-full px-4 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-[#E8743B] focus:border-[#E8743B] outline-none transition-colors text-black"
                        >
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-zinc-900 mb-2">Phone Number</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone', $user->phone ?? '') }}"
                        autocomplete="tel"
                        class="w-full px-4 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-[#E8743B] focus:border-[#E8743B] outline-none transition-colors text-black"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-900 mb-2">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $user->email ?? '') }}"
                        required
                        autocomplete="email"
                        class="w-full px-4 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-[#E8743B] focus:border-[#E8743B] outline-none transition-colors text-black"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if($hasUnverifiedEmail)
                        <div class="mt-3 text-sm text-zinc-600">
                            Your email address is unverified.
                            <form action="{{ route('customer.profile.resend') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-[#19A7CE] hover:underline cursor-pointer">
                                    Click here to re-send the verification email.
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-2 bg-[#E8743B] hover:bg-[#d66532] text-white font-medium rounded-lg transition-colors">
                        Save
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            User not found.
        </div>
    @endif
</div>
@endsection
