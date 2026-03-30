<div class="grid gap-4 md:grid-cols-3">
    <div>
        <label class="mb-1 block text-sm font-medium">Mijoz</label>
        <select name="customer_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tashrif buyuruvchi</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $order?->customer_id) == $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Stol</label>
        <select name="dining_table_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Biriktirilmagan</option>
            @foreach($tables as $table)
                <option value="{{ $table->id }}" @selected(old('dining_table_id', $order?->dining_table_id) == $table->id)>Stol {{ $table->table_number }} ({{ $table->status }})</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Holat</label>
        <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $order?->status ?? 'pending') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4">
    <label class="mb-1 block text-sm font-medium">Izoh</label>
    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $order?->notes) }}</textarea>
</div>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
    <table class="min-w-full text-sm" id="itemsTable">
        <thead class="bg-slate-50 dark:bg-slate-800/70">
        <tr>
            <th class="px-3 py-2 text-left">Taom</th>
            <th class="px-3 py-2 text-left">Miqdor</th>
            <th class="px-3 py-2 text-left">Amal</th>
        </tr>
        </thead>
        <tbody>
        @php
            $oldItems = old('items', $order?->items?->map(fn($item) => ['menu_item_id' => $item->menu_item_id, 'quantity' => $item->quantity])->toArray() ?? [['menu_item_id' => '', 'quantity' => 1]]);
        @endphp
        @foreach($oldItems as $index => $item)
            <tr>
                <td class="px-3 py-2">
                    <select name="items[{{ $index }}][menu_item_id]" required class="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Taomni tanlang</option>
                        @foreach($menuItems as $menu)
                            <option value="{{ $menu->id }}" @selected(($item['menu_item_id'] ?? null) == $menu->id)>{{ $menu->name }} (${{ number_format($menu->price,2) }})</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="number" min="1" max="99" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required class="w-24 rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                </td>
                <td class="px-3 py-2">
                    <button type="button" class="remove-row rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600">Olib tashlash</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3">
    <button type="button" id="addRowBtn" class="rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700">+ Taom qo'shish</button>
</div>

@error('items')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror

<script>
    (() => {
        const tableBody = document.querySelector('#itemsTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');
        if (!tableBody || !addRowBtn) return;

        const options = `
            <option value="">Taomni tanlang</option>
            @foreach($menuItems as $menu)
                <option value="{{ $menu->id }}">{{ $menu->name }} (${{ number_format($menu->price,2) }})</option>
            @endforeach
        `;

        const bindRemove = () => {
            tableBody.querySelectorAll('.remove-row').forEach((btn) => {
                btn.onclick = () => {
                    if (tableBody.querySelectorAll('tr').length === 1) return;
                    btn.closest('tr').remove();
                    reindex();
                };
            });
        };

        const reindex = () => {
            tableBody.querySelectorAll('tr').forEach((row, index) => {
                row.querySelector('select').setAttribute('name', `items[${index}][menu_item_id]`);
                row.querySelector('input').setAttribute('name', `items[${index}][quantity]`);
            });
        };

        addRowBtn.addEventListener('click', () => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-3 py-2"><select name="items[][menu_item_id]" required class="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">${options}</select></td>
                <td class="px-3 py-2"><input type="number" min="1" max="99" name="items[][quantity]" value="1" required class="w-24 rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800"></td>
                <td class="px-3 py-2"><button type="button" class="remove-row rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600">Olib tashlash</button></td>
            `;
            tableBody.appendChild(row);
            reindex();
            bindRemove();
        });

        bindRemove();
    })();
</script>
