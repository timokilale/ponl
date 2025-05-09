<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions') }}
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

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Transaction Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Current Balance</p>
                                <p class="text-2xl font-semibold">{{ number_format(auth()->user()->balance, 2) }} USDT</p>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total Credits</p>
                                <p class="text-2xl font-semibold text-green-600">{{ number_format($totalCredits, 2) }} USDT</p>
                            </div>
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total Debits</p>
                                <p class="text-2xl font-semibold text-red-600">{{ number_format($totalDebits, 2) }} USDT</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Filter Transactions</h3>
                        <form action="{{ route('transactions.index') }}" method="GET" class="bg-gray-100 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">All Types</option>
                                        <option value="credit" {{ $type === 'credit' ? 'selected' : '' }}>Credits</option>
                                        <option value="debit" {{ $type === 'debit' ? 'selected' : '' }}>Debits</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <h3 class="text-lg font-medium mb-4">Transaction History</h3>
                    @if($transactions->isEmpty())
                        <p class="text-gray-500">No transactions found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($transaction->created_at instanceof \Carbon\Carbon)
                                                        {{ $transaction->created_at->format('M d, Y') }}
                                                    @else
                                                        {{ $transaction->created_at ?? 'N/A' }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    @if($transaction->created_at instanceof \Carbon\Carbon)
                                                        {{ $transaction->created_at->format('H:i:s') }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $transaction->description }}</div>
                                                @if($transaction->reference_type)
                                                    <div class="text-xs text-gray-500">
                                                        {{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }}
                                                        @if($transaction->wallet_address)
                                                            <span class="block truncate max-w-xs" title="{{ $transaction->wallet_address }}">
                                                                {{ $transaction->wallet_address }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} USDT
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($transaction->balance_after, 2) }} USDT</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($transaction->status === 'completed')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Completed
                                                    </span>
                                                @elseif($transaction->status === 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($transaction->status === 'cancelled')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Cancelled
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
