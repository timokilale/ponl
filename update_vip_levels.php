<?php

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the VipService
$vipService = $app->make(\App\Services\VipService::class);

// Get all users
$users = \App\Models\User::all();
$totalUsers = $users->count();
$updatedCount = 0;

echo "Updating VIP levels for {$totalUsers} users...\n";

foreach ($users as $user) {
    $oldVipLevelId = $user->vip_level_id;
    
    // Get the highest VIP level the user is eligible for
    $eligibleVipLevel = $vipService->getEligibleVipLevel($user->balance);
    
    // If the eligible VIP level is higher than the current one, upgrade
    if ($eligibleVipLevel->id > $oldVipLevelId) {
        $user->vip_level_id = $eligibleVipLevel->id;
        $user->save();
        
        // Log the VIP level upgrade
        \Illuminate\Support\Facades\Log::info("User {$user->id} ({$user->username}) VIP level upgraded from {$oldVipLevelId} to {$eligibleVipLevel->id} by script");
        
        // Create notification for the user
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'VIP Level Upgraded',
            'message' => "Congratulations! Your VIP level has been upgraded to {$eligibleVipLevel->name} based on your current balance.",
            'type' => 'vip'
        ]);
        
        $updatedCount++;
        echo "Upgraded user {$user->username} from VIP level {$oldVipLevelId} to {$eligibleVipLevel->id}\n";
    }
}

echo "VIP level update complete. {$updatedCount} out of {$totalUsers} users were upgraded.\n";
