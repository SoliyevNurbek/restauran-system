<?php

return [
    'trial_days' => (int) env('BILLING_TRIAL_DAYS', 7),
    'currency' => env('BILLING_CURRENCY', 'UZS'),
    'testing' => [
        'enabled' => (bool) env('BILLING_TESTING_ENABLED', true),
    ],
    'click' => [
        'checkout_url' => env('CLICK_CHECKOUT_URL', 'https://my.click.uz/services/pay'),
        'service_id' => env('CLICK_SERVICE_ID'),
        'merchant_id' => env('CLICK_MERCHANT_ID'),
        'secret_key' => env('CLICK_SECRET_KEY'),
        'merchant_user_id' => env('CLICK_MERCHANT_USER_ID'),
    ],
    'payme' => [
        'checkout_url' => env('PAYME_CHECKOUT_URL', 'https://checkout.paycom.uz'),
        'merchant_id' => env('PAYME_MERCHANT_ID'),
        'secret_key' => env('PAYME_SECRET_KEY'),
        'account_key' => env('PAYME_ACCOUNT_KEY', 'payment_id'),
    ],
];
