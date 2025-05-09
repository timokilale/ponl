<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details') }}
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
                        <h3 class="text-lg font-medium">Transaction Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">Transaction Information</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Transaction ID</h5>
                                    <p>{{ $transaction->id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Type</h5>
                                    <p>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type == 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Amount</h5>
                                    <p class="font-bold">{{ number_format($transaction->amount, 2) }} USDT</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Reference Type</h5>
                                    <p>{{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Reference ID</h5>
                                    <p>{{ $transaction->reference_id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Status</h5>
                                    <p>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($transaction->status == 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Created At</h5>
                                    <p>{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Updated At</h5>
                                    <p>{{ $transaction->updated_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-medium mb-4">User Information</h4>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">User ID</h5>
                                    <p>{{ $transaction->user->id }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Username</h5>
                                    <p>{{ $transaction->user->username }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Email</h5>
                                    <p>{{ $transaction->user->email }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">VIP Level</h5>
                                    <p>{{ $transaction->user->vipLevel->name }}</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500">Current Balance</h5>
                                    <p>{{ number_format($transaction->user->balance, 2) }} USDT</p>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $transaction->user) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View Full User Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($transaction->metadata)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="text-md font-medium mb-4">Transaction Metadata</h4>
                            <div class="mt-1 p-2 bg-white rounded border border-gray-200">
                                <pre class="text-sm">{{ json_encode(json_decode($transaction->metadata), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif

                    @if($transaction->notes)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="text-md font-medium mb-4">Transaction Notes</h4>
                            <div class="mt-1 p-2 bg-white rounded border border-gray-200">
                                {!! nl2br(e($transaction->notes)) !!}
                            </div>
                        </div>
                    @endif

                    @if($relatedEntity)
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <h4 class="text-md font-medium mb-4">Related {{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($relatedEntityDetails as $key => $value)
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</h5>
                                        <p>{{ $value }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @if($relatedEntityLink)
                                <div class="mt-4">
                                    <a href="{{ $relatedEntityLink }}" class="text-indigo-600 hover:text-indigo-900">
                                        View Full {{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }} Details
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
