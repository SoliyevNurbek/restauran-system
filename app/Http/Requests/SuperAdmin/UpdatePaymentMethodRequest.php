<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:manual,online,gateway'],
            'is_enabled' => ['nullable', 'boolean'],
            'proof_required' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:1', 'max:999'],
            'config_placeholder' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
