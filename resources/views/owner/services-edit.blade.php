<x-layouts::app.owner>
    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto" x-data="{ 
        previewImage: null,
        showRemove: false,
        isLoading: false,
        existingImage: @if($service->image) '{{ asset('storage/' . $service->image) }}' @else '' @endif,
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                    this.showRemove = true;
                };
                reader.readAsDataURL(file);
            }
        },
        removeImage() {
            this.$refs.imageInput.value = '';
            this.previewImage = null;
            this.showRemove = false;
        },
        handleSubmit() {
            this.isLoading = true;
            this.$refs.form.submit();
        }
    }">
        <!-- Header -->
        <div class="bg-blue-600 rounded-lg px-6 py-4 mb-6">
            <h1 class="text-xl font-bold text-white">Edit Service</h1>
            <p class="text-blue-100 text-sm">Update service information</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-300 p-6">
            <!-- Card Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-bold text-slate-900">Service</h2>
                    <span class="inline-block px-2 py-0.5 text-[11px] font-semibold rounded-lg {{ $service->service_type === 'printing' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-purple-100 text-purple-800 border border-purple-200' }}">
                        {{ ucfirst($service->service_type) }}
                    </span>
                </div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <span class="text-sm font-medium text-slate-700">Active</span>
                    <div class="relative">
                        <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </div>
                </label>
            </div>

            <form x-ref="form" action="{{ route('owner.services.update', ['id' => $service->id, 'serviceType' => $service->service_type]) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit.prevent="handleSubmit">
                @csrf
                @method('PUT')

                <!-- Image Upload Section -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-slate-700 mb-2">Service Image</label>
                    <div class="flex items-start gap-4">
                        <!-- Image Preview -->
                        <div class="relative">
                            <div class="relative inline-block">
                                <img :src="previewImage || existingImage" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-slate-300 bg-slate-100">
                                <div x-show="!previewImage && !existingImage" class="absolute inset-0 flex items-center justify-center text-slate-400 text-xs">
                                    No image
                                </div>
                                <button type="button" @click="removeImage()" x-show="showRemove" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                    ×
                                </button>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" x-ref="imageInput" name="image" accept="image/*"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                @change="handleFileUpload($event)">
                            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service ID (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-2">Service ID</label>
                        <input type="text" value="{{ $service->id }}" readonly
                            class="w-full px-4 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600 font-mono">
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Service Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter service name">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-slate-700 mb-2">Price (₱) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">₱</span>
                            <input type="number" id="price" name="price" value="{{ old('price', $service->price) }}" step="0.01" min="0" required
                                class="w-full pl-7 pr-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0.00">
                        </div>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Production Time -->
                    <div>
                        <label for="production_time" class="block text-sm font-medium text-slate-700 mb-2">Production Time (days)</label>
                        <div class="relative">
                            <input type="number" id="production_time" name="production_time" value="{{ old('production_time', $service->production_time) }}" min="1"
                                class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="7">
                        </div>
                        @error('production_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description <span class="text-red-500">*</span></label>
                        <textarea id="description" name="description" rows="4" required
                            class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter service description">{{ old('description', $service->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                    <a href="{{ route('owner.services.index') }}"
                        class="px-6 py-2 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" :disabled="isLoading"
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Updating...' : 'Update Service'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app.owner>