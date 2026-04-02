<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Toy / Booking</label>
        <select name="booking_id" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tanlang</option>
            @foreach($bookings as $booking)
                <option value="{{ $booking->id }}" @selected((string) old('booking_id', $bookingUsageItem?->booking_id) === (string) $booking->id)>{{ $booking->booking_number ?? 'BRN' }} - {{ optional($booking->client)->full_name ?? 'Mijoz' }} - {{ optional($booking->event_date)->format('d.m.Y') }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Master product</label>
        <select id="usage-product-select" name="product_id" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tanlang</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}" data-unit="{{ $product->unit }}" @selected((string) old('product_id', $bookingUsageItem?->product_id) === (string) $product->id)>{{ $product->sku }} - {{ $product->name }} ({{ $product->unit }})</option>
            @endforeach
        </select>
        <p id="usage-stock-note" class="mt-1 text-xs text-slate-500">Mahsulot tanlang, mavjud qoldiq ko'rinadi.</p>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Ishlatilgan miqdor</label>
        <input name="quantity" type="number" min="0.001" step="0.001" value="{{ old('quantity', $bookingUsageItem?->quantity ?? 1) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Izoh</label>
        <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $bookingUsageItem?->notes) }}</textarea>
    </div>
</div>

<script>
    (() => {
        const select = document.getElementById('usage-product-select');
        const note = document.getElementById('usage-stock-note');
        if (!select || !note) return;

        const updateStockNote = () => {
            const option = select.options[select.selectedIndex];
            if (!option || !option.dataset.stock) {
                note.textContent = "Mahsulot tanlang, mavjud qoldiq ko'rinadi.";
                return;
            }

            note.textContent = `Mavjud qoldiq: ${Number(option.dataset.stock).toFixed(3)} ${option.dataset.unit}`;
        };

        select.addEventListener('change', updateStockNote);
        updateStockNote();
    })();
</script>
