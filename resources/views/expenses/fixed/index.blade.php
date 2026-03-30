<x-app-layout title="Doimiy xarajatlar" pageTitle="Doimiy xarajatlar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Doimiy xarajatlar</h2>
        <a href="{{ route('expenses.fixed.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Xarajat qo'shish</a>
    </div>
    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70"><tr><th class="px-4 py-3">Bron</th><th class="px-4 py-3">Nomi</th><th class="px-4 py-3">Ajratilgan summa</th><th class="px-4 py-3">Soliq</th><th class="px-4 py-3 text-right">Amallar</th></tr></thead>
            <tbody>
            @forelse($costs as $cost)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3">{{ $cost->booking?->booking_number }}</td>
                    <td class="px-4 py-3">{{ $cost->name }}</td>
                    <td class="px-4 py-3">{{ number_format($cost->allocated_amount, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3">{{ number_format($cost->tax_share, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('expenses.fixed.edit', $cost) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a><form action="{{ route('expenses.fixed.destroy', $cost) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Xarajatlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $costs->links() }}</div>
</x-app-layout>

