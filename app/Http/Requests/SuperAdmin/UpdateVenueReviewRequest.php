<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVenueReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,under_review,approved,rejected,suspended'],
            'approval_notes' => ['nullable', 'string', 'max:2000'],
            'review_reason' => ['nullable', 'string', 'max:500'],
            'health_status' => ['nullable', 'string', 'max:50'],
            'halls_count' => ['nullable', 'integer', 'min:0'],
            'bookings_count' => ['nullable', 'integer', 'min:0'],
            'revenue_total' => ['nullable', 'numeric', 'min:0'],
            'send_telegram' => ['nullable', 'boolean'],
        ];
    }
}
