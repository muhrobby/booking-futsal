<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = [];

        // Create 50 member users
        for ($i = 1; $i <= 50; $i++) {
            $members[] = [
                'name' => fake()->name(),
                'email' => 'member' . $i . '@futsal.com',
                'phone' => fake()->phoneNumber(),
                'password' => bcrypt('password123'),
                'role' => 'member',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all members at once for better performance
        User::insert($members);

        $this->command->info('✅ 50 member users created successfully!');
    }
}
