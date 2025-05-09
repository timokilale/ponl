<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\CoinbaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function __construct(CoinbaseService $coinbaseService)
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
        $user = auth()->user();

        // Get minimum deposit amount from settings
        $minDepositAmount = \App\Models\Setting::getValue('min_deposit_amount', 10);

        return view('payment.deposit', compact('minDepositAmount'));
    }

    /**
     * Create a Coinbase charge.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCharge(Request $request)
    {
        $user = auth()->user();

        // Get minimum deposit amount from settings
        $minDepositAmount = \App\Models\Setting::getValue('min_deposit_amount', 10);

        // Validate the request
        $request->validate([
            'amount' => "required|numeric|min:{$minDepositAmount}",
        ]);

        $amount = $request->amount;
        $currency = 'USD';

        // Generate a unique reference
        $reference = 'DEP-' . Str::random(10);

        // Create metadata for the charge
        $metadata = [
            'customer_id' => $user->id,
            'customer_email' => $user->email,
            'reference' => $reference,
        ];

        // Create success and cancel URLs
        $successUrl = route('payment.coinbase.success', ['reference' => $reference]);
        $cancelUrl = route('payment.coinbase.cancel', ['reference' => $reference]);

        // Create the charge
        $charge = $this->coinbaseService->createCharge($amount, $currency, $metadata, $successUrl, $cancelUrl);

        if (!$charge) {
            return redirect()->back()->with('error', 'Failed to create payment. Please try again later.');
        }

        // Store the charge details in the session
        session()->put('coinbase_charge', [
            'id' => $charge['id'],
            'amount' => $amount,
            'reference' => $reference,
            'created_at' => now(),
        ]);

        // Create a pending transaction record
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'description' => 'Deposit via Coinbase (pending)',
            'reference_id' => $charge['id'],
            'reference_type' => 'coinbase_charge',
            'status' => 'pending',
            'balance_after' => $user->balance,
            'created_at' => now()
        ]);

        // Redirect to the hosted checkout page
        return redirect($charge['hosted_url']);
    }

    /**
     * Handle successful payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        $reference = $request->query('reference');
        $chargeData = session()->get('coinbase_charge');

        if (!$chargeData || $chargeData['reference'] !== $reference) {
            return redirect()->route('payment.deposit')->with('error', 'Invalid payment reference.');
        }

        // Clear the charge data from the session
        session()->forget('coinbase_charge');

        // Note: The actual payment processing is done by the webhook
        // This is just a success page for the user

        return redirect()->route('transactions.index')->with('success', 'Your deposit is being processed. It will be credited to your account once confirmed.');
    }

    /**
     * Handle cancelled payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request)
    {
        $reference = $request->query('reference');
        $chargeData = session()->get('coinbase_charge');

        if ($chargeData && $chargeData['reference'] === $reference) {
            // Clear the charge data from the session
            session()->forget('coinbase_charge');

            // Update the transaction status to cancelled
            \App\Models\Transaction::where('reference_id', $chargeData['id'])
                ->where('reference_type', 'coinbase_charge')
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        return redirect()->route('payment.deposit')->with('info', 'Payment was cancelled.');
    }
}
