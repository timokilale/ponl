<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Deposit Funds') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">{{ __('Make a Deposit') }}</h3>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('info') }}</span>
                        </div>
                    @endif

                    <div class="mb-6">
                        <p class="mb-2">{{ __('Current Balance') }}: <span class="font-bold">{{ number_format(auth()->user()->balance, 2) }} USDT</span></p>
                        <p class="text-sm text-gray-600">{{ __('Minimum deposit amount') }}: {{ number_format($minDepositAmount, 2) }} USDT</p>
                    </div>

                    <form method="POST" action="{{ route('payment.coinbase.charge') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="amount" :value="__('Amount (USDT)')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" required step="0.01" min="{{ $minDepositAmount }}" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <h4 class="font-medium mb-2">{{ __('Payment Method') }}</h4>
                            <div class="flex items-center p-4 border rounded">
                                <input id="coinbase" type="radio" name="payment_method" value="coinbase" class="w-4 h-4" checked>
                                <label for="coinbase" class="ml-2 text-sm font-medium text-gray-900">
                                    {{ __('Cryptocurrency (via Coinbase)') }}
                                </label>
                                <img src="https://www.coinbase.com/assets/press/coinbase-logo-5d75166d75dd0b28a2d676c0cdcac7d0e2d3a8c3c7069f5080adc11d294ff39c.png" alt="Coinbase" class="h-6 ml-auto">
                            </div>
                            <p class="mt-2 text-sm text-gray-600">{{ __('Pay with Bitcoin, Ethereum, USDT, USDC and other cryptocurrencies') }}</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-3">
                                {{ __('Proceed to Payment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
