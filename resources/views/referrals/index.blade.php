<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Referrals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Your Referral Link</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Share this link with your friends and earn {{ $referralRewardPercentage }}% of their earnings!</p>
                            <div class="flex">
                                <input type="text" value="{{ $referralLink }}" class="flex-1 border-gray-300 rounded-l-md shadow-sm" readonly>
                                <button onclick="copyToClipboard('{{ $referralLink }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-r-md">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Referral Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total Referrals</p>
                                <p class="text-2xl font-semibold">{{ $referrals->count() }}</p>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total Earnings</p>
                                <p class="text-2xl font-semibold">${{ number_format($totalEarnings, 2) }}</p>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Reward Rate</p>
                                <p class="text-2xl font-semibold">{{ $referralRewardPercentage }}%</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Your Referrals</h3>
                    @if($referrals->isEmpty())
                        <p class="text-gray-500">You haven't referred anyone yet. Share your referral link to start earning!</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Joined</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earnings</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($referrals as $referral)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $referral->referred->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $referral->referred->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $referral->created_at->format('M d, Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($referral->referred->email_verified_at)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Verified
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-green-600">${{ number_format($referral->earnings, 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Referral link copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</x-app-layout>
