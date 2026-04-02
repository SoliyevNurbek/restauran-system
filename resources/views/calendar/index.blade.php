<x-app-layout title="Kalendar" pageTitle="Kalendar">
    <div class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
        <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Yaqinlashayotgan bronlar</h2>
                <span class="text-xs text-slate-500">Band sanalar va zallar</span>
            </div>
            <div class="mobile-fit-table overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-500"><tr><th class="pb-3">Sana</th><th class="pb-3">Vaqt</th><th class="pb-3">Zal</th><th class="pb-3">Mijoz</th><th class="pb-3">Tadbir</th></tr></thead>
                    <tbody>
                    @forelse($bookings as $booking)
                        <tr class="border-t border-slate-100 dark:border-slate-800">
                            <td class="py-3">{{ $booking->event_date?->format('d.m.Y') }}</td>
                            <td class="py-3">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                            <td class="py-3">{{ $booking->hall?->name }}</td>
                            <td class="py-3">{{ $booking->client?->full_name }}</td>
                            <td class="py-3">{{ $booking->eventType?->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Bronlar topilmadi.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
                <h3 class="text-sm font-semibold">Zallar holati</h3>
                <div class="mt-4 space-y-3">
                    @foreach($halls as $hall)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                            <div>
                                <p class="font-medium">{{ $hall->name }}</p>
                                <p class="text-xs text-slate-500">{{ $hall->capacity }} mehmon</p>
                            </div>
                            <x-status-badge :status="$hall->status" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
                <h3 class="text-sm font-semibold">Bandlik eslatmasi</h3>
                <p class="mt-3 text-sm text-slate-500">Bir xil zal uchun bir xil sana va vaqt oralig'ida ustma-ust bronlarga ruxsat berilmaydi. Validatsiya booking yaratish va yangilash qatlamida majburiy.</p>
            </div>
        </div>
    </div>
</x-app-layout>

