<x-app-layout title="Bronlar" pageTitle="Bronlar" pageSubtitle="Bronlar, mijozlar, zal va to'lov holatini bitta ishchi jadvalda boshqaring.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Toy boshqaruvi" title="Bronlar" subtitle="Sana, zal, mijoz va holat bo'yicha kerakli bronni tez toping. Har bir qator real operatsion ish uchun tayyor ko'rinishda berildi.">
            <x-slot:actions>
                <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Yangi bron qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 lg:grid-cols-[1.4fr_0.9fr_0.9fr_0.9fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Bron raqami, mijoz yoki zal bo'yicha qidiring" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <select name="status" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha holatlar</option>
                    @foreach(['Yangi', 'Tasdiqlangan', 'Tayyorlanmoqda', 'Otkazildi', 'Bekor qilindi'] as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="hall_id" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha zallar</option>
                    @foreach($hallOptions as $hall)
                        <option value="{{ $hall->id }}" @selected($filters['hallId'] === $hall->id)>{{ $hall->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ $filters['date'] }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('bookings.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($bookings->count())
            <div class="overflow-hidden rounded-[30px] border border-slate-200/80 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <div class="mobile-fit-table overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/90 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4">Bron</th>
                                <th class="px-5 py-4">Mijoz</th>
                                <th class="px-5 py-4">Tadbir</th>
                                <th class="px-5 py-4">Moliyaviy holat</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($bookings as $booking)
                                @php
                                    $paymentState = (float) $booking->remaining_amount <= 0 ? 'To‘langan' : ((float) $booking->paid_amount > 0 ? 'Qisman to‘langan' : 'Kutilmoqda');
                                @endphp
                                <tr class="align-top transition hover:bg-slate-50/70 dark:hover:bg-slate-950/40">
                                    <td class="px-5 py-4">
                                        <div class="flex min-w-[250px] items-start gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                <i data-lucide="calendar-days" class="h-5 w-5"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900 dark:text-white">{{ $booking->booking_number }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ optional($booking->event_date)->format('d.m.Y') }} · {{ $booking->start_time }} - {{ $booking->end_time }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $booking->client?->full_name ?? 'Mijoz yo‘q' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $booking->hall?->name ?? 'Zal yo‘q' }} · {{ $booking->guest_count }} mehmon</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $booking->eventType?->name ?? 'Tadbir turi yo‘q' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $booking->package?->name ?? 'Paket tanlanmagan' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ number_format((float) $booking->total_amount, 0, '.', ' ') }} UZS</p>
                                        <p class="mt-1 text-xs text-slate-500">To‘langan: {{ number_format((float) $booking->paid_amount, 0, '.', ' ') }} · Qoldiq: {{ number_format((float) $booking->remaining_amount, 0, '.', ' ') }}</p>
                                        <div class="mt-2 inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $paymentState }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <x-status-badge :status="$booking->status" />
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="responsive-actions flex justify-end gap-2">
                                            <x-action-link href="{{ route('bookings.show', $booking) }}" icon="eye" variant="view">Ko'rish</x-action-link>
                                            <x-action-link href="{{ route('bookings.edit', $booking) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-delete-button />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <x-admin.empty-state icon="calendar-off" title="Bronlar topilmadi" text="Hozirgi filter bo'yicha bron yozuvlari yo'q. Yangi bron qo'shish yoki filterni tozalab ko'ring." action-href="{{ route('bookings.create') }}" action-label="Yangi bron qo'shish" />
        @endif

        <div>{{ $bookings->links() }}</div>
    </div>
</x-app-layout>
