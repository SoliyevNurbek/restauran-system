<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hall;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $period = collect(CarbonPeriod::create($monthStart, '1 day', $monthEnd))->map(fn (Carbon $day) => $day->copy());
        $monthlyBookings = Booking::query()
            ->with(['hall', 'client', 'eventType'])
            ->whereBetween('event_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Booking $booking) => $booking->event_date?->toDateString());

        return view('calendar.index', [
            'halls' => Hall::orderBy('name')->get(),
            'bookings' => Booking::with(['hall', 'client', 'eventType'])->upcoming()->take(30)->get(),
            'calendarDays' => $period,
            'monthlyBookings' => $monthlyBookings,
            'monthLabel' => $monthStart->translatedFormat('F Y'),
        ]);
    }
}
