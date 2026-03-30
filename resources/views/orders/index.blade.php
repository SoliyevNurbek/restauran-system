<x-app-layout title="Bronlar" pageTitle="Bronlar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Bronlar</h2>
        <a href="{{ route('bookings.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Bron yaratish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Bron #</th>
                <th class="px-4 py-3">Mijoz</th>
                <th class="px-4 py-3">Tadbir</th>
                <th class="px-4 py-3">Zal</th>
                <th class="px-4 py-3">Sana</th>
                <th class="px-4 py-3">Jami</th>
                <th class="px-4 py-3">Qarz</th>
                <th class="px-4 py-3">Holat</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $booking->booking_number }}</td>
                    <td class="px-4 py-3">{{ $booking->client?->full_name ?? 'Mijoz biriktirilmagan' }}</td>
                    <td class="px-4 py-3">{{ $booking->eventType?->name }}</td>
                    <td class="px-4 py-3">{{ $booking->hall?->name }}</td>
                    <td class="px-4 py-3">{{ optional($booking->event_date)->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">{{ number_format($booking->total_amount, 2) }}</td>
                    <td class="px-4 py-3">{{ number_format($booking->remaining_amount, 2) }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$booking->status" /></td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('bookings.show', $booking) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Ko'rish</a>
                            <a href="{{ route('bookings.edit', $booking) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a>
                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-4 py-6 text-center text-slate-500">Bronlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</x-app-layout>

