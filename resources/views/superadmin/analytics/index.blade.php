<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-2">
        @foreach ([
            ['title' => 'Revenue trend', 'series' => $analytics['revenue_trend'], 'icon' => 'banknote'],
            ['title' => 'New businesses', 'series' => $analytics['business_trend'], 'icon' => 'building-2'],
            ['title' => 'Active subscriptions', 'series' => $analytics['subscription_trend'], 'icon' => 'repeat'],
            ['title' => 'Payment failures', 'series' => $analytics['failure_trend'], 'icon' => 'x-circle'],
        ] as $card)
            <x-superadmin.panel :title="$card['title']" subtitle="Last 6 months" :icon="$card['icon']">
                <div class="flex h-52 items-end gap-3">
                    @php($max = max(collect($card['series'])->pluck('value')->max(), 1))
                    @foreach($card['series'] as $point)
                        <div class="flex flex-1 flex-col items-center gap-3">
                            <div class="flex h-40 w-full items-end rounded-2xl bg-slate-50 p-2">
                                <span class="sa-chart-bar block w-full rounded-2xl" style="height: {{ max(($point['value'] / $max) * 100, 8) }}%"></span>
                            </div>
                            <div class="text-center text-xs text-slate-500">
                                <div class="font-semibold text-slate-700">{{ $point['label'] }}</div>
                                <div>{{ number_format($point['value'], 0, '.', ' ') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-superadmin.panel>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_1fr]">
        <x-superadmin.panel title="Approval conversion" subtitle="Monthly tasdiqlash foizi." icon="badge-check">
            <div class="space-y-3">
                @forelse($analytics['approval_conversion'] as $point)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-semibold text-slate-900">{{ $point['label'] }}</p>
                            <x-superadmin.status-badge status="info" :label="$point['value'].'%'" />
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-sky-500" style="width: {{ min($point['value'], 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <x-superadmin.empty-state icon="badge-check" title="Approval conversion ma'lumoti yo'q" description="Oylik tasdiqlash ko'rsatkichlari shu bo'limda chiqadi." />
                @endforelse
            </div>
        </x-superadmin.panel>

        <x-superadmin.panel title="Top va inactive businesses" subtitle="Revenue liderlari va so'nggi paytda sust tenantlar." icon="briefcase-business">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Top performers</h3>
                    @forelse($analytics['top_businesses'] as $business)
                        <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                            <p class="font-semibold text-slate-900">{{ $business->venue_name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ number_format((float) $business->revenue_total, 0, '.', ' ') }} UZS</p>
                        </div>
                    @empty
                        <x-superadmin.empty-state icon="trophy" title="Top performers yo'q" description="Revenue signal paydo bo'lgach bu yer to'ladi." />
                    @endforelse
                </div>
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Recently inactive</h3>
                    @forelse($analytics['inactive_businesses'] as $business)
                        <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                            <p class="font-semibold text-slate-900">{{ $business->venue_name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $business->last_seen_at?->diffForHumans() ?? "Signal yo'q" }}</p>
                        </div>
                    @empty
                        <x-superadmin.empty-state icon="moon-star" title="Inactive business yo'q" description="Hozircha nofaol tenant aniqlanmadi." />
                    @endforelse
                </div>
            </div>
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
