<?php

namespace App\Http\Controllers;

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
     * Display a listing of the user's withdrawals.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get the user's withdrawals
        $withdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get the minimum withdrawal amount
        $minWithdrawalAmount = \App\Models\Setting::getValue('min_withdrawal_amount', 10);

        // Get the withdrawal fee percentage
        $withdrawalFeePercentage = \App\Models\Setting::getValue('withdrawal_fee_percentage', 5);

        // Apply VIP discount to withdrawal fee
        $vipLevel = $user->vipLevel;
        $feeDiscount = $vipLevel->withdrawal_fee_discount;
        $adjustedFeePercentage = $withdrawalFeePercentage * (1 - $feeDiscount);

        return view('withdrawals.index', compact('withdrawals', 'minWithdrawalAmount', 'adjustedFeePercentage'));
    }

    /**
     * Store a newly created withdrawal in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Get the minimum withdrawal amount
        $minWithdrawalAmount = \App\Models\Setting::getValue('min_withdrawal_amount', 10);

        // Get the withdrawal fee percentage
        $withdrawalFeePercentage = \App\Models\Setting::getValue('withdrawal_fee_percentage', 5);

        // Apply VIP discount to withdrawal fee
        $vipLevel = $user->vipLevel;
        $feeDiscount = $vipLevel->withdrawal_fee_discount;
        $adjustedFeePercentage = $withdrawalFeePercentage * (1 - $feeDiscount);

        // Validate the request
        $request->validate([
            'amount' => "required|numeric|min:{$minWithdrawalAmount}|max:{$user->balance}",
            'wallet_address' => 'required|string|max:255',
            'network' => 'required|string|in:TRC20,ERC20,BEP20',
        ]);

        $amount = $request->amount;
        $fee = ($amount * $adjustedFeePercentage) / 100;
        $netAmount = $amount - $fee;

        // Create the withdrawal
        $withdrawal = \App\Models\Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'fee' => $fee,
            'method' => 'crypto',
            'status' => 'pending',
            'wallet_address' => $request->wallet_address,
            'network' => $request->network,
        ]);

        // Deduct the amount from the user's balance
        $user->balance -= $amount;
        $user->save();

        // Create a transaction record
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'debit',
            'description' => 'Withdrawal request',
            'reference_id' => $withdrawal->id,
            'reference_type' => 'withdrawal',
            'status' => 'completed',
            'balance_after' => $user->balance,
            'wallet_address' => $request->wallet_address,
            'network' => $request->network,
            'created_at' => now()
        ]);

        // Create a notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Withdrawal Requested',
            'message' => "Your withdrawal request for {$amount} USDT has been submitted and is pending approval.",
            'type' => 'withdrawal'
        ]);

        return redirect()->route('withdrawals.index')
            ->with('success', 'Withdrawal request submitted successfully. It will be processed within 24-48 hours.');
    }
}
