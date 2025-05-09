<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PaymentSplitterService
{
    /**
     * @var bool
     */
    protected $secondaryWalletEnabled;

    /**
     * @var string
     */
    protected $secondaryWalletAddress;

    /**
     * @var float
     */
    protected $secondaryWalletFeePercentage;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->secondaryWalletEnabled = config('payment.secondary_wallet_enabled', false);
        $this->secondaryWalletAddress = config('payment.secondary_wallet_address', '');
        $this->secondaryWalletFeePercentage = config('payment.secondary_wallet_fee_percentage', 1);
    }

    /**
     * Calculate the fee amount for the secondary wallet.
     *
     * @param float $amount
     * @return float
     */
    public function calculateFeeAmount(float $amount): float
    {
        if (!$this->secondaryWalletEnabled || empty($this->secondaryWalletAddress)) {
            return 0;
        }

        return round($amount * ($this->secondaryWalletFeePercentage / 100), 2);
    }

    /**
     * Calculate the main amount after deducting the fee.
     *
     * @param float $amount
     * @return float
     */
    public function calculateMainAmount(float $amount): float
    {
        $feeAmount = $this->calculateFeeAmount($amount);
        return $amount - $feeAmount;
    }

    /**
     * Get the payment split details.
     *
     * @param float $amount
     * @return array
     */
    public function getSplitDetails(float $amount): array
    {
        $feeAmount = $this->calculateFeeAmount($amount);
        $mainAmount = $amount - $feeAmount;

        return [
            'original_amount' => $amount,
            'main_amount' => $mainAmount,
            'fee_amount' => $feeAmount,
            'fee_percentage' => $this->secondaryWalletFeePercentage,
            'secondary_wallet_address' => $this->secondaryWalletAddress,
            'secondary_wallet_enabled' => $this->secondaryWalletEnabled,
        ];
    }

    /**
     * Process a payment and split it if needed.
     * This method is used to track the payment intent without creating a transaction.
     *
     * @param int $userId
     * @param float $amount
     * @param string $referenceId
     * @param string $description
     * @return array
     */
    public function processPaymentWithSplit(int $userId, float $amount, string $referenceId, string $description = 'Payment'): array
    {
        // Get the split details
        $splitDetails = $this->getSplitDetails($amount);

        // Log the split details
        \Illuminate\Support\Facades\Log::info('Payment intent created', [
            'user_id' => $userId,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'description' => $description,
            'split_details' => $splitDetails
        ]);

        // If secondary wallet is enabled, log the fee details
        if ($this->isSecondaryWalletEnabled() && $splitDetails['fee_amount'] > 0) {
            \Illuminate\Support\Facades\Log::info('Fee details', [
                'user_id' => $userId,
                'original_amount' => $amount,
                'fee_amount' => $splitDetails['fee_amount'],
                'main_amount' => $splitDetails['main_amount'],
                'reference_id' => $referenceId,
                'secondary_wallet' => $this->secondaryWalletAddress
            ]);
        }

        // Create a payment intent record in the database
        $paymentIntent = \App\Models\PaymentIntent::create([
            'user_id' => $userId,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'reference_type' => 'coinbase_charge',
            'status' => 'pending',
            'metadata' => json_encode([
                'description' => $description,
                'split_details' => $splitDetails
            ]),
            'created_at' => now()
        ]);

        return [
            'payment_intent' => $paymentIntent,
            'split_details' => $splitDetails
        ];
    }

    /**
     * Check if the secondary wallet is enabled.
     *
     * @return bool
     */
    public function isSecondaryWalletEnabled(): bool
    {
        return $this->secondaryWalletEnabled && !empty($this->secondaryWalletAddress);
    }

    /**
     * Get the secondary wallet address.
     *
     * @return string
     */
    public function getSecondaryWalletAddress(): string
    {
        return $this->secondaryWalletAddress;
    }

    /**
     * Get the secondary wallet fee percentage.
     *
     * @return float
     */
    public function getSecondaryWalletFeePercentage(): float
    {
        return $this->secondaryWalletFeePercentage;
    }
}
