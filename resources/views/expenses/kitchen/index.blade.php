<x-app-layout title="Oshxona xarajatlari" pageTitle="Oshxona xarajatlari">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Oshxona xarajatlari</h2>
        <a href="{{ route('expenses.kitchen.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Xarajat qo'shish</a>
    </div>
    <div class="mobile-fit-table overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70"><tr><th class="px-4 py-3">Bron</th><th class="px-4 py-3">Kategoriya</th><th class="px-4 py-3">Mahsulot</th><th class="px-4 py-3">Jami</th><th class="px-4 py-3 text-right">Amallar</th></tr></thead>
            <tbody>
            @forelse($costs as $cost)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3">{{ $cost->booking?->booking_number }}</td>
                    <td class="px-4 py-3">{{ $cost->category?->name ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $cost->product_name }}</td>
                    <td class="px-4 py-3">{{ number_format($cost->grand_total, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3"><div class="responsive-actions flex justify-end gap-2"><x-action-link href="{{ route('expenses.kitchen.edit', $cost) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link><form action="{{ route('expenses.kitchen.destroy', $cost) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Xarajatlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $costs->links() }}</div>
</x-app-layout>
