<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WpUsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wp_users')->insert([
            [
                'user_login' => 'admin',
                'user_pass' => '$P$BUpWMfGwYij62HH2PFDBd5NV.JmeYt0',
                'user_nicename' => 'admin',
                'user_email' => 'admin@example.com',
                'user_url' => '',
                'user_registered' => '2025-01-01 00:00:00',
                'user_activation_key' => '',
                'user_status' => 0,
                'display_name' => 'Admin',
            ],
            [
                'user_login' => 'john_customer',
                'user_pass' => '$P$BUpWMfGwYij62HH2PFDBd5NV.JmeYt0',
                'user_nicename' => 'John Doe',
                'user_email' => 'john@example.com',
                'user_url' => '',
                'user_registered' => '2025-01-15 10:00:00',
                'user_activation_key' => '',
                'user_status' => 0,
                'display_name' => 'John Doe',
            ],
            [
                'user_login' => 'jane_customer',
                'user_pass' => '$P$BUpWMfGwYij62HH2PFDBd5NV.JmeYt0',
                'user_nicename' => 'Jane Smith',
                'user_email' => 'jane@example.com',
                'user_url' => '',
                'user_registered' => '2025-02-01 12:00:00',
                'user_activation_key' => '',
                'user_status' => 0,
                'display_name' => 'Jane Smith',
            ],
            [
                'user_login' => 'bob_customer',
                'user_pass' => '$P$BUpWMfGwYij62HH2PFDBd5NV.JmeYt0',
                'user_nicename' => 'Bob Wilson',
                'user_email' => 'bob@example.com',
                'user_url' => '',
                'user_registered' => '2025-02-10 09:00:00',
                'user_activation_key' => '',
                'user_status' => 0,
                'display_name' => 'Bob Wilson',
            ],
        ]);
    }
}
