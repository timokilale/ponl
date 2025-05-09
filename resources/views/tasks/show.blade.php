<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('tasks.index') }}" class="text-blue-500 hover:underline">
                            &larr; Back to Tasks
                        </a>
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

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-xl font-semibold mb-2">{{ $task->title }}</h3>
                        <div class="flex items-center mb-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">VIP Level {{ $task->vip_level_required }}</span>
                            <span class="ml-4 text-green-600 font-semibold">${{ number_format($task->reward, 2) }}</span>
                        </div>
                        <p class="mb-4">{{ $task->description }}</p>
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-medium mb-2">Task Instructions:</h4>
                            <div class="prose max-w-none">
                                {!! $task->instructions !!}
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Your Task Status</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <p>VIP Level: <span class="font-semibold">{{ $vipLevel->name }}</span></p>
                            <p>Completed Tasks Today: <span class="font-semibold">{{ $completedTasksToday }} / {{ $dailyLimit }}</span></p>
                        </div>
                    </div>

                    @if($canComplete)
                        <div class="bg-white rounded-lg border p-6">
                            <h3 class="text-lg font-medium mb-4">Submit Task Completion</h3>
                            <form action="{{ route('tasks.complete', $task) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="proof" class="block text-sm font-medium text-gray-700 mb-1">Proof of Completion</label>
                                    <textarea id="proof" name="proof" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Provide proof that you've completed this task as instructed..." required></textarea>
                                    @error('proof')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-center justify-end">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Submit Task
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif(auth()->user()->balance < 30)
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative">
                            <p>You need a minimum balance of 30 USDT to complete tasks. Please <a href="{{ route('payment.deposit') }}" class="underline font-semibold">make a deposit</a> to continue.</p>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative">
                            <p>You have reached your daily task limit ({{ $dailyLimit }} tasks). Please come back tomorrow for more tasks.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
