<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoinbaseController extends Controller
{
    /**
     * @var \App\Services\CoinbaseService
     */
    protected $coinbaseService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\CoinbaseService $coinbaseService
     * @return void
     */
    public function __construct(\App\Services\CoinbaseService $coinbaseService)
    {
        $this->middleware('auth');
        $this->coinbaseService = $coinbaseService;
    }

    /**
     * Show the deposit form.
     *
     * @return \Illuminate\View\View
     */
    public function showDepositForm()
    {
        return view('payment.deposit');
    }

    /**
     * Create a new deposit charge.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
        ]);

        $user = auth()->user();
        $amount = $request->input('amount');

        $reference = 'DEP-' . time() . '-' . $user->id;

        $chargeData = [
            'name' => 'Account Deposit',
            'description' => 'Deposit to ' . $user->username . ' account',
            'amount' => $amount,
            'currency' => 'USDT',
            'userId' => $user->id,
            'username' => $user->username,
            'reference' => $reference,
            'redirectUrl' => route('payment.coinbase.success'),
            'cancelUrl' => route('payment.coinbase.cancel')
        ];

        $result = $this->coinbaseService->createCharge($chargeData);

        if (!$result['success']) {
            return back()->with('error', 'Failed to create payment: ' . ($result['error'] ?? 'Unknown error'));
        }

        // Store charge info in session
        session(['coinbase_charge' => [
            'id' => $result['chargeId'],
            'code' => $result['code'],
            'amount' => $amount,
            'reference' => $reference
        ]]);

        return redirect($result['hostedUrl']);
    }

    /**
     * Handle successful payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function success(Request $request)
    {
        $chargeData = session('coinbase_charge');

        if (!$chargeData) {
            return redirect()->route('payment.deposit')->with('error', 'Payment information not found');
        }

        // Clear the session data
        session()->forget('coinbase_charge');

        return view('payment.success', [
            'amount' => $chargeData['amount'],
            'reference' => $chargeData['reference']
        ]);
    }

    /**
     * Handle cancelled payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request)
    {
        session()->forget('coinbase_charge');

        return redirect()->route('payment.deposit')->with('info', 'Payment was cancelled');
    }
}
