<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CostCategory;
use App\Models\KitchenCost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenCostController extends Controller
{
    public function index(): View
    {
        return view('expenses.kitchen.index', [
            'costs' => KitchenCost::with(['booking.client', 'category'])->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('expenses.kitchen.create', [
            'bookings' => Booking::with('client')->latest()->get(),
            'categories' => CostCategory::where('type', 'kitchen')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        KitchenCost::create($this->payload($request));

        return redirect()->route('expenses.kitchen.index')->with('success', 'Oshxona xarajati qo\'shildi.');
    }

    public function show(KitchenCost $kitchen): RedirectResponse
    {
        return redirect()->route('expenses.kitchen.edit', $kitchen);
    }

    public function edit(KitchenCost $kitchen): View
    {
        return view('expenses.kitchen.edit', [
            'cost' => $kitchen,
            'bookings' => Booking::with('client')->latest()->get(),
            'categories' => CostCategory::where('type', 'kitchen')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, KitchenCost $kitchen): RedirectResponse
    {
        $kitchen->update($this->payload($request));

        return redirect()->route('expenses.kitchen.index')->with('success', 'Oshxona xarajati yangilandi.');
    }

    public function destroy(KitchenCost $kitchen): RedirectResponse
    {
        $kitchen->delete();

        return redirect()->route('expenses.kitchen.index')->with('success', 'Oshxona xarajati o\'chirildi.');
    }

    private function payload(Request $request): array
    {
        $data = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'category_id' => ['nullable', 'exists:cost_categories,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'gas_cost' => ['nullable', 'numeric', 'min:0'],
            'electric_cost' => ['nullable', 'numeric', 'min:0'],
            'salary_cost' => ['nullable', 'numeric', 'min:0'],
            'tax_share' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['gas_cost'] = (float) ($data['gas_cost'] ?? 0);
        $data['electric_cost'] = (float) ($data['electric_cost'] ?? 0);
        $data['salary_cost'] = (float) ($data['salary_cost'] ?? 0);
        $data['tax_share'] = (float) ($data['tax_share'] ?? 0);

        $data['total_price'] = (float) $data['quantity'] * (float) $data['unit_price'];

        return $data;
    }
}
