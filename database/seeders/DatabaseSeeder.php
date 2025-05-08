<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed VIP levels first as they are referenced by users
        $this->call(VipLevelSeeder::class);

        // Create admin user
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'vip_level_id' => 5, // Diamond level
        ]);

        // Seed settings
        $this->call(SettingsSeeder::class);
    }
}
