<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Faol xizmatlar" :value="$serviceSummary['serviceCount']" icon="sparkles" />
        <x-stat-card title="Xizmatli bronlar" :value="$serviceSummary['serviceBookings']" icon="clipboard-list" />
        <x-stat-card title="Jami tanlov" :value="$serviceSummary['serviceQuantity']" icon="badge-plus" />
        <x-stat-card title="Xizmat aylanmasi" :value="number_format($serviceSummary['serviceRevenue'], 0, '.', ' ')" suffix="so'm" icon="wallet" />
    </div>

    <div class="grid gap-6 2xl:grid-cols-[1.1fr_0.9fr]">
        <x-admin.section-card icon="sparkles" title="Eng ko'p tanlangan xizmatlar" subtitle="Bronlarda eng ko'p ishlatilayotgan xizmatlar reytingi.">
            <div class="space-y-3">
                @forelse($topServices as $service)
                    <div class="rounded-[28px] border border-slate-200/80 p-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $service->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ (int) $service->booking_count }} ta bron ichida tanlangan</p>
                            </div>
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">{{ (int) $service->total_quantity }} marta</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3 text-xs text-slate-500">
                            <span>Xizmat aylanmasi</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($service->total_amount, 0, '.', ' ') }} so'm</span>
                        </div>
                    </div>
                @empty
                    <x-admin.empty-state icon="sparkles" title="Xizmat statistikasi yo'q" text="Bronlarga xizmatlar qo'shilganda ushbu bo'lim to'ladi." />
                @endforelse
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="briefcase-business" title="Xizmat summary" subtitle="Tijoriy va operatsion ko'rinishdagi qisqa xulosa.">
            <div class="space-y-4">
                <div class="rounded-[28px] bg-gradient-to-br from-sky-500 to-blue-600 p-5 text-white shadow-soft">
                    <p class="text-xs uppercase tracking-[0.24em] text-sky-100">Eng yuqori activity</p>
                    <p class="mt-3 text-2xl font-semibold">{{ $topServices->first()?->name ?? 'Maʼlumot yoʻq' }}</p>
                    <p class="mt-2 text-sm text-sky-100">Eng ko'p tanlangan xizmat tenant uchun asosiy upsell nuqtasi bo'lib turibdi.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="text-xs text-slate-400">Bron qamrovi</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $serviceSummary['serviceBookings'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">Xizmat qo'shilgan bronlar soni.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="text-xs text-slate-400">O'rtacha tanlov</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $serviceSummary['serviceBookings'] > 0 ? number_format($serviceSummary['serviceQuantity'] / $serviceSummary['serviceBookings'], 1, '.', ' ') : '0.0' }}</p>
                        <p class="mt-1 text-xs text-slate-500">Bir bron uchun o'rtacha xizmat soni.</p>
                    </div>
                </div>
            </div>
        </x-admin.section-card>
    </div>
</div>
