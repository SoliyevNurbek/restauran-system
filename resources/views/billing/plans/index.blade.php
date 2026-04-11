<x-app-layout title="Tariflar" pageTitle="Tariflar" pageSubtitle="Biznesingiz uchun mos SaaS rejasini tanlang va billingni shu yerdan boshqaring.">
    <div class="space-y-6">
        <x-admin.page-intro eyebrow="SaaS Billing" title="Tariflar" subtitle="Basic, Pro va Premium rejalari bilan funksional imkoniyatlar, narx va obuna holatini bir joyda ko'ring.">
            <x-slot:actions>
                <a href="{{ route('billing.subscriptions.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <i data-lucide="repeat" class="h-4 w-4"></i>
                    Obuna tafsiloti
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        @if(! auth()->user()?->venueConnection?->telegram_chat_id)
            <div class="rounded-[28px] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-200">
                Telegram ulanmagan. Manual to'lov ko'rsatmasini olish uchun avval <a href="{{ route('settings.edit', ['section' => 'integrations']) }}" class="font-semibold underline">Integratsiya</a> bo'limida botni ulang.
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-3">
            @foreach($plans as $plan)
                @php
                    $isCurrent = (int) $currentSubscription?->subscription_plan_id === (int) $plan->id && in_array($currentSubscription?->status, ['active', 'trial'], true);
                    $isTrial = $currentSubscription?->status === 'trial' && $isCurrent;
                @endphp
                <div class="group relative overflow-hidden rounded-[32px] border border-slate-200/80 bg-white p-6 shadow-soft transition duration-300 hover:-translate-y-1 hover:shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $plan->slug === 'premium' ? 'from-amber-400 via-orange-500 to-rose-500' : ($plan->slug === 'pro' ? 'from-sky-400 via-cyan-500 to-emerald-500' : 'from-slate-400 via-slate-500 to-slate-700') }}"></div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Tarif</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $plan->name }}</h3>
                        </div>
                        @if($isCurrent)
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300">{{ $isTrial ? 'Trial' : 'Joriy tarif' }}</span>
                        @endif
                    </div>

                    <div class="mt-6">
                        <p class="text-4xl font-semibold text-slate-950 dark:text-white">{{ number_format($plan->amount, 0, '.', ' ') }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $plan->currency }} / {{ $plan->duration_days }} kun</p>
                        <p class="mt-4 text-sm leading-6 text-slate-500">{{ $plan->description }}</p>
                    </div>

                    <div class="mt-6 space-y-3">
                        @foreach($plan->features ?? [] as $feature)
                            <div class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:bg-slate-950/60 dark:text-slate-200">
                                <i data-lucide="check" class="h-4 w-4 text-emerald-500"></i>
                                <span>{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 grid gap-3">
                        @if($isCurrent)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">Joriy tarif faol</div>
                        @endif

                        @foreach([
                            ['provider' => 'click', 'label' => 'Click bilan to\'lash', 'icon' => 'mouse-pointer-click'],
                            ['provider' => 'payme', 'label' => 'Payme bilan to\'lash', 'icon' => 'wallet-cards'],
                            ['provider' => 'manual', 'label' => 'Telegram orqali to\'lov', 'icon' => 'send'],
                        ] as $action)
                            <form method="POST" action="{{ route('billing.checkout.store', $plan) }}">
                                @csrf
                                <input type="hidden" name="provider" value="{{ $action['provider'] }}">
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl {{ $action['provider'] === 'click' ? 'bg-slate-950 text-white dark:bg-white dark:text-slate-950' : 'border border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200' }} px-4 py-3 text-sm font-semibold transition hover:-translate-y-0.5">
                                    <i data-lucide="{{ $action['icon'] }}" class="h-4 w-4"></i>
                                    {{ $isCurrent ? "Tarifni uzaytirish" : "Tanlash" }} · {{ $action['label'] }}
                                </button>
                            </form>
                        @endforeach

                        @if(config('billing.testing.enabled'))
                            <form method="POST" action="{{ route('billing.checkout.store', $plan) }}">
                                @csrf
                                <input type="hidden" name="provider" value="test">
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-dashed border-primary-300 bg-primary-50 px-4 py-3 text-sm font-semibold text-primary-800 transition hover:bg-primary-100 dark:border-primary-800 dark:bg-primary-950/30 dark:text-primary-200">
                                    <i data-lucide="flask-conical" class="h-4 w-4"></i>
                                    Test to'lovi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
