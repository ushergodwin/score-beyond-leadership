<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Kampala Pickup (HQ)',
                'code' => 'PICKUP_KLA',
                'type' => 'local',
                'region' => 'Kampala',
                'carrier' => 'Score Beyond',
                'base_rate' => 0,
                'currency' => 'UGX',
                'estimated_min_days' => 1,
                'estimated_max_days' => 2,
                'is_pickup' => true,
            ],
            [
                'name' => 'Adjumani Pickup (Field Office)',
                'code' => 'PICKUP_ADJ',
                'type' => 'local',
                'region' => 'Adjumani',
                'carrier' => 'Score Beyond',
                'base_rate' => 0,
                'currency' => 'UGX',
                'estimated_min_days' => 1,
                'estimated_max_days' => 3,
                'is_pickup' => true,
            ],
            [
                'name' => 'Uganda Regional Delivery',
                'code' => 'UG_REGIONAL',
                'type' => 'local',
                'region' => 'Uganda',
                'carrier' => 'Regional Courier',
                'base_rate' => 15000,
                'currency' => 'UGX',
                'estimated_min_days' => 2,
                'estimated_max_days' => 5,
            ],
            [
                'name' => 'International Economy',
                'code' => 'INTL_ECON',
                'type' => 'international',
                'region' => 'Worldwide',
                'carrier' => 'DHL Express',
                'base_rate' => 45000,
                'currency' => 'UGX',
                'estimated_min_days' => 5,
                'estimated_max_days' => 12,
            ],
            [
                'name' => 'International Priority',
                'code' => 'INTL_PRIORITY',
                'type' => 'international',
                'region' => 'Worldwide',
                'carrier' => 'FedEx',
                'base_rate' => 85000,
                'currency' => 'UGX',
                'estimated_min_days' => 3,
                'estimated_max_days' => 7,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method + ['is_active' => true]
            );
        }
    }
}
