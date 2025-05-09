<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCompletion;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get total users count
        $totalUsers = User::count();

        // Get new users in the last 7 days
        $newUsers = User::where('created_at', '>=', now()->subDays(7))->count();

        // Get active users in the last 24 hours
        $activeUsers = User::where('last_login', '>=', now()->subHours(24))->count();

        // Get total deposits
        $totalDeposits = Transaction::where('type', 'credit')
            ->where('reference_type', 'coinbase_charge')
            ->where('status', 'completed')
            ->sum('amount');

        // Get total withdrawals
        $totalWithdrawals = Transaction::where('type', 'debit')
            ->where('reference_type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');

        // Get pending withdrawals
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();

        // Get pending task completions
        $pendingTaskCompletions = TaskCompletion::where('status', 'pending')->count();

        // Get recent transactions
        $recentTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get task completion statistics
        $taskCompletionStats = TaskCompletion::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Get deposit statistics
        $depositStats = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('type', 'credit')
            ->where('reference_type', 'coinbase_charge')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Get withdrawal statistics
        $withdrawalStats = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('type', 'debit')
            ->where('reference_type', 'withdrawal')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsers',
            'activeUsers',
            'totalDeposits',
            'totalWithdrawals',
            'pendingWithdrawals',
            'pendingTaskCompletions',
            'recentTransactions',
            'recentUsers',
            'taskCompletionStats',
            'depositStats',
            'withdrawalStats'
        ));
    }
}
