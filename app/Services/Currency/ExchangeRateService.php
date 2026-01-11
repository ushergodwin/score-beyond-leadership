<?php

declare(strict_types=1);

namespace App\Services\Currency;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Handles retrieval and persistence of currency exchange rates.
 * Uses exchangerate.host API: https://exchangerate.host/documentation
 */
class ExchangeRateService
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    /**
     * Fetches latest FX rates and stores them for later use.
     * Uses exchangerate.host API /live endpoint.
     */
    public function syncLatestRates(): void
    {
        $config = config('services.exchange_rate');
        $baseCurrencyCode = (string) Arr::get($config, 'base_currency', 'USD');
        $apiKey = (string) Arr::get($config, 'api_key', '');
        $targetCurrencies = Arr::wrap(Arr::get($config, 'target_currencies', ['UGX', 'EUR']));
        $baseUrl = (string) Arr::get($config, 'base_url', 'https://api.exchangerate.host');

        if ($apiKey === '' || $targetCurrencies === []) {
            throw new RuntimeException('Exchange rate configuration incomplete. Please set EXCHANGE_RATE_API_KEY and EXCHANGE_RATE_TARGETS.');
        }

        // Build currencies parameter (comma-separated currency codes)
        $currencies = implode(',', $targetCurrencies);
        
        // Call the /live endpoint with access_key parameter
        // Documentation: https://exchangerate.host/documentation
        $response = $this->http->baseUrl($baseUrl)->acceptJson()->get('/live', [
            'access_key' => $apiKey,
            'source' => $baseCurrencyCode,
            'currencies' => $currencies,
        ]);

        if (!$response->successful()) {
            Log::error('Exchange rate API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Failed to fetch exchange rates: HTTP ' . $response->status());
        }

        $data = $response->json();
        
        // Check if API returned success
        if (!Arr::get($data, 'success', false)) {
            $error = Arr::get($data, 'error', []);
            $errorMessage = is_array($error) ? Arr::get($error, 'info', 'Unknown error') : 'API returned error';
            Log::error('Exchange rate API error', [
                'error' => $error,
                'response' => $data,
            ]);
            throw new RuntimeException('Exchange rate API error: ' . $errorMessage);
        }

        $quotes = Arr::get($data, 'quotes', []);
        if (!is_array($quotes) || empty($quotes)) {
            Log::error('Exchange rate payload missing quotes', ['data' => $data]);
            throw new RuntimeException('Exchange rate payload missing quotes array.');
        }

        DB::transaction(function () use ($quotes, $baseCurrencyCode): void {
            // Ensure base currency exists
            $baseCurrency = Currency::firstOrCreate(
                ['code' => $baseCurrencyCode],
                ['name' => $baseCurrencyCode, 'symbol' => $baseCurrencyCode]
            );

            $fetchedAt = now();
            $ratesStored = 0;

            // Process quotes (format: "USDUGX" => 3578.5)
            foreach ($quotes as $pair => $rate) {
                if (!is_string($pair) || !is_numeric($rate)) {
                    continue;
                }

                // Extract quote currency code from pair (e.g., "USDUGX" -> "UGX")
                // Pair format is {base}{quote}, so we remove the base currency prefix
                $quoteCurrencyCode = str_replace($baseCurrencyCode, '', $pair);
                
                if ($quoteCurrencyCode === '' || $quoteCurrencyCode === $pair) {
                    continue; // Skip invalid pairs
                }

                $quoteCurrency = Currency::firstOrCreate(
                    ['code' => $quoteCurrencyCode],
                    ['name' => $quoteCurrencyCode, 'symbol' => $quoteCurrencyCode]
                );

                // Get the latest existing rate for this currency pair
                $latestRate = ExchangeRate::where('base_currency_id', $baseCurrency->id)
                    ->where('quote_currency_id', $quoteCurrency->id)
                    ->latest('fetched_at')
                    ->first();

                $newRate = (float) $rate;

                // Only update if rate has increased (to avoid lowering profit margins)
                // If no existing rate, store the new rate
                if ($latestRate === null || $newRate > $latestRate->rate) {
                    // Store the rate (e.g., USD/UGX = 3578.5 means 1 USD = 3578.5 UGX)
                    ExchangeRate::create([
                        'base_currency_id' => $baseCurrency->id,
                        'quote_currency_id' => $quoteCurrency->id,
                        'rate' => $newRate,
                        'source' => 'exchangerate.host',
                        'fetched_at' => $fetchedAt,
                    ]);

                    $ratesStored++;
                    
                    if ($latestRate !== null) {
                        Log::info('Exchange rate updated (increased)', [
                            'pair' => $baseCurrencyCode . '/' . $quoteCurrencyCode,
                            'old_rate' => $latestRate->rate,
                            'new_rate' => $newRate,
                        ]);
                    }
                } else {
                    Log::info('Exchange rate not updated (decreased or unchanged)', [
                        'pair' => $baseCurrencyCode . '/' . $quoteCurrencyCode,
                        'current_rate' => $latestRate->rate,
                        'fetched_rate' => $newRate,
                    ]);
                }
            }

            Log::info('Exchange rates synced successfully', [
                'base_currency' => $baseCurrencyCode,
                'rates_stored' => $ratesStored,
                'fetched_at' => $fetchedAt->toDateTimeString(),
            ]);
        });
    }
}
