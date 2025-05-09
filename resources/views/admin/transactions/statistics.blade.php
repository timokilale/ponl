<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.transactions.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Transactions
                        </a>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Transaction Statistics</h3>
                        <div class="flex space-x-2">
                            <form action="{{ route('admin.transactions.statistics') }}" method="GET" class="flex items-center space-x-2">
                                <select name="period" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="7" {{ request('period') == 7 ? 'selected' : '' }}>Last 7 days</option>
                                    <option value="30" {{ request('period') == 30 ? 'selected' : '' }}>Last 30 days</option>
                                    <option value="90" {{ request('period') == 90 ? 'selected' : '' }}>Last 90 days</option>
                                    <option value="365" {{ request('period') == 365 ? 'selected' : '' }}>Last year</option>
                                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>All time</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                    Apply
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                            <h4 class="text-sm font-medium text-gray-500">Total Deposits</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDeposits, 2) }} USDT</p>
                            <p class="text-sm text-gray-600">{{ $depositCount }} transactions</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                            <h4 class="text-sm font-medium text-gray-500">Total Withdrawals</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalWithdrawals, 2) }} USDT</p>
                            <p class="text-sm text-gray-600">{{ $withdrawalCount }} transactions</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                            <h4 class="text-sm font-medium text-gray-500">Total Task Rewards</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalTaskRewards, 2) }} USDT</p>
                            <p class="text-sm text-gray-600">{{ $taskRewardCount }} transactions</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                            <h4 class="text-sm font-medium text-gray-500">Total Referral Rewards</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalReferralRewards, 2) }} USDT</p>
                            <p class="text-sm text-gray-600">{{ $referralRewardCount }} transactions</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Deposits vs Withdrawals</h4>
                            <div class="h-64">
                                <canvas id="depositsWithdrawalsChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Transaction Types Distribution</h4>
                            <div class="h-64">
                                <canvas id="transactionTypesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                        <h4 class="text-md font-medium mb-4">Daily Transaction Volume</h4>
                        <div class="h-80">
                            <canvas id="dailyVolumeChart"></canvas>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Top Users by Transaction Volume</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total Volume
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Transactions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($topUsers as $user)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $user->username }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $user->email }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($user->total_volume, 2) }} USDT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $user->transaction_count }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Recent Large Transactions</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Type
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Amount
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($largeTransactions as $transaction)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $transaction->user->username }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type == 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                    <div class="text-xs text-gray-500">
                                                        {{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($transaction->amount, 2) }} USDT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->created_at->format('M d, Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Deposits vs Withdrawals Chart
            const depositsWithdrawalsCtx = document.getElementById('depositsWithdrawalsChart').getContext('2d');
            new Chart(depositsWithdrawalsCtx, {
                type: 'bar',
                data: {
                    labels: ['Deposits', 'Withdrawals'],
                    datasets: [{
                        label: 'Amount (USDT)',
                        data: [{{ $totalDeposits }}, {{ $totalWithdrawals }}],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Transaction Types Distribution Chart
            const transactionTypesCtx = document.getElementById('transactionTypesChart').getContext('2d');
            new Chart(transactionTypesCtx, {
                type: 'pie',
                data: {
                    labels: ['Deposits', 'Withdrawals', 'Task Rewards', 'Referral Rewards', 'Other'],
                    datasets: [{
                        label: 'Transaction Count',
                        data: [
                            {{ $depositCount }}, 
                            {{ $withdrawalCount }}, 
                            {{ $taskRewardCount }}, 
                            {{ $referralRewardCount }},
                            {{ $otherCount }}
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Daily Volume Chart
            const dailyVolumeCtx = document.getElementById('dailyVolumeChart').getContext('2d');
            new Chart(dailyVolumeCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyVolume->pluck('date')) !!},
                    datasets: [{
                        label: 'Credits',
                        data: {!! json_encode($dailyVolume->pluck('credits')) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    },
                    {
                        label: 'Debits',
                        data: {!! json_encode($dailyVolume->pluck('debits')) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
