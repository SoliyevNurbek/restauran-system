<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $features = preg_split('/\r\n|\r|\n/', (string) $this->input('features_text', ''));
        $features = collect($features)
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();

        $this->merge([
            'slug' => \Illuminate\Support\Str::slug((string) $this->input('slug', $this->input('name'))),
            'features' => $features,
        ]);
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->getKey();

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', Rule::unique('subscription_plans', 'slug')->ignore($planId)],
            'description' => ['nullable', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3660'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'manual'])],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:1', 'max:999'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'features_text' => ['nullable', 'string'],
        ];
    }
}
