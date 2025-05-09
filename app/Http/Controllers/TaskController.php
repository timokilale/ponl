<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
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
     * Display a listing of available tasks.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get tasks available for the user's VIP level
        $tasks = \App\Models\Task::where('is_active', true)
            ->where('vip_level_required', '<=', $user->vip_level_id)
            ->orderBy('vip_level_required')
            ->orderBy('reward', 'desc')
            ->get();

        // Get the user's claimed tasks for today
        $today = now()->startOfDay();
        $claimedTasksToday = \App\Models\TaskClaim::where('user_id', $user->id)
            ->where('created_at', '>=', $today)
            ->count();

        // Get the user's active task claims
        $activeClaimIds = $user->activeTaskClaims()->pluck('task_id')->toArray();

        // Get the user's VIP level details
        $vipLevel = $user->vipLevel;

        return view('tasks.index', compact('tasks', 'claimedTasksToday', 'activeClaimIds', 'vipLevel'));
    }

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function show(\App\Models\Task $task)
    {
        $user = auth()->user();

        // Check if the user has the required VIP level
        if ($task->vip_level_required > $user->vip_level_id) {
            return redirect()->route('tasks.index')
                ->with('error', 'You do not have the required VIP level to access this task.');
        }

        // Check if the task is active
        if (!$task->is_active) {
            return redirect()->route('tasks.index')
                ->with('error', 'This task is no longer available.');
        }

        // Get the user's claimed tasks for today
        $today = now()->startOfDay();
        $claimedTasksToday = \App\Models\TaskClaim::where('user_id', $user->id)
            ->where('created_at', '>=', $today)
            ->count();

        // Check if the user has an active claim for this task
        $activeClaim = $task->getActiveClaimForUser($user->id);
        $isAlreadyClaimed = !is_null($activeClaim);

        // Get the user's VIP level details
        $vipLevel = $user->vipLevel;

        // Check if the user has reached their daily task limit
        $dailyLimit = $vipLevel->daily_tasks_limit;
        $canClaim = !$isAlreadyClaimed && $claimedTasksToday < $dailyLimit;

        return view('tasks.show', compact('task', 'claimedTasksToday', 'vipLevel', 'dailyLimit', 'canClaim', 'isAlreadyClaimed', 'activeClaim'));
    }
}
