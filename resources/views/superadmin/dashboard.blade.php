<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5">
        <x-superadmin.stat-card title="Jami bizneslar" :value="number_format($dashboard['kpis']['total_businesses'])" icon="building-2" tone="blue" />
        <x-superadmin.stat-card title="Tasdiq kutayotganlar" :value="number_format($dashboard['kpis']['pending_approvals'])" icon="badge-check" tone="amber" />
        <x-superadmin.stat-card title="Faol obunalar" :value="number_format($dashboard['kpis']['active_subscriptions'])" icon="repeat" tone="green" />
        <x-superadmin.stat-card title="Oylik revenue" :value="number_format($dashboard['kpis']['monthly_revenue'], 0, '.', ' ').' UZS'" icon="banknote" tone="green" />
        <x-superadmin.stat-card title="To'lov success rate" :value="$dashboard['kpis']['payment_success_rate'].'%'" icon="circle-dollar-sign" tone="slate" :hint="$dashboard['kpis']['failed_payments'].' ta failed attempt'" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.6fr_1fr]">
        <x-superadmin.panel title="Platforma pulse" subtitle="So'nggi 6 oy kesimidagi asosiy KPI dinamikasi." icon="activity">
            <div class="grid gap-6 lg:grid-cols-2">
                @foreach ([
                    ['title' => 'Bizneslar trendi', 'series' => $dashboard['trends']['businesses'], 'suffix' => 'ta', 'tone' => 'sa-chart-bar'],
                    ['title' => 'Revenue trendi', 'series' => $dashboard['trends']['revenue'], 'suffix' => ' UZS', 'tone' => 'sa-grid-chart-bar'],
                ] as $chart)
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-sm font-semibold text-slate-900">{{ $chart['title'] }}</h3>
                            <span class="text-xs text-slate-500">6 oy</span>
                        </div>
                        <div class="mt-5 flex h-48 items-end gap-3">
                            @php($max = max(collect($chart['series'])->pluck('value')->max(), 1))
                            @foreach($chart['series'] as $point)
                                <div class="flex flex-1 flex-col items-center gap-3">
                                    <div class="relative flex h-36 w-full items-end justify-center rounded-2xl bg-white px-2 py-2">
                                        <span class="{{ $chart['tone'] }} block w-full rounded-2xl" style="height: {{ max(($point['value'] / $max) * 100, 8) }}%"></span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs font-semibold text-slate-700">{{ $point['label'] }}</div>
                                        <div class="text-[11px] text-slate-500">{{ number_format($point['value'], 0, '.', ' ') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-superadmin.panel>

        <x-superadmin.panel title="Tizim xulosasi" subtitle="Quick health summary va tezkor harakatlar." icon="shield-check">
            <div class="space-y-3">
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Tasdiqlangan bizneslar <strong class="text-slate-950">{{ $dashboard['kpis']['approved_businesses'] }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Rad etilgan yoki to'xtatilgan <strong class="text-slate-950">{{ $dashboard['kpis']['rejected_or_suspended'] }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Expired obunalar <strong class="text-slate-950">{{ $dashboard['kpis']['expired_subscriptions'] }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Enabled payment methods <strong class="text-slate-950">{{ $dashboard['system_status']['payment_methods_enabled'] }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Telegram integratsiyasi <strong class="text-slate-950">{{ $dashboard['system_status']['telegram_configured'] ? 'Faol' : 'Sozlanmagan' }}</strong></div>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('superadmin.approvals.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Tasdiqlarni ko'rish</a>
                <a href="{{ route('superadmin.integrations.edit') }}" class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">Telegram sozlash</a>
            </div>
        </x-superadmin.panel>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_1fr_1fr]">
        <x-superadmin.panel title="So'nggi ro'yxatlar" subtitle="Yangi venue va onboarding oqimi." icon="building-2">
            @forelse($dashboard['recent_registrations'] as $item)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $item->venue_name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $item->owner_name }}  -  {{ $item->phone ?: $item->username }}</p>
                    </div>
                    <x-superadmin.status-badge :status="$item->status" />
                </div>
            @empty
                <x-superadmin.empty-state icon="building" title="Yangi bizneslar hali yo'q" description="Ro'yxatdan o'tgan yangi venue paydo bo'lganda shu yerda ko'rinadi." />
            @endforelse
        </x-superadmin.panel>

        <x-superadmin.panel title="Recent payment activity" subtitle="Yaqin to'lovlar oqimi." icon="wallet">
            @forelse($dashboard['recent_payments'] as $payment)
                <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $payment->venueConnection?->venue_name ?? 'Biznes biriktirilmagan' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ number_format((float) $payment->amount, 0, '.', ' ') }} {{ $payment->currency }}  -  {{ $payment->paymentMethod?->label ?? "Usul yo'q" }}</p>
                        </div>
                        <x-superadmin.status-badge :status="$payment->status" />
                    </div>
                </div>
            @empty
                <x-superadmin.empty-state icon="wallet-cards" title="To'lovlar hali yo'q" description="Birinchi billing yozuvi yaratilgach payment activity blokida ko'rinadi." />
            @endforelse
        </x-superadmin.panel>

        <x-superadmin.panel title="Muhim alertlar" subtitle="Operational signal va unread holatlar." icon="triangle-alert">
            @forelse($dashboard['alerts'] as $alert)
                <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $alert->title }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $alert->description }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ optional($alert->occurred_at)->diffForHumans() }}</p>
                        </div>
                        <x-superadmin.status-badge :status="$alert->status" />
                    </div>
                </div>
            @empty
                <x-superadmin.empty-state icon="shield-check" title="Muhim alertlar yo'q" description="Platforma barqaror ishlayapti. Yangi alertlar shu blokda ko'rinadi." />
            @endforelse
        </x-superadmin.panel>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.3fr_1fr]">
        <x-superadmin.panel title="Obuna planlari taqsimoti" subtitle="Har bir tarif bo'yicha tenant ulushi." icon="pie-chart">
            @forelse($dashboard['subscription_distribution'] as $plan)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $plan->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $plan->description }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-slate-950">{{ $plan->subscriptions_count }}</p>
                        <p class="text-xs text-slate-500">tenant</p>
                    </div>
                </div>
            @empty
                <x-superadmin.empty-state icon="repeat" title="Planlar bo'yicha ma'lumot yo'q" description="Obunalar yaratilganda taqsimot shu yerda ko'rinadi." />
            @endforelse
        </x-superadmin.panel>

        <x-superadmin.panel title="Recent activity" subtitle="Platformadagi so'nggi event stream." icon="rss">
            @forelse($dashboard['recent_activity'] as $event)
                <div class="flex gap-3 {{ !$loop->last ? 'mb-4' : '' }}">
                    <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <i data-lucide="{{ $event['icon'] }}" class="h-4 w-4"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="truncate font-semibold text-slate-900">{{ $event['title'] }}</p>
                            <x-superadmin.status-badge :status="$event['status']" class="scale-90" />
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ $event['description'] }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ optional($event['time'])->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <x-superadmin.empty-state icon="activity" title="Activity feed bo'sh" description="Tizimda muhim eventlar paydo bo'lgach activity stream shu yerda chiqadi." />
            @endforelse
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
