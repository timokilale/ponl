<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\PaymentIntent;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentSplitterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoinbaseWebhookController extends Controller
{
    /**
     * @var \App\Services\CoinbaseService
     */
    protected $coinbaseService;

    /**
     * @var PaymentSplitterService
     */
    protected $paymentSplitterService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\CoinbaseService $coinbaseService
     * @param PaymentSplitterService $paymentSplitterService
     * @return void
     */
    public function __construct(\App\Services\CoinbaseService $coinbaseService, PaymentSplitterService $paymentSplitterService)
    {
        $this->coinbaseService = $coinbaseService;
        $this->paymentSplitterService = $paymentSplitterService;

        // Middleware is now applied in the routes file
    }

    /**
     * Handle Coinbase webhook.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        // Get the request payload and signature
        $payload = $request->getContent();
        $signature = $request->header('X-CC-Webhook-Signature');

        // Verify the webhook signature
        if (!$this->coinbaseService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Coinbase webhook signature');
            return response('Invalid signature', 400);
        }

        // Parse the payload
        $data = json_decode($payload, true);

        // Check if this is a charge event
        if (!isset($data['event']['type']) || !isset($data['event']['data'])) {
            Log::warning('Invalid Coinbase webhook payload');
            return response('Invalid payload', 400);
        }

        $eventType = $data['event']['type'];
        $chargeData = $data['event']['data'];

        // Process the event
        if ($eventType === 'charge:confirmed') {
            $this->processConfirmedCharge($chargeData);
        } elseif ($eventType === 'charge:failed') {
            $this->processFailedCharge($chargeData);
        }

        return response('Webhook processed', 200);
    }

    /**
     * Process a confirmed charge.
     *
     * @param array $chargeData
     * @return void
     */
    protected function processConfirmedCharge(array $chargeData)
    {
        // Extract metadata
        $metadata = $chargeData['metadata'] ?? [];
        $userId = $metadata['customer_id'] ?? null;
        $reference = $metadata['reference'] ?? null;
        $chargeId = $chargeData['id'] ?? 'unknown';

        if (!$userId || !$reference) {
            Log::warning('Missing user ID or reference in Coinbase charge metadata', [
                'charge_id' => $chargeId
            ]);
            return;
        }

        // Get the user
        $user = User::find($userId);

        if (!$user) {
            Log::warning('User not found for Coinbase charge', [
                'user_id' => $userId,
                'charge_id' => $chargeId
            ]);
            return;
        }

        // Get the payment amount
        $pricing = $chargeData['pricing'] ?? [];
        $localPrice = $pricing['local'] ?? [];
        $amount = $localPrice['amount'] ?? 0;

        if (!$amount) {
            Log::warning('Invalid amount in Coinbase charge', [
                'charge_id' => $chargeId
            ]);
            return;
        }

        // Check if this transaction has already been processed
        $existingTransaction = Transaction::where('reference_id', $chargeId)
            ->where('reference_type', 'coinbase_charge')
            ->where('status', 'completed')
            ->first();

        if ($existingTransaction) {
            Log::info('Coinbase charge already processed', [
                'charge_id' => $chargeId
            ]);
            return;
        }

        // Find the payment intent
        $paymentIntent = PaymentIntent::where('reference_id', $chargeId)
            ->where('reference_type', 'coinbase_charge')
            ->first();

        // Get split details if available
        $splitDetails = null;
        if ($paymentIntent && $paymentIntent->metadata) {
            $metadata = json_decode($paymentIntent->metadata, true);
            $splitDetails = $metadata['split_details'] ?? null;
        }

        // Calculate the main amount and fee amount
        $mainAmount = $amount;
        $feeAmount = 0;

        if ($splitDetails && isset($splitDetails['fee_amount'])) {
            $feeAmount = $splitDetails['fee_amount'];
            $mainAmount = $splitDetails['main_amount'];
        }

        // Begin transaction
        \DB::beginTransaction();

        try {
            // Update user balance with the main amount (not the fee)
            $user->balance += $mainAmount;
            $user->save();

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $mainAmount, // Only credit the main amount to the user
                'type' => 'credit',
                'description' => 'Deposit via Coinbase',
                'reference_id' => $chargeId,
                'reference_type' => 'coinbase_charge',
                'status' => 'completed',
                'balance_after' => $user->balance,
                'blockchain_txid' => $chargeData['payments'][0]['transaction_id'] ?? null,
                'created_at' => now()
            ]);

            // If there was a fee, log it
            if ($feeAmount > 0) {
                Log::info('Fee collected', [
                    'user_id' => $user->id,
                    'charge_id' => $chargeId,
                    'fee_amount' => $feeAmount,
                    'secondary_wallet' => $this->paymentSplitterService->getSecondaryWalletAddress()
                ]);
            }

            // Update payment intent status
            if ($paymentIntent) {
                $paymentIntent->status = 'completed';
                $paymentIntent->save();
            }

            // Create notification
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'Deposit Successful',
                'message' => "Your deposit of {$mainAmount} USDT has been confirmed and added to your balance.",
                'type' => 'deposit'
            ]);

            \DB::commit();

            Log::info('Coinbase charge processed successfully', [
                'user_id' => $user->id,
                'amount' => $amount,
                'main_amount' => $mainAmount,
                'fee_amount' => $feeAmount,
                'charge_id' => $chargeId
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            Log::error('Error processing Coinbase charge', [
                'user_id' => $user->id,
                'charge_id' => $chargeId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process a failed charge.
     *
     * @param array $chargeData
     * @return void
     */
    protected function processFailedCharge(array $chargeData)
    {
        // Extract metadata
        $metadata = $chargeData['metadata'] ?? [];
        $userId = $metadata['customer_id'] ?? null;

        if (!$userId) {
            \Log::warning('Missing user ID in Coinbase charge metadata', [
                'charge_id' => $chargeData['id'] ?? 'unknown'
            ]);
            return;
        }

        // Get the user
        $user = \App\Models\User::find($userId);

        if (!$user) {
            \Log::warning('User not found for Coinbase charge', [
                'user_id' => $userId,
                'charge_id' => $chargeData['id'] ?? 'unknown'
            ]);
            return;
        }

        // Create notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Deposit Failed',
            'message' => 'Your deposit has failed. Please try again or contact support if the issue persists.',
            'type' => 'deposit'
        ]);

        \Log::info('Coinbase failed charge processed', [
            'user_id' => $user->id,
            'charge_id' => $chargeData['id'] ?? 'unknown'
        ]);
    }
}
