<x-app-layout title="Mijozlar" pageTitle="Mijozlar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Mijozlar</h2>
        <a href="{{ route('customers.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Mijoz qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70"><tr><th class="px-4 py-3">Nomi</th><th class="px-4 py-3">Telefon</th><th class="px-4 py-3">Buyurtmalar</th><th class="px-4 py-3 text-right">Amallar</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                    <td class="px-4 py-3">{{ $customer->phone ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $customer->orders_count }}</td>
                    <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('customers.show', $customer) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Ko'rish</a><a href="{{ route('customers.edit', $customer) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a><form action="{{ route('customers.destroy', $customer) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Mijozlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
</x-app-layout>
