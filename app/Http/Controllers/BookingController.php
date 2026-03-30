<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\EventType;
use App\Models\Hall;
use App\Models\Service;
use App\Models\WeddingPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        return view('orders.index', [
            'bookings' => Booking::with(['client', 'hall', 'eventType', 'package', 'services.service'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('orders.create', $this->formData(new Booking([
            'status' => 'Yangi',
            'event_date' => now()->toDateString(),
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $booking = Booking::create([
                ...$data,
                'booking_number' => $this->makeBookingNumber(),
            ]);

            $this->syncServicesAndMoney($booking, $data['services'] ?? []);
        });

        return redirect()->route('bookings.index')->with('success', 'Bron muvaffaqiyatli yaratildi.');
    }

    public function show(Booking $booking): View
    {
        return view('orders.show', [
            'booking' => $booking->load([
                'client',
                'hall',
                'eventType',
                'package',
                'services.service',
                'payments',
                'kitchenCosts',
                'eventCosts',
                'fixedCosts',
            ]),
        ]);
    }

    public function edit(Booking $booking): View
    {
        return view('orders.edit', $this->formData($booking->load('services')));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $this->validateData($request, $booking);

        DB::transaction(function () use ($booking, $data) {
            $booking->update($data);
            $this->syncServicesAndMoney($booking, $data['services'] ?? []);
        });

        return redirect()->route('bookings.index')->with('success', 'Bron muvaffaqiyatli yangilandi.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Bron muvaffaqiyatli o\'chirildi.');
    }

    private function formData(Booking $booking): array
    {
        return [
            'booking' => $booking,
            'clients' => Client::orderBy('full_name')->get(),
            'eventTypes' => EventType::orderBy('name')->get(),
            'halls' => Hall::orderBy('name')->get(),
            'packages' => WeddingPackage::orderBy('name')->get(),
            'services' => Service::where('status', 'Faol')->orderBy('name')->get(),
            'statuses' => $this->statuses(),
        ];
    }

    private function validateData(Request $request, ?Booking $booking = null): array
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'hall_id' => ['required', 'exists:halls,id'],
            'event_type_id' => ['required', 'exists:event_types,id'],
            'package_id' => ['nullable', 'exists:wedding_packages,id'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'advance_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', array_keys($this->statuses()))],
            'notes' => ['nullable', 'string', 'max:2000'],
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->ensureNoHallConflict($data, $booking);

        return $data;
    }

    private function ensureNoHallConflict(array $data, ?Booking $booking = null): void
    {
        $query = Booking::where('hall_id', $data['hall_id'])
            ->whereDate('event_date', $data['event_date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($booking) {
            $query->whereKeyNot($booking->id);
        }

        if ($query->exists()) {
            abort(422, 'Tanlangan zal ushbu vaqt oralig\'ida band.');
        }
    }

    private function syncServicesAndMoney(Booking $booking, array $services): void
    {
        $booking->services()->delete();

        $packageTotal = (float) $booking->guest_count * (float) $booking->price_per_person;
        $servicesTotal = 0;

        foreach ($services as $serviceRow) {
            $service = Service::findOrFail($serviceRow['service_id']);
            $quantity = (int) $serviceRow['quantity'];
            $price = (float) $service->price;
            $total = $price * $quantity;

            $booking->services()->create([
                'service_id' => $service->id,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
            ]);

            $servicesTotal += $total;
        }

        $totalAmount = $packageTotal + $servicesTotal;
        $advanceAmount = (float) ($booking->advance_amount ?? 0);
        $paidAmount = max((float) $booking->payments()->sum('amount'), $advanceAmount);
        $remainingAmount = max($totalAmount - $paidAmount, 0);

        $booking->update([
            'total_amount' => $totalAmount,
            'advance_amount' => $advanceAmount,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
        ]);
    }

    private function makeBookingNumber(): string
    {
        return 'BRN-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
    }

    private function statuses(): array
    {
        return [
            'Yangi' => 'Yangi',
            'Tasdiqlangan' => 'Tasdiqlangan',
            'Tayyorlanmoqda' => 'Tayyorlanmoqda',
            'Otkazildi' => 'Otkazildi',
            'Bekor qilindi' => 'Bekor qilindi',
        ];
    }
}
