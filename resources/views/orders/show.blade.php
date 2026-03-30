<x-app-layout title="Buyurtma tafsilotlari" pageTitle="Buyurtma tafsilotlari">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <h3 class="text-lg font-semibold">{{ $order->order_number }}</h3>
            <p class="mt-2 text-sm text-slate-500">Mijoz: {{ $order->customer?->name ?? 'Tashrif buyuruvchi' }}</p>
            <p class="text-sm text-slate-500">Stol: {{ $order->diningTable?->table_number ?? '—' }}</p>
            <div class="mt-3"><x-status-badge :status="$order->status" /></div>
            <p class="mt-4 text-sm">Oraliq summa: <span class="font-semibold">${{ number_format($order->subtotal, 2) }}</span></p>
            <p class="text-sm">Soliq: <span class="font-semibold">${{ number_format($order->tax, 2) }}</span></p>
            <p class="text-sm">Jami: <span class="font-semibold">${{ number_format($order->total, 2) }}</span></p>
        </div>

        <div class="overflow-x-auto rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900 lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold">Taomlar</h3>
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500"><tr><th class="pb-3">Taom</th><th class="pb-3">Miqdor</th><th class="pb-3">Narx</th><th class="pb-3">Qator summasi</th></tr></thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="py-3">{{ $item->menuItem->name }}</td>
                        <td class="py-3">{{ $item->quantity }}</td>
                        <td class="py-3">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3">${{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
