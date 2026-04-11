<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'status' => ['required', 'in:paid,pending,failed,refunded,canceled,rejected,payment_details_sent,awaiting_proof,under_review'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'transaction_reference' => ['nullable', 'string', 'max:120'],
            'invoice_number' => ['nullable', 'string', 'max:80'],
            'paid_at' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
