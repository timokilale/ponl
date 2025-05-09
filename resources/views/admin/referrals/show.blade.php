<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Referral Details') }}
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
                        <h3 class="text-lg font-medium">Referral Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">Referral Information</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Referral ID</h5>
                                    <p>{{ $referral->id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Status</h5>
                                    <p>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($referral->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($referral->status == 'active') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($referral->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Reward</h5>
                                    <p>{{ number_format($referral->reward, 2) }} USDT</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Created At</h5>
                                    <p>{{ $referral->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Updated At</h5>
                                    <p>{{ $referral->updated_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">Referrer Information</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">User ID</h5>
                                    <p>{{ $referral->referrer->id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Username</h5>
                                    <p>{{ $referral->referrer->username }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Email</h5>
                                    <p>{{ $referral->referrer->email }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Referral Code</h5>
                                    <p>{{ $referral->referrer->referral_code }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Total Referrals</h5>
                                    <p>{{ $referrerStats['total_referrals'] }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Total Rewards</h5>
                                    <p>{{ number_format($referrerStats['total_rewards'], 2) }} USDT</p>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $referral->referrer) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View Full User Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="text-md font-medium mb-4">Referred User Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">User ID</h5>
                                <p>{{ $referral->referred->id }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Username</h5>
                                <p>{{ $referral->referred->username }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Email</h5>
                                <p>{{ $referral->referred->email }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Registration Date</h5>
                                <p>{{ $referral->referred->created_at->format('M d, Y H:i:s') }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">VIP Level</h5>
                                <p>{{ $referral->referred->vipLevel->name }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Current Balance</h5>
                                <p>{{ number_format($referral->referred->balance, 2) }} USDT</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Total Deposits</h5>
                                <p>{{ number_format($referredStats['total_deposits'], 2) }} USDT</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Total Task Completions</h5>
                                <p>{{ $referredStats['total_task_completions'] }}</p>
                            </div>
                            <div class="col-span-2">
                                <a href="{{ route('admin.users.show', $referral->referred) }}" class="text-indigo-600 hover:text-indigo-900">
                                    View Full User Details
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($referralTransactions->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">Related Transactions</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                ID
                                            </th>
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
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($referralTransactions as $transaction)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $transaction->user->username }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type == 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($transaction->amount, 2) }} USDT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($transaction->status == 'completed') bg-green-100 text-green-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
