<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hall;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('calendar.index', [
            'halls' => Hall::orderBy('name')->get(),
            'bookings' => Booking::with(['hall', 'client', 'eventType'])->upcoming()->take(30)->get(),
        ]);
    }
}
