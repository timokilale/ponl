<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">System Settings</h3>
                        <a href="{{ route('admin.settings.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Add New Setting
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- General Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">General Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['general'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                            value="{{ $setting->value }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">Payment Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['payment'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                            value="{{ $setting->value }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Withdrawal Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">Withdrawal Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['withdrawal'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                            value="{{ $setting->value }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Referral Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">Referral Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['referral'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                            value="{{ $setting->value }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Task Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">Task Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['task'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                            value="{{ $setting->value }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- VIP Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">VIP Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['vip'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>

                                        @if($setting->key === 'vip_auto_upgrade')
                                            <select name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="true" {{ $setting->value === 'true' ? 'selected' : '' }}>Enabled - Auto-upgrade based on balance</option>
                                                <option value="false" {{ $setting->value === 'false' ? 'selected' : '' }}>Disabled - Manual VIP level assignment only</option>
                                            </select>
                                        @else
                                            <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                value="{{ $setting->value }}"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Other Settings -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium mb-4 pb-2 border-b">Other Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($groupedSettings['other'] as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->description)
                                                <span class="text-xs text-gray-500 ml-1" title="{{ $setting->description }}">
                                                    (?)
                                                </span>
                                            @endif
                                        </label>
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                            value="{{ $setting->value }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
