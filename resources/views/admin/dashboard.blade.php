<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Users Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Users') }}</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                                <p class="text-sm text-gray-600">{{ __('Total Users') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-semibold text-green-600">+{{ $newUsers }}</p>
                                <p class="text-sm text-gray-600">{{ __('Last 7 days') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm">{{ $activeUsers }} {{ __('active users in the last 24 hours') }}</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('View all users') }} &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Deposits Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Deposits') }}</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-3xl font-bold">${{ number_format($totalDeposits, 2) }}</p>
                                <p class="text-sm text-gray-600">{{ __('Total Deposits') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.transactions.index', ['type' => 'credit', 'reference_type' => 'coinbase_charge']) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('View all deposits') }} &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Withdrawals Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Withdrawals') }}</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-3xl font-bold">${{ number_format($totalWithdrawals, 2) }}</p>
                                <p class="text-sm text-gray-600">{{ __('Total Withdrawals') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-semibold text-yellow-600">{{ $pendingWithdrawals }}</p>
                                <p class="text-sm text-gray-600">{{ __('Pending') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.withdrawals.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('View all withdrawals') }} &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Task Completions Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Task Completions') }}</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-3xl font-bold">{{ $pendingTaskCompletions }}</p>
                                <p class="text-sm text-gray-600">{{ __('Pending Approvals') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.task-completions.index', ['status' => 'pending']) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('View pending tasks') }} &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Recent Transactions') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('User') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->user->username }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="{{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($transaction->reference_type) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.transactions.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('View all transactions') }} &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Recent Users') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Username') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Balance') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Joined') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->username }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($user->balance, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('View all users') }} &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
