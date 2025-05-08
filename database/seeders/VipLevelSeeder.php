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
        \DB::table('vip_levels')->insert([
            [
                'name' => 'Bronze',
                'points_required' => 0,
                'reward_multiplier' => 1.00,
                'daily_tasks_limit' => 10,
                'withdrawal_fee_discount' => 0.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Silver',
                'points_required' => 1000,
                'reward_multiplier' => 1.10,
                'daily_tasks_limit' => 15,
                'withdrawal_fee_discount' => 0.10,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Gold',
                'points_required' => 5000,
                'reward_multiplier' => 1.25,
                'daily_tasks_limit' => 20,
                'withdrawal_fee_discount' => 0.25,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Platinum',
                'points_required' => 10000,
                'reward_multiplier' => 1.50,
                'daily_tasks_limit' => 30,
                'withdrawal_fee_discount' => 0.50,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Diamond',
                'points_required' => 25000,
                'reward_multiplier' => 2.00,
                'daily_tasks_limit' => 50,
                'withdrawal_fee_discount' => 0.75,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
