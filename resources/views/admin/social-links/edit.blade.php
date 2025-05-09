<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Social Link') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.social-links.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Social Links
                        </a>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Edit Social Link: {{ $socialLink->platform }}</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.social-links.update', $socialLink) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="platform" class="block text-sm font-medium text-gray-700 mb-1">
                                    Platform Name
                                </label>
                                <input type="text" name="platform" id="platform" value="{{ old('platform', $socialLink->platform) }}" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <p class="mt-1 text-sm text-gray-500">
                                    E.g., Facebook, Twitter, Instagram, etc.
                                </p>
                            </div>

                            <div>
                                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">
                                    URL
                                </label>
                                <input type="url" name="url" id="url" value="{{ old('url', $socialLink->url) }}" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <p class="mt-1 text-sm text-gray-500">
                                    Full URL including https://
                                </p>
                            </div>

                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                                    Icon Class
                                </label>
                                <input type="text" name="icon" id="icon" value="{{ old('icon', $socialLink->icon) }}" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <p class="mt-1 text-sm text-gray-500">
                                    Font Awesome icon class (e.g., fab fa-facebook, fab fa-twitter)
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                                        {{ old('is_active', $socialLink->is_active) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Only active social links will be displayed on the website
                                </p>
                            </div>

                            <div class="flex justify-between">
                                <form action="{{ route('admin.social-links.destroy', $socialLink) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this social link? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Delete Social Link
                                    </button>
                                </form>
                                
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Update Social Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
