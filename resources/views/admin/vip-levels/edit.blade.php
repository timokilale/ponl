<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit VIP Level') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.vip-levels.show', $vipLevel) }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to VIP Level Details
                        </a>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Edit VIP Level</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.vip-levels.update', $vipLevel) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $vipLevel->name)" required autofocus />
                                <p class="text-sm text-gray-500 mt-1">E.g., "VIP 1", "Bronze", "Silver", etc.</p>
                            </div>

                            <div>
                                <x-input-label for="deposit_required" :value="__('Deposit Required (USDT)')" />
                                <x-text-input id="deposit_required" class="block mt-1 w-full" type="number" name="deposit_required" :value="old('deposit_required', $vipLevel->deposit_required)" step="0.01" min="0" required />
                                <p class="text-sm text-gray-500 mt-1">Minimum deposit amount required to reach this level.</p>
                            </div>

                            <div>
                                <x-input-label for="reward_multiplier" :value="__('Reward Multiplier')" />
                                <x-text-input id="reward_multiplier" class="block mt-1 w-full" type="number" name="reward_multiplier" :value="old('reward_multiplier', $vipLevel->reward_multiplier)" step="0.01" min="1" required />
                                <p class="text-sm text-gray-500 mt-1">Multiplier applied to task rewards (1.0 = no bonus).</p>
                            </div>

                            <div>
                                <x-input-label for="daily_tasks_limit" :value="__('Daily Tasks Limit')" />
                                <x-text-input id="daily_tasks_limit" class="block mt-1 w-full" type="number" name="daily_tasks_limit" :value="old('daily_tasks_limit', $vipLevel->daily_tasks_limit)" min="1" required />
                                <p class="text-sm text-gray-500 mt-1">Maximum number of tasks a user can complete per day.</p>
                            </div>

                            <div>
                                <x-input-label for="withdrawal_fee_discount" :value="__('Withdrawal Fee Discount')" />
                                <x-text-input id="withdrawal_fee_discount" class="block mt-1 w-full" type="number" name="withdrawal_fee_discount" :value="old('withdrawal_fee_discount', $vipLevel->withdrawal_fee_discount)" step="0.01" min="0" max="1" required />
                                <p class="text-sm text-gray-500 mt-1">Discount on withdrawal fees (0 to 1, where 0 = no discount, 0.5 = 50% discount).</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update VIP Level') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
