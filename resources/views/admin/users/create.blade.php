<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Users
                        </a>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Create New User</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="username" :value="__('Username')" />
                                <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            </div>

                            <div>
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" />
                            </div>

                            <div>
                                <x-input-label for="balance" :value="__('Initial Balance (USDT)')" />
                                <x-text-input id="balance" class="block mt-1 w-full" type="number" name="balance" :value="old('balance', 0)" step="0.01" min="0" />
                            </div>

                            <div>
                                <x-input-label for="vip_level_id" :value="__('VIP Level')" />
                                <select id="vip_level_id" name="vip_level_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($vipLevels as $vipLevel)
                                        <option value="{{ $vipLevel->id }}" {{ (old('vip_level_id') == $vipLevel->id) ? 'selected' : '' }}>
                                            {{ $vipLevel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="flex items-center mt-4">
                                <input id="is_admin" type="checkbox" name="is_admin" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_admin') ? 'checked' : '' }}>
                                <label for="is_admin" class="ml-2 text-sm text-gray-600">{{ __('Admin User') }}</label>
                            </div>

                            <div class="flex items-center mt-4">
                                <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 text-sm text-gray-600">{{ __('Active Account') }}</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
