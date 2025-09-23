<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Field::query()->delete();
        Field::create([
            'name' => 'Lapangan A (Vinyl)',
            'description' => 'Permukaan vinyl, cocok untuk permainan cepat.',
            'price_per_hour' => 150000,
            'is_active' => true,
        ]);
        Field::create([
            'name' => 'Lapangan B (Sintetis)',
            'description' => 'Rumput sintetis, grip nyaman.',
            'price_per_hour' => 175000,
            'is_active' => true,
        ]);
    }
}
