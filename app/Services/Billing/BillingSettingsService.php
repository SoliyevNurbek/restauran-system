<?php

namespace App\Services\Billing;

use App\Models\IntegrationSetting;

class BillingSettingsService
{
    public function click(): array
    {
        return [
            'service_id' => IntegrationSetting::valueFor('billing.click.service_id') ?: config('billing.click.service_id'),
            'merchant_id' => IntegrationSetting::valueFor('billing.click.merchant_id') ?: config('billing.click.merchant_id'),
            'secret_key' => IntegrationSetting::valueFor('billing.click.secret_key') ?: config('billing.click.secret_key'),
            'merchant_user_id' => IntegrationSetting::valueFor('billing.click.merchant_user_id') ?: config('billing.click.merchant_user_id'),
            'checkout_url' => IntegrationSetting::valueFor('billing.click.checkout_url') ?: config('billing.click.checkout_url'),
        ];
    }

    public function payme(): array
    {
        return [
            'merchant_id' => IntegrationSetting::valueFor('billing.payme.merchant_id') ?: config('billing.payme.merchant_id'),
            'secret_key' => IntegrationSetting::valueFor('billing.payme.secret_key') ?: config('billing.payme.secret_key'),
            'checkout_url' => IntegrationSetting::valueFor('billing.payme.checkout_url') ?: config('billing.payme.checkout_url'),
            'account_key' => IntegrationSetting::valueFor('billing.payme.account_key') ?: config('billing.payme.account_key', 'payment_id'),
        ];
    }
}
