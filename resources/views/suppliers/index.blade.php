<x-app-layout title="Ta'minotchilar" pageTitle="Ta'minotchilar" pageSubtitle="Ta'minotchilar, balans va xarid aloqalarini aniq va premium jadval ko'rinishida boshqaring.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Ombor va moliya" title="Ta'minotchilar" subtitle="Ta'minotchi bilan ishlash, qarzdorlik holati va so'nggi operatsiyalarni tizimli boshqaring.">
            <x-slot:actions>
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Ta'minotchi qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 md:grid-cols-[1.5fr_0.9fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Ism, telefon yoki kompaniya bo'yicha qidiring" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <select name="balance" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha balanslar</option>
                    <option value="debt" @selected($filters['balance'] === 'debt')>Qarzdorlik mavjud</option>
                </select>
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('suppliers.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($suppliers->count())
            <div class="overflow-hidden rounded-[30px] border border-slate-200/80 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <div class="mobile-fit-table overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/90 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4">Ta'minotchi</th>
                                <th class="px-5 py-4">Aloqa</th>
                                <th class="px-5 py-4">Xarid va to'lov</th>
                                <th class="px-5 py-4">Balans</th>
                                <th class="px-5 py-4 text-right">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($suppliers as $supplier)
                                <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-950/40">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $supplier->full_name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $supplier->company_name ?: 'Kompaniya ko‘rsatilmagan' }}</p>
                                    </td>
                                    <td class="px-5 py-4">{{ $supplier->phone }}</td>
                                    <td class="px-5 py-4 text-xs text-slate-500">
                                        <p>Xarid: {{ number_format((float) ($supplier->purchases_sum_total_amount ?? 0), 0, '.', ' ') }} UZS</p>
                                        <p class="mt-1">To'lov: {{ number_format((float) ($supplier->payments_sum_amount ?? 0), 0, '.', ' ') }} UZS</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $supplier->balance > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                            {{ number_format($supplier->balance, 0, '.', ' ') }} UZS
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="responsive-actions flex justify-end gap-2">
                                            <x-action-link href="{{ route('suppliers.show', $supplier) }}" icon="eye" variant="view">Ko'rish</x-action-link>
                                            <x-action-link href="{{ route('suppliers.edit', $supplier) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-delete-button />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <x-admin.empty-state icon="truck" title="Ta'minotchilar topilmadi" text="Toyxona uchun xomashyo va xizmat yetkazib beruvchilarni shu yerga qo'shing." action-href="{{ route('suppliers.create') }}" action-label="Ta'minotchi qo'shish" />
        @endif

        <div>{{ $suppliers->links() }}</div>
    </div>
</x-app-layout>
