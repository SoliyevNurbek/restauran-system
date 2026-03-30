@php($expenseCategory = $expenseCategory ?? null)

<div class="space-y-4">
    <div>
        <label class="mb-1 block text-sm font-medium">Kategoriya nomi</label>
        <input name="name" value="{{ old('name', $expenseCategory?->name) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Izoh</label>
        <textarea name="description" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('description', $expenseCategory?->description) }}</textarea>
    </div>
</div>
