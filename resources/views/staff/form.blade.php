<div>
    <label class="mb-1 block text-sm font-medium">Nomi</label>
    <input name="name" value="{{ old('name', $staff?->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Elektron pochta</label>
    <input name="email" type="email" value="{{ old('email', $staff?->email) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Telefon</label>
    <input name="phone" value="{{ old('phone', $staff?->phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Lavozim</label>
    <select name="role" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="admin" @selected(old('role', $staff?->role ?? 'waiter') === 'admin')>Admin</option>
        <option value="waiter" @selected(old('role', $staff?->role ?? 'waiter') === 'waiter')>Ofitsiant</option>
        <option value="cashier" @selected(old('role', $staff?->role ?? 'waiter') === 'cashier')>Kassir</option>
    </select>
</div>
