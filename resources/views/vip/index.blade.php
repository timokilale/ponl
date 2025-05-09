<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('VIP Levels') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Your VIP Status</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm text-gray-600">Current Level</p>
                                    <p class="text-lg font-semibold">{{ $currentVipLevel->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Current Balance</p>
                                    <p class="text-lg font-semibold">{{ number_format(auth()->user()->balance, 2) }} USDT</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Daily Task Limit</p>
                                    <p class="text-lg font-semibold">{{ $currentVipLevel->daily_tasks_limit }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Reward Multiplier</p>
                                    <p class="text-lg font-semibold">{{ $currentVipLevel->reward_multiplier }}x</p>
                                </div>
                            </div>

                            @if($nextVipLevel)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-1">Progress to {{ $nextVipLevel->name }}</p>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Need to deposit {{ number_format($nextVipLevel->deposit_required - $currentVipLevel->deposit_required, 2) }} USDT more to reach next level
                                    </p>
                                </div>
                            @else
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600">You have reached the highest VIP level!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mb-4">VIP Levels and Benefits</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deposit Required</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Task Limit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward Multiplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Withdrawal Fee Discount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($vipLevels as $vipLevel)
                                    <tr class="{{ $vipLevel->id == $currentVipLevel->id ? 'bg-blue-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $vipLevel->name }}
                                                </div>
                                                @if($vipLevel->id == $currentVipLevel->id)
                                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Current
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($vipLevel->deposit_required, 2) }} USDT</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $vipLevel->daily_tasks_limit }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $vipLevel->reward_multiplier }}x</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $vipLevel->withdrawal_fee_discount * 100 }}%</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium mb-4">How to Increase Your VIP Level</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="mb-4">
                                <p class="font-medium mb-2">VIP Level Upgrade System:</p>
                                @if($autoUpgradeEnabled)
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Automatic
                                        </span>
                                        <span class="ml-2 text-sm text-gray-600">Your VIP level will automatically increase as your balance grows.</span>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Manual
                                        </span>
                                        <span class="ml-2 text-sm text-gray-600">VIP levels must be purchased separately. Contact support for details.</span>
                                    </div>
                                @endif
                            </div>

                            <ul class="list-disc list-inside space-y-2">
                                <li>Make deposits to increase your VIP level</li>
                                <li>Higher VIP levels give you better rewards and more daily tasks</li>
                                <li>VIP level benefits are permanent once you reach them</li>
                                <li>Refer friends to earn bonus USDT</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
