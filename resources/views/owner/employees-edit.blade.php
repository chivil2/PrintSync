<x-layouts::app.owner>
    <div class="p-6">
        <div class="bg-gradient-to-r from-blue-600 to-orange-500 -mx-6 -mt-6 px-6 pt-6 pb-8 mb-8">
            <h1 class="text-2xl font-bold text-white">Edit Employee</h1>
            <p class="text-white/80">Update employee information</p>
        </div>

        <div>
            <form action="{{ route('owner.employees.update', $employee) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm p-6 space-y-5">
                        <h2 class="text-lg font-semibold text-zinc-800">Personal Information</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-zinc-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-zinc-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" required
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-zinc-700 mb-1">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="+63XXXXXXXXXX"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm p-6 space-y-5">
                        <h2 class="text-lg font-semibold text-zinc-800">Employment Details</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="employee_id" class="block text-sm font-medium text-zinc-700 mb-1">Employee ID</label>
                                <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('employee_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-zinc-700 mb-1">Hire Date</label>
                                <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                @error('hire_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="specialization" class="block text-sm font-medium text-zinc-700 mb-1">Specialization</label>
                                <select id="specialization" name="specialization"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                    <option value="">Select specialization</option>
                                    <option value="printing_staff" {{ old('specialization', $employee->specialization) === 'printing_staff' ? 'selected' : '' }}>Printing Staff</option>
                                    <option value="technical_staff" {{ old('specialization', $employee->specialization) === 'technical_staff' ? 'selected' : '' }}>Technical Staff</option>
                                </select>
                                @error('specialization') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-zinc-700 mb-1">Hourly Rate</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm">$</span>
                                    <input type="number" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $employee->hourly_rate) }}" step="0.01" min="0"
                                        class="w-full rounded-lg border border-zinc-300 pl-7 pr-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                </div>
                                @error('hourly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="employee_status" class="block text-sm font-medium text-zinc-700 mb-1">Status</label>
                            <select id="employee_status" name="employee_status"
                                class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="active" {{ old('employee_status', $employee->employee_status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('employee_status', $employee->employee_status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('employee_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                        Update Employee
                    </button>
                    <a href="{{ route('owner.employees') }}"
                        class="px-5 py-2.5 text-zinc-700 font-medium rounded-lg hover:bg-zinc-100 transition-colors text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app.owner>
