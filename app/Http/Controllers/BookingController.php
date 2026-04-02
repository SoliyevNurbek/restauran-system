<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bookings\UpsertBookingRequest;
use App\Models\Booking;
use App\Models\Client;
use App\Models\EventType;
use App\Models\Hall;
use App\Models\Service;
use App\Models\WeddingPackage;
use App\Models\WeddingPackageImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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

    public function store(UpsertBookingRequest $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            $booking = Booking::create([
                ...$data,
                'booking_number' => $this->makeBookingNumber(),
            ]);

            $this->syncServicesAndMoney($booking, $data['services'] ?? []);
        });

        Log::channel('audit')->info('Booking created.', [
            'user_id' => $request->user()?->getKey(),
            'hall_id' => $data['hall_id'],
            'event_date' => $data['event_date'],
            'ip' => $request->ip(),
        ]);

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

    public function update(UpsertBookingRequest $request, Booking $booking): RedirectResponse
    {
        $data = $this->validatedData($request, $booking);
        $oldStatus = $booking->status;

        DB::transaction(function () use ($booking, $data) {
            $oldPackageImagePath = $booking->package_image_path;
            $booking->update($data);
            $this->syncServicesAndMoney($booking, $data['services'] ?? []);

            if ($oldPackageImagePath && $oldPackageImagePath !== $booking->package_image_path) {
                Storage::disk('public')->delete($oldPackageImagePath);
            }
        });

        Log::channel('audit')->warning('Booking updated.', [
            'user_id' => $request->user()?->getKey(),
            'booking_id' => $booking->getKey(),
            'old_status' => $oldStatus,
            'new_status' => $data['status'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('bookings.index')->with('success', 'Bron muvaffaqiyatli yangilandi.');
    }

    public function destroy(\Illuminate\Http\Request $request, Booking $booking): RedirectResponse
    {
        $bookingId = $booking->getKey();
        $status = $booking->status;

        if ($booking->package_image_path) {
            Storage::disk('public')->delete($booking->package_image_path);
        }

        $booking->delete();

        Log::channel('audit')->warning('Booking deleted.', [
            'user_id' => $request->user()?->getKey(),
            'booking_id' => $bookingId,
            'status' => $status,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('bookings.index')->with('success', 'Bron muvaffaqiyatli o\'chirildi.');
    }

    private function formData(Booking $booking): array
    {
        return [
            'booking' => $booking,
            'clients' => Client::orderBy('full_name')->get(),
            'eventTypes' => EventType::orderBy('name')->get(),
            'halls' => Hall::orderBy('name')->get(),
            'packages' => WeddingPackage::with('images')->orderBy('name')->get(),
            'services' => Service::where('status', 'Faol')->orderBy('name')->get(),
            'statuses' => $this->statuses(),
            'paymentMethods' => $this->paymentMethods(),
            'currencies' => $this->currencies(),
        ];
    }

    private function validatedData(UpsertBookingRequest $request, ?Booking $booking = null): array
    {
        $data = $request->validated();

        $data['start_time'] = substr((string) $data['start_time'], 0, 5);
        $data['end_time'] = substr((string) $data['end_time'], 0, 5);

        validator($data, [
            'end_time' => ['required', 'after:start_time'],
        ])->validate();

        $this->ensureNoHallConflict($data, $booking);
        $this->ensurePackageImageBelongsToPackage($data);

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
            throw ValidationException::withMessages([
                'hall_id' => 'Tanlangan zal ushbu vaqt oralig\'ida band.',
            ]);
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
        if ($advanceAmount > $totalAmount) {
            throw ValidationException::withMessages([
                'advance_amount' => 'Boshlang\'ich to\'lov jami summadan oshib ketmasligi kerak.',
            ]);
        }
        $paidAmount = max((float) $booking->payments()->sum('amount'), $advanceAmount);
        $remainingAmount = max($totalAmount - $paidAmount, 0);

        $booking->update([
            'total_amount' => $totalAmount,
            'advance_amount' => $advanceAmount,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
        ]);
    }

    private function ensurePackageImageBelongsToPackage(array &$data): void
    {
        if (empty($data['package_gallery_image_id'])) {
            $data['package_gallery_image_id'] = null;
            $data['package_image_path'] = null;

            return;
        }

        if (empty($data['package_id'])) {
            throw ValidationException::withMessages([
                'package_gallery_image_id' => 'Avval to\'y paketini tanlang.',
            ]);
        }

        $image = WeddingPackageImage::where('id', $data['package_gallery_image_id'])
            ->where('wedding_package_id', $data['package_id'])
            ->first();

        if (! $image) {
            throw ValidationException::withMessages([
                'package_gallery_image_id' => 'Tanlangan rasm ushbu paketga tegishli emas.',
            ]);
        }

        $data['package_image_path'] = $this->copyPackageImageToBooking($image->image_path);
    }

    private function copyPackageImageToBooking(string $imagePath): string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($imagePath)) {
            throw ValidationException::withMessages([
                'package_gallery_image_id' => 'Tanlangan paket rasmi topilmadi.',
            ]);
        }

        $extension = pathinfo($imagePath, PATHINFO_EXTENSION) ?: 'jpg';
        $newPath = 'booking-packages/'.now()->format('Y/m').'/'.Str::uuid().'.'.$extension;
        $disk->copy($imagePath, $newPath);

        return $newPath;
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

    private function paymentMethods(): array
    {
        return ['Naqd', 'Karta', 'Bank o\'tkazma', 'Click', 'Payme', 'Boshqa'];
    }

    private function currencies(): array
    {
        return [
            'UZS' => 'So\'m',
            'USD' => 'Dollar',
        ];
    }
}
