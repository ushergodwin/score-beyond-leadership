<?php

declare(strict_types=1);

namespace App\Services\Pesapal;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Lightweight wrapper around the Pesapal API 3.0 JSON endpoints.
 * Handles authentication token caching and common request plumbing.
 */
class PesapalClient
{
    private const TOKEN_CACHE_KEY = 'pesapal.api.token';

    private CacheRepository $cache;

    private HttpFactory $http;

    private string $baseUrl;

    private string $consumerKey;

    private string $consumerSecret;

    /**
     * @param  HttpFactory|null  $httpFactory  Allows injecting a fake HTTP client for tests.
     */
    public function __construct(
        CacheRepository $cache,
        ?HttpFactory $httpFactory = null,
        ?string $baseUrl = null,
        ?string $consumerKey = null,
        ?string $consumerSecret = null,
    ) {
        $this->cache = $cache;
        $this->http = $httpFactory ?? app(HttpFactory::class);
        $this->baseUrl = $baseUrl ?? (string) config('services.pesapal.base_url');
        $this->consumerKey = $consumerKey ?? (string) config('services.pesapal.consumer_key');
        $this->consumerSecret = $consumerSecret ?? (string) config('services.pesapal.consumer_secret');

        if ($this->baseUrl === '' || $this->consumerKey === '' || $this->consumerSecret === '') {
            throw new RuntimeException('Pesapal credentials are not configured.');
        }
    }

    /**
     * Submits an order payload to Pesapal and returns the API response.
     * According to API 3.0 documentation: https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/submitorderrequest
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function submitOrderRequest(array $payload): array
    {
        try {
            $response = $this->authorizedRequest()
                ->post('/api/Transactions/SubmitOrderRequest', $payload);

            return $this->handleResponse($response);
        } catch (RuntimeException $e) {
            // If we get an auth error, retry once with a fresh token
            if (str_contains($e->getMessage(), 'Invalid Access Token') || str_contains($e->getMessage(), 'authentication')) {
                Log::info('Retrying Pesapal request with fresh token');
                $this->forgetToken();
                
                $response = $this->authorizedRequest()
                    ->post('/api/Transactions/SubmitOrderRequest', $payload);

                return $this->handleResponse($response);
            }
            
            throw $e;
        }
    }

    /**
     * Registers or updates the IPN URL.
     *
     * @param  string  $ipnUrl
     * @return array<string, mixed>
     */
    public function registerIpnUrl(string $ipnUrl): array
    {
        try {
            $payload = [
                'url' => $ipnUrl,
                'ipn_notification_type' => 'GET',
            ];

            $response = $this->authorizedRequest()
                ->post('/api/URLSetup/RegisterIPN', $payload);

            return $this->handleResponse($response);
        } catch (RuntimeException $e) {
            // If we get an auth error, retry once with a fresh token
            if (str_contains($e->getMessage(), 'Invalid Access Token') || str_contains($e->getMessage(), 'authentication')) {
                Log::info('Retrying Pesapal request with fresh token');
                $this->forgetToken();
                
                $payload = [
                    'url' => $ipnUrl,
                    'ipn_notification_type' => 'GET',
                ];
                
                $response = $this->authorizedRequest()
                    ->post('/api/URLSetup/RegisterIPN', $payload);

                return $this->handleResponse($response);
            }
            
            throw $e;
        }
    }

