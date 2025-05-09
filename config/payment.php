<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration related to payments
    | including the secondary wallet settings for fee splitting.
    |
    */

    'secondary_wallet_enabled' => env('SECONDARY_WALLET_ENABLED', false),
    'secondary_wallet_address' => env('SECONDARY_WALLET_ADDRESS', ''),
    'secondary_wallet_fee_percentage' => env('SECONDARY_WALLET_FEE_PERCENTAGE', 1),

];
