<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskCompletion;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class TaskCompletionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware is now applied in the routes file
    }

    /**
     * Display a listing of the task completions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = TaskCompletion::with(['user', 'task']);

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('task', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by task
        if ($request->has('task_id') && $request->task_id != '') {
            $query->where('task_id', $request->task_id);
        }

        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        // Paginate results
        $taskCompletions = $query->paginate(15)->withQueryString();

        return view('admin.task-completions.index', compact('taskCompletions'));
    }

    /**
     * Display the specified task completion.
     *
     * @param  \App\Models\TaskCompletion  $taskCompletion
     * @return \Illuminate\View\View
     */
    public function show(TaskCompletion $taskCompletion)
    {
        // Load relationships
        $taskCompletion->load(['user', 'task']);

        return view('admin.task-completions.show', compact('taskCompletion'));
    }

    /**
     * Update the specified task completion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskCompletion  $taskCompletion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TaskCompletion $taskCompletion)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        // Update the task completion
        $taskCompletion->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_task_completion',
            'description' => 'Updated task completion status to ' . $request->status,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.task-completions.show', $taskCompletion)
            ->with('success', 'Task completion updated successfully.');
    }

    /**
     * Approve the specified task completion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskCompletion  $taskCompletion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, TaskCompletion $taskCompletion)
    {
        // Check if the task completion is already approved or rejected
        if ($taskCompletion->status !== 'pending') {
            return redirect()->route('admin.task-completions.show', $taskCompletion)
                ->with('error', 'This task completion has already been ' . $taskCompletion->status . '.');
        }

        // Get the user
        $user = $taskCompletion->user;

        // Get the task
        $task = $taskCompletion->task;

        // Get the reward amount
        $reward = $taskCompletion->reward;

        // Update the task completion
        $taskCompletion->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Update user balance
        $user->balance += $reward;
        $user->save();

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $reward,
            'type' => 'credit',
            'description' => 'Task completion reward: ' . $task->title,
            'reference_id' => $taskCompletion->id,
            'reference_type' => 'task_completion',
            'status' => 'completed',
            'balance_after' => $user->balance,
            'created_at' => now()
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Task Approved',
            'message' => "Your completion of '{$task->title}' has been approved. {$reward} USDT has been added to your balance.",
            'type' => 'task',
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_task_completion',
            'description' => 'Approved task completion for task: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.task-completions.show', $taskCompletion)
            ->with('success', 'Task completion approved successfully.');
    }

    /**
     * Reject the specified task completion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaskCompletion  $taskCompletion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, TaskCompletion $taskCompletion)
    {
        $request->validate([
            'admin_notes' => ['required', 'string'],
        ]);

        // Check if the task completion is already approved or rejected
        if ($taskCompletion->status !== 'pending') {
            return redirect()->route('admin.task-completions.show', $taskCompletion)
                ->with('error', 'This task completion has already been ' . $taskCompletion->status . '.');
        }

        // Get the user
        $user = $taskCompletion->user;

        // Get the task
        $task = $taskCompletion->task;

        // Update the task completion
        $taskCompletion->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Task Rejected',
            'message' => "Your completion of '{$task->title}' has been rejected. Reason: {$request->admin_notes}",
            'type' => 'task',
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_task_completion',
            'description' => 'Rejected task completion for task: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.task-completions.show', $taskCompletion)
            ->with('success', 'Task completion rejected successfully.');
    }
}
