<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('settings')->insert([
            [
                'key' => 'withdrawal_fee_percentage',
                'value' => '5',
                'description' => 'Withdrawal fee percentage',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'min_withdrawal_amount',
                'value' => '10',
                'description' => 'Minimum withdrawal amount in USDT',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'referral_reward_percentage',
                'value' => '10',
                'description' => 'Percentage of earnings given to referrer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'description' => 'Whether the site is in maintenance mode (0=off, 1=on)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
