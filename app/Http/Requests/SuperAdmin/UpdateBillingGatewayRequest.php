<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'click_service_id' => ['nullable', 'string', 'max:120'],
            'click_merchant_id' => ['nullable', 'string', 'max:120'],
            'click_secret_key' => ['nullable', 'string', 'max:255'],
            'click_merchant_user_id' => ['nullable', 'string', 'max:120'],
            'click_checkout_url' => ['nullable', 'url', 'max:255'],
            'payme_merchant_id' => ['nullable', 'string', 'max:120'],
            'payme_secret_key' => ['nullable', 'string', 'max:255'],
            'payme_checkout_url' => ['nullable', 'url', 'max:255'],
            'payme_account_key' => ['nullable', 'string', 'max:64'],
        ];
    }
}
