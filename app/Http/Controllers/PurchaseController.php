<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        return view('purchases.index', [
            'purchases' => Purchase::with(['supplier', 'items.product'])->latest('purchase_date')->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('full_name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePurchase($request);

        DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
            ]);

            $this->syncItems($purchase, $data['items']);
        });

        return redirect()->route('purchases.index')->with('success', 'Kirim muvaffaqiyatli saqlandi.');
    }

    public function show(Purchase $purchase): RedirectResponse
    {
        return redirect()->route('purchases.edit', $purchase);
    }

    public function edit(Purchase $purchase): View
    {
        return view('purchases.edit', [
            'purchase' => $purchase->load('items'),
            'suppliers' => Supplier::orderBy('full_name')->get(),
            'products' => Product::where('is_active', true)
                ->orWhereIn('id', $purchase->items->pluck('product_id'))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $this->validatePurchase($request);

        DB::transaction(function () use ($purchase, $data) {
            $this->rollbackStock($purchase);

            $purchase->update([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
            ]);

            $purchase->items()->delete();
            $this->syncItems($purchase, $data['items']);
        });

        return redirect()->route('purchases.index')->with('success', 'Kirim yangilandi.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        DB::transaction(function () use ($purchase) {
            $this->rollbackStock($purchase);
            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('success', 'Kirim o\'chirildi.');
    }

    private function validatePurchase(Request $request): array
    {
        $tenantId = TenantContext::id();

        return $request->validate([
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->where('venue_connection_id', $tenantId)],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('venue_connection_id', $tenantId)],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function syncItems(Purchase $purchase, array $items): void
    {
        $total = 0;

        foreach ($items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            $quantity = round((float) $itemData['quantity'], 3);
            $unitPrice = round((float) $itemData['unit_price'], 2);
            $lineTotal = round($quantity * $unitPrice, 2);

            $purchase->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ]);

            $product->increment('current_stock', $quantity);
            $product->update(['last_purchase_price' => $unitPrice]);

            $total += $lineTotal;
        }

        $purchase->update(['total_amount' => $total]);
    }

    private function rollbackStock(Purchase $purchase): void
    {
        $purchase->loadMissing('items.product');

        foreach ($purchase->items as $item) {
            if ($item->product) {
                $newStock = max(0, (float) $item->product->current_stock - (float) $item->quantity);
                $item->product->update(['current_stock' => $newStock]);
            }
        }
    }
}
