<x-app-layout title="Buyurtmalar" pageTitle="Buyurtmalar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Buyurtmalar</h2>
        <a href="{{ route('orders.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Buyurtma yaratish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70"><tr><th class="px-4 py-3">Buyurtma #</th><th class="px-4 py-3">Mijoz</th><th class="px-4 py-3">Stol</th><th class="px-4 py-3">Mahsulotlar</th><th class="px-4 py-3">Jami</th><th class="px-4 py-3">Holat</th><th class="px-4 py-3 text-right">Amallar</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->customer?->name ?? 'Tashrif buyuruvchi' }}</td>
                    <td class="px-4 py-3">{{ $order->diningTable?->table_number ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $order->items->count() }}</td>
                    <td class="px-4 py-3">${{ number_format($order->total, 2) }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                    <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('orders.show', $order) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Ko'rish</a><a href="{{ route('orders.edit', $order) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a><form action="{{ route('orders.destroy', $order) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Buyurtmalar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</x-app-layout>
