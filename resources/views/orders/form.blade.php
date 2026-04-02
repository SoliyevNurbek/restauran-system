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
        <input name="start_time" type="time" value="{{ old('start_time', $booking?->start_time ? substr((string) $booking->start_time, 0, 5) : null) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Tugash vaqti</label>
        <input name="end_time" type="time" value="{{ old('end_time', $booking?->end_time ? substr((string) $booking->end_time, 0, 5) : null) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
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

<div class="mt-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Paket rasmlari</h3>
            <p class="mt-1 text-xs text-slate-500">Paket tanlang, keyin klient uchun mos rasmni belgilang. Rasmlar to'liq ko'rinishda chiqadi.</p>
        </div>
    </div>

    <div id="packageGalleryEmpty" class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/40">Tanlangan paket uchun rasmlar shu yerda chiqadi.</div>
    <div id="packageGallery" class="mt-4 hidden grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
    @error('package_gallery_image_id')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

<div id="packageImageModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/75 p-4">
    <button type="button" id="packageImageModalClose" class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white backdrop-blur hover:bg-white/20">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 6 6 18"></path>
            <path d="m6 6 12 12"></path>
        </svg>
    </button>
    <img id="packageImageModalTarget" src="" alt="Paket rasmi" class="max-h-[88vh] w-auto max-w-full rounded-3xl object-contain shadow-2xl">
</div>

