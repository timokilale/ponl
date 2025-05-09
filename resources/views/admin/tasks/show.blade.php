<x-admin-layout>
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
                        <a href="{{ route('admin.tasks.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Tasks
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Task Details</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.tasks.edit', $task) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                Edit Task
                            </a>
                            <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this task?')">
                                    Delete Task
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">ID</h4>
                                <p>{{ $task->id }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Title</h4>
                                <p>{{ $task->title }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Platform</h4>
                                <p>{{ $task->platform }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Reward</h4>
                                <p>{{ number_format($task->reward, 2) }} USDT</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Time Required</h4>
                                <p>{{ $task->time_required }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Difficulty</h4>
                                <p class="capitalize">{{ $task->difficulty }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">VIP Level Required</h4>
                                <p>{{ $task->vipLevel->name }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                <p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $task->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Created At</h4>
                                <p>{{ $task->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Updated At</h4>
                                <p>{{ $task->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <div class="mt-1 p-2 bg-white rounded border border-gray-200">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Task Completion Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-indigo-800">Total Completions</h4>
                                <p class="text-2xl font-bold text-indigo-600">{{ $completionStats['total'] }}</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-yellow-800">Pending</h4>
                                <p class="text-2xl font-bold text-yellow-600">{{ $completionStats['pending'] }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800">Approved</h4>
                                <p class="text-2xl font-bold text-green-600">{{ $completionStats['approved'] }}</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-red-800">Rejected</h4>
                                <p class="text-2xl font-bold text-red-600">{{ $completionStats['rejected'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium mb-4">Recent Completions</h3>
                        @if($task->completions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Reward
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Completed At
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($task->completions as $completion)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $completion->user->username }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $completion->user->email }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($completion->status == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($completion->status == 'approved') bg-green-100 text-green-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ ucfirst($completion->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($completion->reward, 2) }} USDT
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $completion->completed_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('admin.task-completions.show', $completion) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No completions yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