    /**
     * Retrieves the Pesapal IPN list to confirm registration.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getIpnList(): array
    {
        try {
            $response = $this->authorizedRequest()
                ->get('/api/URLSetup/GetIpnList');

            $data = $this->handleResponse($response);

            $items = Arr::get($data, 'ipn_list', []);

            if (!is_array($items)) {
                return [];
            }

            return array_values(
                array_filter(
                    array_map(
                        static fn ($item): array => is_array($item) ? $item : [],
                        $items
                    ),
                    static fn ($item): bool => $item !== []
                )
            );
        } catch (RuntimeException $e) {
            // If we get an auth error, retry once with a fresh token
            if (str_contains($e->getMessage(), 'Invalid Access Token') || str_contains($e->getMessage(), 'authentication')) {
                Log::info('Retrying Pesapal request with fresh token');
                $this->forgetToken();
                
                $response = $this->authorizedRequest()
                    ->get('/api/URLSetup/GetIpnList');

                $data = $this->handleResponse($response);

                $items = Arr::get($data, 'ipn_list', []);

                if (!is_array($items)) {
                    return [];
                }

                return array_values(
                    array_filter(
                        array_map(
                            static fn ($item): array => is_array($item) ? $item : [],
                            $items
                        ),
                        static fn ($item): bool => $item !== []
                    )
                );
            }
            
            throw $e;
        }
    }

    /**
     * Requests the latest transaction status from Pesapal.
     * According to API 3.0 documentation: https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/gettransactionstatus
     *
     * @param  string  $orderTrackingId
     * @return array<string, mixed>
     */
    public function getTransactionStatus(string $orderTrackingId): array
    {
        try {
            $response = $this->authorizedRequest()
                ->get('/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($orderTrackingId));

            return $this->handleResponse($response);
        } catch (RuntimeException $e) {
            // If we get an auth error, retry once with a fresh token
            if (str_contains($e->getMessage(), 'Invalid Access Token') || str_contains($e->getMessage(), 'authentication')) {
                Log::info('Retrying Pesapal request with fresh token');
                $this->forgetToken();
                
                $response = $this->authorizedRequest()
                    ->get('/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($orderTrackingId));

                return $this->handleResponse($response);
            }
            
            throw $e;
        }
    }

    /**
     * Note: Recurring payments are enabled by including 'account_number' field
     * in the SubmitOrderRequest payload. No separate endpoint is needed.
     * According to API 3.0 documentation: https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/recurringpayments
     */

    /**
     * Revokes the cached token forcing the next call to refresh credentials.
     */
    public function forgetToken(): void
    {
        $this->cache->forget(self::TOKEN_CACHE_KEY);
    }

    /**
     * Ensures the HTTP client is authenticated and returns it for chaining.
     */
    private function authorizedRequest()
    {
        $token = $this->getAccessToken();

        return $this->http
            ->baseUrl($this->baseUrl)
            ->withToken($token)
            ->acceptJson()
            ->asJson();
    }

    /**
     * Retrieves and caches the Pesapal token.
     * Tokens expire after 5 minutes, so we cache for 4 minutes to be safe.
     */
    private function getAccessToken(): string
    {
        return (string) $this->cache->remember(self::TOKEN_CACHE_KEY, now()->addMinutes(4), function (): string {
            Log::info('Requesting new Pesapal access token');
            
            $response = $this->http
                ->baseUrl($this->baseUrl)
                ->acceptJson()
                ->asJson()
                ->post('/api/Auth/RequestToken', [
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret,
                ]);

            if (!$response->successful()) {
                $responseBody = $response->json();
                $message = Arr::get($responseBody, 'error.message', 'Failed to authenticate with Pesapal');
                
                Log::error('Pesapal authentication failed', [
                    'status' => $response->status(),
                    'body' => $responseBody,
                ]);
                
                throw new RuntimeException($message);
            }

            $data = $response->json();
            $token = Arr::get($data, 'token');

            if (!is_string($token) || $token === '') {
                Log::error('Pesapal token missing from response', ['data' => $data]);
                throw new RuntimeException('Pesapal token missing from response.');
            }

            Log::info('Successfully obtained Pesapal access token');
            return $token;
        });
    }

    /**
     * Normalises Pesapal responses and throws detailed exceptions on errors.
     * Handles 401 errors by clearing the token cache and retrying once.
     *
     * @return array<string, mixed>
     */
    private function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            $data = $response->json();
            return is_array($data) ? $data : [];
        }

        $responseBody = $response->json();
        $message = 'Pesapal API error';
        $status = $response->status();

        if (is_array($responseBody)) {
            $message = Arr::get($responseBody, 'error.message', $message);
        }

        // If we get a 401 (Unauthorized), the token might be expired
        // Clear the cache and let the next request get a fresh token
        if ($status === 401) {
            Log::warning('Pesapal API authentication failed - clearing token cache', [
                'status' => $status,
                'body' => $responseBody,
            ]);
            $this->forgetToken();
        } else {
            Log::warning('Pesapal API request failed', [
                'status' => $status,
                'body' => $responseBody,
            ]);
        }

        throw new RuntimeException($message);
    }
}


