<?php

declare(strict_types=1);

namespace App\Support\Currency;

use App\Models\ExchangeRate;

class PriceDisplay
{
    private static ?float $usdRateCache = null;

    /**
     * Returns display amounts for UGX -> USD pairing.
     *
     * @return array{ugx: float, usd: float}
     */
    public static function forUgx(float $amountUgx): array
    {
        $usdRate = self::getUsdRate();

        return [
            'ugx' => $amountUgx,
            'usd' => round($amountUgx * $usdRate, 2),
        ];
    }

    private static function getUsdRate(): float
    {
        if (self::$usdRateCache !== null) {
            return self::$usdRateCache;
        }

        // Get USD/UGX rate (e.g., 1 USD = 3578.5 UGX)
        // We need to convert UGX to USD, so we need 1 / (USD/UGX) = UGX/USD
        $rate = ExchangeRate::query()
            ->whereHas('baseCurrency', fn ($query) => $query->where('code', 'USD'))
            ->whereHas('quoteCurrency', fn ($query) => $query->where('code', 'UGX'))
            ->orderByDesc('fetched_at')
            ->value('rate');

        if ($rate === null || $rate <= 0) {
            // Fallback: use approximate rate if no data available
            self::$usdRateCache = 1 / 3578.5; // Approximate: 1 UGX = 0.000279 USD

            return self::$usdRateCache;
        }

        // Convert: if 1 USD = X UGX, then 1 UGX = 1/X USD
        self::$usdRateCache = 1 / (float) $rate;

        return self::$usdRateCache;
    }
}


