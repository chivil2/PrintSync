<x-layouts::customer>
    @section('content')
        <h1 class="text-3xl font-bold mb-4">Customer Dashboard</h1>
        <p class="text-gray-600 mb-8">Welcome, {{ auth()->user()->name }}</p>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-2">Recent Quotes</h3>
                <p class="text-4xl font-bold">0</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-2">Active Orders</h3>
                <p class="text-4xl font-bold">0</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold mb-2">Credit Limit</h3>
                <p class="text-4xl font-bold">₱{{ number_format(auth()->user()->credit_limit ?? 0, 2) }}</p>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">Quick Actions</h2>
            <div class="flex gap-4">
                <a href="{{ route('customer.store') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Request Quote
                </a>
                <a href="{{ route('customer.orders') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                    View Orders
                </a>
                <a href="{{ route('customer.profile') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                    Update Profile
                </a>
            </div>
        </div>
    @endsection
</x-layouts::customer>
