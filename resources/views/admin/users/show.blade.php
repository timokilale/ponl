<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            &larr; Back to Users
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">User Details: {{ $user->username }}</h3>
                        <div>
                            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 mr-2">
                                Edit User
                            </a>
                            @if(!$user->is_admin)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                        Deactivate User
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h4 class="text-md font-medium mb-4">Basic Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">User ID</h5>
                                <p>{{ $user->id }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Username</h5>
                                <p>{{ $user->username }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Email</h5>
                                <p>{{ $user->email }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Phone Number</h5>
                                <p>{{ $user->phone_number ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Registration Date</h5>
                                <p>{{ $user->created_at->format('M d, Y H:i:s') }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Last Login</h5>
                                <p>{{ $user->last_login ? $user->last_login->format('M d, Y H:i:s') : 'Never' }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Status</h5>
                                <p>
                                    @if($user->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Role</h5>
                                <p>
                                    @if($user->is_admin)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Admin
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            User
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
                        <h4 class="text-md font-medium mb-4">Financial Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Current Balance</h5>
                                <p class="text-lg font-bold">{{ number_format($user->balance, 2) }} USDT</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-500">VIP Level</h5>
                                <p>{{ $user->vipLevel->name }}</p>
                            </div>

                            <div>
                                <h5 class="text-sm font-medium text-gray-500">Referral Code</h5>
                                <p>{{ $user->referral_code }}</p>
                            </div>
                        </div>
                    </div>

                    @if($user->transactions->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-4">Recent Transactions</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($user->transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($transaction->amount > 0)
                                                <span class="text-green-600">+{{ number_format($transaction->amount, 2) }} USDT</span>
                                            @else
                                                <span class="text-red-600">{{ number_format($transaction->amount, 2) }} USDT</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($user->taskCompletions->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-4">Recent Task Completions</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($user->taskCompletions as $completion)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $completion->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $completion->task->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($completion->status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif($completion->status === 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Approved
                                                </span>
                                            @elseif($completion->status === 'rejected')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($completion->reward, 2) }} USDT</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $completion->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.task-completions.show', $completion) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
