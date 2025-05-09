<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        
                        <h3 class="mt-2 text-lg font-medium text-gray-900">{{ __('Payment Successful!') }}</h3>
                        
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">
                                {{ __('Your payment of') }} <span class="font-bold">${{ number_format($amount, 2) }}</span> {{ __('is being processed.') }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ __('Reference:') }} <span class="font-mono">{{ $reference }}</span>
                            </p>
                            <p class="text-sm text-gray-600 mt-4">
                                {{ __('Your account will be credited once the payment is confirmed on the blockchain.') }}
                            </p>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('View Transactions') }}
                            </a>
                            <a href="{{ route('dashboard') }}" class="ml-3 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Go to Dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
