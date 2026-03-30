<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('payments.index', [
            'payments' => Payment::with('booking.client')->latest('payment_date')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('payments.create', [
            'bookings' => Booking::with('client')->latest()->get(),
            'methods' => $this->methods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $payment = Payment::create($data);
            $this->syncBookingAmounts($payment->booking);
        });

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

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($payment, $data) {
            $oldBooking = $payment->booking;
            $payment->update($data);
            $this->syncBookingAmounts($payment->booking);
            if ($oldBooking && $oldBooking->id !== $payment->booking_id) {
                $this->syncBookingAmounts($oldBooking->fresh());
            }
        });

        return redirect()->route('payments.index')->with('success', 'To\'lov yangilandi.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment) {
            $booking = $payment->booking;
            $payment->delete();
            if ($booking) {
                $this->syncBookingAmounts($booking->fresh());
            }
        });

        return redirect()->route('payments.index')->with('success', 'To\'lov o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:'.implode(',', $this->methods())],
            'payment_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
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
