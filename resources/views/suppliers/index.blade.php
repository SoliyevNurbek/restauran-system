<x-app-layout title="Ta'minotchilar" pageTitle="Ta'minotchilar">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Ta'minotchilar ro'yxati</h2>
            <p class="text-sm text-slate-500">F.I.O, telefon, kompaniya va balans bo'yicha nazorat</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Ta'minotchi qo'shish</a>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white shadow-soft dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/70 dark:text-slate-300">
                <tr>
                    <th class="px-5 py-4">F.I.O</th>
                    <th class="px-5 py-4">Telefon</th>
                    <th class="px-5 py-4">Kompaniya</th>
                    <th class="px-5 py-4">Balans</th>
                    <th class="px-5 py-4">Amallar</th>
                </tr>
                </thead>
                <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">{{ $supplier->full_name }}</td>
                        <td class="px-5 py-4">{{ $supplier->phone }}</td>
                        <td class="px-5 py-4">{{ $supplier->company_name ?: '—' }}</td>
                        <td class="px-5 py-4 font-semibold {{ $supplier->balance > 0 ? 'text-amber-600 dark:text-amber-300' : 'text-emerald-600 dark:text-emerald-300' }}">{{ number_format($supplier->balance, 2) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium dark:border-slate-700">Ko'rish</a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-medium text-white dark:bg-slate-100 dark:text-slate-900">Tahrirlash</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">Ta'minotchilar hali kiritilmagan.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $suppliers->links() }}</div>
</x-app-layout>
