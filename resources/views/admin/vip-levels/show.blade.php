<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('VIP Level Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.vip-levels.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to VIP Levels
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">VIP Level Details: {{ $vipLevel->name }}</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.vip-levels.edit', $vipLevel) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Edit VIP Level
                            </a>
                            <form action="{{ route('admin.vip-levels.destroy', $vipLevel) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this VIP level?')">
                                    Delete VIP Level
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ID</h4>
                                <p>{{ $vipLevel->id }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Name</h4>
                                <p>{{ $vipLevel->name }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Deposit Required</h4>
                                <p>{{ number_format($vipLevel->deposit_required, 2) }} USDT</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Reward Multiplier</h4>
                                <p>{{ number_format($vipLevel->reward_multiplier, 2) }}x</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Daily Tasks Limit</h4>
                                <p>{{ $vipLevel->daily_tasks_limit }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Withdrawal Fee Discount</h4>
                                <p>{{ number_format($vipLevel->withdrawal_fee_discount * 100, 0) }}%</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Created At</h4>
                                <p>{{ $vipLevel->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Updated At</h4>
                                <p>{{ $vipLevel->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Users with this VIP Level</h3>
                        @if($users->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Username
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Balance
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Joined
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($users as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $user->id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $user->username }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $user->email }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($user->balance, 2) }} USDT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $user->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $users->links() }}
                            </div>
                        @else
                            <p class="text-gray-500">No users with this VIP level.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-medium mb-4">Tasks Requiring this VIP Level</h3>
                        @if($tasks->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Title
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Platform
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Reward
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($tasks as $task)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $task->id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $task->title }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $task->platform }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($task->reward, 2) }} USDT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $task->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('admin.tasks.show', $task) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $tasks->links() }}
                            </div>
                        @else
                            <p class="text-gray-500">No tasks requiring this VIP level.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
