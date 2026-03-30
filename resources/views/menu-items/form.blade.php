<div>
    <label class="mb-1 block text-sm font-medium">Nomi</label>
    <input name="name" value="{{ old('name', $weddingPackage?->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Bir kishilik narx</label>
    <input name="price_per_person" type="number" step="0.01" min="0" value="{{ old('price_per_person', $weddingPackage?->price_per_person) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('price_per_person')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Holat</label>
    <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="Faol" @selected(old('status', $weddingPackage?->status ?? 'Faol') === 'Faol')>Faol</option>
        <option value="Nofaol" @selected(old('status', $weddingPackage?->status) === 'Nofaol')>Nofaol</option>
    </select>
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Rasm</label>
    <input name="image" type="file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Tavsif</label>
    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('description', $weddingPackage?->description) }}</textarea>
    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

