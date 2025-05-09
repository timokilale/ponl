<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Withdrawals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Request Withdrawal</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">Available Balance</p>
                                <p class="text-2xl font-semibold">{{ number_format(auth()->user()->balance, 2) }} USDT</p>
                            </div>

                            @if(auth()->user()->balance >= $minWithdrawalAmount)
                                <form action="{{ route('withdrawals.store') }}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (USDT)</label>
                                            <input type="number" id="amount" name="amount" min="{{ $minWithdrawalAmount }}" max="{{ auth()->user()->balance }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <p class="text-xs text-gray-500 mt-1">Minimum: {{ number_format($minWithdrawalAmount, 2) }} USDT</p>
                                            @error('amount')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="network" class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                                            <select id="network" name="network" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="TRC20">USDT (TRC20 - TRON)</option>
                                                <option value="ERC20">USDT (ERC20 - ETHEREUM)</option>
                                                <option value="BEP20">USDT (BEP20 - BINANCE SMART CHAIN)</option>
                                            </select>
                                            @error('network')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-1">Wallet Address</label>
                                            <input type="text" id="wallet_address" name="wallet_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            @error('wallet_address')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!--<div class="mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative">
                                        <p class="text-sm">
                                            <strong>Note:</strong> A fee of {{ $adjustedFeePercentage }}% will be deducted from your withdrawal amount.
                                            Please double-check your wallet address before submitting.
                                        </p>
                                    </div>-->

                                    <div class="mt-4 flex justify-end">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Request Withdrawal
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative">
                                    <p>You need at least {{ number_format($minWithdrawalAmount, 2) }} USDT to request a withdrawal.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Your Withdrawals</h3>
                    @if($withdrawals->isEmpty())
                        <p class="text-gray-500">You haven't made any withdrawals yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($withdrawals as $withdrawal)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $withdrawal->created_at->format('M d, Y H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($withdrawal->amount, 2) }} USDT</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($withdrawal->fee, 2) }} USDT</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ ucfirst($withdrawal->method) }}
                                                    @if($withdrawal->network)
                                                        ({{ $withdrawal->network }})
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $withdrawal->wallet_address }}">
                                                    {{ $withdrawal->wallet_address }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($withdrawal->status === 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($withdrawal->status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($withdrawal->status === 'rejected')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $withdrawals->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
