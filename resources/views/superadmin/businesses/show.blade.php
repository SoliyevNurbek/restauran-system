<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <x-superadmin.panel title="Overview" subtitle="Owner, status, health va asosiy tenant signal." icon="building-2">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Owner</p><p class="mt-1 font-semibold text-slate-900">{{ $business->owner_name }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Admin user</p><p class="mt-1 font-semibold text-slate-900">{{ $business->adminUser?->name ?? 'Biriktirilmagan' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Telefon</p><p class="mt-1 font-semibold text-slate-900">{{ $business->phone ?: "Ko'rsatilmagan" }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Email / username</p><p class="mt-1 font-semibold text-slate-900">{{ $business->email ?: $business->username }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Subscription</p><p class="mt-1 font-semibold text-slate-900">{{ $business->latestSubscription?->plan?->name ?? "Yo'q" }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Revenue</p><p class="mt-1 font-semibold text-slate-900">{{ number_format((float) $business->revenue_total, 0, '.', ' ') }} UZS</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Telegram</p><p class="mt-1 font-semibold text-slate-900">{{ $business->telegram_chat_id ? 'Ulangan' : 'Ulanmagan' }}</p><p class="mt-1 text-xs text-slate-500">{{ $business->telegram_username ? '@'.$business->telegram_username : 'Username yo‘q' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Chat / linked</p><p class="mt-1 font-semibold text-slate-900">{{ $business->telegram_chat_id ? str_repeat('*', max(strlen($business->telegram_chat_id) - 4, 0)).substr($business->telegram_chat_id, -4) : 'Ulanmagan' }}</p><p class="mt-1 text-xs text-slate-500">{{ $business->telegram_verified_at?->format('d.m.Y H:i') ?? 'Tasdiqlanmagan' }}</p></div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <x-superadmin.status-badge :status="$business->status" />
                <x-superadmin.status-badge status="info" :label="'Health: '.str($business->health_status)->headline()" />
                @if($business->latestSubscription)
                    <x-superadmin.status-badge :status="$business->latestSubscription->status" :label="'Subscription: '.str($business->latestSubscription->status)->headline()" />
                @endif
            </div>

            @if($business->message)
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4 text-sm leading-6 text-slate-600">
                    <p class="font-semibold text-slate-900">Ro'yxatdan o'tish izohi</p>
                    <p class="mt-2">{{ $business->message }}</p>
                </div>
            @endif
        </x-superadmin.panel>

        <x-superadmin.panel title="Status controls" subtitle="Moderation, suspend va critical action oqimi." icon="shield-check">
            <form method="POST" action="{{ route('superadmin.businesses.update', $business) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</label>
                        <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            @foreach(['pending','under_review','approved','rejected','suspended'] as $status)
                                <option value="{{ $status }}" @selected($business->status === $status)>{{ str($status)->replace('_', ' ')->headline() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Health</label>
                        <input type="text" name="health_status" value="{{ old('health_status', $business->health_status) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Hall count</label>
                        <input type="number" min="0" name="halls_count" value="{{ old('halls_count', $business->halls_count) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Booking count</label>
                        <input type="number" min="0" name="bookings_count" value="{{ old('bookings_count', $business->bookings_count) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Revenue</label>
                    <input type="number" min="0" step="0.01" name="revenue_total" value="{{ old('revenue_total', $business->revenue_total) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Internal notes</label>
                    <textarea name="approval_notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('approval_notes', $business->approval_notes) }}</textarea>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Reason</label>
                    <input type="text" name="review_reason" value="{{ old('review_reason', $business->review_reason) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                    <input type="checkbox" name="send_telegram" value="1" class="rounded border-slate-300">
                    <span>Muhim o'zgarishda Telegram alert yuborilsin</span>
                </label>
                <button class="w-full rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">O'zgarishlarni saqlash</button>
            </form>
        </x-superadmin.panel>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <x-superadmin.panel title="Payment history" subtitle="Biznesga bog'langan billing recordlar." icon="credit-card" class="xl:col-span-2">
            @forelse($business->subscriptionPayments->take(8) as $payment)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $payment->invoice_number ?: "Invoice yo'q" }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ number_format((float) $payment->amount, 0, '.', ' ') }} {{ $payment->currency }}  -  {{ $payment->paymentMethod?->label ?? "Usul yo'q" }}</p>
                    </div>
                    <x-superadmin.status-badge :status="$payment->status" />
                </div>
            @empty
                <x-superadmin.empty-state icon="wallet-cards" title="Payment history bo'sh" description="Bu biznes bo'yicha hali subscription payment yozuvlari yo'q." />
            @endforelse
        </x-superadmin.panel>

        <x-superadmin.panel title="Activity & audit" subtitle="Security event va moderation izi." icon="history">
            @forelse($business->securityEvents->take(8) as $event)
                <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-slate-900">{{ $event->title }}</p>
                        <x-superadmin.status-badge :status="$event->severity" />
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ $event->description }}</p>
                    <p class="mt-2 text-xs text-slate-400">{{ optional($event->occurred_at)->diffForHumans() }}</p>
                </div>
            @empty
                <x-superadmin.empty-state icon="history" title="Activity log topilmadi" description="Biznes bo'yicha security yoki audit eventlar paydo bo'lganda shu yerda ko'rinadi." />
            @endforelse
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
