@php
    $nextBillingDate = $subscription?->renews_at ?? $subscription?->trial_ends_at ?? $subscription?->expires_at;
    $remainingDays = $nextBillingDate ? max(now()->startOfDay()->diffInDays($nextBillingDate->copy()->startOfDay(), false), 0) : null;
    $telegramConnected = filled($telegramVenue?->telegram_chat_id);
@endphp

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Tarif" :value="$subscription?->plan?->name ?? 'Basic'" icon="badge-check" />
        <x-stat-card title="Holat" :value="ucfirst($subscription?->status ?? 'trial')" icon="shield-check" />
        <x-stat-card title="Billing kuni" :value="$nextBillingDate?->format('d.m.Y') ?? '—'" icon="calendar-range" />
        <x-stat-card title="Qolgan muddat" :value="$remainingDays !== null ? $remainingDays.' kun' : '—'" icon="clock-3" />
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-admin.section-card icon="send" title="Telegram ulash" subtitle="Bitta platforma boti ishlatiladi, ammo xabarlar faqat sizning biznes chat ID'ingizga yuboriladi.">
            <div class="space-y-4">
                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/60">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Ulanish holati</p>
                            <p class="mt-1 text-sm text-slate-500">
                                @if($telegramConfigured)
                                    Telegram billing va tenant alertlari shu integratsiya orqali yuboriladi.
                                @else
                                    Telegram workflow hozircha superadmin tomonidan sozlanmagan.
                                @endif
                            </p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $telegramConnected ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300' }}">
                            {{ $telegramConnected ? 'Ulangan' : 'Ulanmagan' }}
                        </span>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">
                            <p class="text-xs text-slate-400">Bot</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $telegramBotUsername ? '@'.$telegramBotUsername : 'Username kiritilmagan' }}</p>
                        </div>
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">
                            <p class="text-xs text-slate-400">Chat ID</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $telegramMask }}</p>
                        </div>
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">
                            <p class="text-xs text-slate-400">Username</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $telegramVenue?->telegram_username ? '@'.$telegramVenue->telegram_username : 'Ulanmagan' }}</p>
                        </div>
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">
                            <p class="text-xs text-slate-400">Ulangan sana</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $telegramVenue?->telegram_verified_at?->format('d.m.Y H:i') ?? 'Tasdiqlanmagan' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Botni ulash bo'yicha yo'riqnoma</p>
                    <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">1. Pastdagi tugma orqali botni oching.</div>
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">2. Botga yuborilgan start kodi sizning biznesingiz bilan chatni bog'laydi.</div>
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">3. Shundan keyin to'lov yo'riqnomasi, tasdiqlash va eslatmalar faqat shu chatga yuboriladi.</div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($telegramConfigured && $telegramLink)
                            <a href="{{ $telegramLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                <i data-lucide="send" class="h-4 w-4"></i>
                                Botga ulash
                            </a>
                        @endif

                        <form method="POST" action="{{ route('settings.telegram.regenerate') }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                Yangi link yaratish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="bell-ring" title="Telegram bildirishnomalari" subtitle="Faqat sizning biznesingizga tegishli billing va alertlar yuboriladi.">
            <div class="grid gap-4">
                <form method="POST" action="{{ route('settings.telegram.update') }}" class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/60">
                    @csrf
                    @method('PUT')
                    <label class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 shadow-sm dark:bg-slate-900 dark:text-slate-200">
                        <input type="checkbox" name="telegram_notifications_enabled" value="1" @checked($telegramVenue?->telegram_notifications_enabled ?? true) class="rounded border-slate-300">
                        <span>Tenant bildirishnomalari shu chatga yuborilsin</span>
                    </label>
                    <button class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        Saqlash
                    </button>
                </form>

                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Qanday xabarlar yuboriladi</p>
                    <div class="mt-4 grid gap-3">
                        @foreach([
                            'Obuna bo‘yicha to‘lov yo‘riqnomalari',
                            'To‘lov tasdiqlandi yoki rad etildi xabarlari',
                            'Obuna muddati tugash eslatmalari',
                            'Superadmin tomonidan yuborilgan tenant xabarlari',
                        ] as $item)
                            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 shadow-sm dark:bg-slate-900 dark:text-slate-200">
                                <i data-lucide="check" class="h-4 w-4 text-emerald-500"></i>
                                <span>{{ $item }}</span>
                            </div>
                        @endforeach
                    </div>
                    @unless($telegramConnected)
                        <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-200">
                            Telegram ulanmagan. To'lov ko'rsatmasini olish uchun avval botni ulang.
                        </div>
                    @endunless
                </div>
            </div>
        </x-admin.section-card>
    </div>
</div>
