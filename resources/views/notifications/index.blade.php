<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
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

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Your Notifications</h3>
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-blue-500 hover:text-blue-700 text-sm">
                                    Mark all as read
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($notifications->isEmpty())
                        <p class="text-gray-500">You don't have any notifications yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="border rounded-lg overflow-hidden {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                                    <div class="px-4 py-3 flex justify-between items-start border-b">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $notification->title }}</h4>
                                            <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->is_read)
                                            <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-500 hover:text-blue-700 text-sm">
                                                    Mark as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="px-4 py-3">
                                        <p class="text-gray-700">{{ $notification->message }}</p>
                                    </div>
                                    <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500">
                                        @if($notification->type === 'task')
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Task
                                            </span>
                                        @elseif($notification->type === 'withdrawal')
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Withdrawal
                                            </span>
                                        @elseif($notification->type === 'deposit')
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Deposit
                                            </span>
                                        @elseif($notification->type === 'referral')
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Referral
                                            </span>
                                        @else
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                System
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
