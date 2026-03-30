@php($product = $product ?? null)

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Mahsulot nomi</label>
        <input name="name" value="{{ old('name', $product?->name) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Birlik</label>
        <input name="unit" value="{{ old('unit', $product?->unit ?? 'kg') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">SKU / Kod</label>
        <input name="sku" value="{{ old('sku', $product?->sku) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Minimal qoldiq</label>
        <input name="minimum_stock" type="number" min="0" step="0.001" value="{{ old('minimum_stock', $product?->minimum_stock ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Joriy qoldiq</label>
        <input name="current_stock" type="number" min="0" step="0.001" value="{{ old('current_stock', $product?->current_stock ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Oxirgi narx</label>
        <input name="last_purchase_price" type="number" min="0" step="0.01" value="{{ old('last_purchase_price', $product?->last_purchase_price ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Holati</label>
        <select name="is_active" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="1" @selected((string) old('is_active', $product?->is_active ?? 1) === '1')>Faol</option>
            <option value="0" @selected((string) old('is_active', $product?->is_active ?? 1) === '0')>Faol emas</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Izoh</label>
        <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $product?->notes) }}</textarea>
    </div>
</div>
