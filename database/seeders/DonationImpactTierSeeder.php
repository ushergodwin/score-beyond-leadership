<?php

namespace Database\Seeders;

use App\Models\DonationImpactTier;
use Illuminate\Database\Seeder;

class DonationImpactTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            // UGX Tiers
            [
                'label' => 'Supporter',
                'amount' => 50000,
                'currency' => 'UGX',
                'description' => 'Help provide basic sports equipment for one child',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'Champion',
                'amount' => 100000,
                'currency' => 'UGX',
                'description' => 'Support a month of training for a young athlete',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'Leader',
                'amount' => 250000,
                'currency' => 'UGX',
                'description' => 'Fund educational materials for a student',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'label' => 'Hero',
                'amount' => 500000,
                'currency' => 'UGX',
                'description' => 'Sponsor a complete program for a community',
                'display_order' => 4,
                'is_active' => true,
            ],
            // USD Tiers
            [
                'label' => 'Supporter',
                'amount' => 15,
                'currency' => 'USD',
                'description' => 'Help provide basic sports equipment for one child',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'Champion',
                'amount' => 30,
                'currency' => 'USD',
                'description' => 'Support a month of training for a young athlete',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'Leader',
                'amount' => 75,
                'currency' => 'USD',
                'description' => 'Fund educational materials for a student',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'label' => 'Hero',
                'amount' => 150,
                'currency' => 'USD',
                'description' => 'Sponsor a complete program for a community',
                'display_order' => 4,
                'is_active' => true,
            ],
            // EUR Tiers
            [
                'label' => 'Supporter',
                'amount' => 15,
                'currency' => 'EUR',
                'description' => 'Help provide basic sports equipment for one child',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'Champion',
                'amount' => 30,
                'currency' => 'EUR',
                'description' => 'Support a month of training for a young athlete',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'Leader',
                'amount' => 75,
                'currency' => 'EUR',
                'description' => 'Fund educational materials for a student',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'label' => 'Hero',
                'amount' => 150,
                'currency' => 'EUR',
                'description' => 'Sponsor a complete program for a community',
                'display_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            DonationImpactTier::firstOrCreate(
                [
                    'label' => $tier['label'],
                    'currency' => $tier['currency'],
                ],
                $tier
            );
        }
    }
}

