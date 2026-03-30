<div>
    <label class="mb-1 block text-sm font-medium">Nomi</label>
    <input name="name" value="{{ old('name', $eventType?->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Tavsif</label>
    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('description', $eventType?->description) }}</textarea>
    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

