<x-app-layout title="Obunalar" pageTitle="Obunalar" pageSubtitle="Joriy tarif, renewal sanalari va obuna tarixini shu yerda kuzating.">
    <div class="space-y-6">
        <x-admin.page-intro eyebrow="SaaS Billing" title="Obunalar" subtitle="Faol, trial va tarixiy subscription yozuvlari bo‘yicha to‘liq ko‘rinish.">
            <x-slot:actions>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('billing.plans.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        <i data-lucide="layers-3" class="h-4 w-4"></i>
                        Tarifni o‘zgartirish
                    </a>
                    <a href="{{ route('billing.payments.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        <i data-lucide="credit-card" class="h-4 w-4"></i>
                        To‘lovlar tarixi
                    </a>
                </div>
            </x-slot:actions>
        </x-admin.page-intro>

        @if($currentSubscription)
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-stat-card title="Joriy reja" :value="$currentSubscription->plan?->name ?? 'Trial'" icon="badge-check" />
                <x-stat-card title="Holat" :value="ucfirst($currentSubscription->status)" icon="shield-check" />
                <x-stat-card title="Boshlangan" :value="optional($currentSubscription->starts_at)->format('d.m.Y') ?? '—'" icon="calendar-days" />
                <x-stat-card title="Tugash sanasi" :value="optional($currentSubscription->current_period_end)->format('d.m.Y') ?? '—'" icon="calendar-range" />
            </div>
        @endif

        <div class="space-y-4">
            @forelse($subscriptions as $subscription)
                <div class="rounded-[30px] border border-slate-200/80 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $subscription->plan?->name ?? 'Trial' }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array($subscription->status, ['active', 'trial'], true) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : ($subscription->status === 'expired' ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200') }}">{{ ucfirst($subscription->status) }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $subscription->notes ?: 'Subscription lifecycle yozuvi.' }}</p>
                        </div>
                        <a href="{{ route('billing.plans.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                            <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
                            Tarifni boshqarish
                        </a>
                    </div>

                    <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">
                            <p class="text-xs text-slate-400">Boshlanish</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ optional($subscription->starts_at)->format('d.m.Y') ?? '—' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">
                            <p class="text-xs text-slate-400">Tugash</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ optional($subscription->expires_at)->format('d.m.Y') ?? optional($subscription->trial_ends_at)->format('d.m.Y') ?? '—' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">
                            <p class="text-xs text-slate-400">Keyingi billing</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ optional($subscription->renews_at)->format('d.m.Y') ?? '—' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">
                            <p class="text-xs text-slate-400">Narx</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ number_format($subscription->amount, 0, '.', ' ') }} {{ $subscription->currency }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">
                            <p class="text-xs text-slate-400">To‘lov manbasi</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $subscription->sourcePayment?->invoice_number ?? 'Avtomatik / Trial' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <x-admin.empty-state icon="repeat" title="Obuna topilmadi" text="Tarif tanlangandan keyin obuna tarixi shu yerda ko‘rinadi." action-href="{{ route('billing.plans.index') }}" action-label="Tarif tanlash" />
            @endforelse
        </div>

        <div>{{ $subscriptions->links() }}</div>
    </div>
</x-app-layout>
