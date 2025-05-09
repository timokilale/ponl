<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Withdrawal Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.withdrawals.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Withdrawals
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Withdrawal Details</h3>
                        <div class="flex space-x-2">
                            @if($withdrawal->status == 'pending')
                                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Are you sure you want to reject this withdrawal?')">
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">Withdrawal Information</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Withdrawal ID</h5>
                                    <p>{{ $withdrawal->id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Amount</h5>
                                    <p>{{ number_format($withdrawal->amount, 2) }} USDT</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Fee</h5>
                                    <p>{{ number_format($withdrawal->fee, 2) }} USDT ({{ number_format(($withdrawal->fee / $withdrawal->amount) * 100, 2) }}%)</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Net Amount</h5>
                                    <p class="font-bold">{{ number_format($withdrawal->amount - $withdrawal->fee, 2) }} USDT</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Wallet Address</h5>
                                    <p class="break-all">{{ $withdrawal->wallet_address }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Network/Chain</h5>
                                    <p>{{ $withdrawal->network ?? 'TRON (TRC20)' }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Status</h5>
                                    <p>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($withdrawal->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($withdrawal->status == 'approved') bg-blue-100 text-blue-800
                                            @elseif($withdrawal->status == 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Transaction Hash</h5>
                                    <p>{{ $withdrawal->transaction_hash ?? 'Not available' }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Created At</h5>
                                    <p>{{ $withdrawal->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Processed At</h5>
                                    <p>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y H:i:s') : 'Not processed yet' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">User Information</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">User ID</h5>
                                    <p>{{ $withdrawal->user->id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Username</h5>
                                    <p>{{ $withdrawal->user->username }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Email</h5>
                                    <p>{{ $withdrawal->user->email }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">VIP Level</h5>
                                    <p>{{ $withdrawal->user->vipLevel->name }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Current Balance</h5>
                                    <p>{{ number_format($withdrawal->user->balance, 2) }} USDT</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Total Deposits</h5>
                                    <p>{{ number_format($totalDeposits ?? 0, 2) }} USDT</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Total Withdrawals</h5>
                                    <p>{{ number_format($totalWithdrawals ?? 0, 2) }} USDT</p>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $withdrawal->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View Full User Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($withdrawal->admin_notes)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="text-md font-medium mb-4">Admin Notes</h4>
                            <div class="mt-1 p-2 bg-white rounded border border-gray-200">
                                {!! nl2br(e($withdrawal->admin_notes)) !!}
                            </div>
                        </div>
                    @endif

                    @if($withdrawal->status == 'pending')
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">Process Withdrawal</h4>
                            <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="transaction_hash" :value="__('Transaction Hash (Optional)')" />
                                    <x-text-input id="transaction_hash" class="block mt-1 w-full" type="text" name="transaction_hash" :value="old('transaction_hash')" />
                                    <p class="text-sm text-gray-500 mt-1">Enter the transaction hash if you've already processed this withdrawal.</p>
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="admin_notes_approve" :value="__('Admin Notes (Optional)')" />
                                    <textarea id="admin_notes_approve" name="admin_notes" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <x-primary-button>
                                        {{ __('Approve Withdrawal') }}
                                    </x-primary-button>
                                </div>
                            </form>

                            <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="admin_notes_reject" :value="__('Rejection Reason (Required)')" />
                                    <textarea id="admin_notes_reject" name="admin_notes" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Reject Withdrawal') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
