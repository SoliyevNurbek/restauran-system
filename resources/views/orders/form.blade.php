<div class="grid gap-4 md:grid-cols-3">
    <div>
        <label class="mb-1 block text-sm font-medium">Mijoz</label>
        <select name="client_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Mijozni tanlang</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" data-phone="{{ $client->phone }}" @selected(old('client_id', $booking?->client_id) == $client->id)>{{ $client->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Telefon</label>
        <input id="clientPhonePreview" type="text" value="{{ old('client_phone', $booking?->client?->phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800" readonly>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Tadbir turi</label>
        <select name="event_type_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tanlang</option>
            @foreach($eventTypes as $eventType)
                <option value="{{ $eventType->id }}" @selected(old('event_type_id', $booking?->event_type_id) == $eventType->id)>{{ $eventType->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-3">
    <div>
        <label class="mb-1 block text-sm font-medium">Zal</label>
        <select name="hall_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tanlang</option>
            @foreach($halls as $hall)
                <option value="{{ $hall->id }}" @selected(old('hall_id', $booking?->hall_id) == $hall->id)>{{ $hall->name }} ({{ $hall->status }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Sana</label>
        <input name="event_date" type="date" value="{{ old('event_date', optional($booking?->event_date)->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Holat</label>
        <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $booking?->status ?? 'Yangi') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-4">
    <div>
        <label class="mb-1 block text-sm font-medium">Boshlanish vaqti</label>
        <input name="start_time" type="time" value="{{ old('start_time', $booking?->start_time) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Tugash vaqti</label>
        <input name="end_time" type="time" value="{{ old('end_time', $booking?->end_time) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Mehmonlar soni</label>
        <input id="guestCountField" name="guest_count" type="number" min="1" value="{{ old('guest_count', $booking?->guest_count ?? 100) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Toy paketi</label>
        <select id="packageSelect" name="package_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tanlang</option>
            @foreach($packages as $package)
                <option value="{{ $package->id }}" data-price="{{ $package->price_per_person }}" @selected(old('package_id', $booking?->package_id) == $package->id)>{{ $package->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-3">
    <div>
        <label class="mb-1 block text-sm font-medium">1 kishi narxi</label>
        <input id="pricePerPersonField" name="price_per_person" type="number" step="0.01" min="0" value="{{ old('price_per_person', $booking?->price_per_person ?? 0) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Avans</label>
        <input name="advance_amount" type="number" step="0.01" min="0" value="{{ old('advance_amount', $booking?->advance_amount ?? 0) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Umumiy summa</label>
        <input id="totalAmountPreview" type="text" value="{{ number_format((float) old('total_amount', $booking?->total_amount ?? 0), 2) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800" readonly>
    </div>
</div>

<div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
    <table class="min-w-full text-sm" id="servicesTable">
        <thead class="bg-slate-50 dark:bg-slate-800/70">
        <tr>
            <th class="px-3 py-2 text-left">Qoshimcha xizmatlar</th>
            <th class="px-3 py-2 text-left">Miqdor</th>
            <th class="px-3 py-2 text-left">Amal</th>
        </tr>
        </thead>
        <tbody>
        @php
            $oldServices = old('services', $booking?->services?->map(fn($item) => ['service_id' => $item->service_id, 'quantity' => $item->quantity])->toArray() ?? [['service_id' => '', 'quantity' => 1]]);
        @endphp
        @foreach($oldServices as $index => $serviceRow)
            <tr>
                <td class="px-3 py-2">
                    <select name="services[{{ $index }}][service_id]" class="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 service-select">
                        <option value="">Xizmatni tanlang</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" @selected(($serviceRow['service_id'] ?? null) == $service->id)>{{ $service->name }} ({{ number_format($service->price, 2) }})</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="number" min="1" name="services[{{ $index }}][quantity]" value="{{ $serviceRow['quantity'] ?? 1 }}" class="w-24 rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 service-qty">
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
    <button type="button" id="addRowBtn" class="rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700">+ Xizmat qo'shish</button>
</div>

<div class="mt-4">
    <label class="mb-1 block text-sm font-medium">Izoh</label>
    <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $booking?->notes) }}</textarea>
</div>

<script>
    (() => {
        const tableBody = document.querySelector('#servicesTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');
        const packageSelect = document.getElementById('packageSelect');
        const guestCountField = document.getElementById('guestCountField');
        const pricePerPersonField = document.getElementById('pricePerPersonField');
        const totalAmountPreview = document.getElementById('totalAmountPreview');
        const clientSelect = document.querySelector('select[name="client_id"]');
        const clientPhonePreview = document.getElementById('clientPhonePreview');
        if (!tableBody || !addRowBtn) return;

        const options = `
            <option value="">Xizmatni tanlang</option>
            @foreach($services as $service)
                <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }} ({{ number_format($service->price, 2) }})</option>
            @endforeach
        `;

        const updatePhone = () => {
            const option = clientSelect?.selectedOptions?.[0];
            clientPhonePreview.value = option?.dataset?.phone || '';
        };

        const reindex = () => {
            tableBody.querySelectorAll('tr').forEach((row, index) => {
                row.querySelector('select').setAttribute('name', `services[${index}][service_id]`);
                row.querySelector('input').setAttribute('name', `services[${index}][quantity]`);
            });
        };

        const bindRemove = () => {
            tableBody.querySelectorAll('.remove-row').forEach((btn) => {
                btn.onclick = () => {
                    if (tableBody.querySelectorAll('tr').length === 1) {
                        rowReset(btn.closest('tr'));
                        calculateTotal();
                        return;
                    }
                    btn.closest('tr').remove();
                    reindex();
                    calculateTotal();
                };
            });
        };

        const rowReset = (row) => {
            row.querySelector('select').value = '';
            row.querySelector('input').value = 1;
        };

        const calculateTotal = () => {
            const guestCount = Number(guestCountField.value || 0);
            const packagePrice = Number(pricePerPersonField.value || 0);
            let total = guestCount * packagePrice;

            tableBody.querySelectorAll('tr').forEach((row) => {
                const select = row.querySelector('select');
                const qty = Number(row.querySelector('input').value || 0);
                const price = Number(select.selectedOptions[0]?.dataset?.price || 0);
                total += qty * price;
            });

            totalAmountPreview.value = total.toFixed(2);
        };

        packageSelect?.addEventListener('change', () => {
            const option = packageSelect.selectedOptions[0];
            if (option?.dataset?.price) {
                pricePerPersonField.value = option.dataset.price;
            }
            calculateTotal();
        });

        guestCountField?.addEventListener('input', calculateTotal);
        pricePerPersonField?.addEventListener('input', calculateTotal);
        clientSelect?.addEventListener('change', updatePhone);

        addRowBtn.addEventListener('click', () => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-3 py-2"><select name="services[][service_id]" class="w-full rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 service-select">${options}</select></td>
                <td class="px-3 py-2"><input type="number" min="1" name="services[][quantity]" value="1" class="w-24 rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800 service-qty"></td>
                <td class="px-3 py-2"><button type="button" class="remove-row rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600">Olib tashlash</button></td>
            `;
            tableBody.appendChild(row);
            reindex();
            bindRemove();
            calculateTotal();
        });

        tableBody.addEventListener('input', calculateTotal);
        tableBody.addEventListener('change', calculateTotal);

        updatePhone();
        bindRemove();
        calculateTotal();
    })();
</script>

