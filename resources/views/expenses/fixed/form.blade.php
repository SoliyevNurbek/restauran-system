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
    <label class="mb-1 block text-sm font-medium">Nomi</label>
    <input name="name" value="{{ old('name', $cost?->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Oylik summa</label>
    <input name="monthly_amount" type="number" step="0.01" min="0" value="{{ old('monthly_amount', $cost?->monthly_amount) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('monthly_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Ajratilgan summa</label>
    <input name="allocated_amount" type="number" step="0.01" min="0" value="{{ old('allocated_amount', $cost?->allocated_amount) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('allocated_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Soliq ulushi</label>
    <input name="tax_share" type="number" step="0.01" min="0" value="{{ old('tax_share', $cost?->tax_share) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('tax_share')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

