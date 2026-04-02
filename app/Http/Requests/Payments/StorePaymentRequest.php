<?php

namespace App\Http\Requests\Payments;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'note' => $this->normalizeNullableString('note'),
        ]);
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'payment_method' => ['required', 'in:Naqd,Karta,Bank o\'tkazma,Click,Payme,Boshqa'],
            'payment_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $booking = Booking::find($this->integer('booking_id'));

            if (! $booking) {
                return;
            }

            $currentPayment = $this->route('payment');
            $existingAmount = $currentPayment instanceof Payment ? (float) $currentPayment->amount : 0.0;
            $otherPayments = max((float) $booking->payments()->sum('amount') - $existingAmount, 0);
            $attemptedAmount = (float) $this->input('amount', 0);
            $maxAllowed = max((float) $booking->total_amount - $otherPayments, 0);

            if ($attemptedAmount > $maxAllowed && $maxAllowed >= 0) {
                $validator->errors()->add('amount', 'To\'lov summasi bron jami summasidan oshib ketmasligi kerak.');
            }
        });
    }

    private function normalizeNullableString(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? $value : null;
    }
}
