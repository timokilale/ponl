<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\CoinbaseService;
use App\Services\PaymentSplitterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CoinbaseController extends Controller
{
    /**
     * @var \App\Services\CoinbaseService
     */
    protected $coinbaseService;

    /**
     * @var \App\Services\PaymentSplitterService
     */
    protected $paymentSplitterService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\CoinbaseService $coinbaseService
     * @param \App\Services\PaymentSplitterService $paymentSplitterService
     * @return void
     */
    public function __construct(CoinbaseService $coinbaseService, PaymentSplitterService $paymentSplitterService)
    {
        // Middleware is now applied in the routes file
        $this->coinbaseService = $coinbaseService;
        $this->paymentSplitterService = $paymentSplitterService;
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
        $currency = 'USDT';

        // Get split details but don't show to user
        $splitDetails = $this->paymentSplitterService->getSplitDetails($amount);

        // Only log the split details for backend tracking
        Log::info('Payment split details (hidden from user)', $splitDetails);

        // Generate a unique reference
        $reference = 'DEP-' . Str::random(10);

        // Create metadata for the charge
        $metadata = [
            'customer_id' => $user->id,
            'customer_email' => $user->email,
            'reference' => $reference,
            'amount' => $amount,
        ];

        // Create success and cancel URLs
        $successUrl = route('payment.coinbase.success', ['reference' => $reference]);
        $cancelUrl = route('payment.coinbase.cancel', ['reference' => $reference]);

        // Create the charge
        Log::info('Creating Coinbase charge', [
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $metadata,
            'successUrl' => $successUrl,
            'cancelUrl' => $cancelUrl
        ]);

        // Use the full amount for the charge (fee will be handled internally)
        $charge = $this->coinbaseService->createCharge($amount, $currency, $metadata, $successUrl, $cancelUrl);

        if (!$charge) {
            Log::error('Failed to create Coinbase charge');
            return redirect()->back()->with('error', 'Failed to create payment. Please try again later.');
        }

        Log::info('Coinbase charge created successfully', [
            'charge_id' => $charge['id'],
            'hosted_url' => $charge['hosted_url']
        ]);

        // Store the charge details in the session
        session()->put('coinbase_charge', [
            'id' => $charge['id'],
            'amount' => $amount,
            'reference' => $reference,
            'created_at' => now(),
            'split_details' => $splitDetails, // Store split details for backend processing
        ]);

        // Create a payment intent (but don't create a transaction yet)
        $result = $this->paymentSplitterService->processPaymentWithSplit(
            $user->id,
            $amount,
            $charge['id'],
            'Deposit via Coinbase (pending)'
        );

        // Log the payment intent creation
        Log::info('Payment intent created', [
            'user_id' => $user->id,
            'charge_id' => $charge['id'],
            'amount' => $amount,
            'split_details' => $splitDetails
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
