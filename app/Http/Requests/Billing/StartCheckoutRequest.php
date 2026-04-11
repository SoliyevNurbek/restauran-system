<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', Rule::in(['click', 'payme', 'manual', 'test'])],
            'auto_renew' => ['nullable', 'boolean'],
        ];
    }
}
