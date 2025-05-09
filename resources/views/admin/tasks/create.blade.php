<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.tasks.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Tasks
                        </a>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Create New Task</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.tasks.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="title" :value="__('Title')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="platform" :value="__('Platform')" />
                                <x-text-input id="platform" class="block mt-1 w-full" type="text" name="platform" :value="old('platform')" required />
                            </div>

                            <div>
                                <x-input-label for="reward" :value="__('Reward (USDT)')" />
                                <x-text-input id="reward" class="block mt-1 w-full" type="number" name="reward" :value="old('reward')" step="0.01" min="0" required />
                            </div>

                            <div>
                                <x-input-label for="time_required" :value="__('Time Required')" />
                                <x-text-input id="time_required" class="block mt-1 w-full" type="text" name="time_required" :value="old('time_required')" placeholder="e.g. 5-10 minutes" required />
                            </div>

                            <div>
                                <x-input-label for="difficulty" :value="__('Difficulty')" />
                                <select id="difficulty" name="difficulty" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="vip_level_required" :value="__('VIP Level Required')" />
                                <select id="vip_level_required" name="vip_level_required" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    @foreach($vipLevels as $vipLevel)
                                        <option value="{{ $vipLevel->id }}" {{ old('vip_level_required') == $vipLevel->id ? 'selected' : '' }}>
                                            {{ $vipLevel->name }} (Deposit: {{ number_format($vipLevel->deposit_required, 2) }} USDT)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="5" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('description') }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">Provide detailed instructions for completing this task.</p>
                            </div>

                            <div class="col-span-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <span class="ml-2">Active (task will be visible to users)</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Task') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
