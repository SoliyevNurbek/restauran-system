<div>
    <label class="mb-1 block text-sm font-medium">Stol raqami</label>
    <input name="table_number" value="{{ old('table_number', $table?->table_number) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('table_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Holat</label>
    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="free" @selected(old('status', $table?->status ?? 'free') === 'free')>Bo'sh</option>
        <option value="occupied" @selected(old('status', $table?->status) === 'occupied')>Band</option>
    </select>
</div>
