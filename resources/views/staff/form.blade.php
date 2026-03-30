<div>
    <label class="mb-1 block text-sm font-medium">F.I.Sh</label>
    <input name="full_name" value="{{ old('full_name', $employee?->full_name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('full_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Telefon</label>
    <input name="phone" value="{{ old('phone', $employee?->phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Lavozim</label>
    <select name="role" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        @foreach($roles as $role)
            <option value="{{ $role }}" @selected(old('role', $employee?->role ?? 'Administrator') === $role)>{{ $role }}</option>
        @endforeach
    </select>
    @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Oylik</label>
    <input name="salary" type="number" step="0.01" min="0" value="{{ old('salary', $employee?->salary) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('salary')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Holat</label>
    <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="Faol" @selected(old('status', $employee?->status ?? 'Faol') === 'Faol')>Faol</option>
        <option value="Nofaol" @selected(old('status', $employee?->status) === 'Nofaol')>Nofaol</option>
    </select>
    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Izoh</label>
    <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes', $employee?->notes) }}</textarea>
    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

