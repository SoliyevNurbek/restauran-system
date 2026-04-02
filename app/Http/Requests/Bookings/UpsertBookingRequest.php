<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $services = collect($this->input('services', []))
            ->filter(fn ($row) => is_array($row) && filled($row['service_id'] ?? null))
            ->map(function (array $row) {
                return [
                    'service_id' => isset($row['service_id']) ? (int) $row['service_id'] : null,
                    'quantity' => isset($row['quantity']) ? (int) $row['quantity'] : 1,
                ];
            })
            ->values()
            ->all();

        $this->merge([
            'notes' => $this->normalizeNullableString('notes'),
            'payment_method' => trim((string) $this->input('payment_method', '')),
            'currency' => trim((string) $this->input('currency', '')),
            'services' => $services,
        ]);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'event_type_id' => ['required', 'integer', 'exists:event_types,id'],
            'package_id' => ['nullable', 'integer', 'exists:wedding_packages,id'],
            'package_gallery_image_id' => ['nullable', 'integer', 'exists:wedding_package_images,id'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'price_per_person' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'currency' => ['required', 'in:UZS,USD'],
            'advance_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'payment_method' => ['required', 'in:Naqd,Karta,Bank o\'tkazma,Click,Payme,Boshqa'],
            'status' => ['required', 'in:Yangi,Tasdiqlangan,Tayyorlanmoqda,Otkazildi,Bekor qilindi'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'services' => ['nullable', 'array', 'max:50'],
            'services.*.service_id' => ['required', 'integer', 'exists:services,id', 'distinct'],
            'services.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $guestCount = (int) $this->input('guest_count', 0);
            $pricePerPerson = (float) $this->input('price_per_person', 0);
            $expectedBase = $guestCount * $pricePerPerson;
            $advanceAmount = (float) $this->input('advance_amount', 0);

            if ($expectedBase < 0) {
                $validator->errors()->add('guest_count', 'Noto\'g\'ri bron qiymatlari yuborildi.');
            }

            if ($advanceAmount > $expectedBase + 999999999.99) {
                $validator->errors()->add('advance_amount', 'Boshlang\'ich to\'lov qiymati noto\'g\'ri.');
            }
        });
    }

    private function normalizeNullableString(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value !== '' ? $value : null;
    }
}
