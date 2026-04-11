<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $balance = (string) $request->query('balance', '');

        return view('suppliers.index', [
            'suppliers' => Supplier::query()
                ->withSum('purchases', 'total_amount')
                ->withSum('payments', 'amount')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('full_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
                })
                ->when($balance === 'debt', fn ($query) => $query->whereRaw('(opening_balance + COALESCE((select sum(total_amount) from purchases where purchases.supplier_id = suppliers.id), 0) - COALESCE((select sum(amount) from supplier_payments where supplier_payments.supplier_id = suppliers.id), 0)) > 0'))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'filters' => compact('search', 'balance'),
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
