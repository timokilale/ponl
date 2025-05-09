<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_id', 'like', "%{$search}%")
                  ->orWhere('blockchain_txid', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by reference type
        if ($request->has('reference_type') && $request->reference_type != '') {
            $query->where('reference_type', $request->reference_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        // Get transaction types for filter
        $transactionTypes = Transaction::select('type')->distinct()->pluck('type');

        // Get transaction statuses for filter
        $transactionStatuses = Transaction::select('status')->distinct()->pluck('status');

        // Get reference types for filter
        $referenceTypes = Transaction::select('reference_type')->distinct()->pluck('reference_type');

        // Paginate results
        $transactions = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $totalCredits = Transaction::where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');

        $totalDebits = Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');

        $netBalance = $totalCredits - $totalDebits;

        return view('admin.transactions.index', compact(
            'transactions',
            'transactionTypes',
            'transactionStatuses',
            'referenceTypes',
            'totalCredits',
            'totalDebits',
            'netBalance'
        ));
    }

    /**
     * Display the specified transaction.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\View\View
     */
    public function show(Transaction $transaction)
    {
        // Load relationships
        $transaction->load(['user']);

        // Load reference based on reference_type
        if ($transaction->reference_type === 'task_completion') {
            $transaction->load(['reference' => function($query) {
                $query->with(['task', 'user']);
            }]);
        } elseif ($transaction->reference_type === 'withdrawal') {
            $transaction->load(['reference' => function($query) {
                $query->with(['user']);
            }]);
        } elseif ($transaction->reference_type === 'referral') {
            $transaction->load(['reference' => function($query) {
                $query->with(['referrer', 'referred']);
            }]);
        }

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Show transaction statistics.
     *
     * @return \Illuminate\View\View
     */
    public function statistics()
    {
        // Get daily transaction statistics for the last 30 days
        $dailyStats = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as credits'),
                DB::raw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debits'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get transaction statistics by type
        $typeStats = Transaction::select(
                'type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('type')
            ->get();

        // Get transaction statistics by reference type
        $referenceTypeStats = Transaction::select(
                'reference_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('reference_type')
            ->get();

        // Get top users by transaction volume
        $topUsers = User::select(
                'users.id',
                'users.username',
                DB::raw('COUNT(transactions.id) as transaction_count'),
                DB::raw('SUM(CASE WHEN transactions.type = "credit" THEN transactions.amount ELSE 0 END) as total_credits'),
                DB::raw('SUM(CASE WHEN transactions.type = "debit" THEN transactions.amount ELSE 0 END) as total_debits')
            )
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->groupBy('users.id', 'users.username')
            ->orderBy('transaction_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.transactions.statistics', compact(
            'dailyStats',
            'typeStats',
            'referenceTypeStats',
            'topUsers'
        ));
    }
}
