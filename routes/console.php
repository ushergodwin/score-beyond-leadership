<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule exchange rate sync to run weekly on Monday at 1:00 AM
// Only updates rates if they have increased (to avoid lowering profit margins)
Schedule::command('exchange-rates:sync')
    ->weeklyOn(1, '01:00') // Monday at 1:00 AM
    ->timezone('Africa/Kampala')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled exchange rate sync failed');
    });

// Schedule pending payment sync to run every 5 minutes
// This ensures orders are updated even if the Pesapal callback is missed
// Only checks payments that are at least 2 minutes old (to avoid checking too early)
Schedule::command('payments:sync-pending --minutes=2 --max-age=1440')
    ->everyFiveMinutes()
    ->timezone('Africa/Kampala')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled pending payment sync failed');
    });
