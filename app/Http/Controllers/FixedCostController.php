<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FixedCost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixedCostController extends Controller
{
    public function index(): View
    {
        return view('expenses.fixed.index', [
            'costs' => FixedCost::with('booking.client')->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('expenses.fixed.create', [
            'bookings' => Booking::with('client')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FixedCost::create($this->validateData($request));

        return redirect()->route('expenses.fixed.index')->with('success', 'Doimiy xarajat qo\'shildi.');
    }

    public function show(FixedCost $fixed): RedirectResponse
    {
        return redirect()->route('expenses.fixed.edit', $fixed);
    }

    public function edit(FixedCost $fixed): View
    {
        return view('expenses.fixed.edit', [
            'cost' => $fixed,
            'bookings' => Booking::with('client')->latest()->get(),
        ]);
    }

    public function update(Request $request, FixedCost $fixed): RedirectResponse
    {
        $fixed->update($this->validateData($request));

        return redirect()->route('expenses.fixed.index')->with('success', 'Doimiy xarajat yangilandi.');
    }

    public function destroy(FixedCost $fixed): RedirectResponse
    {
        $fixed->delete();

        return redirect()->route('expenses.fixed.index')->with('success', 'Doimiy xarajat o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'name' => ['required', 'string', 'max:255'],
            'monthly_amount' => ['required', 'numeric', 'min:0'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'tax_share' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
