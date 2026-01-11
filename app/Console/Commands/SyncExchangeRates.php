<?php

namespace App\Console\Commands;

use App\Services\Currency\ExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rates:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync exchange rates from exchangerate.host API';

    /**
     * Execute the console command.
     */
    public function handle(ExchangeRateService $service): int
    {
        $this->info('Syncing exchange rates from exchangerate.host...');

        try {
            $service->syncLatestRates();
            $this->info('Exchange rates synced successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync exchange rates: ' . $e->getMessage());
            Log::error('Exchange rate sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }
}
