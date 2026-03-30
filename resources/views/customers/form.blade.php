<div>
    <label class="mb-1 block text-sm font-medium">F.I.Sh</label>
    <input name="full_name" value="{{ old('full_name', $client?->full_name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('full_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Telefon</label>
    <input name="phone" value="{{ old('phone', $client?->phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Qo'shimcha telefon</label>
    <input name="extra_phone" value="{{ old('extra_phone', $client?->extra_phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('extra_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Manzil</label>
    <input name="address" value="{{ old('address', $client?->address) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Pasport ma'lumoti</label>
    <input name="passport_info" value="{{ old('passport_info', $client?->passport_info) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('passport_info')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Izoh</label>
    <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $client?->notes) }}</textarea>
    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

