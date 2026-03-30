<div>
    <label class="mb-1 block text-sm font-medium">Bron</label>
    <select name="booking_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="">Bronni tanlang</option>
        @foreach($bookings as $booking)
            <option value="{{ $booking->id }}" @selected(old('booking_id', $cost?->booking_id) == $booking->id)>{{ $booking->booking_number }} - {{ $booking->client?->full_name }}</option>
        @endforeach
    </select>
    @error('booking_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Kategoriya</label>
    <select name="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="">Tanlanmagan</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $cost?->category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Xizmat nomi</label>
    <input name="service_name" value="{{ old('service_name', $cost?->service_name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('service_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Miqdor</label>
    <input name="quantity" type="number" step="0.01" min="0.01" value="{{ old('quantity', $cost?->quantity) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('quantity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Birlik narxi</label>
    <input name="unit_price" type="number" step="0.01" min="0" value="{{ old('unit_price', $cost?->unit_price) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('unit_price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Oylik ulushi</label>
    <input name="salary_cost" type="number" step="0.01" min="0" value="{{ old('salary_cost', $cost?->salary_cost) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('salary_cost')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Kommunal ulush</label>
    <input name="utility_cost" type="number" step="0.01" min="0" value="{{ old('utility_cost', $cost?->utility_cost) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('utility_cost')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Soliq ulushi</label>
    <input name="tax_share" type="number" step="0.01" min="0" value="{{ old('tax_share', $cost?->tax_share) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('tax_share')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

