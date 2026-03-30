<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CostCategory;
use App\Models\EventCost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventCostController extends Controller
{
    public function index(): View
    {
        return view('expenses.event.index', [
            'costs' => EventCost::with(['booking.client', 'category'])->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('expenses.event.create', [
            'bookings' => Booking::with('client')->latest()->get(),
            'categories' => CostCategory::where('type', 'event')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        EventCost::create($this->payload($request));

        return redirect()->route('expenses.event.index')->with('success', 'Tadbir xarajati qo\'shildi.');
    }

    public function show(EventCost $event): RedirectResponse
    {
        return redirect()->route('expenses.event.edit', $event);
    }

    public function edit(EventCost $event): View
    {
        return view('expenses.event.edit', [
            'cost' => $event,
            'bookings' => Booking::with('client')->latest()->get(),
            'categories' => CostCategory::where('type', 'event')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, EventCost $event): RedirectResponse
    {
        $event->update($this->payload($request));

        return redirect()->route('expenses.event.index')->with('success', 'Tadbir xarajati yangilandi.');
    }

    public function destroy(EventCost $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('expenses.event.index')->with('success', 'Tadbir xarajati o\'chirildi.');
    }

    private function payload(Request $request): array
    {
        $data = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'category_id' => ['nullable', 'exists:cost_categories,id'],
            'service_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'salary_cost' => ['nullable', 'numeric', 'min:0'],
            'utility_cost' => ['nullable', 'numeric', 'min:0'],
            'tax_share' => ['nullable', 'numeric', 'min:0'],
        ]);
        $data['total_price'] = (float) $data['quantity'] * (float) $data['unit_price'];
        return $data;
    }
}
