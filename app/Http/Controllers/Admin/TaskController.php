<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\VipLevel;
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
     * Display a listing of the tasks.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Task::query();

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        // Filter by VIP level
        if ($request->has('vip_level') && $request->vip_level != '') {
            $query->where('vip_level_required', $request->vip_level);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        // Get VIP levels for filter
        $vipLevels = VipLevel::orderBy('deposit_required')->get();

        // Paginate results
        $tasks = $query->paginate(15)->withQueryString();

        return view('admin.tasks.index', compact('tasks', 'vipLevels'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $vipLevels = VipLevel::orderBy('deposit_required')->get();

        return view('admin.tasks.create', compact('vipLevels'));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'platform' => ['required', 'string', 'max:255'],
            'reward' => ['required', 'numeric', 'min:0'],
            'time_required' => ['required', 'string', 'max:50'],
            'difficulty' => ['required', 'string', 'in:easy,medium,hard'],
            'vip_level_required' => ['required', 'exists:vip_levels,id'],
            'is_active' => ['boolean'],
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'platform' => $request->platform,
            'reward' => $request->reward,
            'time_required' => $request->time_required,
            'difficulty' => $request->difficulty,
            'vip_level_required' => $request->vip_level_required,
            'is_active' => $request->is_active ?? true,
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'create_task',
            'description' => 'Created task: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function show(Task $task)
    {
        // Load relationships
        $task->load([
            'vipLevel',
            'completions' => function($query) {
                $query->with('user')->orderBy('created_at', 'desc')->limit(10);
            },
        ]);

        // Get completion statistics
        $completionStats = [
            'total' => $task->completions()->count(),
            'pending' => $task->completions()->where('status', 'pending')->count(),
            'approved' => $task->completions()->where('status', 'approved')->count(),
            'rejected' => $task->completions()->where('status', 'rejected')->count(),
        ];

        return view('admin.tasks.show', compact('task', 'completionStats'));
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function edit(Task $task)
    {
        $vipLevels = VipLevel::orderBy('deposit_required')->get();

        return view('admin.tasks.edit', compact('task', 'vipLevels'));
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'platform' => ['required', 'string', 'max:255'],
            'reward' => ['required', 'numeric', 'min:0'],
            'time_required' => ['required', 'string', 'max:50'],
            'difficulty' => ['required', 'string', 'in:easy,medium,hard'],
            'vip_level_required' => ['required', 'exists:vip_levels,id'],
            'is_active' => ['boolean'],
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'platform' => $request->platform,
            'reward' => $request->reward,
            'time_required' => $request->time_required,
            'difficulty' => $request->difficulty,
            'vip_level_required' => $request->vip_level_required,
            'is_active' => $request->is_active ?? false,
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'update_task',
            'description' => 'Updated task: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        // Check if task has completions
        if ($task->completions()->count() > 0) {
            // Instead of deleting, deactivate the task
            $task->update(['is_active' => false]);

            // Log the activity
            \App\Models\UserActivityLog::create([
                'user_id' => auth()->id(),
                'activity_type' => 'deactivate_task',
                'description' => 'Deactivated task: ' . $task->title,
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('admin.tasks.index')
                ->with('success', 'Task deactivated successfully.');
        }

        // If no completions, delete the task
        $taskTitle = $task->title;
        $task->delete();

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'delete_task',
            'description' => 'Deleted task: ' . $taskTitle,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
