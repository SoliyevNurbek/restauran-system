<x-app-layout title="Mijoz ma'lumotlari" pageTitle="Mijoz tarixi">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <h3 class="text-lg font-semibold">{{ $customer->name }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $customer->phone ?: 'Telefon raqami yo'q' }}</p>
            <p class="mt-4 text-xs uppercase tracking-widest text-slate-400">Jami buyurtmalar</p>
            <p class="text-2xl font-bold">{{ $customer->orders()->count() }}</p>
        </div>

        <div class="overflow-x-auto rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900 lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold">Buyurtmalar tarixi</h3>
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500"><tr><th class="pb-3">Buyurtma</th><th class="pb-3">Sana</th><th class="pb-3">Jami</th><th class="pb-3">Holat</th></tr></thead>
                <tbody>
                @forelse($orders as $order)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="py-3">{{ $order->order_number }}</td>
                        <td class="py-3">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="py-3">${{ number_format($order->total, 2) }}</td>
                        <td class="py-3"><x-status-badge :status="$order->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-center text-slate-500">Buyurtmalar topilmadi.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
