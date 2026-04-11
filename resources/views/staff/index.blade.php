<x-app-layout title="Xodimlar" pageTitle="Xodimlar" pageSubtitle="Ichki foydalanuvchilar, rollar va faollikni tartibli boshqaruv ko'rinishida olib boring.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Xodimlar" title="Foydalanuvchilar va rollar" subtitle="Egasi, menejer, kassir va operatorlar bo'yicha ishchi tarkibni premium ko'rinishda boshqaring.">
            <x-slot:actions>
                <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    Xodim qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 lg:grid-cols-[1.2fr_0.9fr_0.8fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Ism yoki telefon bo'yicha qidiring" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <select name="role" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha rollar</option>
                    @foreach($roles as $roleOption)
                        <option value="{{ $roleOption }}" @selected($filters['role'] === $roleOption)>{{ $roleOption }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha holatlar</option>
                    <option value="Faol" @selected($filters['status'] === 'Faol')>Faol</option>
                    <option value="Nofaol" @selected($filters['status'] === 'Nofaol')>Nofaol</option>
                </select>
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('employees.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($employees->count())
            <div class="grid gap-4 xl:grid-cols-2">
                @foreach($employees as $employee)
                    <x-admin.section-card icon="badge-check" :title="$employee->full_name" :subtitle="$employee->phone ?: 'Telefon ko‘rsatilmagan'">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Rol</p>
                                <div class="mt-2"><x-status-badge :status="$employee->role" /></div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Holat</p>
                                <div class="mt-2"><x-status-badge :status="$employee->status" /></div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Oylik</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $employee->salary ? number_format($employee->salary, 0, '.', ' ').' UZS' : 'Belgilanmagan' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <p class="text-sm text-slate-500">{{ $employee->notes ?: "Xodim bo'yicha qo'shimcha izoh kiritilmagan." }}</p>
                            <div class="responsive-actions flex gap-2">
                                <x-action-link href="{{ route('employees.edit', $employee) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button />
                                </form>
                            </div>
                        </div>
                    </x-admin.section-card>
                @endforeach
            </div>
        @else
            <x-admin.empty-state icon="users" title="Xodimlar topilmadi" text="Jamoani boshqarish uchun birinchi foydalanuvchini qo'shing." action-href="{{ route('employees.create') }}" action-label="Xodim qo'shish" />
        @endif

        <div>{{ $employees->links() }}</div>
    </div>
</x-app-layout>
