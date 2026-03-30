<x-app-layout title="Xarajatlar" pageTitle="Xarajatlar">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Xarajatlar</h2>
            <p class="text-sm text-slate-500">Kundalik xarajatlarni kategoriya bo'yicha kuzatish</p>
        </div>
        <a href="{{ route('inventory-expenses.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Xarajat qo'shish</a>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white shadow-soft dark:bg-slate-900">
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
                        <td class="px-5 py-4">{{ $expense->category?->name ?? '—' }}</td>
                        <td class="px-5 py-4 font-semibold">{{ number_format($expense->amount, 2) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('inventory-expenses.edit', $expense) }}" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-medium text-white dark:bg-slate-100 dark:text-slate-900">Tahrirlash</a>
                                <form method="POST" action="{{ route('inventory-expenses.destroy', $expense) }}">
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
