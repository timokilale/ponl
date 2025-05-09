<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VipLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by VIP level
        if ($request->has('vip_level') && $request->vip_level != '') {
            $query->where('vip_level_id', $request->vip_level);
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
        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'vipLevels'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $vipLevels = VipLevel::orderBy('deposit_required')->get();

        return view('admin.users.create', compact('vipLevels'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'vip_level_id' => ['required', 'exists:vip_levels,id'],

            'is_admin' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'balance' => $request->balance ?? 0,
            'vip_level_id' => $request->vip_level_id,

            'referral_code' => strtoupper(substr(md5($request->username . time()), 0, 8)),
            'is_admin' => $request->is_admin ?? false,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Load relationships
        $user->load([
            'vipLevel',
            'taskCompletions' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'withdrawals' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'transactions' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'referrals' => function($query) {
                $query->with('referred')->orderBy('created_at', 'desc');
            },
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $vipLevels = VipLevel::orderBy('deposit_required')->get();

        return view('admin.users.edit', compact('user', 'vipLevels'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'balance' => ['required', 'numeric', 'min:0'],
            'vip_level_id' => ['required', 'exists:vip_levels,id'],

            'is_admin' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $userData = [
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'balance' => $request->balance,
            'vip_level_id' => $request->vip_level_id,

            'is_admin' => $request->is_admin ?? false,
            'is_active' => $request->is_active ?? true,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'update_user',
            'description' => 'Updated user: ' . $user->username,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Check if user is an admin
        if ($user->is_admin) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete admin users.');
        }

        // Instead of deleting, deactivate the user
        $user->update([
            'is_active' => false,
            'email' => $user->email . '.deactivated.' . time(),
            'username' => $user->username . '.deactivated.' . time(),
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'deactivate_user',
            'description' => 'Deactivated user: ' . $user->username,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deactivated successfully.');
    }
}
