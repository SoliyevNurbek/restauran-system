<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('suppliers.index', [
            'suppliers' => Supplier::query()
                ->withSum('purchases', 'total_amount')
                ->withSum('payments', 'amount')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validateSupplier($request));

        return redirect()->route('suppliers.index')->with('success', 'Ta\'minotchi yaratildi.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load([
            'purchases' => fn ($query) => $query->latest()->take(10),
            'payments' => fn ($query) => $query->latest()->take(10),
        ])->loadSum('purchases', 'total_amount')
            ->loadSum('payments', 'amount');

        return view('suppliers.show', [
            'supplier' => $supplier,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validateSupplier($request));

        return redirect()->route('suppliers.index')->with('success', 'Ta\'minotchi yangilandi.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists() || $supplier->payments()->exists()) {
            return back()->withErrors([
                'delete' => 'Bog\'langan kirim yoki to\'lov bor ta\'minotchini o\'chirib bo\'lmaydi.',
            ]);
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Ta\'minotchi o\'chirildi.');
    }

    public function storePayment(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $currentBalance = $supplier->loadSum('purchases', 'total_amount')
            ->loadSum('payments', 'amount')
            ->balance;

        if ((float) $data['amount'] > $currentBalance && $currentBalance > 0) {
            return back()->withErrors([
                'amount' => 'To\'lov summasi joriy balansdan katta bo\'lishi mumkin emas.',
            ])->withInput();
        }

        SupplierPayment::create([
            'supplier_id' => $supplier->id,
            ...$data,
        ]);

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Ta\'minotchiga to\'lov saqlandi.');
    }

    private function validateSupplier(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
