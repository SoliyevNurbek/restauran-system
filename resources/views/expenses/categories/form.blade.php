<div>
    <label class="mb-1 block text-sm font-medium">Nomi</label>
    <input name="name" value="{{ old('name', $category?->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Turi</label>
    <select name="type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="kitchen" @selected(old('type', $category?->type ?? 'kitchen') === 'kitchen')>kitchen</option>
        <option value="event" @selected(old('type', $category?->type) === 'event')>event</option>
        <option value="fixed" @selected(old('type', $category?->type) === 'fixed')>fixed</option>
    </select>
    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

