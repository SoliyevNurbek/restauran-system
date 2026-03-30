<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\CostCategory;
use App\Models\EventType;
use App\Models\Hall;
use App\Models\Payment;
use App\Models\Service;
use App\Models\WeddingPackage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $halls = Hall::where('status', 'Faol')->get();
        $packages = WeddingPackage::all();
        $services = Service::all();
        $eventTypes = EventType::all();

        if ($clients->isEmpty() || $halls->isEmpty() || $packages->isEmpty() || $services->isEmpty() || $eventTypes->isEmpty()) {
            return;
        }

        $statuses = ['Yangi', 'Tasdiqlangan', 'Tayyorlanmoqda', 'Otkazildi', 'Bekor qilindi'];
        $kitchenCategory = CostCategory::firstOrCreate(['name' => 'Oziq-ovqat', 'type' => 'kitchen']);
        $eventCategory = CostCategory::firstOrCreate(['name' => 'Xizmat ko\'rsatish', 'type' => 'event']);
        CostCategory::firstOrCreate(['name' => 'Doimiy ulush', 'type' => 'fixed']);

        for ($i = 1; $i <= 8; $i++) {
            $client = $clients[$i % $clients->count()];
            $hall = $halls[$i % $halls->count()];
            $package = $packages[$i % $packages->count()];
            $eventType = $eventTypes[$i % $eventTypes->count()];
            $guestCount = 120 + ($i * 10);
            $eventDate = Carbon::today()->addDays($i - 4);
            $status = $statuses[($i - 1) % count($statuses)];
            $serviceTotal = 0;

            $booking = Booking::updateOrCreate(
                ['booking_number' => 'BRON-20260330-0'.$i],
                [
                    'client_id' => $client->id,
                    'hall_id' => $hall->id,
                    'event_type_id' => $eventType->id,
                    'package_id' => $package->id,
                    'event_date' => $eventDate->toDateString(),
                    'start_time' => sprintf('%02d:00:00', 11 + ($i % 2)),
                    'end_time' => sprintf('%02d:00:00', 16 + ($i % 2)),
                    'guest_count' => $guestCount,
                    'price_per_person' => $package->price_per_person,
                    'total_amount' => $guestCount * $package->price_per_person,
                    'advance_amount' => 0,
                    'paid_amount' => 0,
                    'remaining_amount' => $guestCount * $package->price_per_person,
                    'status' => $status,
                    'notes' => 'Seeded demo booking',
                ]
            );

            $booking->services()->delete();
            foreach ($services->shuffle()->take(2) as $service) {
                $qty = rand(1, 2);
                $lineTotal = $service->price * $qty;
                $booking->services()->create([
                    'service_id' => $service->id,
                    'quantity' => $qty,
                    'price' => $service->price,
                    'total' => $lineTotal,
                ]);
                $serviceTotal += $lineTotal;
            }

            $bookingTotal = ($guestCount * $package->price_per_person) + $serviceTotal;
            $paid = in_array($status, ['Tasdiqlangan', 'Tayyorlanmoqda', 'Otkazildi'], true) ? round($bookingTotal * 0.55, 2) : 0;

            $booking->update([
                'total_amount' => $bookingTotal,
                'advance_amount' => $paid,
                'paid_amount' => $paid,
                'remaining_amount' => max($bookingTotal - $paid, 0),
            ]);

            Payment::where('booking_id', $booking->id)->delete();
            if ($paid > 0) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $paid,
                    'payment_method' => $i % 2 ? 'Naqd' : 'Click',
                    'payment_date' => $eventDate->copy()->subDays(10)->toDateString(),
                    'note' => 'Avans to\'lovi',
                ]);
            }

            $booking->kitchenCosts()->delete();
            $booking->eventCosts()->delete();
            $booking->fixedCosts()->delete();

            $booking->kitchenCosts()->create([
                'category_id' => $kitchenCategory->id,
                'product_name' => 'Gosht va salatlar',
                'quantity' => 1,
                'unit_price' => round($guestCount * 42000, 2),
                'total_price' => round($guestCount * 42000, 2),
                'gas_cost' => 180000,
                'electric_cost' => 220000,
                'salary_cost' => 450000,
                'tax_share' => 150000,
            ]);

            $booking->eventCosts()->create([
                'category_id' => $eventCategory->id,
                'service_name' => 'Ofitsiant va dekor',
                'quantity' => 1,
                'unit_price' => 1800000,
                'total_price' => 1800000,
                'salary_cost' => 550000,
                'utility_cost' => 240000,
                'tax_share' => 160000,
            ]);

            $booking->fixedCosts()->create([
                'name' => 'Kommunal va boshqaruv ulushi',
                'monthly_amount' => 12000000,
                'allocated_amount' => 900000,
                'tax_share' => 110000,
            ]);
        }
    }
}
