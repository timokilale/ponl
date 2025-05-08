<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Account Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Account Summary</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <div class="text-sm text-gray-600">Balance</div>
                            <div class="text-xl font-bold text-blue-600">{{ number_format(auth()->user()->balance, 2) }} USDT</div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                            <div class="text-sm text-gray-600">VIP Level</div>
                            <div class="text-xl font-bold text-purple-600">{{ auth()->user()->vipLevel->name }}</div>
                            <div class="text-xs text-gray-500">{{ number_format(auth()->user()->vip_points) }} points</div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                            <div class="text-sm text-gray-600">Referrals</div>
                            <div class="text-xl font-bold text-green-600">{{ \App\Models\Referral::where('referrer_id', auth()->id())->count() }}</div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                            <div class="text-sm text-gray-600">Completed Tasks</div>
                            <div class="text-xl font-bold text-yellow-600">{{ \App\Models\TaskCompletion::where('user_id', auth()->id())->where('status', 'approved')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Quick Actions</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('tasks.index') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-lg font-medium tracking-tight text-gray-900">Available Tasks</h5>
                            <p class="text-sm text-gray-600">Browse and complete tasks to earn USDT</p>
                        </a>

                        <a href="{{ route('payment.deposit') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-lg font-medium tracking-tight text-gray-900">Deposit Funds</h5>
                            <p class="text-sm text-gray-600">Add USDT to your account balance</p>
                        </a>

                        <a href="{{ route('withdrawals.index') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-lg font-medium tracking-tight text-gray-900">Withdraw Funds</h5>
                            <p class="text-sm text-gray-600">Withdraw your earnings to your wallet</p>
                        </a>

                        <a href="{{ route('referrals.index') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-lg font-medium tracking-tight text-gray-900">Refer Friends</h5>
                            <p class="text-sm text-gray-600">Earn from your referrals' activities</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Recent Activity</h3>
                        <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach (\App\Models\Transaction::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(5)->get() as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $transaction->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} USDT
                                        </td>
                                    </tr>
                                @endforeach

                                @if (\App\Models\Transaction::where('user_id', auth()->id())->count() === 0)
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                            No transactions yet.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Recent Notifications</h3>
                        <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>

                    <div class="space-y-3">
                        @foreach (\App\Models\Notification::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(3)->get() as $notification)
                            <div class="border rounded-lg overflow-hidden {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                                <div class="px-4 py-2 border-b bg-gray-50 flex justify-between items-center">
                                    <div class="flex items-center">
                                        @if (!$notification->is_read)
                                            <span class="h-2 w-2 bg-blue-600 rounded-full mr-2"></span>
                                        @endif
                                        <h4 class="font-medium text-sm">{{ $notification->title }}</h4>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="p-3">
                                    <p class="text-sm text-gray-700">{{ Str::limit($notification->message, 100) }}</p>
                                </div>
                            </div>
                        @endforeach

                        @if (\App\Models\Notification::where('user_id', auth()->id())->count() === 0)
                            <div class="text-center py-4 text-gray-500">
                                <p>No notifications yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
