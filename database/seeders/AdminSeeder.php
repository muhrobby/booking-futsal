<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            ['name' => 'Admin Master', 'email' => 'admin@futsal.com'],
            ['name' => 'Admin Dashboard', 'email' => 'dashboard@futsal.com'],
            ['name' => 'Admin Fields', 'email' => 'fields@futsal.com'],
            ['name' => 'Admin Bookings', 'email' => 'bookings@futsal.com'],
            ['name' => 'Admin Users', 'email' => 'users@futsal.com'],
            ['name' => 'Admin Reports', 'email' => 'reports@futsal.com'],
            ['name' => 'Admin Support', 'email' => 'support@futsal.com'],
            ['name' => 'Admin Finance', 'email' => 'finance@futsal.com'],
            ['name' => 'Admin Marketing', 'email' => 'marketing@futsal.com'],
            ['name' => 'Admin Operations', 'email' => 'operations@futsal.com'],
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'phone' => '62812345678',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('✅ ' . count($admins) . ' admins created successfully!');
    }
}
