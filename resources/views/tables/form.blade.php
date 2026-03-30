<div>
    <label class="mb-1 block text-sm font-medium">Zal nomi</label>
    <input name="name" value="{{ old('name', $hall?->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Sig'imi</label>
    <input name="capacity" type="number" min="1" value="{{ old('capacity', $hall?->capacity) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('capacity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Narxi</label>
    <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $hall?->price) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Holat</label>
    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="Faol" @selected(old('status', $hall?->status ?? 'Faol') === 'Faol')>Faol</option>
        <option value="Nofaol" @selected(old('status', $hall?->status) === 'Nofaol')>Nofaol</option>
        <option value="Ta'mirda" @selected(old('status', $hall?->status) === "Ta'mirda")>Ta'mirda</option>
    </select>
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Rasm</label>
    <input name="image" type="file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Tavsif</label>
    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('description', $hall?->description) }}</textarea>
    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

