<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeliveryCost;

class DeliveryCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deliveryCosts = [
            [
                'min_distance' => 0.00,
                'max_distance' => 2.99,
                'cost' => 1.00,
                'description' => 'Zona cercana (0-2.99 km)',
                'is_active' => true,
            ],
            [
                'min_distance' => 3.00,
                'max_distance' => 5.99,
                'cost' => 2.00,
                'description' => 'Zona media (3-5.99 km)',
                'is_active' => true,
            ],
            [
                'min_distance' => 6.00,
                'max_distance' => 8.49,
                'cost' => 3.00,
                'description' => 'Zona lejana (6-8.49 km)',
                'is_active' => true,
            ],
            [
                'min_distance' => 8.50,
                'max_distance' => 9.99,
                'cost' => 4.00,
                'description' => 'Zona muy lejana (8.5-9.99 km)',
                'is_active' => true,
            ],
        ];

        foreach ($deliveryCosts as $cost) {
            DeliveryCost::create($cost);
        }
    }
}