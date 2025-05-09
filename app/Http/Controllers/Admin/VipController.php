<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VipLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VipController extends Controller
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
     * Display a listing of the VIP levels.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vipLevels = VipLevel::orderBy('deposit_required')->get();

        // Get user counts for each VIP level
        foreach ($vipLevels as $vipLevel) {
            $vipLevel->user_count = User::where('vip_level_id', $vipLevel->id)->count();
        }

        return view('admin.vip-levels.index', compact('vipLevels'));
    }

    /**
     * Show the form for creating a new VIP level.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.vip-levels.create');
    }

    /**
     * Store a newly created VIP level in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:vip_levels'],
            'deposit_required' => ['required', 'numeric', 'min:0', 'unique:vip_levels'],
            'reward_multiplier' => ['required', 'numeric', 'min:1'],
            'daily_tasks_limit' => ['required', 'integer', 'min:1'],
            'withdrawal_fee_discount' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $vipLevel = VipLevel::create([
            'name' => $request->name,
            'deposit_required' => $request->deposit_required,
            'reward_multiplier' => $request->reward_multiplier,
            'daily_tasks_limit' => $request->daily_tasks_limit,
            'withdrawal_fee_discount' => $request->withdrawal_fee_discount,
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_vip_level',
            'description' => 'Created VIP level: ' . $vipLevel->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.vip-levels.index')
            ->with('success', 'VIP level created successfully.');
    }

    /**
     * Display the specified VIP level.
     *
     * @param  \App\Models\VipLevel  $vipLevel
     * @return \Illuminate\View\View
     */
    public function show(VipLevel $vipLevel)
    {
        // Get users with this VIP level
        $users = User::where('vip_level_id', $vipLevel->id)
            ->orderBy('vip_points', 'desc')
            ->paginate(15);

        // Get tasks requiring this VIP level
        $tasks = $vipLevel->tasks()->paginate(15);

        return view('admin.vip-levels.show', compact('vipLevel', 'users', 'tasks'));
    }

    /**
     * Show the form for editing the specified VIP level.
     *
     * @param  \App\Models\VipLevel  $vipLevel
     * @return \Illuminate\View\View
     */
    public function edit(VipLevel $vipLevel)
    {
        return view('admin.vip-levels.edit', compact('vipLevel'));
    }

    /**
     * Update the specified VIP level in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VipLevel  $vipLevel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, VipLevel $vipLevel)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('vip_levels')->ignore($vipLevel->id)],
            'deposit_required' => ['required', 'numeric', 'min:0', Rule::unique('vip_levels')->ignore($vipLevel->id)],
            'reward_multiplier' => ['required', 'numeric', 'min:1'],
            'daily_tasks_limit' => ['required', 'integer', 'min:1'],
            'withdrawal_fee_discount' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $vipLevel->update([
            'name' => $request->name,
            'deposit_required' => $request->deposit_required,
            'reward_multiplier' => $request->reward_multiplier,
            'daily_tasks_limit' => $request->daily_tasks_limit,
            'withdrawal_fee_discount' => $request->withdrawal_fee_discount,
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_vip_level',
            'description' => 'Updated VIP level: ' . $vipLevel->name,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.vip-levels.show', $vipLevel)
            ->with('success', 'VIP level updated successfully.');
    }

    /**
     * Remove the specified VIP level from storage.
     *
     * @param  \App\Models\VipLevel  $vipLevel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(VipLevel $vipLevel)
    {
        // Check if there are users with this VIP level
        $userCount = User::where('vip_level_id', $vipLevel->id)->count();

        if ($userCount > 0) {
            return redirect()->route('admin.vip-levels.index')
                ->with('error', 'Cannot delete VIP level because there are users assigned to it.');
        }

        // Check if there are tasks requiring this VIP level
        $taskCount = $vipLevel->tasks()->count();

        if ($taskCount > 0) {
            return redirect()->route('admin.vip-levels.index')
                ->with('error', 'Cannot delete VIP level because there are tasks requiring it.');
        }

        $vipLevelName = $vipLevel->name;
        $vipLevel->delete();

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_vip_level',
            'description' => 'Deleted VIP level: ' . $vipLevelName,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.vip-levels.index')
            ->with('success', 'VIP level deleted successfully.');
    }
}
