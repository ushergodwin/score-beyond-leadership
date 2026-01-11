<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'pesapal' => [
        'base_url' => env('PESAPAL_BASE_URL', 'https://cybqa.pesapal.com/pesapalv3'),
        'consumer_key' => env('PESAPAL_CONSUMER_KEY'),
        'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),
        'ipn_id' => env('PESAPAL_IPN_ID'),
        'callback_url' => env('PESAPAL_CALLBACK_URL'),
        'ipn_url' => env('PESAPAL_IPN_URL'),
    ],

    'exchange_rate' => [
        'api_key' => env('EXCHANGE_RATE_API_KEY'),
        'base_url' => env('EXCHANGE_RATE_BASE_URL', 'https://api.exchangerate.host'),
        'base_currency' => env('EXCHANGE_RATE_BASE_CURRENCY', 'USD'),
        'target_currencies' => array_filter(
            array_map('trim', explode(',', env('EXCHANGE_RATE_TARGETS', 'UGX,EUR')))
        ),
    ],

    'checkout' => [
        'inline_account_creation' => env('CHECKOUT_INLINE_ACCOUNT_CREATION', true),
    ],

];
