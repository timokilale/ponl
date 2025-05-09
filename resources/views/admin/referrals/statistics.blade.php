<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Referral Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.referrals.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Referrals
                        </a>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Referral Statistics</h3>
                        <div class="flex space-x-2">
                            <form action="{{ route('admin.referrals.statistics') }}" method="GET" class="flex items-center space-x-2">
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
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                            <h4 class="text-sm font-medium text-gray-500">Total Referrals</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalReferrals }}</p>
                            <p class="text-sm text-gray-600">{{ $activeReferrals }} active</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                            <h4 class="text-sm font-medium text-gray-500">Total Rewards Paid</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRewards, 2) }} USDT</p>
                            <p class="text-sm text-gray-600">{{ $rewardTransactions }} transactions</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                            <h4 class="text-sm font-medium text-gray-500">Average Reward</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($averageReward, 2) }} USDT</p>
                            <p class="text-sm text-gray-600">per completed referral</p>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                            <h4 class="text-sm font-medium text-gray-500">Conversion Rate</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($conversionRate, 1) }}%</p>
                            <p class="text-sm text-gray-600">of referrals become active</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Referral Status Distribution</h4>
                            <div class="h-64">
                                <canvas id="referralStatusChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Daily Referral Signups</h4>
                            <div class="h-64">
                                <canvas id="dailyReferralsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Top Referrers</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Referrals
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total Rewards
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($topReferrers as $referrer)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $referrer->username }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $referrer->email }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $referrer->referral_count }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($referrer->total_rewards, 2) }} USDT
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h4 class="text-md font-medium mb-4">Recent Referrals</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Referrer
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Referred User
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentReferrals as $referral)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $referral->referrer->username }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $referral->referred->username }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($referral->status == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($referral->status == 'active') bg-blue-100 text-blue-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ ucfirst($referral->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $referral->created_at->format('M d, Y') }}
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
            // Referral Status Distribution Chart
            const statusCtx = document.getElementById('referralStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: ['Pending', 'Active', 'Completed'],
                    datasets: [{
                        label: 'Referral Status',
                        data: [
                            {{ $pendingReferrals }}, 
                            {{ $activeReferrals }}, 
                            {{ $completedReferrals }}
                        ],
                        backgroundColor: [
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Daily Referrals Chart
            const dailyCtx = document.getElementById('dailyReferralsChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyReferrals->pluck('date')) !!},
                    datasets: [{
                        label: 'New Referrals',
                        data: {!! json_encode($dailyReferrals->pluck('count')) !!},
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
