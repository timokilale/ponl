<?php

namespace App\Services;

use App\Models\User;
use App\Models\VipLevel;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class VipService
{
    /**
     * Check and upgrade user's VIP level based on their balance if auto-upgrade is enabled.
     *
     * @param User $user
     * @return bool Whether the user's VIP level was upgraded
     */
    public function checkAndUpgradeVipLevel(User $user): bool
    {
        // Check if auto-upgrade is enabled
        $autoUpgradeEnabled = Setting::getValue('vip_auto_upgrade', 'true') === 'true';
        
        if (!$autoUpgradeEnabled) {
            return false;
        }
        
        // Get the user's current VIP level
        $currentVipLevel = $user->vipLevel;
        
        // Get all VIP levels that require a higher deposit than the current level
        // but less than or equal to the user's balance
        $eligibleVipLevel = VipLevel::where('deposit_required', '>', $currentVipLevel->deposit_required)
            ->where('deposit_required', '<=', $user->balance)
            ->orderBy('deposit_required', 'desc')
            ->first();
        
        // If an eligible VIP level is found, upgrade the user
        if ($eligibleVipLevel) {
            $oldVipLevelId = $user->vip_level_id;
            $user->vip_level_id = $eligibleVipLevel->id;
            $user->save();
            
            // Log the VIP level upgrade
            Log::info("User {$user->id} ({$user->username}) VIP level upgraded from {$oldVipLevelId} to {$eligibleVipLevel->id}");
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the highest VIP level a user is eligible for based on their balance.
     *
     * @param float $balance
     * @return VipLevel
     */
    public function getEligibleVipLevel(float $balance): VipLevel
    {
        // Get the highest VIP level that the user is eligible for based on their balance
        $eligibleVipLevel = VipLevel::where('deposit_required', '<=', $balance)
            ->orderBy('deposit_required', 'desc')
            ->first();
        
        // If no eligible VIP level is found, return the lowest VIP level
        if (!$eligibleVipLevel) {
            $eligibleVipLevel = VipLevel::orderBy('deposit_required', 'asc')->first();
        }
        
        return $eligibleVipLevel;
    }
}
