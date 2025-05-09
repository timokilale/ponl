<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoinbaseWebhookController extends Controller
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
        $this->coinbaseService = $coinbaseService;

        // Disable CSRF protection for webhook endpoints
        $this->middleware('web')->except('handle');
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
            \Log::warning('Invalid Coinbase webhook signature');
            return response('Invalid signature', 400);
        }

        // Parse the payload
        $data = json_decode($payload, true);

        // Check if this is a charge event
        if (!isset($data['event']['type']) || !isset($data['event']['data'])) {
            \Log::warning('Invalid Coinbase webhook payload');
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

        if (!$userId || !$reference) {
            \Log::warning('Missing user ID or reference in Coinbase charge metadata', [
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

        // Get the payment amount
        $pricing = $chargeData['pricing'] ?? [];
        $localPrice = $pricing['local'] ?? [];
        $amount = $localPrice['amount'] ?? 0;

        if (!$amount) {
            \Log::warning('Invalid amount in Coinbase charge', [
                'charge_id' => $chargeData['id'] ?? 'unknown'
            ]);
            return;
        }

        // Check if this transaction has already been processed
        $existingTransaction = \App\Models\Transaction::where('reference_id', $chargeData['id'])
            ->where('reference_type', 'coinbase_charge')
            ->first();

        if ($existingTransaction) {
            \Log::info('Coinbase charge already processed', [
                'charge_id' => $chargeData['id'] ?? 'unknown'
            ]);
            return;
        }

        // Update user balance
        $user->balance += $amount;
        $user->save();

        // Create transaction record
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'description' => 'Deposit via Coinbase',
            'reference_id' => $chargeData['id'],
            'reference_type' => 'coinbase_charge',
            'status' => 'completed',
            'balance_after' => $user->balance,
            'blockchain_txid' => $chargeData['payments'][0]['transaction_id'] ?? null,
            'created_at' => now()
        ]);

        // Create notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Deposit Successful',
            'message' => "Your deposit of {$amount} USDT has been confirmed and added to your balance.",
            'type' => 'deposit'
        ]);

        \Log::info('Coinbase charge processed successfully', [
            'user_id' => $user->id,
            'amount' => $amount,
            'charge_id' => $chargeData['id'] ?? 'unknown'
        ]);
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
