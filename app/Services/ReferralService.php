<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Generate a unique referral code for a user.
     *
     * @param User $user
     * @return string
     */
    public function generateReferralCode(User $user): string
    {
        // Start with the user's ID to ensure uniqueness
        $baseCode = $user->id . strtoupper(Str::random(6));
        
        // Ensure the code is unique
        while (User::where('referral_code', $baseCode)->where('id', '!=', $user->id)->exists()) {
            $baseCode = $user->id . strtoupper(Str::random(6));
        }
        
        return $baseCode;
    }
    
    /**
     * Ensure a user has a referral code.
     * If they don't have one, generate and save it.
     *
     * @param User $user
     * @return string The user's referral code
     */
    public function ensureUserHasReferralCode(User $user): string
    {
        if (empty($user->referral_code)) {
            $referralCode = $this->generateReferralCode($user);
            $user->referral_code = $referralCode;
            $user->save();
        }
        
        return $user->referral_code;
    }
    
    /**
     * Get the referral URL for a user.
     *
     * @param User $user
     * @return string
     */
    public function getReferralUrl(User $user): string
    {
        $referralCode = $this->ensureUserHasReferralCode($user);
        return route('register') . '?ref=' . $referralCode;
    }
    
    /**
     * Process a referral when a new user registers.
     *
     * @param User $newUser
     * @param string|null $referralCode
     * @return bool
     */
    public function processReferral(User $newUser, ?string $referralCode): bool
    {
        if (empty($referralCode)) {
            return false;
        }
        
        // Find the referring user
        $referrer = User::where('referral_code', $referralCode)->first();
        
        if (!$referrer) {
            return false;
        }
        
        // Set the referred_by field
        $newUser->referred_by = $referralCode;
        $newUser->save();
        
        // Create a referral record
        \App\Models\Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'status' => 'pending',
        ]);
        
        return true;
    }
}
