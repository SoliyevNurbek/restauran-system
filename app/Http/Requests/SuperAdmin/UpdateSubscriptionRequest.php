<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'subscription_plan_id' => ['nullable', 'exists:subscription_plans,id'],
            'status' => ['required', 'in:active,trial,expired,canceled,past_due'],
            'activity_state' => ['required', 'in:healthy,attention,inactive,risk'],
            'billing_cycle' => ['required', 'in:monthly,quarterly,yearly,manual'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'manual_override' => ['nullable', 'boolean'],
            'renews_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'trial_ends_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
