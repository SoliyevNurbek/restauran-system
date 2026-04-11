<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingUsageItem;
use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingUsageItemController extends Controller
{
    public function index(): View
    {
        return view('booking-usage-items.index', [
            'items' => BookingUsageItem::with(['booking.client', 'product'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('booking-usage-items.create', $this->formData(new BookingUsageItem()));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $quantity = (float) $data['quantity'];

            $this->ensureStock($product, $quantity);

            BookingUsageItem::create([
                ...$data,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'unit' => $product->unit,
            ]);

            $product->update([
                'current_stock' => max(0, (float) $product->current_stock - $quantity),
            ]);
        });

        return redirect()->route('booking-usage-items.index')->with('success', 'Toy uchun ishlatilgan mahsulot saqlandi.');
    }

    public function show(BookingUsageItem $bookingUsageItem): RedirectResponse
    {
        return redirect()->route('booking-usage-items.edit', $bookingUsageItem);
    }

    public function edit(BookingUsageItem $bookingUsageItem): View
    {
        return view('booking-usage-items.edit', $this->formData($bookingUsageItem->load('product')));
    }

    public function update(Request $request, BookingUsageItem $bookingUsageItem): RedirectResponse
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data, $bookingUsageItem) {
            $oldProduct = $bookingUsageItem->product()->lockForUpdate()->first();
            if ($oldProduct) {
                $oldProduct->update([
                    'current_stock' => (float) $oldProduct->current_stock + (float) $bookingUsageItem->quantity,
                ]);
            }

            $product = Product::findOrFail($data['product_id']);
            $quantity = (float) $data['quantity'];
            $this->ensureStock($product, $quantity);

            $bookingUsageItem->update([
                ...$data,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'unit' => $product->unit,
            ]);

            $product->update([
                'current_stock' => max(0, (float) $product->current_stock - $quantity),
            ]);
        });

        return redirect()->route('booking-usage-items.index')->with('success', 'Toy mahsuloti yangilandi.');
    }

    public function destroy(BookingUsageItem $bookingUsageItem): RedirectResponse
    {
        DB::transaction(function () use ($bookingUsageItem) {
            $product = $bookingUsageItem->product;

            if ($product) {
                $product->update([
                    'current_stock' => (float) $product->current_stock + (float) $bookingUsageItem->quantity,
                ]);
            }

            $bookingUsageItem->delete();
        });

        return redirect()->route('booking-usage-items.index')->with('success', 'Toy mahsuloti o\'chirildi.');
    }

    private function formData(BookingUsageItem $bookingUsageItem): array
    {
        return [
            'bookingUsageItem' => $bookingUsageItem,
            'bookings' => Booking::with(['client', 'eventType'])->latest('event_date')->get(),
            'products' => Product::where('is_active', true)->orderBy('sku')->get(),
        ];
    }

    private function validateData(Request $request): array
    {
        $tenantId = TenantContext::id();

        return $request->validate([
            'booking_id' => ['required', Rule::exists('bookings', 'id')->where('venue_connection_id', $tenantId)],
            'product_id' => ['required', Rule::exists('products', 'id')->where('venue_connection_id', $tenantId)],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function ensureStock(Product $product, float $quantity): void
    {
        if ((float) $product->current_stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Mahsulot qoldig\'i yetarli emas. Mavjud qoldiq: '.number_format((float) $product->current_stock, 3).' '.$product->unit,
            ]);
        }
    }
}
