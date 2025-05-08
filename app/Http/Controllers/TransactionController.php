<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get filter parameters
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Build query
        $query = \App\Models\Transaction::where('user_id', $user->id);

        // Apply filters
        if ($type) {
            $query->where('type', $type);
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        // Get transactions
        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get transaction statistics
        $totalCredits = \App\Models\Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');

        $totalDebits = \App\Models\Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        return view('transactions.index', compact('transactions', 'totalCredits', 'totalDebits', 'type', 'dateFrom', 'dateTo'));
    }
}
