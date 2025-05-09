<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register the UpdateUserVipLevels command
Artisan::command('users:update-vip-levels', function () {
    $this->call('users:update-vip-levels');
})->purpose('Update all users\' VIP levels based on their current balance');
