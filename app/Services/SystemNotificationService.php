<?php

namespace App\Services;

use App\Mail\SystemAlertsDigestMail;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SystemNotificationService
{
    public function sendDigest(int $days = 3): array
    {
        $setting = Setting::current();
        $email = trim((string) ($setting->notification_email ?? ''));

        if ($email === '') {
            return [
                'sent' => false,
                'reason' => 'Notification email sozlanmagan.',
                'bookings_count' => 0,
                'low_stock_count' => 0,
            ];
        }

        $upcomingBookings = $this->upcomingBookings($days);
        $lowStockProducts = $this->lowStockProducts();

        if ($upcomingBookings->isEmpty() && $lowStockProducts->isEmpty()) {
            return [
                'sent' => false,
                'reason' => 'Yuborish uchun eslatmalar topilmadi.',
                'bookings_count' => 0,
                'low_stock_count' => 0,
            ];
        }

        Mail::to($email)->send(new SystemAlertsDigestMail(
            restaurantName: $setting->restaurant_name,
            upcomingBookings: $upcomingBookings,
            lowStockProducts: $lowStockProducts,
            days: $days,
        ));

        return [
            'sent' => true,
            'reason' => null,
            'email' => $email,
            'bookings_count' => $upcomingBookings->count(),
            'low_stock_count' => $lowStockProducts->count(),
        ];
    }

    private function upcomingBookings(int $days): Collection
    {
        $start = now()->startOfDay();
        $end = now()->copy()->addDays(max($days, 1))->endOfDay();

        return Booking::with(['hall', 'client', 'eventType'])
            ->where('status', '!=', 'Bekor qilindi')
            ->whereBetween('event_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get()
            ->map(function (Booking $booking) {
                $daysLeft = now()->startOfDay()->diffInDays(Carbon::parse($booking->event_date)->startOfDay(), false);

                return (object) [
                    'booking_number' => $booking->booking_number,
                    'event_date' => $booking->event_date,
                    'start_time' => $booking->start_time ? substr((string) $booking->start_time, 0, 5) : null,
                    'end_time' => $booking->end_time ? substr((string) $booking->end_time, 0, 5) : null,
                    'hall_name' => $booking->hall?->name ?? '-',
                    'client_name' => $booking->client?->full_name ?? 'Mijoz ko`rsatilmagan',
                    'event_type' => $booking->eventType?->name ?? '-',
                    'status' => $booking->status,
                    'days_left' => $daysLeft,
                ];
            });
    }

    private function lowStockProducts(): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderByRaw('(minimum_stock - current_stock) DESC')
            ->orderBy('current_stock')
            ->get()
            ->map(function (Product $product) {
                $restockAmount = max((float) $product->minimum_stock - (float) $product->current_stock, 0);

                return (object) [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'unit' => $product->unit,
                    'current_stock' => number_format((float) $product->current_stock, 3, '.', ' '),
                    'minimum_stock' => number_format((float) $product->minimum_stock, 3, '.', ' '),
                    'restock_amount' => number_format($restockAmount, 3, '.', ' '),
                ];
            });
    }
}
