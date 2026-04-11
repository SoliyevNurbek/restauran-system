<x-app-layout title="To'lovni yakunlash" pageTitle="To'lovni yakunlash" pageSubtitle="Tanlangan provider orqali billing to'lovini xavfsiz yakunlang.">
    <div class="grid gap-6 xl:grid-cols-[1fr_0.8fr]">
        <x-admin.section-card icon="shield-check" title="Checkout" subtitle="Invoice va provider ma'lumotlari tayyorlandi.">
            <div class="space-y-4">
                <div class="rounded-[28px] bg-gradient-to-br from-slate-950 via-slate-900 to-slate-700 p-5 text-white">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-300">Invoice</p>
                    <p class="mt-2 text-2xl font-semibold">{{ $payment->invoice_number }}</p>
                    <p class="mt-2 text-sm text-slate-300">{{ $payment->plan?->name ?? 'Tarif' }} · {{ number_format($payment->amount, 0, '.', ' ') }} {{ $payment->currency }}</p>
                </div>

                @if($checkout['type'] === 'form')
                    <form id="checkoutForm" method="{{ $checkout['method'] }}" action="{{ $checkout['url'] }}" class="space-y-3">
                        @foreach($checkout['fields'] as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                            <i data-lucide="mouse-pointer-click" class="h-4 w-4"></i>
                            Click checkoutga o'tish
                        </button>
                    </form>
                @elseif($checkout['type'] === 'redirect')
                    <a href="{{ $checkout['url'] }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        <i data-lucide="wallet-cards" class="h-4 w-4"></i>
                        Payme checkoutga o'tish
                    </a>
                @elseif($checkout['type'] === 'manual_telegram')
                    <div class="space-y-3">
                        <div class="rounded-[28px] border {{ $checkout['telegram_connected'] ? 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900/50 dark:bg-sky-950/20 dark:text-sky-200' : 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-200' }} p-5 text-sm">
                            @if($checkout['telegram_connected'])
                                Telegram ulanib bo'lgan. To'lov yo'riqnomasi va keyingi xabarlar sizning chat ID'ingizga yuboriladi.
                            @else
                                Telegram ulanmagan. Avval botni ulang, keyin shu oynadan to'lov ko'rsatmasini oling.
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @if(! $checkout['telegram_connected'] && $checkout['link_deep_link'])
                                <a href="{{ $checkout['link_deep_link'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                    <i data-lucide="link" class="h-4 w-4"></i>
                                    Botni ulash
                                </a>
                            @endif

                            @if($checkout['telegram_connected'] && $checkout['deep_link'])
                                <a href="{{ $checkout['deep_link'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                                    <i data-lucide="send" class="h-4 w-4"></i>
                                    To'lov ko'rsatmasini ochish
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="rounded-[28px] border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-200">
                        Manual to'lov yaratildi. Superadmin tasdiqlagach obuna avtomatik faollashadi.
                    </div>
                @endif
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="info" title="To'lov tafsiloti" subtitle="Status yangilangach dashboard va obuna widgetlari avtomatik ko'rinadi.">
            <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">Provider: <strong>{{ strtoupper($payment->provider) }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">Usul: <strong>{{ $payment->method ?: '—' }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">Maqsad: <strong>{{ str($payment->payment_for)->headline() }}</strong></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/60">Holat: <strong>{{ ucfirst($payment->status) }}</strong></div>
                <a href="{{ route('billing.payments.index', ['highlight' => $payment->id]) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                    <i data-lucide="history" class="h-4 w-4"></i>
                    To'lov tarixiga qaytish
                </a>
            </div>
        </x-admin.section-card>
    </div>
</x-app-layout>
