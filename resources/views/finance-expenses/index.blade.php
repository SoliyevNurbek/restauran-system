<x-app-layout title="Xarajatlar" pageTitle="Xarajatlar">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Xarajatlar</h2>
            <p class="text-sm text-slate-500">Kundalik xarajatlarni kategoriya bo'yicha kuzatish</p>
        </div>
        <a href="{{ route('inventory-expenses.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Xarajat qo'shish</a>
    </div>

    <div class="space-y-3 lg:hidden">
        @forelse($expenses as $expense)
            <div class="rounded-3xl bg-white p-4 shadow-soft dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $expense->title }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ optional($expense->expense_date)->format('d.m.Y') }}</p>
                    </div>
                    <x-money :value="$expense->amount" class="rounded-2xl bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:bg-rose-950/30 dark:text-rose-300" suffixClass="text-[11px] font-medium text-rose-500 dark:text-rose-300" />
                </div>

                <div class="mt-4 grid gap-3 text-xs">
                    <div>
                        <p class="text-slate-400">Kategoriya</p>
                        <p class="mt-1 break-words text-slate-700 dark:text-slate-200">{{ $expense->category?->name ?? '�' }}</p>
                    </div>
                    @if($expense->notes)
                        <div>
                            <p class="text-slate-400">Izoh</p>
                            <p class="mt-1 break-words text-slate-700 dark:text-slate-200">{{ $expense->notes }}</p>
                        </div>
                    @endif
                </div>

                <div class="responsive-actions mt-4 flex flex-wrap gap-2">
                    <x-action-link href="{{ route('inventory-expenses.edit', ['inventory_expense' => $expense]) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                    <form method="POST" action="{{ route('inventory-expenses.destroy', ['inventory_expense' => $expense]) }}">
                        @csrf
                        @method('DELETE')
                        <x-delete-button />
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white p-8 text-center text-sm text-slate-500 shadow-soft dark:bg-slate-900">Xarajatlar hali kiritilmagan.</div>
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-3xl bg-white shadow-soft dark:bg-slate-900 lg:block">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/70 dark:text-slate-300">
                <tr>
                    <th class="px-5 py-4">Sana</th>
                    <th class="px-5 py-4">Nomi</th>
                    <th class="px-5 py-4">Kategoriya</th>
                    <th class="px-5 py-4">Summa</th>
                    <th class="px-5 py-4">Amallar</th>
                </tr>
                </thead>
                <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-4">{{ optional($expense->expense_date)->format('d.m.Y') }}</td>
                        <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">{{ $expense->title }}</td>
                        <td class="px-5 py-4">{{ $expense->category?->name ?? '�' }}</td>
                        <td class="px-5 py-4 font-semibold"><x-money :value="$expense->amount" /></td>
                        <td class="px-5 py-4">
                            <div class="responsive-actions flex flex-wrap gap-2">
                                <x-action-link href="{{ route('inventory-expenses.edit', ['inventory_expense' => $expense]) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                <form method="POST" action="{{ route('inventory-expenses.destroy', ['inventory_expense' => $expense]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">Xarajatlar hali kiritilmagan.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $expenses->links() }}</div>
</x-app-layout>
