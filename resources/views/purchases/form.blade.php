@php
    $purchase = $purchase ?? null;
    $initialItems = old('items', $purchase?->items?->map(fn ($item) => [
        'product_id' => $item->product_id,
        'quantity' => (float) $item->quantity,
        'unit_price' => (float) $item->unit_price,
    ])->values()->all() ?? [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]);
    $productOptions = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'unit' => $product->unit,
        'price' => (float) $product->last_purchase_price,
    ])->values();
@endphp

<div x-data="purchaseForm(@js($initialItems), @js($productOptions))" class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-medium">Ta'minotchi</label>
            <select name="supplier_id" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Tanlang</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $purchase?->supplier_id) === (string) $supplier->id)>{{ $supplier->full_name }} @if($supplier->company_name) - {{ $supplier->company_name }} @endif</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Sana</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($purchase?->purchase_date)->toDateString() ?? now()->toDateString()) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
        <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-800/60 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Kirim tarkibi</h3>
                <p class="text-xs text-slate-500">Mahsulot, miqdor va narxni bir nechta qatorda kiriting</p>
            </div>
            <button type="button" @click="addRow()" class="rounded-2xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Qator qo'shish</button>
        </div>

        <div class="space-y-3 p-4 lg:hidden">
            <template x-for="(row, index) in rows" :key="index">
                <div class="rounded-2xl border border-slate-200/80 p-4 dark:border-slate-800">
                    <div class="grid gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500">Mahsulot</label>
                            <select x-model="row.product_id" :name="`items[${index}][product_id]`" @change="applyDefaultPrice(row)" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                                <option value="">Mahsulot tanlang</option>
                                <template x-for="product in products" :key="product.id">
                                    <option :value="product.id" x-text="`${product.name} (${product.unit})`"></option>
                                </template>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-500">Miqdori</label>
                                <input type="number" min="0.001" step="0.001" x-model="row.quantity" :name="`items[${index}][quantity]`" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-500">Narxi</label>
                                <div class="relative">
                                    <input type="number" min="0" step="0.01" x-model="row.unit_price" :name="`items[${index}][unit_price]`" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 pr-14 dark:border-slate-700 dark:bg-slate-800">
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-medium text-slate-400">so'm</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/70">
                            <div>
                                <p class="text-[11px] font-medium uppercase tracking-[0.2em] text-slate-400">Jami</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="formatMoney((Number(row.quantity || 0) * Number(row.unit_price || 0)))"></p>
                            </div>
                            <button type="button" @click="removeRow(index)" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-900/40 dark:text-red-400">O'chirish</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="hidden lg:block">
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500 dark:text-slate-300">
                <tr>
                    <th class="px-4 py-3">Mahsulot</th>
                    <th class="px-4 py-3">Miqdori</th>
                    <th class="px-4 py-3">Narxi</th>
                    <th class="px-4 py-3">Jami</th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>
                <tbody>
                <template x-for="(row, index) in rows" :key="index">
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-4 py-3">
                            <select x-model="row.product_id" :name="`items[${index}][product_id]`" @change="applyDefaultPrice(row)" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                                <option value="">Mahsulot tanlang</option>
                                <template x-for="product in products" :key="product.id">
                                    <option :value="product.id" x-text="`${product.name} (${product.unit})`"></option>
                                </template>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" min="0.001" step="0.001" x-model="row.quantity" :name="`items[${index}][quantity]`" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                        </td>
                        <td class="px-4 py-3">
                            <div class="relative">
                                <input type="number" min="0" step="0.01" x-model="row.unit_price" :name="`items[${index}][unit_price]`" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 pr-14 dark:border-slate-700 dark:bg-slate-800">
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-medium text-slate-400">so'm</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold" x-text="formatMoney((Number(row.quantity || 0) * Number(row.unit_price || 0)))"></td>
                        <td class="px-4 py-3">
                            <button type="button" @click="removeRow(index)" class="rounded-xl border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-900/40 dark:text-red-400">O'chirish</button>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 text-right dark:border-slate-800 dark:bg-slate-800/40">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Umumiy summa</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white" x-text="formatMoney(totalAmount())"></p>
        </div>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Izoh</label>
        <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $purchase?->notes) }}</textarea>
    </div>
</div>

<script>
    function purchaseForm(initialRows, products) {
        return {
            rows: initialRows.length ? initialRows : [{product_id: '', quantity: 1, unit_price: 0}],
            products,
            addRow() {
                this.rows.push({product_id: '', quantity: 1, unit_price: 0});
            },
            removeRow(index) {
                if (this.rows.length === 1) return;
                this.rows.splice(index, 1);
            },
            applyDefaultPrice(row) {
                const product = this.products.find(item => String(item.id) === String(row.product_id));
                if (product && (!row.unit_price || Number(row.unit_price) === 0)) row.unit_price = product.price ?? 0;
            },
            totalAmount() {
                return this.rows.reduce((carry, row) => carry + (Number(row.quantity || 0) * Number(row.unit_price || 0)), 0);
            },
            formatMoney(value) {
                return `${new Intl.NumberFormat('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(value || 0)} so'm`;
            }
        }
    }
</script>
