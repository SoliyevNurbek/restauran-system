<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Oylik bronlar" :value="$currentMonthBookings" icon="calendar-days" />
        <x-stat-card title="Faol zallar" :value="$hallOccupancy->count()" icon="building-2" />
        <x-stat-card title="O'rtacha mehmon" :value="number_format($avgGuestsPerBooking, 1, '.', ' ')" icon="users" />
        <x-stat-card title="Eng band zal" :value="$busiestHall?->name ?? '—'" icon="badge-check" />
    </div>

    <div class="grid gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
        <x-admin.section-card icon="building-2" title="Zallar bandligi" subtitle="Joriy oy bo'yicha zal ishlatilish taqsimoti.">
            <div class="space-y-4">
                @forelse($hallOccupancy as $hall)
                    <div class="rounded-[28px] border border-slate-200/80 p-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $hall->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">Sig'im: {{ $hall->capacity }} mehmon · Narx: {{ number_format($hall->price, 0, '.', ' ') }} so'm</p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ $hall->monthly_bookings_count }} ta bron</span>
                        </div>
                        <div class="mt-4 h-2 rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-2 rounded-full bg-slate-900 dark:bg-white" style="width: {{ min(($hall->monthly_bookings_count / max($hallOccupancy->max('monthly_bookings_count'), 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-admin.empty-state icon="building-2" title="Bandlik statistikasi yo'q" text="Bronlar paydo bo'lganda zallar yuklamasi shu yerda ko'rinadi." />
                @endforelse
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="calendar-range" title="Hafta kunlari bo'yicha yuklama" subtitle="Qaysi kunlarda tadbir zichligi yuqori ekanini ko'rsatadi.">
            <div class="space-y-3">
                @foreach($weekdayDensity as $day)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $day['label'] }}</span>
                            <span class="text-slate-500">{{ $day['total'] }} ta bron</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-2 rounded-full bg-primary-600" style="width: {{ min(($day['total'] / max($weekdayDensity->max('total'), 1)) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.section-card>
    </div>

    <x-admin.section-card icon="clock-3" title="Yaqin tadbirlar" subtitle="Zallar kesimidagi yaqin yuklama va rezerv holati.">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @forelse($upcomingEvents as $booking)
                <div class="rounded-[28px] border border-slate-200/80 p-4 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->hall?->name ?? 'Zal biriktirilmagan' }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $booking->client?->full_name ?? 'Mijoz' }}</p>
                    <div class="mt-4 space-y-2 text-xs text-slate-500">
                        <p>Sana: {{ optional($booking->event_date)->format('d.m.Y') }}</p>
                        <p>Turi: {{ $booking->eventType?->name ?? 'Belgilanmagan' }}</p>
                        <p>Mehmon: {{ $booking->guest_count }}</p>
                    </div>
                </div>
            @empty
                <x-admin.empty-state icon="calendar-off" title="Yaqin tadbirlar topilmadi" text="Kelgusi bandlik ro'yxati shu yerda ko'rinadi." />
            @endforelse
        </div>
    </x-admin.section-card>
</div>