<div class="mt-4 grid gap-4 md:grid-cols-4">
    <div>
        <label class="mb-1 block text-sm font-medium">1 kishi narxi</label>
        <div class="relative">
            <input id="pricePerPersonField" name="price_per_person" type="number" step="0.01" min="0" value="{{ old('price_per_person', $booking?->price_per_person ?? 0) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-16 dark:border-slate-700 dark:bg-slate-800">
            <span id="priceCurrencySuffix" class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-slate-400">{{ old('currency', $booking?->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}</span>
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Avans</label>
        <div class="relative">
            <input name="advance_amount" type="number" step="0.01" min="0" value="{{ old('advance_amount', $booking?->advance_amount ?? 0) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-16 dark:border-slate-700 dark:bg-slate-800">
            <span id="advanceCurrencySuffix" class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-slate-400">{{ old('currency', $booking?->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}</span>
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Valyuta</label>
        <select id="currencyField" name="currency" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            @foreach($currencies as $code => $label)
                <option value="{{ $code }}" @selected(old('currency', $booking?->currency ?? 'UZS') === $code)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">To'lov turi</label>
        <select name="payment_method" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            @foreach($paymentMethods as $method)
                <option value="{{ $method }}" @selected(old('payment_method', $booking?->payment_method ?? 'Naqd') === $method)>{{ $method }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-3">
        <label class="mb-1 block text-sm font-medium">Umumiy summa</label>
        <input id="totalAmountPreview" type="text" value="{{ number_format((float) old('total_amount', $booking?->total_amount ?? 0), 2) }} {{ old('currency', $booking?->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800" readonly>
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
                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" @selected(($serviceRow['service_id'] ?? null) == $service->id)>{{ $service->name }} ({{ number_format($service->price, 2) }} so'm)</option>
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

@php
    $packageImageData = $packages->map(fn ($package) => [
        'id' => $package->id,
        'price' => (float) $package->price_per_person,
        'images' => $package->images->map(fn ($image) => [
            'id' => $image->id,
            'url' => asset('storage/'.$image->image_path),
        ])->values()->all(),
    ])->values()->all();
@endphp

<script>
    (() => {
        const packageImageData = @json($packageImageData);
        const selectedPackageImageId = @json(old('package_gallery_image_id', $booking?->package_gallery_image_id));
        const tableBody = document.querySelector('#servicesTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');
        const packageSelect = document.getElementById('packageSelect');
        const packageGallery = document.getElementById('packageGallery');
        const packageGalleryEmpty = document.getElementById('packageGalleryEmpty');
        const packageImageModal = document.getElementById('packageImageModal');
        const packageImageModalTarget = document.getElementById('packageImageModalTarget');
        const packageImageModalClose = document.getElementById('packageImageModalClose');
        const guestCountField = document.getElementById('guestCountField');
        const pricePerPersonField = document.getElementById('pricePerPersonField');
        const totalAmountPreview = document.getElementById('totalAmountPreview');
        const currencyField = document.getElementById('currencyField');
        const priceCurrencySuffix = document.getElementById('priceCurrencySuffix');
        const advanceCurrencySuffix = document.getElementById('advanceCurrencySuffix');
        const clientSelect = document.querySelector('select[name="client_id"]');
        const clientPhonePreview = document.getElementById('clientPhonePreview');
        if (!tableBody || !addRowBtn) return;

        const options = `
            <option value="">Xizmatni tanlang</option>
            @foreach($services as $service)
                <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }} ({{ number_format($service->price, 2) }} so'm)</option>
            @endforeach
        `;

        const currencySuffix = () => currencyField?.value === 'USD' ? '$' : 'so\'m';

        const updatePhone = () => {
            const option = clientSelect?.selectedOptions?.[0];
            clientPhonePreview.value = option?.dataset?.phone || '';
        };

        const updateCurrencyUi = () => {
            const suffix = currencySuffix();
            if (priceCurrencySuffix) priceCurrencySuffix.textContent = suffix;
            if (advanceCurrencySuffix) advanceCurrencySuffix.textContent = suffix;
            calculateTotal();
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

            totalAmountPreview.value = `${total.toFixed(2)} ${currencySuffix()}`;
        };

        const renderPackageGallery = () => {
            if (!packageGallery || !packageGalleryEmpty) return;

            const selectedId = Number(packageSelect?.value || 0);
            const selectedPackage = packageImageData.find(item => Number(item.id) === selectedId);

            packageGallery.innerHTML = '';

            if (!selectedPackage || !selectedPackage.images.length) {
                packageGallery.classList.add('hidden');
                packageGalleryEmpty.classList.remove('hidden');
                packageGalleryEmpty.textContent = selectedId
                    ? 'Ushbu paketga rasm biriktirilmagan.'
                    : 'Tanlangan paket uchun rasmlar shu yerda chiqadi.';
                return;
            }

            packageGallery.classList.remove('hidden');
            packageGalleryEmpty.classList.add('hidden');

            const fallbackSelectedId = Number(selectedPackageImageId || 0);
            const currentCheckedInput = document.querySelector('input[name="package_gallery_image_id"]:checked');
            const preferredSelectedId = Number(currentCheckedInput?.value || fallbackSelectedId || 0);
            const hasPreferredImage = selectedPackage.images.some(image => Number(image.id) === preferredSelectedId);
            const currentSelectedId = hasPreferredImage ? preferredSelectedId : Number(selectedPackage.images[0].id);

            selectedPackage.images.forEach((image, index) => {
                const card = document.createElement('label');
                const isChecked = Number(image.id) === currentSelectedId || (!currentSelectedId && index === 0);
                card.className = `block cursor-pointer overflow-hidden rounded-3xl border bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900 ${
                    isChecked
                        ? 'border-primary-500 ring-2 ring-primary-200 dark:border-primary-400 dark:ring-primary-900/60'
                        : 'border-slate-200 dark:border-slate-700'
                }`;
                card.innerHTML = `
                    <div class="group relative aspect-[4/3] w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img src="${image.url}" alt="Paket rasmi" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                        <button type="button" data-preview-image="${image.url}" class="absolute inset-x-3 bottom-3 inline-flex items-center justify-center rounded-2xl bg-slate-950/70 px-3 py-2 text-xs font-medium text-white opacity-0 backdrop-blur transition group-hover:opacity-100">
                            Katta ko'rish
                        </button>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Paket rasmi</p>
                            <p class="mt-1 text-xs text-slate-500">${index + 1}-variant</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium ${
                            isChecked
                                ? 'bg-primary-50 text-primary-700 dark:bg-primary-950/50 dark:text-primary-300'
                                : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'
                        }">
                            <input type="radio" name="package_gallery_image_id" value="${image.id}" class="h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-500" ${isChecked ? 'checked' : ''}>
                            Tanlash
                        </span>
                    </div>
                `;
                packageGallery.appendChild(card);
            });

            packageGallery.querySelectorAll('[data-preview-image]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    const imageUrl = button.getAttribute('data-preview-image');
                    if (!packageImageModal || !packageImageModalTarget || !imageUrl) return;
                    packageImageModalTarget.src = imageUrl;
                    packageImageModal.classList.remove('hidden');
                    packageImageModal.classList.add('flex');
                });
            });
        };

        packageImageModalClose?.addEventListener('click', () => {
            packageImageModal?.classList.add('hidden');
            packageImageModal?.classList.remove('flex');
            if (packageImageModalTarget) packageImageModalTarget.src = '';
        });

        packageImageModal?.addEventListener('click', (event) => {
            if (event.target !== packageImageModal) return;
            packageImageModal.classList.add('hidden');
            packageImageModal.classList.remove('flex');
            if (packageImageModalTarget) packageImageModalTarget.src = '';
        });

        packageSelect?.addEventListener('change', () => {
            const option = packageSelect.selectedOptions[0];
            if (option?.dataset?.price) {
                pricePerPersonField.value = option.dataset.price;
            }
            renderPackageGallery();
            calculateTotal();
        });

        guestCountField?.addEventListener('input', calculateTotal);
        pricePerPersonField?.addEventListener('input', calculateTotal);
        currencyField?.addEventListener('change', updateCurrencyUi);
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
        renderPackageGallery();
        updateCurrencyUi();
        calculateTotal();
    })();
</script>

