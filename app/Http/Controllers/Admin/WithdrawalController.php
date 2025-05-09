<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
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
     * Display a listing of the withdrawals.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with('user');

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('wallet_address', 'like', "%{$search}%")
                  ->orWhere('blockchain_txid', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->has('method') && $request->method != '') {
            $query->where('method', $request->method);
        }

        // Filter by network
        if ($request->has('network') && $request->network != '') {
            $query->where('network', $request->network);
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

        // Get withdrawal statuses for filter
        $withdrawalStatuses = Withdrawal::select('status')->distinct()->pluck('status');

        // Get withdrawal methods for filter
        $withdrawalMethods = Withdrawal::select('method')->distinct()->pluck('method');

        // Get withdrawal networks for filter
        $withdrawalNetworks = Withdrawal::select('network')->distinct()->pluck('network');

        // Paginate results
        $withdrawals = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $totalPending = Withdrawal::where('status', 'pending')->sum('amount');
        $totalApproved = Withdrawal::where('status', 'approved')->sum('amount');
        $totalRejected = Withdrawal::where('status', 'rejected')->sum('amount');

        return view('admin.withdrawals.index', compact(
            'withdrawals',
            'withdrawalStatuses',
            'withdrawalMethods',
            'withdrawalNetworks',
            'totalPending',
            'totalApproved',
            'totalRejected'
        ));
    }

    /**
     * Display the specified withdrawal.
     *
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\View\View
     */
    public function show(Withdrawal $withdrawal)
    {
        // Load relationships
        $withdrawal->load(['user']);

        // Get related transaction
        $transaction = Transaction::where('reference_id', $withdrawal->id)
            ->where('reference_type', 'withdrawal')
            ->first();

        return view('admin.withdrawals.show', compact('withdrawal', 'transaction'));
    }

    /**
     * Update the specified withdrawal in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected'],
            'admin_notes' => ['nullable', 'string'],
            'blockchain_txid' => ['nullable', 'string', 'max:255'],
        ]);

        // Update the withdrawal
        $withdrawal->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'blockchain_txid' => $request->blockchain_txid,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'update_withdrawal',
            'description' => 'Updated withdrawal status to ' . $request->status,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.withdrawals.show', $withdrawal)
            ->with('success', 'Withdrawal updated successfully.');
    }

    /**
     * Approve the specified withdrawal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'blockchain_txid' => ['required', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        // Check if the withdrawal is already approved or rejected
        if ($withdrawal->status !== 'pending') {
            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('error', 'This withdrawal has already been ' . $withdrawal->status . '.');
        }

        // Get the user
        $user = $withdrawal->user;

        // Update the withdrawal
        $withdrawal->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'blockchain_txid' => $request->blockchain_txid,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Update the transaction status
        Transaction::where('reference_id', $withdrawal->id)
            ->where('reference_type', 'withdrawal')
            ->update([
                'status' => 'completed',
                'blockchain_txid' => $request->blockchain_txid,
            ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Withdrawal Approved',
            'message' => "Your withdrawal request for {$withdrawal->amount} USDT has been approved and processed. Transaction ID: {$request->blockchain_txid}",
            'type' => 'withdrawal',
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'approve_withdrawal',
            'description' => 'Approved withdrawal for user: ' . $user->username,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.withdrawals.show', $withdrawal)
            ->with('success', 'Withdrawal approved successfully.');
    }

    /**
     * Reject the specified withdrawal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'admin_notes' => ['required', 'string'],
        ]);

        // Check if the withdrawal is already approved or rejected
        if ($withdrawal->status !== 'pending') {
            return redirect()->route('admin.withdrawals.show', $withdrawal)
                ->with('error', 'This withdrawal has already been ' . $withdrawal->status . '.');
        }

        // Get the user
        $user = $withdrawal->user;

        // Update the withdrawal
        $withdrawal->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Refund the amount to the user's balance
        $user->balance += $withdrawal->amount;
        $user->save();

        // Update the transaction status
        Transaction::where('reference_id', $withdrawal->id)
            ->where('reference_type', 'withdrawal')
            ->update([
                'status' => 'cancelled',
            ]);

        // Create a refund transaction
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $withdrawal->amount,
            'type' => 'credit',
            'description' => 'Withdrawal request refund',
            'reference_id' => $withdrawal->id,
            'reference_type' => 'withdrawal_refund',
            'status' => 'completed',
            'balance_after' => $user->balance,
            'created_at' => now()
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Withdrawal Rejected',
            'message' => "Your withdrawal request for {$withdrawal->amount} USDT has been rejected. Reason: {$request->admin_notes}. The amount has been refunded to your balance.",
            'type' => 'withdrawal',
        ]);

        // Log the activity
        \App\Models\UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'reject_withdrawal',
            'description' => 'Rejected withdrawal for user: ' . $user->username,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.withdrawals.show', $withdrawal)
            ->with('success', 'Withdrawal rejected and refunded successfully.');
    }
}
