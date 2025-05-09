<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Services\VipService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateUserVipLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-vip-levels {--force : Force update even if auto-upgrade is disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all users\' VIP levels based on their current balance';

    /**
     * @var VipService
     */
    protected $vipService;

    /**
     * Create a new command instance.
     *
     * @param VipService $vipService
     * @return void
     */
    public function __construct(VipService $vipService)
    {
        parent::__construct();
        $this->vipService = $vipService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $autoUpgradeEnabled = Setting::getValue('vip_auto_upgrade', 'true') === 'true';
        $force = $this->option('force');
        
        if (!$autoUpgradeEnabled && !$force) {
            $this->error('Automatic VIP upgrades are disabled. Use --force to update anyway.');
            return 1;
        }
        
        $this->info('Updating user VIP levels based on their current balance...');
        
        $users = User::all();
        $totalUsers = $users->count();
        $updatedCount = 0;
        
        $this->output->progressStart($totalUsers);
        
        foreach ($users as $user) {
            $oldVipLevelId = $user->vip_level_id;
            
            // Get the highest VIP level the user is eligible for
            $eligibleVipLevel = $this->vipService->getEligibleVipLevel($user->balance);
            
            // If the eligible VIP level is higher than the current one, upgrade
            if ($eligibleVipLevel->id > $oldVipLevelId) {
                $user->vip_level_id = $eligibleVipLevel->id;
                $user->save();
                
                // Log the VIP level upgrade
                Log::info("User {$user->id} ({$user->username}) VIP level upgraded from {$oldVipLevelId} to {$eligibleVipLevel->id} by command");
                
                // Create notification for the user
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => 'VIP Level Upgraded',
                    'message' => "Congratulations! Your VIP level has been upgraded to {$eligibleVipLevel->name} based on your current balance.",
                    'type' => 'vip'
                ]);
                
                $updatedCount++;
            }
            
            $this->output->progressAdvance();
        }
        
        $this->output->progressFinish();
        
        $this->info("VIP level update complete. {$updatedCount} out of {$totalUsers} users were upgraded.");
        
        return 0;
    }
}
