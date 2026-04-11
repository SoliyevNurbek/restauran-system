<x-app-layout title="Bosh sahifa" pageTitle="Bosh sahifa" pageSubtitle="Bugungi bronlar, tushum, xarajat va ustuvor vazifalarni bir qarashda boshqaring.">
    <div class="space-y-6">
        <x-admin.page-intro eyebrow="Toyxona operatsiyalari" title="Bugungi muhim ko'rsatkichlar" subtitle="Toyxona egasi va operatori uchun eng muhim raqamlar, yaqin tadbirlar va tezkor signal bloklari shu sahifada jamlandi.">
            <x-slot:actions>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        Yangi bron
                    </a>
                    <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        <i data-lucide="calendar-days" class="h-4 w-4"></i>
                        Kalendar
                    </a>
                </div>
            </x-slot:actions>
        </x-admin.page-intro>

        @php
            $nextBillingDate = $tenantSubscription?->renews_at ?? $tenantSubscription?->trial_ends_at ?? $tenantSubscription?->expires_at;
            $remainingDays = $nextBillingDate ? max(now()->startOfDay()->diffInDays($nextBillingDate->copy()->startOfDay(), false), 0) : null;
        @endphp

        <div class="grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
            <x-admin.section-card title="Tezkor amallar" subtitle="Ko'p ishlatiladigan amallarni bir bosishda boshlang." icon="zap">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <a href="{{ route('bookings.create') }}" class="rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:-translate-y-0.5 hover:bg-white dark:border-slate-700 dark:bg-slate-950/60 dark:hover:bg-slate-900">
                        <i data-lucide="calendar-plus" class="h-5 w-5 text-slate-600 dark:text-slate-300"></i>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">Yangi bron</p>
                        <p class="mt-1 text-xs text-slate-500">Band kunni darhol rasmiylashtiring.</p>
                    </a>
                    <a href="{{ route('payments.create') }}" class="rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:-translate-y-0.5 hover:bg-white dark:border-slate-700 dark:bg-slate-950/60 dark:hover:bg-slate-900">
                        <i data-lucide="wallet" class="h-5 w-5 text-slate-600 dark:text-slate-300"></i>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">To'lov qo'shish</p>
                        <p class="mt-1 text-xs text-slate-500">Tushumni bir zumda kiriting.</p>
                    </a>
                    <a href="{{ route('clients.create') }}" class="rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:-translate-y-0.5 hover:bg-white dark:border-slate-700 dark:bg-slate-950/60 dark:hover:bg-slate-900">
                        <i data-lucide="user-plus" class="h-5 w-5 text-slate-600 dark:text-slate-300"></i>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">Mijoz qo'shish</p>
                        <p class="mt-1 text-xs text-slate-500">Yangi mijoz bazasini kengaytiring.</p>
                    </a>
                    <a href="{{ route('inventory-expenses.create') }}" class="rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:-translate-y-0.5 hover:bg-white dark:border-slate-700 dark:bg-slate-950/60 dark:hover:bg-slate-900">
                        <i data-lucide="receipt-text" class="h-5 w-5 text-slate-600 dark:text-slate-300"></i>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">Xarajat qo'shish</p>
                        <p class="mt-1 text-xs text-slate-500">Sarf yozuvini darhol belgilang.</p>
                    </a>
                    <a href="{{ route('products.create') }}" class="rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 transition hover:-translate-y-0.5 hover:bg-white dark:border-slate-700 dark:bg-slate-950/60 dark:hover:bg-slate-900">
                        <i data-lucide="package-plus" class="h-5 w-5 text-slate-600 dark:text-slate-300"></i>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">Mahsulot qo'shish</p>
                        <p class="mt-1 text-xs text-slate-500">Omborga yangi pozitsiya kiriting.</p>
                    </a>
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="SaaS hisob holati" subtitle="Tarif va billing holatini tez ko'rish uchun qisqa blok." icon="credit-card">
                @if($tenantSubscription)
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[24px] bg-slate-50 px-4 py-4 dark:bg-slate-950/60">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reja</p>
                            <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $tenantSubscription->plan?->name ?? 'Trial' }}</p>
                        </div>
                        <div class="rounded-[24px] bg-slate-50 px-4 py-4 dark:bg-slate-950/60">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Holat</p>
                            <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ ucfirst($tenantSubscription->status ?? 'trial') }}</p>
                        </div>
                        <div class="rounded-[24px] bg-slate-50 px-4 py-4 dark:bg-slate-950/60">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tugash sanasi</p>
                            <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ ($tenantSubscription->expires_at ?? $tenantSubscription->trial_ends_at)?->format('d M Y') ?? "Noma'lum" }}</p>
                        </div>
                        <div class="rounded-[24px] bg-slate-50 px-4 py-4 dark:bg-slate-950/60">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Keyingi to'lov</p>
                            <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $nextBillingDate?->format('d M Y') ?? "Noma'lum" }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('billing.payments.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <i data-lucide="credit-card" class="h-4 w-4"></i>
                            To'lov qilish
                        </a>
                        <a href="{{ route('billing.plans.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            Tarifni o'zgartirish
                        </a>
                    </div>
                @elseif($pendingBillingPayment)
                    <div class="space-y-3">
                        <div class="rounded-[24px] bg-amber-50 px-4 py-4 dark:bg-amber-950/20">
                            <p class="text-xs uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">To‘lov kutilmoqda</p>
                            <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $pendingBillingPayment->plan?->name ?? 'Tarif tanlangan' }}</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                                {{ match ($pendingBillingPayment->status) {
                                    'pending' => "Manual to‘lov yaratildi. Bot orqali yo‘riqnoma olinishi kutilmoqda.",
                                    'payment_details_sent' => "To‘lov ma’lumotlari yuborildi. Chekni Telegram botga yuboring.",
                                    'awaiting_proof' => "Chek yuborilishi kutilmoqda.",
                                    default => "Chek qabul qilindi va ko‘rib chiqilmoqda.",
                                } }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('billing.checkout.show', $pendingBillingPayment) }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                                <i data-lucide="send" class="h-4 w-4"></i>
                                Chek yuborish
                            </a>
                            <a href="{{ route('billing.payments.index', ['highlight' => $pendingBillingPayment->id]) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                <i data-lucide="history" class="h-4 w-4"></i>
                                Holatni ko‘rish
                            </a>
                        </div>
                @else
                    <x-admin.empty-state icon="credit-card" title="Tarif ulanmagan" text="Billing tizimi tayyor. Obuna boshlash uchun tarif tanlang va to‘lovni yakunlang." action-href="{{ route('billing.plans.index') }}" action-label="Tarif tanlash" />
                @endif
            </x-admin.section-card>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-8">
            <x-stat-card title="Bugungi bronlar" :value="$stats['todayBookings']" icon="calendar-days" />
            <x-stat-card title="Yaqin tadbirlar" :value="$stats['upcomingBookings']" icon="clock-3" />
            <x-stat-card title="Oylik tushum" :value="number_format($stats['monthlyRevenue'], 0, '.', ' ')" suffix="UZS" icon="wallet" />
            <x-stat-card title="Oylik xarajat" :value="number_format($stats['monthlyExpenses'], 0, '.', ' ')" suffix="UZS" icon="receipt-text" />
            <x-stat-card title="Sof foyda" :value="number_format($stats['monthlyProfit'], 0, '.', ' ')" suffix="UZS" icon="wallet" />
            <x-stat-card title="Qarzdor mijozlar" :value="$stats['debtClients']" icon="hand-coins" />
            <x-stat-card title="Faol buyurtmalar" :value="$stats['activeBookings']" icon="clipboard-list" />
            <x-stat-card title="Kam qoldiq" :value="$stats['lowStockCount']" icon="triangle-alert" />
        </div>

        <div class="grid gap-6 2xl:grid-cols-[1.55fr_1fr]">
            <x-admin.section-card title="Haftalik bronlar oqimi" subtitle="So'nggi 7 kunda bronlar va xarajatlar qanchalik faol bo'lganini ko'rsatadi." icon="bar-chart-3">
                <div class="grid gap-4 xl:grid-cols-[1.3fr_0.7fr]">
                    <div class="rounded-[26px] bg-slate-50 p-4 dark:bg-slate-950/60">
                        <canvas id="weeklyFlowChart" height="120"></canvas>
                    </div>
                    <div class="grid gap-3">
                        <div class="rounded-[24px] bg-emerald-50 p-4 dark:bg-emerald-950/20">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-600 dark:text-emerald-300">Hafta davomida bron</p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ collect($bookingValues)->sum() }}</p>
                            <p class="mt-1 text-sm text-emerald-700/70 dark:text-emerald-200/70">Operatorlar uchun joriy yuklama kesimi.</p>
                        </div>
                        <div class="rounded-[24px] bg-rose-50 p-4 dark:bg-rose-950/20">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-600 dark:text-rose-300">Hafta xarajati</p>
                            <p class="mt-2 text-2xl font-semibold text-rose-700 dark:text-rose-300">{{ number_format(collect($expenseValues)->sum(), 0, '.', ' ') }} UZS</p>
                            <p class="mt-1 text-sm text-rose-700/70 dark:text-rose-200/70">Sarf-intizomni tez ko'rish uchun qisqa blok.</p>
                        </div>
                    </div>
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="Tezkor ogohlantirishlar" subtitle="Bugun e'tibor talab qiladigan holatlar." icon="bell-ring">
                <div class="space-y-3">
                    @forelse($urgentAlerts as $alert)
                        <div class="rounded-[24px] border px-4 py-4 {{ $alert['status'] === 'danger' ? 'border-red-200 bg-red-50 dark:border-red-900/40 dark:bg-red-950/20' : ($alert['status'] === 'warning' ? 'border-amber-200 bg-amber-50 dark:border-amber-900/40 dark:bg-amber-950/20' : 'border-blue-200 bg-blue-50 dark:border-blue-900/40 dark:bg-blue-950/20') }}">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-white/80 text-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                                    <i data-lucide="{{ $alert['icon'] }}" class="h-5 w-5"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $alert['title'] }}</p>
                                        <span class="rounded-full bg-white/80 px-2.5 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-900/70 dark:text-slate-300">{{ $alert['badge'] }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $alert['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-admin.empty-state icon="shield-check" title="Hozircha muhim ogohlantirish yo'q" text="Panel bo'yicha jiddiy xatar yoki kechikayotgan vazifa topilmadi." />
                    @endforelse
                </div>
            </x-admin.section-card>
        </div>

        <div class="grid gap-6 2xl:grid-cols-[1.3fr_0.7fr]">
            <x-admin.section-card title="Tushum va xarajat trendlari" subtitle="So'nggi 6 oy bo'yicha pul oqimi solishtirmasi." icon="bar-chart-3">
                <div class="rounded-[26px] bg-slate-50 p-4 dark:bg-slate-950/60">
                    <canvas id="monthlyFinanceChart" height="120"></canvas>
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="Yaqinlashayotgan tadbirlar" subtitle="Tasdiqlangan va yaqin sanali bronlar ro'yxati." icon="calendar-days">
                <div class="space-y-3">
                    @forelse($upcomingEvents as $booking)
                        <div class="rounded-[24px] border border-slate-200/80 p-4 dark:border-slate-800">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->client?->full_name ?? 'Mijoz biriktirilmagan' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $booking->hall?->name ?? 'Zal yo‘q' }} · {{ $booking->eventType?->name ?? 'Tadbir turi yo‘q' }}</p>
                                </div>
                                <x-status-badge :status="$booking->status" />
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-500">
                                <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    Sana: {{ optional($booking->event_date)->format('d.m.Y') }}
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    Mehmon: {{ $booking->guest_count }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-admin.empty-state icon="calendar-off" title="Yaqin tadbir topilmadi" text="Kelasi kunlar uchun bronlar kiritilmagan yoki hammasi yakunlangan." action-href="{{ route('bookings.create') }}" action-label="Yangi bron qo'shish" />
                    @endforelse
                </div>
            </x-admin.section-card>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-admin.section-card title="So'nggi to'lovlar" subtitle="Yaqinda qabul qilingan tushum yozuvlari." icon="wallet-cards">
                <div class="space-y-3">
                    @forelse($latestPayments as $payment)
                        <div class="rounded-[24px] border border-slate-200/80 p-4 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $payment->booking?->client?->full_name ?? 'Mijoz' }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $payment->booking?->booking_number ?? 'Bron raqami yo‘q' }} · {{ $payment->payment_method }}</p>
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ number_format($payment->amount, 0, '.', ' ') }} UZS</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-400">{{ optional($payment->payment_date)->format('d.m.Y') }}</p>
                        </div>
                    @empty
                        <x-admin.empty-state icon="wallet" title="To'lovlar hali yo'q" text="Moliya bo'limida tushum yozuvlari paydo bo'lgach shu yerda ko'rinadi." />
                    @endforelse
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="So'nggi xarajatlar" subtitle="Yangi kiritilgan sarf yozuvlari." icon="receipt-text">
                <div class="space-y-3">
                    @forelse($recentExpenses as $expense)
                        <div class="rounded-[24px] border border-slate-200/80 p-4 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $expense->title }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $expense->category?->name ?? 'Kategoriya yo‘q' }}</p>
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ number_format($expense->amount, 0, '.', ' ') }} UZS</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-400">{{ optional($expense->expense_date)->format('d.m.Y') }}</p>
                        </div>
                    @empty
                        <x-admin.empty-state icon="receipt" title="Xarajatlar yozilmagan" text="Sarf yozuvlari shu bo'limda ketma-ket ko'rinadi." />
                    @endforelse
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="Kam qolgan mahsulotlar" subtitle="Minimal limitga yaqinlashgan pozitsiyalar." icon="package-search">
                <div class="space-y-3">
                    @forelse($lowStockProducts as $product)
                        <div class="rounded-[24px] border border-slate-200/80 p-4 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $product->category }} · {{ $product->subcategory }}</p>
                                </div>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Kam qoldi</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                <span>Qoldiq: {{ number_format($product->current_stock, 3) }} {{ $product->unit }}</span>
                                <span>Minimal: {{ number_format($product->minimum_stock, 3) }}</span>
                            </div>
                        </div>
                    @empty
                        <x-admin.empty-state icon="package-check" title="Ombor holati yaxshi" text="Hozircha minimal limitdan past mahsulot topilmadi." />
                    @endforelse
                </div>
            </x-admin.section-card>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-admin.section-card title="Eng faol mijozlar" subtitle="Bronlar soni bo'yicha yuqori segment." icon="users">
                <div class="space-y-3">
                    @forelse($topClients as $client)
                        <div class="flex items-center justify-between gap-3 rounded-[24px] border border-slate-200/80 px-4 py-4 dark:border-slate-800">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $client->full_name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $client->phone ?: 'Telefon ko‘rsatilmagan' }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $client->bookings_count }} ta bron</span>
                        </div>
                    @empty
                        <x-admin.empty-state icon="users" title="Mijozlar faolligi hali shakllanmagan" text="Bronlar ko'paygan sari eng faol mijozlar shu yerda ko'rinadi." />
                    @endforelse
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="Eng faol xizmatlar" subtitle="Bronlar ichida eng ko'p tanlangan xizmatlar." icon="sparkles">
                <div class="space-y-3">
                    @forelse($topServices as $service)
                        <div class="flex items-center justify-between gap-3 rounded-[24px] border border-slate-200/80 px-4 py-4 dark:border-slate-800">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $service->name }}</p>
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/40 dark:text-blue-300">{{ (int) $service->total_quantity }} marta</span>
                        </div>
                    @empty
                        <x-admin.empty-state icon="sparkles" title="Xizmat statistikasi yo'q" text="Paket va xizmat tanlovlari to'plangach bu yerda ko'rinadi." />
                    @endforelse
                </div>
            </x-admin.section-card>

            <x-admin.section-card title="Zallar bandligi" subtitle="Joriy oy bo'yicha bandlik ko'rsatkichi." icon="building-2">
                <div class="space-y-3">
                    @forelse($hallOccupancy as $hall)
                        <div class="rounded-[24px] border border-slate-200/80 px-4 py-4 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $hall->name }}</p>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ $hall->monthly_bookings_count }} ta bron</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-2 rounded-full bg-slate-900 dark:bg-white" style="width: {{ min(($hall->monthly_bookings_count / max($hallOccupancy->max('monthly_bookings_count'), 1)) * 100, 100) }}%"></div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Sig'im: {{ $hall->capacity }} mehmon · Narx: {{ number_format($hall->price, 0, '.', ' ') }} UZS</p>
                        </div>
                    @empty
                        <x-admin.empty-state icon="building" title="Bandlik statistikasi yo'q" text="Bronlar tushishi bilan zallar kesimidagi yuklama shu yerda chiqadi." />
                    @endforelse
                </div>
            </x-admin.section-card>
        </div>
    </div>

    <script>
        const weeklyChart = document.getElementById('weeklyFlowChart');
        if (weeklyChart) {
            new Chart(weeklyChart, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Bronlar',
                            data: @json($bookingValues),
                            borderColor: '#0f172a',
                            backgroundColor: 'rgba(15, 23, 42, 0.08)',
                            fill: true,
                            tension: 0.35
                        },
                        {
                            label: 'Xarajat',
                            data: @json($expenseValues),
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.08)',
                            fill: true,
                            tension: 0.35
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,.12)' } }
                    }
                }
            });
        }

        const monthlyChart = document.getElementById('monthlyFinanceChart');
        if (monthlyChart) {
            new Chart(monthlyChart, {
                type: 'bar',
                data: {
                    labels: @json($monthlyChartLabels),
                    datasets: [
                        {
                            label: 'Tushum',
                            data: @json($monthlyChartRevenue),
                            backgroundColor: 'rgba(16, 185, 129, 0.9)',
                            borderRadius: 14,
                            borderSkipped: false
                        },
                        {
                            label: 'Xarajat',
                            data: @json($monthlyChartExpenses),
                            backgroundColor: 'rgba(244, 63, 94, 0.82)',
                            borderRadius: 14,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,.12)' } }
                    }
                }
            });
        }
    </script>
</x-app-layout>
