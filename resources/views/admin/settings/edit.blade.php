<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Setting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.settings.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Settings
                        </a>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Edit Setting: {{ $setting->key }}</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update', $setting) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="key" class="block text-sm font-medium text-gray-700 mb-1">
                                    Setting Key
                                </label>
                                <input type="text" name="key" id="key" value="{{ old('key', $setting->key) }}" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100"
                                    readonly>
                                <p class="mt-1 text-sm text-gray-500">
                                    The setting key cannot be changed
                                </p>
                            </div>

                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                                    Setting Value
                                </label>
                                <input type="text" name="value" id="value" value="{{ old('value', $setting->value) }}" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description (Optional)
                                </label>
                                <textarea name="description" id="description" rows="3" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $setting->description) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">
                                    Brief explanation of what this setting controls
                                </p>
                            </div>

                            <div class="flex justify-between">
                                <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this setting? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Delete Setting
                                    </button>
                                </form>
                                
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Update Setting
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
