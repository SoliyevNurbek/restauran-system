<x-app-layout title="Bron tafsilotlari" pageTitle="Bron tafsilotlari">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <h3 class="text-lg font-semibold">{{ $booking->booking_number }}</h3>
            <p class="mt-2 text-sm text-slate-500">Mijoz: {{ $booking->client?->full_name ?? 'Mijoz biriktirilmagan' }}</p>
            <p class="text-sm text-slate-500">Tadbir: {{ $booking->eventType?->name }}</p>
            <p class="text-sm text-slate-500">Zal: {{ $booking->hall?->name }}</p>
            <p class="text-sm text-slate-500">Sana: {{ optional($booking->event_date)->format('d.m.Y') }}</p>
            <div class="mt-3"><x-status-badge :status="$booking->status" /></div>
            <p class="mt-4 text-sm">Tushum: <span class="font-semibold">{{ number_format($booking->total_amount, 2) }}</span></p>
            <p class="text-sm">Oshxona xarajatlari jami: <span class="font-semibold">{{ number_format($booking->kitchen_costs_total, 2) }}</span></p>
            <p class="text-sm">Tadbir xarajatlari jami: <span class="font-semibold">{{ number_format($booking->event_costs_total, 2) }}</span></p>
            <p class="text-sm">Doimiy xarajatlar jami: <span class="font-semibold">{{ number_format($booking->fixed_costs_total, 2) }}</span></p>
            <p class="text-sm">Jami xarajat: <span class="font-semibold">{{ number_format($booking->total_costs, 2) }}</span></p>
            <p class="text-sm">Foyda: <span class="font-semibold">{{ number_format($booking->profit, 2) }}</span></p>
        </div>

        <div class="overflow-x-auto rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900 lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold">Qoshimcha xizmatlar</h3>
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500"><tr><th class="pb-3">Xizmat</th><th class="pb-3">Miqdor</th><th class="pb-3">Narx</th><th class="pb-3">Jami</th></tr></thead>
                <tbody>
                @forelse($booking->services as $service)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="py-3">{{ $service->service?->name }}</td>
                        <td class="py-3">{{ $service->quantity }}</td>
                        <td class="py-3">{{ number_format($service->price, 2) }}</td>
                        <td class="py-3">{{ number_format($service->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-3 text-slate-500">Qoshimcha xizmatlar yo'q.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

