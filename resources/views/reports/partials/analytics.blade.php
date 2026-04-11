<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Jami tushum" :value="number_format($analyticsSummary['totalRevenue'], 0, '.', ' ')" suffix="so'm" icon="wallet" />
        <x-stat-card title="Oylik tushum" :value="number_format($analyticsSummary['monthlyRevenue'], 0, '.', ' ')" suffix="so'm" icon="badge-dollar-sign" />
        <x-stat-card title="Oylik xarajat" :value="number_format($analyticsSummary['monthlyExpenses'], 0, '.', ' ')" suffix="so'm" icon="receipt-text" />
        <x-stat-card title="Sof balans" :value="number_format($analyticsSummary['netBalance'], 0, '.', ' ')" suffix="so'm" icon="scale" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <x-admin.section-card icon="layout-grid" title="Asosiy KPI overview" subtitle="Qaror qabul qilish uchun eng muhim tenant ko'rsatkichlari.">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Faol bronlar</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $analyticsSummary['activeBookings'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Jarayonda yoki tasdiqlangan bronlar.</p>
                </div>
                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Yaqin tadbirlar</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $analyticsSummary['upcomingBookings'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Kelgusi sanalardagi ish yuklamasi.</p>
                </div>
                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Qarzdor bronlar</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $analyticsSummary['debtBookings'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Qolgan to'lovi yopilmagan buyurtmalar.</p>
                </div>
                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">O'rtacha чек</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($analyticsSummary['averageBookingValue'], 0, '.', ' ') }}</p>
                    <p class="mt-2 text-sm text-slate-500">Bir bron uchun o'rtacha tushum hajmi.</p>
                </div>
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="activity" title="Operatsion insight" subtitle="Bugungi nazorat uchun qisqa signal bloklari.">
            <div class="space-y-4">
                <div class="rounded-[28px] bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-soft">
                    <p class="text-xs uppercase tracking-[0.24em] text-emerald-100">Oylik marja</p>
                    <p class="mt-3 text-3xl font-semibold">{{ number_format($analyticsSummary['netBalance'], 0, '.', ' ') }} so'm</p>
                    <p class="mt-2 text-sm text-emerald-100">Tushum va xarajat orasidagi joriy balans.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="text-xs text-slate-400">O'rtacha mehmon</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ number_format($analyticsSummary['averageGuests'], 1, '.', ' ') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="text-xs text-slate-400">Kam qoldiq</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $lowStockCount }} ta</p>
                    </div>
                </div>
            </div>
        </x-admin.section-card>
    </div>

    <div class="grid gap-6 2xl:grid-cols-3">
        <x-admin.section-card class="2xl:col-span-2" icon="receipt-text" title="Xarajat kategoriyalari" subtitle="Qaysi yo'nalishlar budjetga ko'proq ta'sir qilayotganini ko'rsatadi.">
            <div class="grid gap-3 md:grid-cols-2">
                @forelse($expenseByCategory as $category)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 px-4 py-3 text-sm dark:border-slate-800">
                        <span class="min-w-0 break-words">{{ $category->name }}</span>
                        <x-money :value="$category->total" class="font-semibold" />
                    </div>
                @empty
                    <x-admin.empty-state icon="receipt-text" title="Xarajat kategoriyalari yo'q" text="Xarajatlar kiritilganda kategoriya kesimi shu yerda ko'rinadi." />
                @endforelse
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="truck" title="Ta'minotchi yuklamasi" subtitle="Eng katta aylanmaga ega hamkorlar.">
            <div class="space-y-3">
                @forelse($topSuppliers as $supplier)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <span class="min-w-0 break-words text-sm font-medium">{{ $supplier->full_name }}</span>
                            <x-money :value="$supplier->balance" class="text-sm font-semibold" />
                        </div>
                        <div class="mt-2 grid gap-1 text-xs text-slate-500">
                            <p>Kirim: {{ number_format($supplier->purchases_sum_total_amount ?? 0, 0, '.', ' ') }} so'm</p>
                            <p>To'lov: {{ number_format($supplier->payments_sum_amount ?? 0, 0, '.', ' ') }} so'm</p>
                        </div>
                    </div>
                @empty
                    <x-admin.empty-state icon="truck" title="Ta'minotchi ma'lumoti yo'q" text="Faol hamkorlar paydo bo'lganda shu blok to'ladi." />
                @endforelse
            </div>
        </x-admin.section-card>
    </div>
</div>
