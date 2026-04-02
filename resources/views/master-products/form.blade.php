<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">SKU / Kod</label>
        <input id="product-sku-input" name="sku" list="product-sku-catalog" value="{{ old('sku', $product?->sku) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <datalist id="product-sku-catalog">
            @foreach($catalogProducts as $catalogProduct)
                <option value="{{ $catalogProduct->sku }}">{{ $catalogProduct->name }}</option>
            @endforeach
        </datalist>
        <p class="mt-1 text-xs text-slate-500">Qidirib tanlang yoki yangi SKU ni qo'lda yozing.</p>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Mahsulot nomi</label>
        <input id="product-name-input" name="name" list="product-name-catalog" value="{{ old('name', $product?->name) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <datalist id="product-name-catalog">
            @foreach($catalogProducts as $catalogProduct)
                <option value="{{ $catalogProduct->name }}">{{ $catalogProduct->sku }}</option>
            @endforeach
        </datalist>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Category</label>
        <input id="product-category-input" name="category" list="product-categories" value="{{ old('category', $product?->category) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <datalist id="product-categories">
            @foreach($categories as $category)
                <option value="{{ $category }}"></option>
            @endforeach
        </datalist>
    </div>
    <div>
        <div class="mb-1 flex items-center gap-2">
            <label class="block text-sm font-medium">Subcategory</label>
            <span id="product-unit-badge" class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                {{ old('unit', $product?->unit ?? 'kg') }}
            </span>
        </div>
        <input id="product-subcategory-input" name="subcategory" list="product-subcategories" value="{{ old('subcategory', $product?->subcategory) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <datalist id="product-subcategories">
            @foreach($subcategories as $subcategory)
                <option value="{{ $subcategory }}"></option>
            @endforeach
        </datalist>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Birlik</label>
        <select id="product-unit-input" name="unit" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            @foreach($unitOptions as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $product?->unit ?? 'kg') === $unit)>{{ $unit }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Mahsulot uchun o'lchov birligini tanlang.</p>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Minimal qoldiq</label>
        <input name="minimum_stock" type="number" min="0" step="0.001" value="{{ old('minimum_stock', $product?->minimum_stock ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Hozir olib kelingan</label>
        <div class="relative">
            <input name="received_quantity" type="number" min="0" step="0.001" value="{{ old('received_quantity', $product?->received_quantity ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 pr-20 dark:border-slate-700 dark:bg-slate-800">
            <span id="received-unit-label" class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-slate-400">{{ old('unit', $product?->unit ?? 'kg') }}</span>
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Joriy qoldiq</label>
        <input name="current_stock" type="number" min="0" step="0.001" value="{{ old('current_stock', $product?->current_stock ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Oxirgi narx</label>
        <div class="relative">
            <input name="last_purchase_price" type="number" min="0" step="0.01" value="{{ old('last_purchase_price', $product?->last_purchase_price ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 pr-16 dark:border-slate-700 dark:bg-slate-800">
            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-slate-400">so'm</span>
        </div>
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

<script>
    (() => {
        const catalog = @json($catalogProducts);
        const skuInput = document.getElementById('product-sku-input');
        const nameInput = document.getElementById('product-name-input');
        const categoryInput = document.getElementById('product-category-input');
        const subcategoryInput = document.getElementById('product-subcategory-input');
        const unitInput = document.getElementById('product-unit-input');
        const unitBadge = document.getElementById('product-unit-badge');
        const receivedUnitLabel = document.getElementById('received-unit-label');

        if (!skuInput || !nameInput) return;

        const updateUnitLabels = (unit) => {
            const safeUnit = unit || 'kg';
            if (unitBadge) unitBadge.textContent = safeUnit;
            if (receivedUnitLabel) receivedUnitLabel.textContent = safeUnit;
        };

        const applyCatalogItem = (matched) => {
            if (!matched) return;

            skuInput.value = matched.sku ?? skuInput.value;
            nameInput.value = matched.name ?? nameInput.value;
            categoryInput.value = matched.category ?? '';
            subcategoryInput.value = matched.subcategory ?? '';
            unitInput.value = matched.unit ?? 'kg';
            updateUnitLabels(unitInput.value);
        };

        const findCatalogItem = () => {
            const skuValue = skuInput.value.trim().toLowerCase();
            const nameValue = nameInput.value.trim().toLowerCase();

            return catalog.find((item) => {
                return (skuValue && item.sku?.toLowerCase() === skuValue)
                    || (nameValue && item.name?.toLowerCase() === nameValue);
            });
        };

        skuInput.addEventListener('change', () => applyCatalogItem(findCatalogItem()));
        nameInput.addEventListener('change', () => applyCatalogItem(findCatalogItem()));
        unitInput.addEventListener('change', () => updateUnitLabels(unitInput.value));
        updateUnitLabels(unitInput.value);
    })();
</script>
