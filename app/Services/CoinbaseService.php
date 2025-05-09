<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinbaseService
{
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $webhookSecret;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiKey = config('services.coinbase.api_key');
        $this->apiSecret = config('services.coinbase.api_secret');
        $this->apiUrl = config('services.coinbase.api_url', 'https://api.commerce.coinbase.com');
        $this->webhookSecret = config('services.coinbase.webhook_secret');
    }

    /**
     * Create a new charge.
     *
     * @param float $amount
     * @param string $currency
     * @param array $metadata
     * @param string $redirectUrl
     * @param string $cancelUrl
     * @return array|null
     */
    public function createCharge($amount, $currency, $metadata, $redirectUrl, $cancelUrl)
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'X-CC-Api-Key' => $this->apiKey,
                'X-CC-Version' => '2018-03-22',
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/charges", [
                'name' => 'Deposit to Pawnzz',
                'description' => 'Deposit funds to your Pawnzz account',
                'pricing_type' => 'fixed_price',
                'local_price' => [
                    'amount' => $amount,
                    'currency' => $currency,
                ],
                'metadata' => $metadata,
                'redirect_url' => $redirectUrl,
                'cancel_url' => $cancelUrl,
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('Coinbase charge creation failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Coinbase charge creation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Get a charge by ID.
     *
     * @param string $chargeId
     * @return array|null
     */
    public function getCharge($chargeId)
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'X-CC-Api-Key' => $this->apiKey,
                'X-CC-Version' => '2018-03-22',
                'Content-Type' => 'application/json',
            ])->get("{$this->apiUrl}/charges/{$chargeId}");

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('Coinbase get charge failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Coinbase get charge exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Verify webhook signature.
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        if (empty($this->webhookSecret) || empty($signature)) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        return hash_equals($computedSignature, $signature);
    }
}
