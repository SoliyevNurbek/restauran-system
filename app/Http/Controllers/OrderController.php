<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', [
            'orders' => Order::with(['customer', 'diningTable', 'items'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'tables' => DiningTable::orderBy('table_number')->get(),
            'menuItems' => MenuItem::where('status', 'available')->orderBy('name')->get(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $order = Order::create([
                'order_number' => $this->makeOrderNumber(),
                'customer_id' => $data['customer_id'] ?? null,
                'dining_table_id' => $data['dining_table_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            $this->syncItemsAndTotals($order, $data['items']);
            $this->syncTableStatus($order);
        });

        return redirect()->route('orders.index')->with('success', 'Buyurtma muvaffaqiyatli yaratildi.');
    }

    public function show(Order $order): View
    {
        return view('orders.show', [
            'order' => $order->load(['items.menuItem', 'customer', 'diningTable']),
        ]);
    }

    public function edit(Order $order): View
    {
        return view('orders.edit', [
            'order' => $order->load('items'),
            'customers' => Customer::orderBy('name')->get(),
            'tables' => DiningTable::orderBy('table_number')->get(),
            'menuItems' => MenuItem::where('status', 'available')->orWhereIn('id', $order->items->pluck('menu_item_id'))->orderBy('name')->get(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($order, $data) {
            $previousTableId = $order->dining_table_id;

            $order->update([
                'customer_id' => $data['customer_id'] ?? null,
                'dining_table_id' => $data['dining_table_id'] ?? null,
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'paid_at' => $data['status'] === 'paid' ? now() : null,
            ]);

            $this->syncItemsAndTotals($order, $data['items']);
            $this->syncTableStatus($order);

            if ($previousTableId && $previousTableId !== $order->dining_table_id) {
                DiningTable::whereKey($previousTableId)->update(['status' => 'free']);
            }
        });

        return redirect()->route('orders.index')->with('success', 'Buyurtma muvaffaqiyatli yangilandi.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order) {
            $tableId = $order->dining_table_id;
            $order->delete();

            if ($tableId) {
                $table = DiningTable::find($tableId);
                if ($table) {
                    $table->update(['status' => 'free']);
                }
            }
        });

        return redirect()->route('orders.index')->with('success', 'Buyurtma muvaffaqiyatli o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'status' => ['required', 'in:pending,preparing,served,paid'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);
    }

    private function syncItemsAndTotals(Order $order, array $items): void
    {
        $order->items()->delete();

        $subtotal = 0;

        foreach ($items as $itemData) {
            $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
            $quantity = (int) $itemData['quantity'];
            $unitPrice = (float) $menuItem->price;
            $lineTotal = $unitPrice * $quantity;

            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ]);

            $subtotal += $lineTotal;
        }

        $tax = round($subtotal * 0.1, 2);
        $total = $subtotal + $tax;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    private function syncTableStatus(Order $order): void
    {
        if (! $order->dining_table_id) {
            return;
        }

        $table = DiningTable::find($order->dining_table_id);
        if (! $table) {
            return;
        }

        $table->update([
            'status' => $order->status === 'paid' ? 'free' : 'occupied',
        ]);
    }

    private function makeOrderNumber(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
    }

    private function statuses(): array
    {
        return [
            'pending' => 'Kutilmoqda',
            'preparing' => 'Tayyorlanmoqda',
            'served' => 'Yetkazildi',
            'paid' => 'To\'landi',
        ];
    }
}
