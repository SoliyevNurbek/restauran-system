@php($expense = $expense ?? null)

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Kategoriya</label>
        <select name="expense_category_id" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
            <option value="">Tanlanmagan</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('expense_category_id', $expense?->expense_category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Sana</label>
        <input type="date" name="expense_date" value="{{ old('expense_date', optional($expense?->expense_date)->toDateString() ?? now()->toDateString()) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Xarajat nomi</label>
        <input name="title" value="{{ old('title', $expense?->title) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium">Summa</label>
        <input type="number" name="amount" min="0" step="0.01" value="{{ old('amount', $expense?->amount ?? 0) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium">Izoh</label>
        <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $expense?->notes) }}</textarea>
    </div>
</div>
