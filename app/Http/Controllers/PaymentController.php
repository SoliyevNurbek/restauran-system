<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $method = (string) $request->query('method', '');

        return view('payments.index', [
            'payments' => Payment::with('booking.client', 'booking.hall')
                ->when($search !== '', function ($query) use ($search) {
                    $query->whereHas('booking', function ($booking) use ($search) {
                        $booking->where('booking_number', 'like', "%{$search}%")
                            ->orWhereHas('client', fn ($client) => $client->where('full_name', 'like', "%{$search}%"));
                    });
                })
                ->when($method !== '', fn ($query) => $query->where('payment_method', $method))
                ->latest('payment_date')
                ->paginate(12)
                ->withQueryString(),
            'filters' => compact('search', 'method'),
            'methods' => $this->methods(),
        ]);
    }

    public function create(): View
    {
        return view('payments.create', [
            'bookings' => Booking::with('client')->latest()->get(),
            'methods' => $this->methods(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $payment = Payment::create($data);
            $this->syncBookingAmounts($payment->booking);
        });

        Log::channel('audit')->info('Payment created.', [
            'user_id' => $request->user()?->getKey(),
            'booking_id' => $data['booking_id'],
            'amount' => $data['amount'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('payments.index')->with('success', 'To\'lov qo\'shildi.');
    }

    public function show(Payment $payment): RedirectResponse
    {
        return redirect()->route('payments.edit', $payment);
    }

    public function edit(Payment $payment): View
    {
        return view('payments.edit', [
            'payment' => $payment,
            'bookings' => Booking::with('client')->latest()->get(),
            'methods' => $this->methods(),
        ]);
    }

    public function update(StorePaymentRequest $request, Payment $payment): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($payment, $data) {
            $oldBooking = $payment->booking;
            $payment->update($data);
            $this->syncBookingAmounts($payment->booking);
            if ($oldBooking && $oldBooking->id !== $payment->booking_id) {
                $this->syncBookingAmounts($oldBooking->fresh());
            }
        });

        Log::channel('audit')->warning('Payment updated.', [
            'user_id' => $request->user()?->getKey(),
            'payment_id' => $payment->getKey(),
            'booking_id' => $data['booking_id'],
            'amount' => $data['amount'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('payments.index')->with('success', 'To\'lov yangilandi.');
    }

    public function destroy(\Illuminate\Http\Request $request, Payment $payment): RedirectResponse
    {
        $paymentId = $payment->getKey();
        $bookingId = $payment->booking_id;
        $amount = $payment->amount;

        DB::transaction(function () use ($payment) {
            $booking = $payment->booking;
            $payment->delete();
            if ($booking) {
                $this->syncBookingAmounts($booking->fresh());
            }
        });

        Log::channel('audit')->warning('Payment deleted.', [
            'user_id' => $request->user()?->getKey(),
            'payment_id' => $paymentId,
            'booking_id' => $bookingId,
            'amount' => $amount,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('payments.index')->with('success', 'To\'lov o\'chirildi.');
    }

    private function syncBookingAmounts(?Booking $booking): void
    {
        if (! $booking) {
            return;
        }

        $paidAmount = (float) $booking->payments()->sum('amount');
        $booking->update([
            'paid_amount' => $paidAmount,
            'remaining_amount' => max((float) $booking->total_amount - $paidAmount, 0),
            'advance_amount' => max((float) $booking->advance_amount, $paidAmount > 0 ? min($paidAmount, (float) $booking->total_amount) : 0),
        ]);
    }

    private function methods(): array
    {
        return ['Naqd', 'Karta', 'Bank o\'tkazma', 'Click', 'Payme', 'Boshqa'];
    }
}
