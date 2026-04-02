<x-app-layout title="Mijoz ma'lumotlari" pageTitle="Mijoz tarixi">
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <h3 class="text-lg font-semibold">{{ $client->full_name }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $client->phone ?: 'Telefon raqami yo\'q' }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ $client->extra_phone ?: 'Qo\'shimcha telefon yo\'q' }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ $client->address ?: 'Manzil ko\'rsatilmagan' }}</p>
            <p class="mt-2 text-sm text-slate-500">{{ $client->passport_info ?: 'Pasport ma\'lumoti yo\'q' }}</p>
            <p class="mt-4 text-xs uppercase tracking-widest text-slate-400">Jami bronlar</p>
            <p class="text-2xl font-bold">{{ $bookings->total() }}</p>
        </div>

        <div class="mobile-fit-table overflow-x-auto rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900 lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold">Bronlar tarixi</h3>
            <table class="min-w-full text-sm">
                <thead class="text-left text-slate-500">
                <tr>
                    <th class="pb-3">Bron</th>
                    <th class="pb-3">Tadbir</th>
                    <th class="pb-3">Sana</th>
                    <th class="pb-3">Qarz</th>
                    <th class="pb-3">Holat</th>
                </tr>
                </thead>
                <tbody>
                @forelse($bookings as $booking)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="py-3">{{ $booking->booking_number }}</td>
                        <td class="py-3">{{ $booking->eventType?->name ?: '—' }}</td>
                        <td class="py-3">{{ $booking->event_date?->format('d.m.Y') }}</td>
                        <td class="py-3">{{ number_format($booking->remaining_amount, 0, '.', ' ') }} so'm</td>
                        <td class="py-3"><x-status-badge :status="$booking->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">Bronlar topilmadi.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    </div>
</x-app-layout>

