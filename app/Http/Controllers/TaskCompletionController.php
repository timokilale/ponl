<?php

namespace App\Http\Controllers;

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
     * Display a listing of the user's task completions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get the user's task completions
        $taskCompletions = \App\Models\TaskCompletion::with('task')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tasks.completions.index', compact('taskCompletions'));
    }

    /**
     * Store a newly created task completion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, \App\Models\Task $task)
    {
        $user = auth()->user();

        // Validate the request
        $request->validate([
            'proof' => 'required|string|max:1000',
        ]);

        // Check if the user has the required VIP level
        if ($task->vip_level_required > $user->vip_level_id) {
            return redirect()->route('tasks.index')
                ->with('error', 'You do not have the required VIP level to complete this task.');
        }

        // Check if the user has the minimum required balance (30 USDT)
        if ($user->balance < 30) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'You need a minimum balance of 30 USDT to complete tasks. Please make a deposit.');
        }

        // Check if the task is active
        if (!$task->is_active) {
            return redirect()->route('tasks.index')
                ->with('error', 'This task is no longer available.');
        }

        // Check if the user has reached their daily task limit
        $today = now()->startOfDay();
        $completedTasksToday = \App\Models\TaskCompletion::where('user_id', $user->id)
            ->where('created_at', '>=', $today)
            ->count();

        $vipLevel = $user->vipLevel;
        $dailyLimit = $vipLevel->daily_tasks_limit;

        if ($completedTasksToday >= $dailyLimit) {
            return redirect()->route('tasks.show', $task)
                ->with('error', "You have reached your daily limit of {$dailyLimit} tasks.");
        }

        // Calculate the reward with VIP bonus
        $baseReward = $task->reward;
        $rewardMultiplier = $vipLevel->reward_multiplier;
        $finalReward = $baseReward * $rewardMultiplier;

        // Create the task completion
        $taskCompletion = \App\Models\TaskCompletion::create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'status' => 'pending',
            'proof' => $request->proof,
            'reward' => $finalReward,
            'completed_at' => now(),
        ]);

        // Create a notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Task Submitted',
            'message' => "Your completion of '{$task->title}' has been submitted and is pending review.",
            'type' => 'task',
        ]);

        return redirect()->route('tasks.completions')
            ->with('success', 'Task completion submitted successfully. It will be reviewed by an admin.');
    }
}
