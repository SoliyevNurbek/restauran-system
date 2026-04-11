<x-app-layout title="Kalendar" pageTitle="Kalendar" pageSubtitle="Oy kesimida bronlar bandligi, zal yuklamasi va yaqin tadbirlarni operatsion ko'rinishda boshqaring.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Toy boshqaruvi" title="Kalendar" subtitle="Har bir kun bo'yicha bandlikni ko'ring, zal kesimidagi tadbirlarni ajrating va yaqin sanali bronlarni tez oching." />

        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <x-admin.section-card :title="$monthLabel" subtitle="Oylik bandlik ko'rinishi" icon="calendar-days">
                <div class="grid grid-cols-7 gap-3 text-center text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                    @foreach(['Du', 'Se', 'Cho', 'Pa', 'Ju', 'Sha', 'Ya'] as $dayName)
                        <div class="rounded-2xl bg-slate-50 px-2 py-3 dark:bg-slate-950/60">{{ $dayName }}</div>
                    @endforeach
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4 xl:grid-cols-7">
                    @foreach($calendarDays as $day)
                        @php($dayBookings = $monthlyBookings->get($day->toDateString(), collect()))
                        <div class="min-h-[150px] rounded-[24px] border border-slate-200/80 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $day->format('d') }}</p>
                                @if($day->isToday())
                                    <span class="rounded-full bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white dark:bg-white dark:text-slate-950">Bugun</span>
                                @endif
                            </div>
                            <div class="mt-3 space-y-2">
                                @forelse($dayBookings->take(3) as $booking)
                                    <a href="{{ route('bookings.show', $booking) }}" class="block rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800">
                                        <p class="truncate text-xs font-semibold text-slate-900 dark:text-white">{{ $booking->hall?->name ?? 'Zal' }}</p>
                                        <p class="mt-1 truncate text-[11px] text-slate-500">{{ $booking->client?->full_name ?? 'Mijoz' }}</p>
                                        <p class="mt-1 text-[11px] text-slate-400">{{ substr((string) $booking->start_time, 0, 5) }}</p>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-200 px-3 py-5 text-center text-[11px] text-slate-400 dark:border-slate-700">Bo'sh kun</div>
                                @endforelse
                                @if($dayBookings->count() > 3)
                                    <div class="text-center text-[11px] font-semibold text-slate-500">+{{ $dayBookings->count() - 3 }} ta bron</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.section-card>

            <div class="space-y-6">
                <x-admin.section-card title="Yaqinlashayotgan tadbirlar" subtitle="Tezkor ochish uchun qisqa ro'yxat." icon="clock-3">
                    <div class="space-y-3">
                        @forelse($bookings as $booking)
                            <a href="{{ route('bookings.show', $booking) }}" class="block rounded-[24px] border border-slate-200/80 p-4 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-950/40">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->client?->full_name ?? 'Mijoz yo‘q' }}</p>
                                        <p class="mt-1 truncate text-xs text-slate-500">{{ $booking->hall?->name ?? 'Zal yo‘q' }} · {{ $booking->eventType?->name ?? 'Tadbir turi yo‘q' }}</p>
                                    </div>
                                    <x-status-badge :status="$booking->status" />
                                </div>
                                <p class="mt-3 text-xs text-slate-400">{{ $booking->event_date?->format('d.m.Y') }} · {{ substr((string) $booking->start_time, 0, 5) }} - {{ substr((string) $booking->end_time, 0, 5) }}</p>
                            </a>
                        @empty
                            <x-admin.empty-state icon="calendar-off" title="Yaqin bronlar yo'q" text="Joriy va kelgusi kunlar uchun bronlar yo'qligida shu blok bo'sh turadi." />
                        @endforelse
                    </div>
                </x-admin.section-card>

                <x-admin.section-card title="Zallar bo'yicha bandlik" subtitle="Zal holati va sig'imni tez ko'rish." icon="building-2">
                    <div class="space-y-3">
                        @foreach($halls as $hall)
                            <div class="flex items-center justify-between rounded-[22px] border border-slate-200/80 px-4 py-3 dark:border-slate-800">
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $hall->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $hall->capacity }} mehmon sig'imi</p>
                                </div>
                                <x-status-badge :status="$hall->status" />
                            </div>
                        @endforeach
                    </div>
                </x-admin.section-card>
            </div>
        </div>
    </div>
</x-app-layout>
