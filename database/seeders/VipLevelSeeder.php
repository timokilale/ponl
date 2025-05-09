<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VipLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing VIP levels
        \DB::table('vip_levels')->truncate();

        // Create 10 numbered VIP levels
        $vipLevels = [];

        for ($i = 1; $i <= 10; $i++) {
            $vipLevels[] = [
                'name' => 'VIP ' . $i,
                'deposit_required' => $this->getDepositRequired($i),
                'reward_multiplier' => $this->getRewardMultiplier($i),
                'daily_tasks_limit' => $this->getDailyTasksLimit($i),
                'withdrawal_fee_discount' => $this->getWithdrawalFeeDiscount($i),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        \DB::table('vip_levels')->insert($vipLevels);
    }

    /**
     * Get the deposit required for a VIP level.
     *
     * @param int $level
     * @return float
     */
    private function getDepositRequired(int $level): float
    {
        // Linear increase in deposit requirements from 30 to 180 USDT
        switch ($level) {
            case 1: return 30;
            case 2: return 45;
            case 3: return 60;
            case 4: return 75;
            case 5: return 90;
            case 6: return 105;
            case 7: return 120;
            case 8: return 135;
            case 9: return 150;
            case 10: return 180;
            default: return 30;
        }
    }

    /**
     * Get the reward multiplier for a VIP level.
     *
     * @param int $level
     * @return float
     */
    private function getRewardMultiplier(int $level): float
    {
        // Linear increase in reward multiplier
        return 1 + (($level - 1) * 0.1);
    }

    /**
     * Get the daily tasks limit for a VIP level.
     *
     * @param int $level
     * @return int
     */
    private function getDailyTasksLimit(int $level): int
    {
        // Linear increase in daily tasks limit
        return 10 + (($level - 1) * 5);
    }

    /**
     * Get the withdrawal fee discount for a VIP level.
     *
     * @param int $level
     * @return float
     */
    private function getWithdrawalFeeDiscount(int $level): float
    {
        // Linear increase in withdrawal fee discount, max 90%
        return min(0.9, ($level - 1) * 0.1);
    }
}
