@php($supplier = $supplier ?? null)

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">F.I.O</label>
        <input name="full_name" value="{{ old('full_name', $supplier?->full_name) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Telefon raqam</label>
        <input name="phone" value="{{ old('phone', $supplier?->phone) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Kompaniya</label>
        <input name="company_name" value="{{ old('company_name', $supplier?->company_name) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Boshlang'ich balans</label>
        <div class="relative">
            <input name="opening_balance" type="number" step="0.01" min="0" value="{{ old('opening_balance', $supplier?->opening_balance ?? 0) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 pr-16 dark:border-slate-700 dark:bg-slate-800">
            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-slate-400">so'm</span>
        </div>
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Izoh</label>
        <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $supplier?->notes) }}</textarea>
    </div>
</div>
