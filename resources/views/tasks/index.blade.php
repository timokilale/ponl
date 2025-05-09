<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Available Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Your Task Status</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <p>VIP Level: <span class="font-semibold">{{ $vipLevel->name }}</span></p>
                            <p>Completed Tasks Today: <span class="font-semibold">{{ $completedTasksToday }} / {{ $vipLevel->daily_tasks_limit }}</span></p>
                            <p>Current Balance: <span class="font-semibold">{{ number_format(auth()->user()->balance, 2) }} USDT</span></p>

                            @if(auth()->user()->balance < 30)
                                <div class="mt-2 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-2 rounded text-sm">
                                    <p>You need a minimum balance of 30 USDT to complete tasks. Please <a href="{{ route('payment.deposit') }}" class="underline font-semibold">make a deposit</a> to continue.</p>
                                </div>
                            @endif
                        </div>
                    </div>

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

                    <h3 class="text-lg font-medium mb-4">Available Tasks</h3>

                    @if($tasks->isEmpty())
                        <p class="text-gray-500">No tasks available for your VIP level.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($tasks as $task)
                                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <div class="bg-gray-50 px-4 py-2 border-b">
                                        <h4 class="font-medium">{{ $task->title }}</h4>
                                        <p class="text-sm text-gray-500">VIP Level {{ $task->vip_level_required }}</p>
                                    </div>
                                    <div class="p-4">
                                        <p class="text-sm mb-4">{{ Str::limit($task->description, 100) }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-green-600 font-semibold">{{ number_format($task->reward, 2) }} USDT</span>
                                            <a href="{{ route('tasks.show', $task) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
