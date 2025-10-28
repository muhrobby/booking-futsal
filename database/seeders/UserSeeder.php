<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 admins
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => "admin{$i}@example.com"],
                [
                    'name' => "Admin {$i}",
                    'phone' => '0812345' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'role' => 'admin',
                    'password' => Hash::make('password'),
                ]
            );
        }

        // Create 50 users
        for ($i = 1; $i <= 50; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'phone' => '0856789' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'role' => 'user',
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
